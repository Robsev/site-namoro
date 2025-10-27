<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Show subscription plans
     */
    public function plans()
    {
        $user = Auth::user();
        $currentSubscription = $user->subscriptions()->active()->first();
        
        $plans = [
            'free' => [
                'name' => __('messages.subscriptions.free'),
                'price' => 0,
                'currency' => 'BRL',
                'interval' => 'forever',
                'features' => [
                    __('messages.subscriptions.feature_profile_photos', ['count' => 6]),
                    __('messages.subscriptions.feature_likes_per_day', ['count' => 5]),
                    __('messages.subscriptions.feature_super_likes', ['count' => 1]),
                    __('messages.subscriptions.feature_basic_chat'),
                    __('messages.subscriptions.feature_basic_matching')
                ],
                'limitations' => [
                    __('messages.subscriptions.limitation_no_filters'),
                    __('messages.subscriptions.limitation_no_who_liked'),
                    __('messages.subscriptions.limitation_no_boost')
                ]
            ],
            'premium_monthly' => [
                'name' => __('messages.subscriptions.premium_monthly'),
                'price' => 29.90,
                'currency' => 'BRL',
                'interval' => 'month',
                'features' => [
                    __('messages.subscriptions.feature_profile_photos', ['count' => 20]),
                    __('messages.subscriptions.feature_unlimited_likes'),
                    __('messages.subscriptions.feature_super_likes', ['count' => 5]),
                    __('messages.subscriptions.feature_advanced_chat'),
                    __('messages.subscriptions.feature_advanced_filters'),
                    __('messages.subscriptions.feature_see_who_liked'),
                    __('messages.subscriptions.feature_profile_boost'),
                    __('messages.subscriptions.feature_stealth_mode')
                ],
                'popular' => true
            ],
            'premium_yearly' => [
                'name' => __('messages.subscriptions.premium_yearly'),
                'price' => 299.90,
                'currency' => 'BRL',
                'interval' => 'year',
                'features' => [
                    __('messages.subscriptions.feature_profile_photos', ['count' => 20]),
                    __('messages.subscriptions.feature_unlimited_likes'),
                    __('messages.subscriptions.feature_super_likes', ['count' => 5]),
                    __('messages.subscriptions.feature_advanced_chat'),
                    __('messages.subscriptions.feature_advanced_filters'),
                    __('messages.subscriptions.feature_see_who_liked'),
                    __('messages.subscriptions.feature_profile_boost'),
                    __('messages.subscriptions.feature_stealth_mode'),
                    __('messages.subscriptions.feature_priority_support')
                ],
                'savings' => __('messages.subscriptions.savings_two_months')
            ]
        ];

        return view('subscriptions.plans', compact('plans', 'currentSubscription'));
    }

    /**
     * Show current subscription details
     */
    public function show()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->orderBy('created_at', 'desc')->get();
        $currentSubscription = $user->subscriptions()->active()->first();

        return view('subscriptions.show', compact('subscriptions', 'currentSubscription'));
    }

    /**
     * Create new subscription
     */
    public function create(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:premium_monthly,premium_yearly',
            'payment_method' => 'required|string'
        ]);

        $user = Auth::user();
        $plan = $request->plan;
        
        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()->active()->first();
        if ($activeSubscription) {
            return redirect()->back()->with('error', 'Você já possui uma assinatura ativa.');
        }

        // In a real implementation, you would integrate with Stripe here
        // For now, we'll create a mock subscription
        
        $planDetails = $this->getPlanDetails($plan);
        
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'stripe_subscription_id' => 'mock_sub_' . time(),
            'stripe_customer_id' => 'mock_customer_' . $user->id,
            'plan' => $plan,
            'status' => 'active',
            'amount' => $planDetails['price'],
            'currency' => 'BRL',
            'starts_at' => now(),
            'ends_at' => $plan === 'premium_monthly' ? now()->addMonth() : now()->addYear(),
            'metadata' => [
                'payment_method' => $request->payment_method,
                'created_via' => 'web'
            ]
        ]);

        // Update user subscription type
        $user->update([
            'subscription_type' => 'premium',
            'subscription_expires_at' => $subscription->ends_at
        ]);

        // Send notification
        $notificationService = new NotificationService();
        $notificationService->notifySubscriptionChange($user, 'premium');

        return redirect()->route('subscriptions.show')
            ->with('success', 'Assinatura criada com sucesso! Bem-vindo ao Premium!');
    }

    /**
     * Cancel subscription
     */
    public function cancel(Subscription $subscription)
    {
        $user = Auth::user();
        
        // Ensure user can only cancel their own subscription
        if ($subscription->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        // In a real implementation, you would cancel the Stripe subscription here
        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now()
        ]);

        // Update user subscription type
        $user->update([
            'subscription_type' => 'free',
            'subscription_expires_at' => null
        ]);

        return redirect()->route('subscriptions.show')
            ->with('success', 'Assinatura cancelada com sucesso.');
    }

    /**
     * Resume subscription
     */
    public function resume(Subscription $subscription)
    {
        $user = Auth::user();
        
        // Ensure user can only resume their own subscription
        if ($subscription->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        if ($subscription->status !== 'canceled') {
            return redirect()->back()->with('error', 'Esta assinatura não pode ser reativada.');
        }

        // In a real implementation, you would resume the Stripe subscription here
        $subscription->update([
            'status' => 'active',
            'canceled_at' => null
        ]);

        // Update user subscription type
        $user->update([
            'subscription_type' => 'premium',
            'subscription_expires_at' => $subscription->ends_at
        ]);

        return redirect()->route('subscriptions.show')
            ->with('success', 'Assinatura reativada com sucesso!');
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request, Subscription $subscription)
    {
        $user = Auth::user();
        
        // Ensure user can only update their own subscription
        if ($subscription->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $request->validate([
            'payment_method' => 'required|string'
        ]);

        // In a real implementation, you would update the Stripe payment method here
        $subscription->update([
            'metadata' => array_merge($subscription->metadata ?? [], [
                'payment_method' => $request->payment_method,
                'updated_at' => now()->toISOString()
            ])
        ]);

        return redirect()->route('subscriptions.show')
            ->with('success', 'Método de pagamento atualizado com sucesso.');
    }

    /**
     * Get subscription usage statistics
     */
    public function usage()
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->active()->first();
        
        if (!$subscription) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Você não possui uma assinatura ativa.');
        }

        // Calculate usage statistics
        $usage = [
            'photos_used' => $user->photos()->count(),
            'photos_limit' => $user->subscription_type === 'premium' ? 20 : 6,
            'likes_used_today' => $this->getLikesUsedToday($user),
            'likes_limit' => $user->subscription_type === 'premium' ? -1 : 5, // -1 means unlimited
            'super_likes_used_today' => $this->getSuperLikesUsedToday($user),
            'super_likes_limit' => $user->subscription_type === 'premium' ? 5 : 1,
            'boosts_used_this_month' => $this->getBoostsUsedThisMonth($user),
            'boosts_limit' => $user->subscription_type === 'premium' ? 5 : 0
        ];

        return view('subscriptions.usage', compact('subscription', 'usage'));
    }

    /**
     * Get plan details
     */
    private function getPlanDetails($plan)
    {
        $plans = [
            'premium_monthly' => [
                'price' => 29.90,
                'name' => 'Premium Mensal'
            ],
            'premium_yearly' => [
                'price' => 299.90,
                'name' => 'Premium Anual'
            ]
        ];

        return $plans[$plan] ?? $plans['premium_monthly'];
    }

    /**
     * Get likes used today
     */
    private function getLikesUsedToday($user)
    {
        return $user->matchesAsUser1()
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get super likes used today
     */
    private function getSuperLikesUsedToday($user)
    {
        return $user->matchesAsUser1()
            ->where('is_super_like', true)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Get boosts used this month
     */
    private function getBoostsUsedThisMonth($user)
    {
        // This would be implemented with a separate boosts table
        return 0;
    }
}