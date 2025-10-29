@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-crown text-yellow-500 mr-2"></i>{{ __('messages.subscriptions.subscription_plans') }}
    </h2>

    @if(isset($warning))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span class="font-semibold">{{ $warning }}</span>
            </div>
        </div>
    @endif

    @if($currentSubscription)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="font-semibold">{{ __('messages.subscriptions.current') }}:</span>
                <span class="ml-2">{{ $currentSubscription->plan === 'premium_monthly' ? __('messages.subscriptions.premium_monthly') : __('messages.subscriptions.premium_yearly') }}</span>
                <span class="ml-4 text-sm">{{ __('messages.subscriptions.expires_on') }} {{ $currentSubscription->ends_at->format('d/m/Y') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $planKey => $plan)
            <div class="border border-gray-200 rounded-lg p-6 {{ $plan['popular'] ?? false ? 'border-blue-500 ring-2 ring-blue-500' : '' }} {{ $currentSubscription && $currentSubscription->plan === $planKey ? 'bg-green-50' : '' }}">
                                @if($plan['popular'] ?? false)
                                    <div class="text-center mb-4">
                                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ __('messages.subscriptions.most_popular') }}
                                        </span>
                                    </div>
                                @endif

                @if($currentSubscription && $currentSubscription->plan === $planKey)
                    <div class="text-center mb-4">
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ __('messages.subscriptions.current_plan') }}
                        </span>
                    </div>
                @endif

                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan['name'] }}</h3>
                
                <div class="mb-4">
                    <span class="text-3xl font-bold text-gray-800">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                    @if($plan['interval'] !== 'forever')
                        <span class="text-gray-600">{{ __('messages.subscriptions.per_' . $plan['interval']) }}</span>
                    @endif
                </div>

                @if(isset($plan['savings']))
                    <div class="bg-yellow-100 text-yellow-800 px-3 py-2 rounded-lg text-sm font-semibold mb-4">
                        <i class="fas fa-gift mr-1"></i>{{ $plan['savings'] ?? '' }}
                    </div>
                @endif

                <ul class="space-y-3 mb-6">
                    @foreach($plan['features'] as $feature)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @if(isset($plan['limitations']))
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-600 mb-2">{{ __('messages.subscriptions.limitations') }}</h4>
                        <ul class="space-y-1">
                            @foreach($plan['limitations'] as $limitation)
                                <li class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-times text-red-400 mr-2"></i>
                                    {{ $limitation }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-center">
                    @if($currentSubscription && $currentSubscription->plan === $planKey)
                        <a href="{{ route('subscriptions.show') }}" 
                           class="w-full bg-green-500 text-white py-3 px-4 rounded-lg hover:bg-green-600 transition duration-200">
                            <i class="fas fa-eye mr-2"></i>{{ __('messages.subscriptions.manage') }}
                        </a>
                    @elseif($planKey === 'free')
                        <span class="w-full bg-gray-300 text-gray-600 py-3 px-4 rounded-lg cursor-not-allowed">
                            <i class="fas fa-check mr-2"></i>{{ __('messages.subscriptions.current_plan') }}
                        </span>
                    @elseif(isset($plan['disabled']) && $plan['disabled'])
                        <span class="w-full bg-gray-300 text-gray-600 py-3 px-4 rounded-lg cursor-not-allowed">
                            <i class="fas fa-tools mr-2"></i>{{ $plan['disabled_message'] ?? 'Indisponível' }}
                        </span>
                    @elseif(!isset($stripeConfigured) || !$stripeConfigured)
                        <span class="w-full bg-gray-300 text-gray-600 py-3 px-4 rounded-lg cursor-not-allowed">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Sistema em manutenção
                        </span>
                    @else
                        <button type="button" 
                                onclick="openPaymentModal('{{ $planKey }}', '{{ $plan['name'] }}', {{ $plan['price'] }}, '{{ $plan['interval'] }}')"
                                class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-credit-card mr-2"></i>{{ __('messages.subscriptions.subscribe_now') }}
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="mt-12">
        <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('messages.subscriptions.faq') }}</h3>
        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">{{ __('messages.subscriptions.faq_cancel') }}</h4>
                <p class="text-gray-600">{{ __('messages.subscriptions.faq_cancel_answer') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">{{ __('messages.subscriptions.faq_refund') }}</h4>
                <p class="text-gray-600">{{ __('messages.subscriptions.faq_refund_answer') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">{{ __('messages.subscriptions.faq_payment') }}</h4>
                <p class="text-gray-600">{{ __('messages.subscriptions.faq_payment_answer') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">Confirmar Assinatura</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Plan Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold text-gray-900" id="plan-name">Premium Mensal</h4>
                        <p class="text-sm text-gray-600" id="plan-description">por mês</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold text-gray-900" id="plan-price">R$ 29,90</p>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form id="payment-form">
                @csrf
                <input type="hidden" id="selected-plan" name="plan">
                
                <div class="mb-4">
                    <label for="card-element" class="block text-sm font-medium text-gray-700 mb-2">
                        Informações do Cartão
                    </label>
                    <div id="card-element" class="border border-gray-300 rounded-lg p-4 bg-white min-h-[50px]">
                        <!-- Stripe Elements will create form elements here -->
                    </div>
                    <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closePaymentModal()"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                            id="submit-button"
                            class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Confirmar Pagamento</span>
                        <div id="spinner" class="hidden inline-block ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
let stripe, elements, cardElement;

document.addEventListener('DOMContentLoaded', function() {
    // Only initialize Stripe if the key is available
    const stripeKey = '{{ config("services.stripe.key") }}';
    
    if (stripeKey && stripeKey.trim() !== '' && stripeKey !== 'null') {
        try {
            stripe = Stripe(stripeKey);
            elements = stripe.elements();
            
            cardElement = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#424770',
                        '::placeholder': {
                            color: '#aab7c4',
                        },
                    },
                    invalid: {
                        color: '#9e2146',
                    },
                },
            });
        } catch (error) {
            console.error('Error initializing Stripe:', error);
            stripe = null;
            elements = null;
            cardElement = null;
        }
    } else {
        console.log('Stripe key not configured, payment modal will be disabled');
        stripe = null;
        elements = null;
        cardElement = null;
    }

    // Handle form submission
    const paymentForm = document.getElementById('payment-form');
    if (paymentForm) {
        paymentForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');
            
            // Show loading state
            if (submitButton) submitButton.disabled = true;
            if (buttonText) buttonText.textContent = 'Processando...';
            if (spinner) spinner.classList.remove('hidden');
            
            try {
                console.log('Creating payment method...');
                
                const {error, paymentMethod} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                });

                console.log('Payment method result:', {error, paymentMethod});

                if (error) {
                    console.error('Stripe error:', error);
                    // Show error message
                    const cardErrors = document.getElementById('card-errors');
                    if (cardErrors) {
                        cardErrors.textContent = error.message || 'Erro ao processar pagamento. Verifique os dados do cartão.';
                    }
                    
                    // Reset button state
                    if (submitButton) submitButton.disabled = false;
                    if (buttonText) buttonText.textContent = 'Confirmar Pagamento';
                    if (spinner) spinner.classList.add('hidden');
                } else if (paymentMethod) {
                    console.log('Payment method created successfully:', paymentMethod.id);
                    
                    // Submit form with payment method
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("subscriptions.create") }}';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const planInput = document.createElement('input');
                    planInput.type = 'hidden';
                    planInput.name = 'plan';
                    planInput.value = document.getElementById('selected-plan').value;
                    
                    const paymentMethodInput = document.createElement('input');
                    paymentMethodInput.type = 'hidden';
                    paymentMethodInput.name = 'payment_method_id';
                    paymentMethodInput.value = paymentMethod.id;
                    
                    form.appendChild(csrfToken);
                    form.appendChild(planInput);
                    form.appendChild(paymentMethodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    console.error('No payment method created and no error');
                    const cardErrors = document.getElementById('card-errors');
                    if (cardErrors) {
                        cardErrors.textContent = 'Erro ao processar pagamento. Tente novamente.';
                    }
                    
                    // Reset button state
                    if (submitButton) submitButton.disabled = false;
                    if (buttonText) buttonText.textContent = 'Confirmar Pagamento';
                    if (spinner) spinner.classList.add('hidden');
                }
            } catch (err) {
                console.error('Unexpected error:', err);
                const cardErrors = document.getElementById('card-errors');
                if (cardErrors) {
                    cardErrors.textContent = 'Erro inesperado. Verifique sua conexão e tente novamente.';
                }
                
                // Reset button state
                if (submitButton) submitButton.disabled = false;
                if (buttonText) buttonText.textContent = 'Confirmar Pagamento';
                if (spinner) spinner.classList.add('hidden');
            }
        });
    }

    // Handle real-time validation errors from the card Element
    if (cardElement) {
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (displayError) {
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            }
        });
    }
});

// Global functions for modal handling
window.openPaymentModal = function(planKey, planName, price, interval) {
    console.log('Opening payment modal for plan:', planKey);
    
    const selectedPlan = document.getElementById('selected-plan');
    const planNameEl = document.getElementById('plan-name');
    const planPriceEl = document.getElementById('plan-price');
    const planDescEl = document.getElementById('plan-description');
    const modal = document.getElementById('payment-modal');
    
    if (selectedPlan) selectedPlan.value = planKey;
    if (planNameEl) planNameEl.textContent = planName;
    if (planPriceEl) planPriceEl.textContent = 'R$ ' + price.toFixed(2).replace('.', ',');
    if (planDescEl) planDescEl.textContent = interval === 'month' ? 'por mês' : interval === 'year' ? 'por ano' : '';
    
    // Reset button state
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    
    if (submitButton) submitButton.disabled = false;
    if (buttonText) buttonText.textContent = 'Confirmar Pagamento';
    if (spinner) spinner.classList.add('hidden');
    
    // Clear any previous errors
    const cardErrors = document.getElementById('card-errors');
    if (cardErrors) cardErrors.textContent = '';
    
    // Show modal first
    if (modal) {
        modal.classList.remove('hidden');
    }
    
    // Mount Stripe element after modal is visible
    setTimeout(function() {
        const cardElementEl = document.getElementById('card-element');
        if (cardElementEl && stripe && cardElement) {
            try {
                // Clear any existing content
                cardElementEl.innerHTML = '';
                
                // Mount the card element
                cardElement.mount('#card-element');
                console.log('Stripe card element mounted successfully');
            } catch (error) {
                console.error('Error mounting Stripe element:', error);
                if (cardErrors) {
                    cardErrors.textContent = 'Erro ao carregar formulário de pagamento. Tente novamente.';
                }
            }
        } else {
            console.error('Stripe not initialized or card element not found');
            if (cardErrors) {
                cardErrors.textContent = 'Sistema de pagamentos não disponível no momento.';
            }
            if (submitButton) {
                submitButton.disabled = true;
            }
        }
    }, 200);
};

window.closePaymentModal = function() {
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
    
    const cardErrors = document.getElementById('card-errors');
    if (cardErrors) {
        cardErrors.textContent = '';
    }
    
    // Reset button state
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    
    if (submitButton) {
        submitButton.disabled = false;
    }
    
    if (buttonText) {
        buttonText.textContent = 'Confirmar Pagamento';
    }
    
    if (spinner) {
        spinner.classList.add('hidden');
    }
};
</script>
@endsection
