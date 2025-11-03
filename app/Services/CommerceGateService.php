<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CommerceGateService
{
    protected $merchantId;
    protected $websiteId;
    protected $authLogin;
    protected $authPassword;
    protected $baseUrl;
    protected $hostedPaymentUrl;
    protected $testMode;

    public function __construct()
    {
        $this->merchantId = config('services.commercegate.merchant_id');
        $this->websiteId = config('services.commercegate.website_id');
        $this->authLogin = config('services.commercegate.auth_login');
        $this->authPassword = config('services.commercegate.auth_password');
        $this->testMode = config('services.commercegate.test_mode', true);
        
        // CommerceGate URLs - configuráveis via .env ou usar padrões
        $this->baseUrl = $this->testMode 
            ? config('services.commercegate.api_url_test', 'https://secure.commercegate.com')
            : config('services.commercegate.api_url_production', 'https://secure.commercegate.com');
        
        $this->hostedPaymentUrl = $this->testMode
            ? config('services.commercegate.hosted_payment_url_test', 'https://secure.commercegate.com/payment')
            : config('services.commercegate.hosted_payment_url_production', 'https://secure.commercegate.com/payment');
    }

    /**
     * Create a subscription using CommerceGate API
     * CommerceGate usa formulários hospedados ou API direta
     */
    public function createSubscription(User $user, array $planData): array
    {
        try {
            // CommerceGate requer dados do cliente e do plano
            $subscriptionData = [
                'merchantId' => $this->merchantId,
                'websiteId' => $this->websiteId,
                'customerId' => $user->id,
                'customerEmail' => $user->email,
                'customerName' => $user->name,
                'amount' => $planData['amount'], // em centavos
                'currency' => $planData['currency'] ?? 'BRL',
                'planCode' => $planData['plan_code'],
                'billingFrequency' => $planData['interval'] === 'month' ? 'monthly' : 'yearly',
                'description' => $planData['description'] ?? 'Assinatura Premium',
                'returnUrl' => route('subscriptions.success'),
                'cancelUrl' => route('subscriptions.payment-cancel'),
                'notificationUrl' => route('commercegate.webhook'),
            ];

            // Autenticação básica HTTP
            // NOTA: Verificar na documentação oficial do CommerceGate o endpoint correto da API
            // Pode ser necessário usar um endpoint diferente ou método de integração alternativo
            $apiEndpoint = $this->baseUrl . '/api/subscriptions/create';
            
            $response = Http::withBasicAuth($this->authLogin, $this->authPassword)
                ->post($apiEndpoint, $subscriptionData);

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => true,
                    'subscription_id' => $result['subscriptionId'] ?? null,
                    'payment_url' => $result['paymentUrl'] ?? null,
                    'redirect_url' => $result['redirectUrl'] ?? null,
                    'status' => $result['status'] ?? 'pending'
                ];
            }

            Log::error('CommerceGate Subscription Creation Failed', [
                'user_id' => $user->id,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Falha ao criar assinatura no CommerceGate: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('CommerceGate Subscription Creation Error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get subscription details
     */
    public function getSubscription(string $subscriptionId): array
    {
        try {
            // NOTA: Verificar endpoint correto na documentação oficial
            $apiEndpoint = $this->baseUrl . '/api/subscriptions/' . $subscriptionId;
            
            $response = Http::withBasicAuth($this->authLogin, $this->authPassword)
                ->get($apiEndpoint, [
                    'merchantId' => $this->merchantId
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao obter detalhes da assinatura');

        } catch (\Exception $e) {
            Log::error('CommerceGate Get Subscription Error', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(string $subscriptionId, bool $immediately = false): array
    {
        try {
            $apiEndpoint = $this->baseUrl . '/api/subscriptions/' . $subscriptionId . '/cancel';
            
            $response = Http::withBasicAuth($this->authLogin, $this->authPassword)
                ->post($apiEndpoint, [
                    'merchantId' => $this->merchantId,
                    'immediate' => $immediately
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao cancelar assinatura');

        } catch (\Exception $e) {
            Log::error('CommerceGate Cancel Subscription Error', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Resume subscription
     */
    public function resumeSubscription(string $subscriptionId): array
    {
        try {
            $apiEndpoint = $this->baseUrl . '/api/subscriptions/' . $subscriptionId . '/resume';
            
            $response = Http::withBasicAuth($this->authLogin, $this->authPassword)
                ->post($apiEndpoint, [
                    'merchantId' => $this->merchantId
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao reativar assinatura');

        } catch (\Exception $e) {
            Log::error('CommerceGate Resume Subscription Error', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update payment method
     * CommerceGate pode requerer uma nova autorização
     */
    public function updatePaymentMethod(string $subscriptionId, array $paymentData): array
    {
        try {
            $apiEndpoint = $this->baseUrl . '/api/subscriptions/' . $subscriptionId . '/update-payment';
            
            $response = Http::withBasicAuth($this->authLogin, $this->authPassword)
                ->post($apiEndpoint, array_merge([
                    'merchantId' => $this->merchantId
                ], $paymentData));

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao atualizar método de pagamento');

        } catch (\Exception $e) {
            Log::error('CommerceGate Update Payment Method Error', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook(array $data, string $signature): bool
    {
        // CommerceGate geralmente usa HMAC SHA256
        $expectedSignature = hash_hmac('sha256', json_encode($data), $this->authPassword);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle webhook event
     */
    public function handleWebhook(array $payload): array
    {
        try {
            $eventType = $payload['eventType'] ?? $payload['type'] ?? null;
            $data = $payload['data'] ?? $payload;

            return [
                'type' => $eventType,
                'data' => $data,
                'subscription_id' => $data['subscriptionId'] ?? $data['subscription_id'] ?? null,
                'transaction_id' => $data['transactionId'] ?? $data['transaction_id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('CommerceGate Webhook Processing Error', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
            throw $e;
        }
    }

    /**
     * Get plan codes for our plans
     */
    public function getPlanCodes(): array
    {
        return [
            'premium_monthly' => 'PREMIUM_MONTHLY',
            'premium_yearly' => 'PREMIUM_YEARLY',
        ];
    }

    /**
     * Generate payment form data for hosted payment page
     * CommerceGate oferece formulários hospedados que são mais seguros
     */
    public function generateHostedPaymentForm(User $user, array $planData): array
    {
        // Dados para o formulário hospedado
        $formData = [
            'merchantId' => $this->merchantId,
            'websiteId' => $this->websiteId,
            'customerId' => $user->id,
            'customerEmail' => $user->email,
            'customerName' => $user->name,
            'amount' => $planData['amount'],
            'currency' => $planData['currency'] ?? 'BRL',
            'planCode' => $planData['plan_code'],
            'billingFrequency' => $planData['interval'] === 'month' ? 'monthly' : 'yearly',
            'description' => $planData['description'] ?? 'Assinatura Premium',
            'returnUrl' => route('subscriptions.success'),
            'cancelUrl' => route('subscriptions.payment-cancel'),
            'notificationUrl' => route('commercegate.webhook'),
            'subscription' => true,
        ];

        // Gerar assinatura HMAC para segurança
        $signature = $this->generateSignature($formData);

        $formData['signature'] = $signature;
        // URL do formulário hospedado - deve ser fornecido pelo CommerceGate no portal do merchant
        $formData['actionUrl'] = $this->hostedPaymentUrl;

        return $formData;
    }

    /**
     * Generate HMAC signature for payment form
     */
    protected function generateSignature(array $data): string
    {
        // Ordenar campos para assinatura consistente
        ksort($data);
        
        // Criar string de dados (excluir signature se existir)
        unset($data['signature']);
        $signatureString = http_build_query($data);
        
        // Gerar HMAC
        return hash_hmac('sha256', $signatureString, $this->authPassword);
    }
}

