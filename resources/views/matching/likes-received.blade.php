@extends('layouts.profile')

@section('title', 'Likes Recebidos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-heart-broken text-blue-500 mr-3"></i>
                {{ __('messages.nav.likes_received') }}
            </h1>
            <p class="mt-2 text-gray-600">{{ __('messages.matching.likes_received_desc') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('matching.discover') }}" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-search mr-2"></i>{{ __('messages.nav.discover') }}
            </a>
            <a href="{{ route('matching.likes-sent') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                <i class="fas fa-heart mr-2"></i>{{ __('messages.dashboard.likes_sent') }}
            </a>
        </div>
    </div>

    @if($likesReceived->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-heart-broken text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.matching.no_likes_received') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('messages.matching.no_likes_received_desc') }}</p>
            <a href="{{ route('profile.show') }}" class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-user mr-2"></i>{{ __('messages.profile.complete_profile') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($likesReceived as $like)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <!-- Profile Photo -->
                    <div class="relative">
                        @if($like->user1->photos->isNotEmpty())
                            @php
                                $photoPath = $like->user1->photos->first()->photo_path;
                                $photoUrl = str_starts_with($photoPath, 'http') ? $photoPath : Storage::url($photoPath);
                            @endphp
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $like->user1->name }}" 
                                 class="w-full h-64 object-cover">
                        @else
                            <div class="w-full h-64 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                <i class="fas fa-user text-6xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-heart mr-1"></i>{{ __('messages.matching.liked_you') }}
                        </div>

                        <!-- Compatibility Score -->
                        <div class="absolute top-4 left-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                            <i class="fas fa-heart text-pink-500 mr-1"></i>{{ round($like->compatibility_score) }}%
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $like->user1->name }}</h3>
                        
                        @if($like->user1->age)
                            <p class="text-gray-600 mb-2">
                                <i class="fas fa-birthday-cake mr-2"></i>{{ $like->user1->age }} {{ __('messages.common.years') }}
                            </p>
                        @endif

                        @if($like->user1->profile && $like->user1->profile->bio)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($like->user1->profile->bio, 100) }}</p>
                        @endif

                        <!-- Match Reason -->
                        @if($like->match_reason)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                                <p class="text-blue-800 text-sm">
                                    <i class="fas fa-lightbulb mr-2"></i>{{ $like->match_reason }}
                                </p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <button onclick="viewProfile({{ $like->user1->id }})" 
                                    class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200">
                                <i class="fas fa-eye mr-1"></i>{{ __('messages.common.view') }}
                            </button>
                            
                            <button onclick="acceptLike({{ $like->user1->id }})" 
                                    class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200">
                                <i class="fas fa-check mr-1"></i>{{ __('messages.matching.accept_like') }}
                            </button>
                            
                            <button onclick="rejectLike({{ $like->user1->id }})" 
                                    class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-200">
                                <i class="fas fa-times mr-1"></i>{{ __('messages.matching.reject_like') }}
                            </button>
                        </div>

                        <!-- Received Date -->
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            {{ __('messages.matching.received_on') }} {{ $like->matched_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $likesReceived->links() }}
        </div>
    @endif
</div>

<!-- JavaScript -->
<script>
function viewProfile(userId) {
    // Implementar visualização de perfil completo
    window.location.href = `/profile/view/${userId}`;
}

function acceptLike(userId) {
    fetch(`/matching/accept-like/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Match criado! Vocês podem conversar agora!', 'success');
            // Redirecionar para conversas
            setTimeout(() => {
                window.location.href = '/conversations';
            }, 2000);
        } else {
            showNotification(data.error || 'Erro ao aceitar like', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao aceitar like', 'error');
    });
}

function rejectLike(userId) {
    if (!confirm('{{ __('messages.matching.confirm_reject_like') }}')) {
        return;
    }

    fetch(`/matching/pass/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Like recusado', 'success');
            // Recarregar a página para atualizar a lista
            window.location.reload();
        } else {
            showNotification(data.error || 'Erro ao recusar like', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao recusar like', 'error');
    });
}

function showNotification(message, type) {
    // Criar elemento de notificação
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remover após 3 segundos
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
