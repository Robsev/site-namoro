@extends('layouts.profile')

@section('title', 'Likes Enviados')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-heart text-pink-500 mr-3"></i>
                {{ __('messages.dashboard.likes_sent') }}
            </h1>
            <p class="mt-2 text-gray-600">{{ __('messages.matching.likes_sent_desc') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('matching.discover') }}" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-search mr-2"></i>{{ __('messages.nav.discover') }}
            </a>
            <a href="{{ route('matching.likes-received') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                <i class="fas fa-heart-broken mr-2"></i>{{ __('messages.nav.likes_received') }}
            </a>
        </div>
    </div>

    @if($likesSent->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <i class="fas fa-heart text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.matching.no_likes_sent') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('messages.matching.no_likes_sent_desc') }}</p>
            <a href="{{ route('matching.discover') }}" class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-search mr-2"></i>{{ __('messages.matching.start_discovering') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($likesSent as $like)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <!-- Profile Photo -->
                    <div class="relative">
                        @if($like->user2->photos->isNotEmpty())
                            @php
                                $photoPath = $like->user2->photos->first()->photo_path;
                                $photoUrl = str_starts_with($photoPath, 'http') ? $photoPath : Storage::url($photoPath);
                            @endphp
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $like->user2->name }}" 
                                 class="w-full h-64 object-cover">
                        @else
                            <div class="w-full h-64 bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center">
                                <i class="fas fa-user text-6xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4 bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-clock mr-1"></i>{{ __('messages.status.pending') }}
                        </div>

                        <!-- Compatibility Score -->
                        <div class="absolute top-4 left-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                            <i class="fas fa-heart text-pink-500 mr-1"></i>{{ round($like->compatibility_score) }}%
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $like->user2->name }}</h3>
                        
                        @if($like->user2->age)
                            <p class="text-gray-600 mb-2">
                                <i class="fas fa-birthday-cake mr-2"></i>{{ $like->user2->age }} {{ __('messages.common.years') }}
                            </p>
                        @endif

                        @if($like->user2->profile && $like->user2->profile->bio)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($like->user2->profile->bio, 100) }}</p>
                        @endif

                        <!-- Match Reason -->
                        @if($like->match_reason)
                            <div class="bg-pink-50 border border-pink-200 rounded-lg p-3 mb-4">
                                <p class="text-pink-800 text-sm">
                                    <i class="fas fa-lightbulb mr-2"></i>{{ $like->match_reason }}
                                </p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            <button onclick="viewProfile({{ $like->user2->id }})" 
                                    class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                                <i class="fas fa-eye mr-1"></i>{{ __('messages.common.view') }}
                            </button>
                            
                            <button onclick="undoLike({{ $like->user2->id }})" 
                                    class="flex-1 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-200">
                                <i class="fas fa-undo mr-1"></i>{{ __('messages.matching.undo_like') }}
                            </button>
                        </div>

                        <!-- Sent Date -->
                        <p class="text-xs text-gray-500 mt-3 text-center">
                            {{ __('messages.matching.sent_on') }} {{ $like->matched_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $likesSent->links() }}
        </div>
    @endif
</div>

<!-- JavaScript -->
<script>
function viewProfile(userId) {
    // Implementar visualização de perfil completo
    window.location.href = `/profile/view/${userId}`;
}

function undoLike(userId) {
    if (!confirm('{{ __('messages.matching.confirm_undo_like') }}')) {
        return;
    }

    fetch(`/matching/undo-like/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Like removido com sucesso!', 'success');
            // Recarregar a página para atualizar a lista
            window.location.reload();
        } else {
            showNotification(data.error || 'Erro ao remover like', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao remover like', 'error');
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
