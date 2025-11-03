<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CommerceGateService
{
    protected $merchantId;
    protected $websiteId;
    protected $authLogin;
    protected $authPassword;
    protected $baseUrl;
    protected $testMode;

    public function __construct()
    {
        $this->merchantId = config('services.commercegate.merchant_id');
        $this->websiteId = config('services.commercegate.website_id');
        $this->authLogin = config('services.commercegate.auth_login');
        $this->authPassword = config('services.commercegate.auth_password');
        $this->testMode = config('services.commercegate.test_mode', true);
        
        // URL base do CommerceGate API conforme Swagger
        // Produção: https://gw.cgpaytech.com
        $this->baseUrl = $this->testMode 
            ? config('services.commercegate.api_url_test', 'https://gw.cgpaytech.com')
            : config('services.commercegate.api_url_production', 'https://gw.cgpaytech.com');
    }

    /**
     * Obter token de autenticação Bearer
     * POST /v1/token
     */
    protected function getAuthToken(): string
    {
        // Cache token por 24 horas (expira em expiresIn segundos)
        $cacheKey = 'commercegate_auth_token';
        
        return Cache::remember($cacheKey, 86400, function () {
            $response = Http::post($this->baseUrl . '/v1/token', [
                'login' => $this->authLogin,
                'password' => $this->authPassword,
            ]);

            if (!$response->successful()) {
                Log::error('CommerceGate Auth Token Failed', [
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                throw new \Exception('Falha ao obter token de autenticação: ' . $response->body());
            }

            $result = $response->json();
            $token = $result['token'] ?? null;
            $expiresIn = $result['expiresIn'] ?? 86400;

            if (!$token) {
                throw new \Exception('Token não retornado na resposta');
            }

            // Cache pelo tempo de expiração
            Cache::put($cacheKey, $token, $expiresIn - 60); // -60 segundos de margem

            return $token;
        });
    }

    /**
     * Criar request HTTP autenticado
     */
    protected function authenticatedRequest()
    {
        $token = $this->getAuthToken();
        return Http::withToken($token);
    }

    /**
     * Criar subscription usando CommerceGate API
     * PUT /v1/api/subscription
     */
    public function createSubscription(User $user, array $planData): array
    {
        try {
            // Montar estrutura conforme Swagger
            $subscriptionData = [
                'MerchantInfo' => [
                    'merchantId' => $this->merchantId,
                    'merchantWebsiteId' => $this->websiteId,
                    'userName' => (string) $user->id, // Identificador único do usuário
                    'externalId' => 'sub_' . $user->id . '_' . time(),
                ],
                'CustomerInfo' => [
                    'ipAddress' => request()->ip() ?? '8.8.8.8',
                    'userLanguage' => app()->getLocale(),
                    'email' => $user->email,
                ],
                'TransactionInfo' => [
                    'description' => $planData['description'] ?? 'Assinatura Premium',
                    'amount' => $planData['amount'], // Em centavos (minor units)
                    'currency' => $planData['currency'] ?? 'BRL',
                    'amountRecurring' => $planData['amount'], // Valor do pagamento recorrente
                ],
                'SubscriptionPeriod' => [
                    'name' => $planData['interval'] === 'month' ? 'month' : 'day',
                    'amount' => $planData['interval'] === 'month' ? 1 : ($planData['interval'] === 'year' ? 365 : 1),
                ],
                'SubscriptionConfig' => [
                    'amountOfRetries' => '10',
                    'amountOfDaysToShiftFirstRecurring' => '0',
                ],
                'ThreeDInfo' => [
                    'redirectUrl' => route('subscriptions.success'),
                ],
                'Callbacks' => [
                    'onSuccessUrl' => route('subscriptions.success'),
                    'onFailUrl' => route('subscriptions.payment-cancel'),
                    'onSubscriptionRecurringSuccessUrl' => route('commercegate.webhook'),
                    'onSubscriptionRecurringFailUrl' => route('commercegate.webhook'),
                    'onSubscriptionCanceledUrl' => route('commercegate.webhook'),
                    'onSubscriptionClosedUrl' => route('commercegate.webhook'),
                ],
                'ConfigurationOptions' => [
                    'paymentMethod' => 'credit-card',
                ],
            ];

            $response = $this->authenticatedRequest()
                ->put($this->baseUrl . '/v1/api/subscription', $subscriptionData);

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => true,
                    'orderId' => $result['orderId'] ?? null,
                    'status' => $result['status'] ?? 'pending',
                    'acsServerUrl' => $result['ThreeDInfo']['acsServerUrl'] ?? null, // URL para 3DS se necessário
                ];
            }

            Log::error('CommerceGate Subscription Creation Failed', [
                'user_id' => $user->id,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Falha ao criar assinatura: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('CommerceGate Subscription Creation Error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Configurar Payment Form (Hosted Payment Form)
     * POST /v1/api/payment_form/configure
     * 
     * Retorna URL do formulário hospedado para redirecionar o usuário
     */
    public function generateHostedPaymentForm(User $user, array $planData): array
    {
        try {
            // Montar estrutura conforme Swagger para Payment Form Configuration
            $formData = [
                'MerchantInfo' => [
                    'merchantId' => $this->merchantId,
                    'merchantWebsiteId' => $this->websiteId,
                    'userName' => (string) $user->id,
                    'externalId' => 'sub_' . $user->id . '_' . time(),
                ],
                'CustomerInfo' => [
                    'ipAddress' => request()->ip() ?? '8.8.8.8',
                    'userLanguage' => app()->getLocale(),
                    'email' => $user->email,
                ],
                'TransactionInfo' => [
                    'description' => $planData['description'] ?? 'Assinatura Premium',
                    'amount' => $planData['amount'],
                    'currency' => $planData['currency'] ?? 'BRL',
                    'amountRecurring' => $planData['amount'],
                ],
                'Offers' => [
                    [
                        'selected' => true,
                        'TransactionInfo' => [
                            'description' => $planData['description'] ?? 'Assinatura Premium',
                            'amount' => $planData['amount'],
                            'currency' => $planData['currency'] ?? 'BRL',
                            'amountRecurring' => $planData['amount'],
                        ],
                        'SubscriptionPeriod' => [
                            'name' => $planData['interval'] === 'month' ? 'month' : 'day',
                            'amount' => $planData['interval'] === 'month' ? 1 : ($planData['interval'] === 'year' ? 365 : 1),
                        ],
                        'SubscriptionConfig' => [
                            'amountOfRetries' => '10',
                            'amountOfDaysToShiftFirstRecurring' => '0',
                        ],
                    ],
                ],
                'ThreeDInfo' => [
                    'redirectUrl' => route('subscriptions.success'),
                ],
                'Callbacks' => [
                    'onSuccessUrl' => route('subscriptions.success'),
                    'onFailUrl' => route('subscriptions.payment-cancel'),
                    'onSubscriptionRecurringSuccessUrl' => route('commercegate.webhook'),
                    'onSubscriptionRecurringFailUrl' => route('commercegate.webhook'),
                    'onSubscriptionCanceledUrl' => route('commercegate.webhook'),
                    'onSubscriptionClosedUrl' => route('commercegate.webhook'),
                ],
                'ConfigurationOptions' => [
                    'paymentMethod' => 'credit-card',
                ],
            ];

            $response = $this->authenticatedRequest()
                ->post($this->baseUrl . '/v1/api/payment_form/configure', $formData);

            if ($response->successful()) {
                $result = $response->json();
                
                $forwardUrl = $result['forwardUrl'] ?? null;
                
                // Log para debug
                Log::info('CommerceGate Payment Form Response', [
                    'user_id' => $user->id,
                    'payment_form_id' => $result['paymentFormId'] ?? null,
                    'forward_url' => $forwardUrl,
                    'status' => $result['status'] ?? null,
                    'full_response' => $result,
                ]);
                
                return [
                    'success' => true,
                    'paymentFormId' => $result['paymentFormId'] ?? null,
                    'forwardUrl' => $forwardUrl, // URL para redirecionar o usuário
                    'qrUrl' => $result['qrUrl'] ?? null,
                    'status' => $result['status'] ?? 'redirect',
                ];
            }

            Log::error('CommerceGate Payment Form Configuration Failed', [
                'user_id' => $user->id,
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Falha ao configurar formulário de pagamento: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('CommerceGate Payment Form Configuration Error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obter detalhes de uma ordem/subscription
     * POST /v1/api/reporting/order/{orderId}
     */
    public function getOrder(string $orderId): array
    {
        try {
            $response = $this->authenticatedRequest()
                ->post($this->baseUrl . '/v1/api/reporting/order/' . $orderId, [
                    'MerchantInfo' => [
                        'merchantId' => $this->merchantId,
                        'merchantWebsiteId' => $this->websiteId,
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao obter detalhes da ordem: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('CommerceGate Get Order Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancelar subscription
     * POST /v1/api/subscription/{orderId}
     */
    public function cancelSubscription(string $orderId, string $cancellationReason = 'Initiated by Customer request.'): array
    {
        try {
            $response = $this->authenticatedRequest()
                ->post($this->baseUrl . '/v1/api/subscription/' . $orderId, [
                    'MerchantInfo' => [
                        'merchantId' => $this->merchantId,
                        'merchantWebsiteId' => $this->websiteId,
                    ],
                    'cancellationReason' => $cancellationReason,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao cancelar assinatura: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('CommerceGate Cancel Subscription Error', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verificar assinatura do webhook
     * CommerceGate usa AES-256-CBC para assinatura de webhooks
     */
    public function verifyWebhook(array $data, string $signature, string $signatureInitVector): bool
    {
        try {
            // Obter securityKey (gerar uma vez e reutilizar)
            $securityKey = $this->getSecurityKey();
            
            // Decriptar assinatura
            $decryptedSignature = openssl_decrypt(
                hex2bin($signature),
                'AES-256-CBC',
                hex2bin($securityKey),
                OPENSSL_RAW_DATA,
                hex2bin($signatureInitVector)
            );

            // Calcular MD5 do body
            $bodyMd5 = md5(json_encode($data, JSON_UNESCAPED_SLASHES));

            return hash_equals($bodyMd5, $decryptedSignature);

        } catch (\Exception $e) {
            Log::error('CommerceGate Webhook Verification Error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obter securityKey para verificação de webhook
     * POST /v1/api/signature
     */
    protected function getSecurityKey(): string
    {
        $cacheKey = 'commercegate_security_key';
        
        return Cache::rememberForever($cacheKey, function () {
            $response = $this->authenticatedRequest()
                ->post($this->baseUrl . '/v1/api/signature', [
                    'MerchantInfo' => [
                        'merchantId' => $this->merchantId,
                        'merchantWebsiteId' => $this->websiteId,
                    ],
                ]);

            if (!$response->successful()) {
                throw new \Exception('Falha ao obter security key: ' . $response->body());
            }

            $result = $response->json();
            return $result['securityKey'] ?? '';
        });
    }

    /**
     * Processar webhook do CommerceGate
     */
    public function handleWebhook(array $payload): array
    {
        try {
            // Extrair informações do webhook
            $orderId = $payload['orderId'] ?? null;
            $type = $payload['type'] ?? null;
            $status = $payload['status'] ?? null;

            return [
                'orderId' => $orderId,
                'type' => $type, // 'subscription', 'subscriptionRecurringPayment', etc.
                'status' => $status, // 'success', 'failed', etc.
                'data' => $payload,
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
     * Obter ordem por PaymentFormId
     * POST /v1/api/reporting/orderByPaymentFormId/{paymentFormId}
     */
    public function getOrderByPaymentFormId(string $paymentFormId): array
    {
        try {
            $response = $this->authenticatedRequest()
                ->post($this->baseUrl . '/v1/api/reporting/orderByPaymentFormId/' . $paymentFormId, [
                    'MerchantInfo' => [
                        'merchantId' => $this->merchantId,
                        'merchantWebsiteId' => $this->websiteId,
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Falha ao obter ordem por PaymentFormId: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('CommerceGate Get Order By PaymentFormId Error', [
                'payment_form_id' => $paymentFormId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Obter códigos de planos (se necessário)
     */
    public function getPlanCodes(): array
    {
        return [
            'premium_monthly' => 'PREMIUM_MONTHLY',
            'premium_yearly' => 'PREMIUM_YEARLY',
        ];
    }
}
