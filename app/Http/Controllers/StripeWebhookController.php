<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Services\StripeService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    protected $stripeService;
    protected $notificationService;

    public function __construct(StripeService $stripeService, NotificationService $notificationService)
    {
        $this->stripeService = $stripeService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->handleWebhook($payload, $signature);
            
            Log::info('Stripe Webhook Received', [
                'event_type' => $event['type'],
                'event_id' => $event['event_id']
            ]);

            switch ($event['type']) {
                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event['data']);
                    break;
                
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event['data']);
                    break;
                
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event['data']);
                    break;
                
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event['data']);
                    break;
                
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event['data']);
                    break;
                
                case 'customer.subscription.trial_will_end':
                    $this->handleTrialWillEnd($event['data']);
                    break;
                
                default:
                    Log::info('Unhandled Stripe Webhook Event', [
                        'event_type' => $event['type']
                    ]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return response()->json(['error' => 'Webhook failed'], 400);
        }
    }

    /**
     * Handle subscription created event
     */
    private function handleSubscriptionCreated($subscription)
    {
        try {
            DB::beginTransaction();

            $user = User::where('stripe_customer_id', $subscription->customer)->first();
            
            if (!$user) {
                Log::error('User not found for Stripe customer', [
                    'customer_id' => $subscription->customer,
                    'subscription_id' => $subscription->id
                ]);
                DB::rollBack();
                return;
            }

            // Create or update subscription record
            $localSubscription = Subscription::updateOrCreate(
                ['stripe_subscription_id' => $subscription->id],
                [
                    'user_id' => $user->id,
                    'stripe_customer_id' => $subscription->customer,
                    'plan' => $this->getPlanFromPriceId($subscription->items->data[0]->price->id),
                    'status' => $subscription->status,
                    'amount' => $subscription->items->data[0]->price->unit_amount / 100, // Convert from cents
                    'currency' => strtoupper($subscription->items->data[0]->price->currency),
                    'starts_at' => now()->createFromTimestamp($subscription->current_period_start),
                    'ends_at' => now()->createFromTimestamp($subscription->current_period_end),
                    'trial_ends_at' => $subscription->trial_end ? now()->createFromTimestamp($subscription->trial_end) : null,
                    'metadata' => [
                        'stripe_subscription_id' => $subscription->id,
                        'stripe_customer_id' => $subscription->customer,
                        'created_via' => 'webhook'
                    ]
                ]
            );

            // Update user subscription status if subscription is active
            if ($subscription->status === 'active') {
                $user->update([
                    'subscription_type' => 'premium',
                    'subscription_expires_at' => $localSubscription->ends_at
                ]);

                // Send notification
                $this->notificationService->notifySubscriptionChange($user, 'premium');
            }

            DB::commit();

            Log::info('Subscription created successfully', [
                'user_id' => $user->id,
                'subscription_id' => $localSubscription->id,
                'stripe_subscription_id' => $subscription->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle subscription created', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle subscription updated event
     */
    private function handleSubscriptionUpdated($subscription)
    {
        try {
            DB::beginTransaction();

            $localSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
            
            if (!$localSubscription) {
                Log::error('Local subscription not found', [
                    'stripe_subscription_id' => $subscription->id
                ]);
                DB::rollBack();
                return;
            }

            $user = $localSubscription->user;

            // Update subscription
            $localSubscription->update([
                'status' => $subscription->status,
                'starts_at' => now()->createFromTimestamp($subscription->current_period_start),
                'ends_at' => now()->createFromTimestamp($subscription->current_period_end),
                'trial_ends_at' => $subscription->trial_end ? now()->createFromTimestamp($subscription->trial_end) : null,
                'canceled_at' => $subscription->canceled_at ? now()->createFromTimestamp($subscription->canceled_at) : null,
            ]);

            // Update user subscription status
            if ($subscription->status === 'active') {
                $user->update([
                    'subscription_type' => 'premium',
                    'subscription_expires_at' => $localSubscription->ends_at
                ]);
            } elseif (in_array($subscription->status, ['canceled', 'unpaid', 'past_due'])) {
                $user->update([
                    'subscription_type' => 'free',
                    'subscription_expires_at' => null
                ]);
            }

            DB::commit();

            Log::info('Subscription updated successfully', [
                'user_id' => $user->id,
                'subscription_id' => $localSubscription->id,
                'status' => $subscription->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle subscription updated', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle subscription deleted event
     */
    private function handleSubscriptionDeleted($subscription)
    {
        try {
            DB::beginTransaction();

            $localSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
            
            if (!$localSubscription) {
                Log::error('Local subscription not found for deletion', [
                    'stripe_subscription_id' => $subscription->id
                ]);
                DB::rollBack();
                return;
            }

            $user = $localSubscription->user;

            // Update subscription status
            $localSubscription->update([
                'status' => 'canceled',
                'canceled_at' => now()
            ]);

            // Update user subscription status
            $user->update([
                'subscription_type' => 'free',
                'subscription_expires_at' => null
            ]);

            DB::commit();

            Log::info('Subscription deleted successfully', [
                'user_id' => $user->id,
                'subscription_id' => $localSubscription->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle subscription deleted', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle payment succeeded event
     */
    private function handlePaymentSucceeded($invoice)
    {
        try {
            $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
            
            if ($subscription) {
                $user = $subscription->user;
                
                // Ensure user has premium status
                $user->update([
                    'subscription_type' => 'premium',
                    'subscription_expires_at' => $subscription->ends_at
                ]);

                Log::info('Payment succeeded for subscription', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoice->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle payment succeeded', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle payment failed event
     */
    private function handlePaymentFailed($invoice)
    {
        try {
            $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
            
            if ($subscription) {
                $user = $subscription->user;
                
                // Send notification about failed payment
                $this->notificationService->notifyPaymentFailed($user, $invoice);

                Log::info('Payment failed for subscription', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $invoice->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle payment failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle trial will end event
     */
    private function handleTrialWillEnd($subscription)
    {
        try {
            $localSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
            
            if ($localSubscription) {
                $user = $localSubscription->user;
                
                // Send notification about trial ending
                $this->notificationService->notifyTrialEnding($user, $subscription);

                Log::info('Trial ending notification sent', [
                    'user_id' => $user->id,
                    'subscription_id' => $localSubscription->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle trial will end', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get plan name from Stripe price ID
     */
    private function getPlanFromPriceId($priceId): string
    {
        $priceIds = $this->stripeService->getPriceIds();
        
        foreach ($priceIds as $plan => $id) {
            if ($id === $priceId) {
                return $plan;
            }
        }

        return 'unknown';
    }
}
