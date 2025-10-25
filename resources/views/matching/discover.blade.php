@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-heart text-pink-500 mr-2"></i>Descobrir Pessoas
    </h2>

    @if($potentialMatches->count() > 0)
        <div id="matches-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('matching.partials.match-cards', ['potentialMatches' => $potentialMatches])
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
    console.log('Like user ID:', userId);
    
    if (!userId) {
        showNotification('ID do usuário não encontrado', 'error');
        return;
    }
    
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
            const card = document.querySelector(`[data-user-id="${userId}"]`)?.closest('.bg-white.border');
            if (card) {
                card.remove();
                currentOffset--; // Decrease offset since we removed a card
            }
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

let currentOffset = {{ $potentialMatches->count() }};
let isLoading = false;
let hasMore = true;

function loadMoreMatches() {
    if (isLoading || !hasMore) {
        return;
    }
    
    isLoading = true;
    const button = document.querySelector('button[onclick="loadMoreMatches()"]');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Carregando...';
    button.disabled = true;
    
    fetch(`{{ route('matching.load-more') }}?offset=${currentOffset}&limit=20`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Append new matches to the container
            const container = document.getElementById('matches-container');
            container.insertAdjacentHTML('beforeend', data.html);
            
            currentOffset += data.count;
            hasMore = data.hasMore;
            
            if (!hasMore) {
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Todos carregados';
                button.disabled = true;
                button.classList.add('bg-gray-400', 'cursor-not-allowed');
                button.classList.remove('bg-pink-500', 'hover:bg-pink-600');
            } else {
                button.innerHTML = originalText;
                button.disabled = false;
            }
            
            showNotification(`${data.count} novas pessoas carregadas!`, 'success');
        } else {
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification(data.message || 'Erro ao carregar mais pessoas', 'error');
            hasMore = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        showNotification('Erro ao carregar mais pessoas', 'error');
    })
    .finally(() => {
        isLoading = false;
    });
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
