@extends('layouts.app')

@section('title', 'Confirmar Pagamento - Amigos Para Sempre')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Confirmar Pagamento</h1>
            <p class="text-gray-600">Complete seu pagamento para ativar sua assinatura Premium</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Payment Status -->
            <div id="payment-status" class="hidden mb-6">
                <div id="payment-success" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Pagamento Confirmado!</h3>
                            <p class="text-sm text-green-700 mt-1">Sua assinatura Premium foi ativada com sucesso.</p>
                        </div>
                    </div>
                </div>

                <div id="payment-error" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erro no Pagamento</h3>
                            <p class="text-sm text-red-700 mt-1" id="error-message">Ocorreu um erro ao processar seu pagamento.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div id="payment-form-container">
                <form id="payment-form">
                    <div class="mb-6">
                        <label for="card-element" class="block text-sm font-medium text-gray-700 mb-2">
                            Informações do Cartão
                        </label>
                        <div id="card-element" class="border border-gray-300 rounded-lg p-3 bg-white">
                            <!-- Stripe Elements will create form elements here -->
                        </div>
                        <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Total</h3>
                                <p class="text-sm text-gray-600">Assinatura Premium</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900" id="total-amount">R$ 29,90</p>
                                <p class="text-sm text-gray-600" id="billing-period">por mês</p>
                            </div>
                        </div>
                    </div>

                    <button id="submit-button" type="submit" class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-pink-600 hover:to-purple-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="button-text">Confirmar Pagamento</span>
                        <div id="spinner" class="hidden inline-block ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 text-center">
                <div class="flex items-center justify-center text-sm text-gray-500">
                    <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    Seus dados são protegidos com criptografia SSL
                </div>
            </div>
        </div>

        <!-- Back to Plans -->
        <div class="text-center mt-6">
            <a href="{{ route('subscriptions.plans') }}" class="text-pink-600 hover:text-pink-700 font-medium">
                ← Voltar para os planos
            </a>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    
    const cardElement = elements.create('card', {
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

    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const paymentStatus = document.getElementById('payment-status');
    const paymentSuccess = document.getElementById('payment-success');
    const paymentError = document.getElementById('payment-error');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        // Show loading state
        submitButton.disabled = true;
        buttonText.textContent = 'Processando...';
        spinner.classList.remove('hidden');
        
        // Hide previous status messages
        paymentStatus.classList.add('hidden');
        paymentSuccess.classList.add('hidden');
        paymentError.classList.add('hidden');

        const {error, paymentIntent} = await stripe.confirmCardPayment('{{ $clientSecret }}', {
            payment_method: {
                card: cardElement,
            }
        });

        if (error) {
            // Show error message
            errorMessage.textContent = error.message;
            paymentError.classList.remove('hidden');
            paymentStatus.classList.remove('hidden');
            
            // Reset button state
            submitButton.disabled = false;
            buttonText.textContent = 'Tentar Novamente';
            spinner.classList.add('hidden');
        } else {
            // Payment succeeded
            paymentSuccess.classList.remove('hidden');
            paymentStatus.classList.remove('hidden');
            
            // Hide form
            document.getElementById('payment-form-container').classList.add('hidden');
            
            // Redirect to success page after 3 seconds
            setTimeout(() => {
                window.location.href = '{{ route("subscriptions.show") }}';
            }, 3000);
        }
    });

    // Handle real-time validation errors from the card Element
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
});
</script>
@endsection
