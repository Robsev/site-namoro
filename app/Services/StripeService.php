<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;
use Stripe\PaymentMethod;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a Stripe customer
     */
    public function createCustomer(User $user): Customer
    {
        try {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                    'app' => 'amigosparasempre'
                ]
            ]);

            // Update user with Stripe customer ID
            $user->update(['stripe_customer_id' => $customer->id]);

            return $customer;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Customer Creation Failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get or create Stripe customer for user
     */
    public function getOrCreateCustomer(User $user): Customer
    {
        if ($user->stripe_customer_id) {
            try {
                return Customer::retrieve($user->stripe_customer_id);
            } catch (ApiErrorException $e) {
                Log::warning('Stripe Customer Not Found', [
                    'user_id' => $user->id,
                    'stripe_customer_id' => $user->stripe_customer_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $this->createCustomer($user);
    }

    /**
     * Create a subscription
     */
    public function createSubscription(User $user, string $priceId, string $paymentMethodId): array
    {
        try {
            $customer = $this->getOrCreateCustomer($user);

            // Attach payment method to customer
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customer->id]);

            // Set as default payment method
            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId
                ]
            ]);

            // Create subscription
            $subscription = StripeSubscription::create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $priceId]
                ],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            return [
                'subscription' => $subscription,
                'client_secret' => $subscription->latest_invoice->payment_intent->client_secret,
                'status' => $subscription->status
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Subscription Creation Failed', [
                'user_id' => $user->id,
                'price_id' => $priceId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Confirm subscription payment
     */
    public function confirmSubscription(string $subscriptionId): StripeSubscription
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);
            
            if ($subscription->status === 'active') {
                return $subscription;
            }

            throw new \Exception('Subscription is not active');
        } catch (ApiErrorException $e) {
            Log::error('Stripe Subscription Confirmation Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(string $subscriptionId, bool $immediately = false): StripeSubscription
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);
            
            if ($immediately) {
                $subscription->cancel();
            } else {
                $subscription->cancel_at_period_end = true;
                $subscription->save();
            }

            return $subscription;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Subscription Cancellation Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(string $subscriptionId): StripeSubscription
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);
            
            if ($subscription->cancel_at_period_end) {
                $subscription->cancel_at_period_end = false;
                $subscription->save();
            }

            return $subscription;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Subscription Resume Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(string $subscriptionId, string $paymentMethodId): StripeSubscription
    {
        try {
            $subscription = StripeSubscription::retrieve($subscriptionId);
            
            // Attach payment method to customer
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $subscription->customer]);

            // Update subscription with new payment method
            StripeSubscription::update($subscriptionId, [
                'default_payment_method' => $paymentMethodId
            ]);

            // Update customer default payment method
            Customer::update($subscription->customer, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId
                ]
            ]);

            return StripeSubscription::retrieve($subscriptionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Method Update Failed', [
                'subscription_id' => $subscriptionId,
                'payment_method_id' => $paymentMethodId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get subscription details
     */
    public function getSubscription(string $subscriptionId): StripeSubscription
    {
        try {
            return StripeSubscription::retrieve($subscriptionId);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Subscription Retrieval Failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get customer subscriptions
     */
    public function getCustomerSubscriptions(string $customerId): array
    {
        try {
            $subscriptions = StripeSubscription::all([
                'customer' => $customerId,
                'status' => 'all'
            ]);

            return $subscriptions->data;
        } catch (ApiErrorException $e) {
            Log::error('Stripe Customer Subscriptions Retrieval Failed', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle webhook events
     */
    public function handleWebhook(string $payload, string $signature): array
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );

            return [
                'type' => $event->type,
                'data' => $event->data->object,
                'event_id' => $event->id
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Verification Failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get price IDs for our plans
     */
    public function getPriceIds(): array
    {
        return [
            'premium_monthly' => config('services.stripe.premium_monthly_price_id'),
            'premium_yearly' => config('services.stripe.premium_yearly_price_id'),
        ];
    }

    /**
     * Create payment intent for one-time payments
     */
    public function createPaymentIntent(User $user, int $amount, string $currency = 'brl'): PaymentIntent
    {
        try {
            $customer = $this->getOrCreateCustomer($user);

            return PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'customer' => $customer->id,
                'metadata' => [
                    'user_id' => $user->id,
                    'app' => 'amigosparasempre'
                ]
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe Payment Intent Creation Failed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
