@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-crown text-yellow-500 mr-2"></i>Minha Assinatura
    </h2>

    @if($currentSubscription)
        <!-- Current Subscription Details -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">
                        {{ $currentSubscription->plan === 'premium_monthly' ? 'Premium Mensal' : 'Premium Anual' }}
                    </h3>
                    <p class="text-pink-100">
                        R$ {{ number_format($currentSubscription->amount, 2, ',', '.') }} 
                        /{{ $currentSubscription->plan === 'premium_monthly' ? 'mês' : 'ano' }}
                    </p>
                    <p class="text-sm text-pink-200 mt-2">
                        Expira em {{ $currentSubscription->ends_at->format('d/m/Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ ucfirst($currentSubscription->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Subscription Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('subscriptions.usage') }}" 
               class="bg-blue-500 text-white py-3 px-4 rounded-lg hover:bg-blue-600 transition duration-200 text-center">
                <i class="fas fa-chart-bar mr-2"></i>Ver Uso da Assinatura
            </a>
            
            @if($currentSubscription->status === 'active')
                <button onclick="cancelSubscription({{ $currentSubscription->id }})" 
                        class="bg-red-500 text-white py-3 px-4 rounded-lg hover:bg-red-600 transition duration-200">
                    <i class="fas fa-times mr-2"></i>Cancelar Assinatura
                </button>
            @elseif($currentSubscription->status === 'canceled')
                <button onclick="resumeSubscription({{ $currentSubscription->id }})" 
                        class="bg-green-500 text-white py-3 px-4 rounded-lg hover:bg-green-600 transition duration-200">
                    <i class="fas fa-play mr-2"></i>Reativar Assinatura
                </button>
            @endif
        </div>

        <!-- Subscription History -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Histórico de Assinaturas</h3>
            <div class="space-y-3">
                @foreach($subscriptions as $subscription)
                    <div class="border border-gray-200 rounded-lg p-4 {{ $subscription->id === $currentSubscription->id ? 'bg-green-50 border-green-200' : '' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-800">
                                    {{ $subscription->plan === 'premium_monthly' ? 'Premium Mensal' : 'Premium Anual' }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    R$ {{ number_format($subscription->amount, 2, ',', '.') }} 
                                    /{{ $subscription->plan === 'premium_monthly' ? 'mês' : 'ano' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $subscription->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                       ($subscription->status === 'canceled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                                @if($subscription->ends_at)
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $subscription->ends_at->format('d/m/Y') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @else
        <!-- No Active Subscription -->
        <div class="text-center py-12">
            <i class="fas fa-crown text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma assinatura ativa</h3>
            <p class="text-gray-500 mb-6">Você está usando o plano gratuito. Faça upgrade para desbloquear recursos premium!</p>
            <a href="{{ route('subscriptions.plans') }}" 
               class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-arrow-up mr-2"></i>Fazer Upgrade
            </a>
        </div>
    @endif
</div>

<!-- JavaScript for subscription actions -->
<script>
function cancelSubscription(subscriptionId) {
    if (!confirm('Tem certeza que deseja cancelar sua assinatura? Você perderá acesso aos recursos premium no final do período pago.')) {
        return;
    }

    fetch(`/subscriptions/${subscriptionId}/cancel`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Erro ao cancelar assinatura');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao cancelar assinatura');
    });
}

function resumeSubscription(subscriptionId) {
    if (!confirm('Tem certeza que deseja reativar sua assinatura?')) {
        return;
    }

    fetch(`/subscriptions/${subscriptionId}/resume`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Erro ao reativar assinatura');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao reativar assinatura');
    });
}
</script>
@endsection
