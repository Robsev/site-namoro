@extends('layouts.profile')

@section('title', 'Moderar Foto - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-image text-blue-500 mr-3"></i>
                        {{ __('messages.admin.photos') }}
                    </h1>
                    <p class="mt-2 text-gray-600">{{ __('messages.admin.photos.moderate') }}</p>
                </div>
                <a href="{{ route('admin.photos.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('messages.admin.photos.back') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Photo Section -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-image text-blue-500 mr-2"></i>
                        {{ __('messages.admin.photos.photo_for_moderation') }}
                    </h2>
                    
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-4">
                        <img src="{{ Storage::url($photo->photo_path) }}" 
                             alt="Foto do usuário {{ $photo->user->name }}"
                             class="w-full h-full object-cover">
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>{{ __('messages.admin.photos.sent_on') }}:</strong> {{ $photo->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>{{ __('messages.dashboard.status') }}:</strong> 
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($photo->moderation_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($photo->moderation_status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($photo->moderation_status) }}
                            </span>
                        </p>
                        @if($photo->moderated_at)
                            <p><strong>{{ __('messages.admin.photos.moderated_on') }}:</strong> {{ $photo->moderated_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($photo->moderator)
                            <p><strong>{{ __('messages.admin.photos.moderated_by') }}:</strong> {{ $photo->moderator->name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Info & Actions -->
            <div class="space-y-6">
                <!-- User Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user text-green-500 mr-2"></i>
                        {{ __('messages.admin.photos.user_info') }}
                    </h2>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        @if($photo->user->profile_photo)
                            <img src="{{ Storage::url($photo->user->profile_photo) }}" 
                                 alt="{{ $photo->user->name }}"
                                 class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $photo->user->name }}</h3>
                            <p class="text-gray-600">{{ $photo->user->email }}</p>
                            <p class="text-sm text-gray-500">
                                {{ __('messages.admin.photos.member_since') }} {{ $photo->user->created_at->format('M/Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <p><strong>{{ __('messages.profile.age') }}:</strong> {{ $photo->user->age ?? __('messages.common.not_informed') }}</p>
                        <p><strong>{{ __('messages.profile.gender') }}:</strong> {{ ucfirst($photo->user->gender ?? __('messages.common.not_informed')) }}</p>
                        <p><strong>{{ __('messages.location.title') }}:</strong> {{ $photo->user->formatted_location ?? __('messages.common.not_informed') }}</p>
                        <p><strong>{{ __('messages.admin.photos.verified') }}:</strong> 
                            @if($photo->user->is_verified)
                                <span class="text-green-600"><i class="fas fa-check-circle"></i> {{ __('messages.common.yes') }}</span>
                            @else
                                <span class="text-red-600"><i class="fas fa-times-circle"></i> {{ __('messages.common.no') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Other Photos from User -->
                @if($userPhotos->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-images text-purple-500 mr-2"></i>
                        {{ __('messages.admin.photos.other_photos') }}
                    </h2>
                    
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($userPhotos as $userPhoto)
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($userPhoto->photo_path) }}" 
                                     alt="Foto do usuário"
                                     class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Moderation Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        <i class="fas fa-gavel text-red-500 mr-2"></i>
                        {{ __('messages.admin.photos.moderation_actions') }}
                    </h2>
                    
                    @if($photo->moderation_status === 'pending')
                        <!-- Approve Form -->
                        <form method="POST" action="{{ route('admin.photos.approve', $photo) }}" class="mb-4">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('messages.admin.photos.notes_optional') }}
                                </label>
                                <textarea name="moderation_notes" rows="3" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                          placeholder="{{ __('messages.admin.photos.approve_notes_placeholder') }}"></textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                                <i class="fas fa-check mr-2"></i>
                                {{ __('messages.admin.photos.approve_photo') }}
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form method="POST" action="{{ route('admin.photos.reject', $photo) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('messages.admin.photos.reject_reason') }}
                                </label>
                                <select name="reason" required 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="">{{ __('messages.admin.photos.select_reason') }}</option>
                                    <option value="inappropriate">{{ __('messages.admin.photos.reason.inappropriate') }}</option>
                                    <option value="low_quality">{{ __('messages.admin.photos.reason.low_quality') }}</option>
                                    <option value="not_clear">{{ __('messages.admin.photos.reason.not_clear') }}</option>
                                    <option value="other">{{ __('messages.admin.photos.reason.other') }}</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('messages.admin.photos.notes_required') }}
                                </label>
                                <textarea name="moderation_notes" required rows="3" 
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                          placeholder="{{ __('messages.admin.photos.reject_notes_placeholder') }}"></textarea>
                            </div>
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition duration-200 font-medium">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('messages.admin.photos.reject_photo') }}
                            </button>
                        </form>
                    @else
                        <!-- Already Moderated -->
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center
                                @if($photo->moderation_status === 'approved') bg-green-100 text-green-600
                                @else bg-red-100 text-red-600 @endif">
                                <i class="fas @if($photo->moderation_status === 'approved') fa-check @else fa-times @endif text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                {{ __('messages.admin.photos.already_moderated', ['status' => $photo->moderation_status === 'approved' ? __('messages.admin.approved_photos') : __('messages.admin.rejected_photos')]) }}
                            </h3>
                            <p class="text-gray-600 mb-4">
                                {{ __('messages.admin.photos.already_moderated_desc', ['date' => $photo->moderated_at->format('d/m/Y H:i')]) }}
                            </p>
                            @if($photo->moderation_notes)
                                <div class="bg-gray-50 rounded-lg p-4 text-left">
                                    <p class="text-sm font-medium text-gray-700 mb-1">{{ __('messages.admin.photos.moderation_notes') }}:</p>
                                    <p class="text-sm text-gray-600">{{ $photo->moderation_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
