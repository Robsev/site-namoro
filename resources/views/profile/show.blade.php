@extends('layouts.profile')

@section('title', __('messages.profile.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Completeness Indicator -->
    @include('profile.completeness-indicator')
    
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-6">
            <!-- Profile Photo -->
            <div class="relative">
                @if($user->profile_photo)
                    <img src="{{ Storage::url($user->profile_photo) }}" 
                         alt="{{ __('messages.profile.profile_photo') }}" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-pink-200">
                @else
                    <div class="w-32 h-32 rounded-full bg-pink-100 flex items-center justify-center border-4 border-pink-200">
                        <i class="fas fa-user text-4xl text-pink-400"></i>
                    </div>
                @endif
                <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center">
                    <i class="fas fa-check text-white text-sm"></i>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ $user->full_name }}
                    @if($user->age)
                        <span class="text-xl text-gray-500">, {{ $user->age }} {{ __('messages.common.years') }}</span>
                    @endif
                </h1>
                
                @if($user->location)
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $user->location }}
                    </p>
                @endif

                @if($user->profile && $user->profile->bio)
                    <p class="text-gray-700 mb-4">{{ $user->profile->bio }}</p>
                @endif

                <div class="flex flex-wrap gap-2">
                    @if($user->profile && $user->profile->interests)
                        @foreach($user->profile->interests as $interest)
                            <span class="px-3 py-1 bg-pink-100 text-pink-800 text-sm rounded-full">
                                {{ $interest }}
                            </span>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
                <a href="{{ route('profile.edit') }}" 
                   class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                    <i class="fas fa-edit mr-2"></i>{{ __('messages.profile.edit_profile') }}
                </a>
                <a href="{{ route('preferences.edit') }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-cog mr-2"></i>{{ __('messages.profile.preferences') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle mr-2"></i>{{ __('messages.profile.basic_info') }}
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.name') }}:</span>
                    <span class="font-medium">{{ $user->full_name }}</span>
                </div>
                
                @if($user->age)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.age') }}:</span>
                    <span class="font-medium">{{ $user->age }} {{ __('messages.common.years') }}</span>
                </div>
                @endif

                @if($user->gender)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.gender') }}:</span>
                    <span class="font-medium">{{ __('messages.register.gender_' . $user->gender) }}</span>
                </div>
                @endif

                @if($user->location)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.location') }}:</span>
                    <span class="font-medium">{{ $user->location }}</span>
                </div>
                @endif

                @if($user->phone)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.phone') }}:</span>
                    <span class="font-medium">{{ $user->phone }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Profile Details -->
        @if($user->profile)
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-user-circle mr-2"></i>{{ __('messages.profile.profile_details') }}
            </h2>
            
            <div class="space-y-3">
                @if($user->profile->occupation)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.occupation') }}:</span>
                    <span class="font-medium">{{ $user->profile->occupation }}</span>
                </div>
                @endif

                @if($user->profile->education_level)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.education') }}:</span>
                    <span class="font-medium">{{ __('messages.profile.' . str_replace('_', '_', $user->profile->education_level)) }}</span>
                </div>
                @endif

                @if($user->profile->relationship_goal)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.relationship_goal') }}:</span>
                    <span class="font-medium">{{ __('messages.profile.' . $user->profile->relationship_goal) }}</span>
                </div>
                @endif

                @if($user->profile->smoking)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.smoking') }}:</span>
                    <span class="font-medium">{{ __('messages.profile.' . $user->profile->smoking) }}</span>
                </div>
                @endif

                @if($user->profile->drinking)
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('messages.profile.drinking') }}:</span>
                    <span class="font-medium">{{ __('messages.profile.' . $user->profile->drinking) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Photos Section -->
    @if($user->photos->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-images mr-2"></i>{{ __('messages.profile.my_photos') }}
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($user->photos as $photo)
            <div class="relative group">
                <img src="{{ Storage::url($photo->photo_path) }}" 
                     alt="Foto do perfil" 
                     class="w-full h-48 object-cover rounded-lg">
                
                @if($photo->is_primary)
                <div class="absolute top-2 left-2 bg-pink-600 text-white px-2 py-1 rounded text-xs font-medium">
                    {{ __('messages.profile.primary') }}
                </div>
                @endif

                @if($photo->moderation_status === 'pending')
                <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium">
                    {{ __('messages.profile.pending') }}
                </div>
                @endif

                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div class="flex space-x-2">
                        @if(!$photo->is_primary)
                        <form method="POST" action="{{ route('photos.primary', $photo) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-pink-600 text-white p-2 rounded-full hover:bg-pink-700 transition duration-200">
                                <i class="fas fa-star"></i>
                            </button>
                        </form>
                        @endif
                        
                        <form method="POST" action="{{ route('photos.destroy', $photo) }}" class="inline" onsubmit="return confirm('{{ __('messages.profile.confirm_delete_photo') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Security Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-shield-alt text-blue-500 mr-2"></i>{{ __('messages.profile.security') }}
        </h3>
        
        <div class="max-w-md">
            <h4 class="text-lg font-medium text-gray-800 mb-3">{{ __('messages.profile.change_password') }}</h4>
            <form id="passwordForm" class="space-y-4">
                @csrf
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('messages.profile.current_password') }}
                    </label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('messages.profile.new_password') }}
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required
                           minlength="8">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('messages.profile.confirm_new_password') }}
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required
                           minlength="8">
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-key mr-2"></i>{{ __('messages.profile.change_password') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("profile.update.password") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('{{ __('messages.profile.password_changed_success') }}', 'success');
            this.reset();
        } else {
            showNotification(result.message || '{{ __('messages.profile.password_change_error') }}', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('{{ __('messages.profile.password_change_error') }}', 'error');
    }
});

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
</script>
@endsection
