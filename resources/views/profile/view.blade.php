@extends('layouts.profile')

@section('title', 'Perfil de ' . $user->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-600">{{ __('messages.profile.public_profile') }}</p>
            </div>
        </div>
        
        @if($compatibilityScore)
            <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                <i class="fas fa-heart mr-2"></i>{{ round($compatibilityScore) }}% {{ __('messages.matching.compatibility') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Photos -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <i class="fas fa-images mr-2"></i>{{ __('messages.profile.photos') }}
                    </h2>
                </div>
                
                @if($user->photos->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-6">
                        @foreach($user->photos as $photo)
                            <div class="aspect-square overflow-hidden rounded-lg">
                                <img src="{{ Storage::url($photo->photo_path) }}" 
                                     alt="Foto de {{ $user->name }}" 
                                     class="w-full h-full object-cover hover:scale-105 transition duration-300 cursor-pointer"
                                     onclick="openImageModal('{{ Storage::url($photo->photo_path) }}')">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-image text-4xl mb-4"></i>
                        <p>{{ __('messages.profile.no_photos_available') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Profile Info -->
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2"></i>{{ __('messages.profile.basic_info') }}
                </h3>
                
                <div class="space-y-3">
                    @if($user->age)
                        <div class="flex items-center">
                            <i class="fas fa-birthday-cake text-gray-400 w-5"></i>
                            <span class="ml-3 text-gray-700">{{ $user->age }} {{ __('messages.common.years') }}</span>
                        </div>
                    @endif
                    
                    @if($user->gender)
                        <div class="flex items-center">
                            <i class="fas fa-venus-mars text-gray-400 w-5"></i>
                            <span class="ml-3 text-gray-700">{{ __('messages.register.gender_' . $user->gender) }}</span>
                        </div>
                    @endif
                    
                    @if($user->city && $user->state)
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                            <span class="ml-3 text-gray-700">{{ $user->city }}, {{ $user->state }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bio -->
            @if($user->profile && $user->profile->bio)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-quote-left mr-2"></i>{{ __('messages.profile.about') }}
                    </h3>
                    <p class="text-gray-700 leading-relaxed">{{ $user->profile->bio }}</p>
                </div>
            @endif

            <!-- Interests -->
            @if($user->interests->isNotEmpty())
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-heart mr-2"></i>{{ __('messages.profile.interests') }}
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->interests as $interest)
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                {{ $interest->interest_value }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Personality Profile -->
            @if($user->psychologicalProfile)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-brain mr-2"></i>{{ __('messages.nav.psychological_profile') }}
                    </h3>
                    <div class="space-y-3">
                        @php
                            $traits = [
                                'openness' => __('messages.psychological.openness'),
                                'conscientiousness' => __('messages.psychological.conscientiousness'),
                                'extraversion' => __('messages.psychological.extraversion'),
                                'agreeableness' => __('messages.psychological.agreeableness'),
                                'neuroticism' => __('messages.psychological.neuroticism')
                            ];
                        @endphp
                        @foreach($traits as $trait => $label)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ $label }}</span>
                                    <span class="text-gray-900">{{ round($user->psychologicalProfile->$trait, 1) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($user->psychologicalProfile->$trait / 5) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-bolt mr-2"></i>{{ __('messages.profile.actions') }}
                </h3>
                
                <div class="space-y-3">
                    @if($existingMatch)
                        @if($existingMatch->status === 'pending')
                            @if($existingMatch->user1_id === auth()->id())
                                <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded-lg text-center">
                                    <i class="fas fa-clock mr-2"></i>{{ __('messages.matching.waiting_response') }}
                                </div>
                            @else
                                <div class="space-y-2">
                                    <button onclick="acceptLike({{ $user->id }})" 
                                            class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200">
                                        <i class="fas fa-check mr-2"></i>{{ __('messages.matching.accept_like') }}
                                    </button>
                                    <button onclick="rejectLike({{ $user->id }})" 
                                            class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-200">
                                        <i class="fas fa-times mr-2"></i>{{ __('messages.matching.reject_like') }}
                                    </button>
                                </div>
                            @endif
                        @elseif($existingMatch->status === 'accepted')
                            <a href="{{ route('conversations.show', $existingMatch->id) }}" 
                               class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200 text-center block">
                                <i class="fas fa-comments mr-2"></i>{{ __('messages.matching.start_conversation') }}
                            </a>
                        @endif
                    @else
                        <div class="space-y-2">
                            <button onclick="likeUser({{ $user->id }})" 
                                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200">
                                <i class="fas fa-heart mr-2"></i>{{ __('messages.matching.like') }}
                            </button>
                            <button onclick="passUser({{ $user->id }})" 
                                    class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">
                                <i class="fas fa-times mr-2"></i>{{ __('messages.matching.pass') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- JavaScript -->
<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

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
            showNotification('{{ __('messages.matching.like_sent') }}', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(data.error || '{{ __('messages.matching.error_liking') }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ __('messages.matching.error_liking') }}', 'error');
    });
}

function passUser(userId) {
    if (!confirm('{{ __('messages.matching.confirm_pass') }}')) {
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
            showNotification('{{ __('messages.matching.user_passed') }}', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(data.error || '{{ __('messages.matching.error_passing') }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ __('messages.matching.error_passing') }}', 'error');
    });
}

function acceptLike(userId) {
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
            showNotification('{{ __('messages.matching.match_created') }}', 'success');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showNotification(data.error || '{{ __('messages.matching.error_accepting') }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ __('messages.matching.error_accepting') }}', 'error');
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
            showNotification('{{ __('messages.matching.like_rejected') }}', 'success');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showNotification(data.error || '{{ __('messages.matching.error_rejecting') }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('{{ __('messages.matching.error_rejecting') }}', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
@endsection
