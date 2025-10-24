@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-heart text-pink-500 mr-2"></i>Descobrir Pessoas
    </h2>

    @if($potentialMatches->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($potentialMatches as $match)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200">
                    <!-- Profile Photo -->
                    <div class="relative">
                        @if($match->profile_photo)
                            <img src="{{ Storage::url($match->profile_photo) }}" 
                                 alt="{{ $match->full_name }}" 
                                 class="w-full h-64 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-64 bg-gradient-to-br from-pink-100 to-purple-100 rounded-t-lg flex items-center justify-center">
                                <i class="fas fa-user text-6xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Compatibility Score -->
                        <div class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                            <i class="fas fa-heart text-pink-500 mr-1"></i>{{ round($match->compatibility_score) }}%
                        </div>

                        <!-- Age Badge -->
                        @if($match->age)
                            <div class="absolute bottom-4 left-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                                {{ $match->age }} anos
                            </div>
                        @endif
                    </div>

                    <!-- Profile Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $match->full_name }}
                            @if($match->is_verified)
                                <i class="fas fa-check-circle text-blue-500 ml-1" title="Verificado"></i>
                            @endif
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $match->location ?? 'Localização não informada' }}
                        </p>

                        @if($match->profile && $match->profile->bio)
                            <p class="text-gray-700 text-sm mb-3 line-clamp-2">{{ Str::limit($match->profile->bio, 100) }}</p>
                        @endif

                        <!-- Interests -->
                        @if($match->profile && $match->profile->interests)
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach(array_slice($match->profile->interests, 0, 3) as $interest)
                                    <span class="bg-pink-100 text-pink-700 text-xs px-2 py-1 rounded-full">{{ $interest }}</span>
                                @endforeach
                                @if(count($match->profile->interests) > 3)
                                    <span class="text-gray-500 text-xs">+{{ count($match->profile->interests) - 3 }} mais</span>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button onclick="passUser({{ $match->id }})" 
                                    class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                                <i class="fas fa-times mr-1"></i>Passar
                            </button>
                            
                            <button onclick="likeUser({{ $match->id }})" 
                                    class="flex-1 bg-pink-500 text-white py-2 px-4 rounded-lg hover:bg-pink-600 transition duration-200">
                                <i class="fas fa-heart mr-1"></i>Curtir
                            </button>
                            
                            <button onclick="superLikeUser({{ $match->id }})" 
                                    class="bg-blue-500 text-white py-2 px-3 rounded-lg hover:bg-blue-600 transition duration-200"
                                    title="Super Like">
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-8">
            <button onclick="loadMoreMatches()" 
                    class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-refresh mr-2"></i>Carregar Mais Pessoas
            </button>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma pessoa encontrada</h3>
            <p class="text-gray-500 mb-6">Tente ajustar suas preferências de matching para ver mais pessoas.</p>
            <a href="{{ route('preferences.edit') }}" 
               class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-cog mr-2"></i>Ajustar Preferências
            </a>
        </div>
    @endif
</div>

<!-- JavaScript for AJAX actions -->
<script>
function likeUser(userId) {
    fetch(`/matching/like/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Curtida enviada!', 'success');
            // Remove the card from the view
            document.querySelector(`[data-user-id="${userId}"]`)?.remove();
        } else {
            showNotification(data.error || 'Erro ao curtir', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao curtir usuário', 'error');
    });
}

function passUser(userId) {
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
            showNotification('Usuário passado', 'info');
            // Remove the card from the view
            document.querySelector(`[data-user-id="${userId}"]`)?.remove();
        } else {
            showNotification(data.error || 'Erro ao passar', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao passar usuário', 'error');
    });
}

function superLikeUser(userId) {
    if (!confirm('Tem certeza que deseja dar um Super Like? Você tem um número limitado por dia.')) {
        return;
    }

    fetch(`/matching/super-like/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Super Like enviado!', 'success');
            // Remove the card from the view
            document.querySelector(`[data-user-id="${userId}"]`)?.remove();
        } else {
            showNotification(data.error || 'Erro ao enviar Super Like', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao enviar Super Like', 'error');
    });
}

function loadMoreMatches() {
    // This would implement pagination or load more functionality
    showNotification('Funcionalidade em desenvolvimento', 'info');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'info' ? 'bg-blue-500 text-white' :
        'bg-gray-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
