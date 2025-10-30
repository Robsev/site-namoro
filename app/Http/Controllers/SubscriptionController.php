<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $stripeService;
    protected $notificationService;
    protected $mode;

    public function __construct(StripeService $stripeService, NotificationService $notificationService)
    {
        $this->stripeService = $stripeService;
        $this->notificationService = $notificationService;
        $this->mode = config('services.subscriptions.mode', 'stripe');
    }

    /**
     * Show subscription plans
     */
    public function plans()
    {
        $user = Auth::user();
        $currentSubscription = $user->subscriptions()->active()->first();
        
        // MOCK mode: exibir planos desabilitados com aviso amigável
        if ($this->mode === 'mock') {
            return view('subscriptions.plans', [
                'plans' => $this->getMockPlans(),
                'currentSubscription' => $currentSubscription,
                'stripeConfigured' => false,
                'mockMode' => true,
                'warning' => 'Assinaturas estarão disponíveis em breve. O serviço é gratuito por enquanto.'
            ]);
        }

        // Check if Stripe is configured
        $stripeConfigured = !empty(config('services.stripe.key')) && 
                           !empty(config('services.stripe.secret')) &&
                           !empty(config('services.stripe.premium_monthly_price_id')) &&
                           !empty(config('services.stripe.premium_yearly_price_id'));

        if (!$stripeConfigured) {
            // Show warning that Stripe is not configured
            return view('subscriptions.plans', [
                'plans' => $this->getMockPlans(),
                'currentSubscription' => $currentSubscription,
                'stripeConfigured' => false,
                'warning' => 'Sistema de pagamentos não configurado. Entre em contato com o administrador.'
            ]);
        }

        try {
            // Get real Stripe prices
            $plans = $this->getStripePlans();
        } catch (\Exception $e) {
            Log::error('Failed to fetch Stripe plans', [
                'error' => $e->getMessage()
            ]);
            
            // Fallback to mock plans if Stripe fails
            $plans = $this->getMockPlans();
        }

        return view('subscriptions.plans', compact('plans', 'currentSubscription', 'stripeConfigured'));
    }

    /**
     * Get Stripe plans with real prices
     */
    private function getStripePlans()
    {
        $priceIds = $this->stripeService->getPriceIds();
        
        return [
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
                'price_id' => $priceIds['premium_monthly'],
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
                'price_id' => $priceIds['premium_yearly'],
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
    }

    /**
     * Get mock plans (fallback)
     */
    private function getMockPlans()
    {
        return [
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
                'popular' => true,
                'disabled' => true,
                'disabled_message' => 'Sistema de pagamentos em manutenção'
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
                'savings' => __('messages.subscriptions.savings_two_months'),
                'disabled' => true,
                'disabled_message' => 'Sistema de pagamentos em manutenção'
            ]
        ];
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
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }
        // Check if Stripe is configured
        $stripeConfigured = !empty(config('services.stripe.key')) && 
                           !empty(config('services.stripe.secret')) &&
                           !empty(config('services.stripe.premium_monthly_price_id')) &&
                           !empty(config('services.stripe.premium_yearly_price_id'));

        if (!$stripeConfigured) {
            return redirect()->back()->with('error', 'Sistema de pagamentos não configurado. Entre em contato com o administrador.');
        }

        $request->validate([
            'plan' => 'required|in:premium_monthly,premium_yearly',
            'payment_method_id' => 'required|string'
        ]);

        $user = Auth::user();
        $plan = $request->plan;
        
        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()->active()->first();
        if ($activeSubscription) {
            return redirect()->back()->with('error', 'Você já possui uma assinatura ativa.');
        }

        try {
            // Get price ID for the plan
            $priceIds = $this->stripeService->getPriceIds();
            $priceId = $priceIds[$plan] ?? null;

            if (!$priceId) {
                return redirect()->back()->with('error', 'Plano não encontrado.');
            }

            // Create Stripe subscription
            $result = $this->stripeService->createSubscription($user, $priceId, $request->payment_method_id);

            // If subscription requires payment confirmation, redirect to payment page
            if ($result['status'] === 'incomplete') {
                return redirect()->route('subscriptions.payment', [
                    'subscription_id' => $result['subscription']->id,
                    'client_secret' => $result['client_secret']
                ]);
            }

            // If subscription is active, create local record
            if ($result['status'] === 'active') {
                $this->createLocalSubscription($user, $result['subscription']);
                
                return redirect()->route('subscriptions.show')
                    ->with('success', 'Assinatura criada com sucesso! Bem-vindo ao Premium!');
            }

            return redirect()->back()->with('error', 'Erro ao criar assinatura. Tente novamente.');

        } catch (\Exception $e) {
            Log::error('Subscription creation failed', [
                'user_id' => $user->id,
                'plan' => $plan,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao processar pagamento. Tente novamente.');
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Subscription $subscription)
    {
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }
        $user = Auth::user();
        
        // Ensure user can only cancel their own subscription
        if ($subscription->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        try {
            // Cancel Stripe subscription
            $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);

            return redirect()->route('subscriptions.show')
                ->with('success', 'Assinatura cancelada com sucesso.');

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao cancelar assinatura. Tente novamente.');
        }
    }

    /**
     * Resume subscription
     */
    public function resume(Subscription $subscription)
    {
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }
        $user = Auth::user();
        
        // Ensure user can only resume their own subscription
        if ($subscription->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        try {
            // Resume Stripe subscription
            $this->stripeService->resumeSubscription($subscription->stripe_subscription_id);

            return redirect()->route('subscriptions.show')
                ->with('success', 'Assinatura reativada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Subscription resume failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao reativar assinatura. Tente novamente.');
        }
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request, Subscription $subscription)
    {
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }
        $user = Auth::user();
        
        // Ensure user can only update their own subscription
        if ($subscription->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $request->validate([
            'payment_method_id' => 'required|string'
        ]);

        try {
            // Update Stripe payment method
            $this->stripeService->updatePaymentMethod($subscription->stripe_subscription_id, $request->payment_method_id);

            return redirect()->route('subscriptions.show')
                ->with('success', 'Método de pagamento atualizado com sucesso.');

        } catch (\Exception $e) {
            Log::error('Payment method update failed', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar método de pagamento. Tente novamente.');
        }
    }

    /**
     * Show payment confirmation page
     */
    public function payment(Request $request)
    {
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }
        $subscriptionId = $request->get('subscription_id');
        $clientSecret = $request->get('client_secret');

        if (!$subscriptionId || !$clientSecret) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Parâmetros de pagamento inválidos.');
        }

        return view('subscriptions.payment', compact('subscriptionId', 'clientSecret'));
    }

    /**
     * Confirm payment and activate subscription
     */
    public function confirmPayment(Request $request)
    {
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }
        $request->validate([
            'subscription_id' => 'required|string'
        ]);

        try {
            $subscription = $this->stripeService->confirmSubscription($request->subscription_id);
            
            if ($subscription->status === 'active') {
                $user = Auth::user();
                $this->createLocalSubscription($user, $subscription);
                
                return redirect()->route('subscriptions.show')
                    ->with('success', 'Pagamento confirmado! Bem-vindo ao Premium!');
            }

            return redirect()->back()->with('error', 'Pagamento ainda não foi processado.');

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'subscription_id' => $request->subscription_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao confirmar pagamento. Tente novamente.');
        }
    }

    /**
     * Create local subscription record from Stripe subscription
     */
    private function createLocalSubscription(User $user, $stripeSubscription)
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'stripe_subscription_id' => $stripeSubscription->id,
            'stripe_customer_id' => $stripeSubscription->customer,
            'plan' => $this->getPlanFromPriceId($stripeSubscription->items->data[0]->price->id),
            'status' => $stripeSubscription->status,
            'amount' => $stripeSubscription->items->data[0]->price->unit_amount / 100,
            'currency' => strtoupper($stripeSubscription->items->data[0]->price->currency),
            'starts_at' => now()->createFromTimestamp($stripeSubscription->current_period_start),
            'ends_at' => now()->createFromTimestamp($stripeSubscription->current_period_end),
            'trial_ends_at' => $stripeSubscription->trial_end ? now()->createFromTimestamp($stripeSubscription->trial_end) : null,
            'metadata' => [
                'stripe_subscription_id' => $stripeSubscription->id,
                'stripe_customer_id' => $stripeSubscription->customer,
                'created_via' => 'web'
            ]
        ]);

        // Update user subscription status
        $user->update([
            'subscription_type' => 'premium',
            'subscription_expires_at' => $subscription->ends_at
        ]);

        // Send notification
        $this->notificationService->notifySubscriptionChange($user, 'premium');

        return $subscription;
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