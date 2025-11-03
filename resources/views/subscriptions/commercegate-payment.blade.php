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
    @if(empty($paymentForm['forwardUrl']))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                <div>
                    <h3 class="font-semibold text-red-800 mb-2">Erro ao Configurar Pagamento</h3>
                    <p class="text-red-700 text-sm">
                        Não foi possível obter a URL do formulário de pagamento. 
                        Por favor, tente novamente ou entre em contato com o suporte.
                    </p>
                    @if(!empty($paymentForm['error']))
                        <p class="text-red-600 text-xs mt-2">Detalhes: {{ $paymentForm['error'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('subscriptions.plans') }}" class="bg-blue-500 text-white py-3 px-6 rounded-lg hover:bg-blue-600 transition duration-200 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar aos Planos
            </a>
        </div>
    @else
        <div class="text-center">
            <p class="text-gray-600 mb-4">Redirecionando para o pagamento seguro...</p>
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
        </div>
        <script>
            // Validar e redirecionar automaticamente para o formulário hospedado
            (function() {
                var forwardUrl = '{{ $paymentForm['forwardUrl'] }}';
                
                // Garantir que a URL tenha protocolo
                if (!forwardUrl.match(/^https?:\/\//)) {
                    forwardUrl = 'https://' + forwardUrl.replace(/^\/+/, '');
                }
                
                // Validar URL antes de redirecionar
                try {
                    var url = new URL(forwardUrl);
                    if (url.protocol === 'http:' || url.protocol === 'https:') {
                        window.location.href = forwardUrl;
                    } else {
                        console.error('Invalid URL protocol:', forwardUrl);
                        alert('Erro: URL de pagamento inválida. Por favor, entre em contato com o suporte.');
                    }
                } catch (e) {
                    console.error('Invalid URL:', forwardUrl, e);
                    alert('Erro: URL de pagamento inválida. Por favor, entre em contato com o suporte.');
                }
            })();
        </script>
    @endif
</div>
@endsection



