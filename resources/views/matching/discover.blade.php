@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-heart text-pink-500 mr-2"></i>{{ __('messages.matching.discover') }}
    </h2>

    @if($potentialMatches->count() > 0)
        <div id="matches-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('matching.partials.match-cards', ['potentialMatches' => $potentialMatches])
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-8">
            <button onclick="loadMoreMatches()" 
                    class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-refresh mr-2"></i>{{ __('messages.matching.load_more_people') }}
            </button>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">{{ __('messages.matching.no_one_found') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('messages.matching.try_adjusting_preferences') }}</p>
            <a href="{{ route('preferences.edit') }}" 
               class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-cog mr-2"></i>{{ __('messages.matching.adjust_preferences') }}
            </a>
        </div>
    @endif
</div>

<!-- JavaScript for AJAX actions -->
<script>
const translations = {
    user_id_not_found: '{{ __('messages.matching.user_id_not_found') }}',
    like_sent: '{{ __('messages.matching.like_sent') }}',
    error_liking: '{{ __('messages.matching.error_liking') }}',
    error_liking_user: '{{ __('messages.matching.error_liking_user') }}',
    user_passed: '{{ __('messages.matching.user_passed') }}',
    error_passing: '{{ __('messages.matching.error_passing') }}',
    error_passing_user: '{{ __('messages.matching.error_passing_user') }}',
    confirm_super_like: '{{ __('messages.matching.confirm_super_like') }}',
    super_like_sent: '{{ __('messages.matching.super_like_sent') }}',
    error_super_liking: '{{ __('messages.matching.error_super_liking') }}',
    error_super_liking_user: '{{ __('messages.matching.error_super_liking_user') }}',
    loading: '{{ __('messages.matching.loading') }}',
    all_loaded: '{{ __('messages.matching.all_loaded') }}',
    new_people_loaded: '{{ __('messages.matching.new_people_loaded') }}',
    error_loading: '{{ __('messages.matching.error_loading') }}'
};

function likeUser(userId) {
    console.log('Like user ID:', userId);
    
    if (!userId) {
        showNotification(translations.user_id_not_found, 'error');
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
            showNotification(translations.like_sent, 'success');
            // Remove the card from the view
            const card = document.querySelector(`[data-user-id="${userId}"]`)?.closest('.bg-white.border');
            if (card) {
                card.remove();
                currentOffset--; // Decrease offset since we removed a card
            }
        } else {
            showNotification(data.error || translations.error_liking, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(translations.error_liking_user, 'error');
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
            showNotification(translations.user_passed, 'info');
            // Remove the card from the view
            document.querySelector(`[data-user-id="${userId}"]`)?.remove();
        } else {
            showNotification(data.error || translations.error_passing, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(translations.error_passing_user, 'error');
    });
}

function superLikeUser(userId) {
    if (!confirm(translations.confirm_super_like)) {
        return;
    }

    fetch(`/matching/super-like/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        // Check if response is ok before parsing JSON
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || translations.error_super_liking);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(translations.super_like_sent, 'success');
            // Remove the card from the view
            document.querySelector(`[data-user-id="${userId}"]`)?.remove();
        } else {
            showNotification(data.error || translations.error_super_liking, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Only show error if it's a real error, not a success message
        const errorMessage = error.message || translations.error_super_liking_user;
        // Check if error message indicates success (super like was actually sent)
        if (errorMessage.includes('limite') || errorMessage.includes('disponíveis')) {
            showNotification(errorMessage, 'warning');
        } else {
            showNotification(errorMessage, 'error');
        }
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
    button.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${translations.loading}`;
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
                button.innerHTML = `<i class="fas fa-check mr-2"></i>${translations.all_loaded}`;
                button.disabled = true;
                button.classList.add('bg-gray-400', 'cursor-not-allowed');
                button.classList.remove('bg-pink-500', 'hover:bg-pink-600');
            } else {
                button.innerHTML = originalText;
                button.disabled = false;
            }
            
            showNotification(`${data.count} ${translations.new_people_loaded}`, 'success');
        } else {
            button.innerHTML = originalText;
            button.disabled = false;
            showNotification(data.message || translations.error_loading, 'error');
            hasMore = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        showNotification(translations.error_loading, 'error');
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
