<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\CommerceGateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $commerceGateService;
    protected $notificationService;
    protected $mode;

    public function __construct(CommerceGateService $commerceGateService, NotificationService $notificationService)
    {
        $this->commerceGateService = $commerceGateService;
        $this->notificationService = $notificationService;
        $this->mode = config('services.subscriptions.mode', 'commercegate');
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

        // Check if CommerceGate is configured
        $commerceGateConfigured = !empty(config('services.commercegate.merchant_id')) && 
                                  !empty(config('services.commercegate.website_id'));

        if (!$commerceGateConfigured && $this->mode !== 'mock') {
            // Show warning that CommerceGate is not configured
            return view('subscriptions.plans', [
                'plans' => $this->getMockPlans(),
                'currentSubscription' => $currentSubscription,
                'commerceGateConfigured' => false,
                'warning' => 'Sistema de pagamentos não configurado. Entre em contato com o administrador.'
            ]);
        }

        // Get plans (CommerceGate ou mock)
        $plans = $this->getCommerceGatePlans();

        return view('subscriptions.plans', compact('plans', 'currentSubscription', 'commerceGateConfigured'));
    }

    /**
     * Get CommerceGate plans
     */
    private function getCommerceGatePlans()
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

        $request->validate([
            'plan' => 'required|in:premium_monthly,premium_yearly',
        ]);

        $user = Auth::user();
        $plan = $request->plan;
        
        // Check if user already has an active subscription
        $activeSubscription = $user->subscriptions()->active()->first();
        if ($activeSubscription) {
            return redirect()->back()->with('error', 'Você já possui uma assinatura ativa.');
        }

        // Get plan details
        $planDetails = $this->getPlanDetails($plan);
        $planAmount = $planDetails['price'] * 100; // Converter para centavos

        try {
            if ($this->mode === 'commercegate') {
                // CommerceGate integration
                $planCodes = $this->commerceGateService->getPlanCodes();
                $planCode = $planCodes[$plan] ?? null;

                if (!$planCode) {
                    return redirect()->back()->with('error', 'Plano não encontrado.');
                }

                // Gerar formulário de pagamento hospedado
                $paymentForm = $this->commerceGateService->generateHostedPaymentForm($user, [
                    'amount' => $planAmount,
                    'currency' => 'BRL',
                    'plan_code' => $planCode,
                    'interval' => $plan === 'premium_monthly' ? 'month' : 'year',
                    'description' => $planDetails['name'],
                ]);

                // Redirecionar para página de pagamento hospedada do CommerceGate
                return view('subscriptions.commercegate-payment', [
                    'formData' => $paymentForm,
                    'plan' => $plan,
                    'planName' => $planDetails['name'],
                ]);

            }

        } catch (\Exception $e) {
            Log::error('Subscription creation failed', [
                'user_id' => $user->id,
                'plan' => $plan,
                'mode' => $this->mode,
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
            if ($this->mode === 'commercegate' && $subscription->commercegate_subscription_id) {
                // Cancel CommerceGate subscription
                $this->commerceGateService->cancelSubscription($subscription->commercegate_subscription_id, false);
            }

            // Update local subscription
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now()
            ]);

            // Update user
            $user->update([
                'subscription_type' => 'free'
            ]);

            return redirect()->route('subscriptions.show')
                ->with('success', 'Assinatura cancelada com sucesso.');

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed', [
                'subscription_id' => $subscription->id,
                'mode' => $this->mode,
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
            if ($this->mode === 'commercegate' && $subscription->commercegate_subscription_id) {
                // Resume CommerceGate subscription
                $this->commerceGateService->resumeSubscription($subscription->commercegate_subscription_id);
            }

            // Update local subscription
            $subscription->update([
                'status' => 'active',
                'canceled_at' => null
            ]);

            // Update user
            $user->update([
                'subscription_type' => 'premium'
            ]);

            return redirect()->route('subscriptions.show')
                ->with('success', 'Assinatura reativada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Subscription resume failed', [
                'subscription_id' => $subscription->id,
                'mode' => $this->mode,
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

        try {
            if ($this->mode === 'commercegate' && $subscription->commercegate_subscription_id) {
                // CommerceGate pode requerer nova autorização
                $request->validate([
                    'payment_data' => 'required|array'
                ]);
                
                $this->commerceGateService->updatePaymentMethod(
                    $subscription->commercegate_subscription_id, 
                    $request->payment_data
                );
            }

            return redirect()->route('subscriptions.show')
                ->with('success', 'Método de pagamento atualizado com sucesso.');

        } catch (\Exception $e) {
            Log::error('Payment method update failed', [
                'subscription_id' => $subscription->id,
                'mode' => $this->mode,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erro ao atualizar método de pagamento. Tente novamente.');
        }
    }

    /**
     * Handle successful payment return from CommerceGate
     */
    public function success(Request $request)
    {
        if ($this->mode === 'mock') {
            return redirect()->route('subscriptions.plans')
                ->with('info', 'Assinaturas em breve. O serviço é gratuito por enquanto.');
        }

        $subscriptionId = $request->get('subscriptionId') ?? $request->get('subscription_id');
        $transactionId = $request->get('transactionId') ?? $request->get('transaction_id');

        if (!$subscriptionId) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'Parâmetros de pagamento inválidos.');
        }

        try {
            // Get subscription details from CommerceGate
            $cgSubscription = $this->commerceGateService->getSubscription($subscriptionId);
            
            if ($cgSubscription && ($cgSubscription['status'] ?? '') === 'active') {
                $user = Auth::user();
                $this->createLocalSubscriptionFromCommerceGate($user, $cgSubscription);
                
                return redirect()->route('subscriptions.show')
                    ->with('success', 'Pagamento confirmado! Bem-vindo ao Premium!');
            }

            return redirect()->route('subscriptions.plans')
                ->with('error', 'Pagamento ainda não foi processado.');

        } catch (\Exception $e) {
            Log::error('Payment success processing failed', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('subscriptions.plans')
                ->with('error', 'Erro ao confirmar pagamento. Tente novamente.');
        }
    }

    /**
     * Handle canceled payment from CommerceGate
     */
    public function cancelPayment(Request $request)
    {
        return redirect()->route('subscriptions.plans')
            ->with('info', 'Pagamento cancelado. Você pode tentar novamente quando quiser.');
    }

    /**
     * Handle CommerceGate webhook
     */
    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();
            $signature = $request->header('X-CommerceGate-Signature') ?? '';

            // Verify webhook signature
            if (!$this->commerceGateService->verifyWebhook($payload, $signature)) {
                Log::warning('CommerceGate webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Process webhook
            $event = $this->commerceGateService->handleWebhook($payload);
            $subscriptionId = $event['subscription_id'] ?? null;

            if (!$subscriptionId) {
                return response()->json(['error' => 'Missing subscription_id'], 400);
            }

            // Find local subscription
            $subscription = Subscription::where('commercegate_subscription_id', $subscriptionId)->first();

            if (!$subscription) {
                Log::warning('CommerceGate webhook: subscription not found', ['subscription_id' => $subscriptionId]);
                return response()->json(['error' => 'Subscription not found'], 404);
            }

            // Handle different event types
            switch ($event['type']) {
                case 'subscription.activated':
                case 'subscription.renewed':
                    $subscription->update([
                        'status' => 'active',
                        'ends_at' => isset($event['data']['next_billing_date']) 
                            ? now()->parse($event['data']['next_billing_date'])
                            : $subscription->ends_at->addMonth()
                    ]);
                    break;

                case 'subscription.canceled':
                    $subscription->update([
                        'status' => 'canceled',
                        'canceled_at' => now()
                    ]);
                    break;

                case 'subscription.failed':
                    $subscription->update(['status' => 'past_due']);
                    break;

                case 'subscription.expired':
                    $subscription->update(['status' => 'canceled']);
                    break;
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('CommerceGate webhook processing failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Create local subscription record from CommerceGate subscription
     */
    private function createLocalSubscriptionFromCommerceGate(User $user, array $cgSubscription)
    {
        $plan = $this->getPlanFromCommerceGateCode($cgSubscription['planCode'] ?? '');
        
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'commercegate_subscription_id' => $cgSubscription['subscriptionId'] ?? null,
            'plan' => $plan,
            'status' => $cgSubscription['status'] ?? 'active',
            'amount' => ($cgSubscription['amount'] ?? 0) / 100, // Convert from cents
            'currency' => strtoupper($cgSubscription['currency'] ?? 'BRL'),
            'starts_at' => now(),
            'ends_at' => isset($cgSubscription['nextBillingDate']) 
                ? now()->parse($cgSubscription['nextBillingDate'])
                : now()->addMonth(),
            'metadata' => [
                'commercegate_subscription_id' => $cgSubscription['subscriptionId'] ?? null,
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
     * Get plan name from CommerceGate plan code
     */
    private function getPlanFromCommerceGateCode(string $planCode): string
    {
        $planCodes = $this->commerceGateService->getPlanCodes();
        
        foreach ($planCodes as $plan => $code) {
            if ($code === $planCode) {
                return $plan;
            }
        }

        return 'premium_monthly'; // Default
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