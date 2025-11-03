@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">
            <i class="fas fa-credit-card text-blue-500 mr-2"></i>
            {{ __('messages.subscriptions.processing_payment') ?? 'Processando Pagamento' }}
        </h2>
        <p class="text-gray-600">{{ __('messages.subscriptions.redirecting') ?? 'Redirecionando para o gateway de pagamento seguro...' }}</p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div>
                <h3 class="font-semibold text-blue-800 mb-2">{{ $planName }}</h3>
                <p class="text-blue-700 text-sm">
                    {{ __('messages.subscriptions.secure_payment') ?? 'Você será redirecionado para uma página segura do CommerceGate para completar seu pagamento.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- CommerceGate Hosted Payment Form -->
    @if(empty($formData['actionUrl']))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-red-800 mb-2">URL de Pagamento Não Configurada</h3>
                    <p class="text-red-700 text-sm">
                        A URL do formulário de pagamento hospedado não está configurada. 
                        Por favor, configure a variável <code>COMMERCEGATE_HOSTED_PAYMENT_URL_TEST</code> 
                        (ou <code>COMMERCEGATE_HOSTED_PAYMENT_URL_PRODUCTION</code>) no arquivo <code>.env</code>
                        com a URL correta fornecida pelo CommerceGate.
                    </p>
                </div>
            </div>
        </div>
    @endif
    
    <form id="commercegate-payment-form" method="POST" action="{{ $formData['actionUrl'] ?? '#' }}">
        @foreach($formData as $key => $value)
            @if($key !== 'actionUrl')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        
        <div class="text-center">
            <button type="submit" class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-200">
                <i class="fas fa-lock mr-2"></i>
                {{ __('messages.subscriptions.proceed_to_payment') ?? 'Prosseguir para o Pagamento' }}
            </button>
        </div>
    </form>

    <!-- Auto-submit form after page load -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('commercegate-payment-form');
            const actionUrl = form.getAttribute('action');
            
            // Só auto-submit se a URL estiver configurada
            if (actionUrl && actionUrl !== '#' && actionUrl.trim() !== '') {
                setTimeout(function() {
                    form.submit();
                }, 1000);
            }
        });
    </script>
</div>
@endsection

