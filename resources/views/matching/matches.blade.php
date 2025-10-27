@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-heart text-pink-500 mr-2"></i>{{ __('messages.matching.my_matches') }}
    </h2>

    @if($matches->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($matches as $match)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200">
                    <!-- Profile Photo -->
                    <div class="relative">
                        @if($match->other_user->profile_photo)
                            <img src="{{ str_starts_with($match->other_user->profile_photo, 'http') ? $match->other_user->profile_photo : Storage::url($match->other_user->profile_photo) }}" 
                                 alt="{{ $match->other_user->full_name }}" 
                                 class="w-full h-48 object-cover rounded-t-lg">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-pink-100 to-purple-100 rounded-t-lg flex items-center justify-center">
                                <i class="fas fa-user text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Compatibility Score -->
                        <div class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                            <i class="fas fa-heart text-pink-500 mr-1"></i>{{ round($match->compatibility_score) }}%
                        </div>

                        <!-- Online Status -->
                        @if($match->other_user->last_seen && $match->other_user->last_seen->diffInMinutes(now()) < 30)
                            <div class="absolute bottom-4 left-4 bg-green-500 text-white rounded-full px-2 py-1 text-xs">
                                <i class="fas fa-circle mr-1"></i>{{ __('messages.matching.online') }}
                            </div>
                        @endif
                    </div>

                    <!-- Profile Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $match->other_user->full_name }}
                            @if($match->other_user->is_verified)
                                <i class="fas fa-check-circle text-blue-500 ml-1" title="{{ __('messages.matching.verified') }}"></i>
                            @endif
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>@if($match->other_user->city && $match->other_user->state){{ $match->other_user->city }}, {{ $match->other_user->state }}@else{{ __('messages.matching.location_not_informed') }}@endif
                        </p>

                        @if($match->other_user->profile && $match->other_user->profile->bio)
                            <p class="text-gray-700 text-sm mb-3 line-clamp-2">{{ Str::limit($match->other_user->profile->bio, 80) }}</p>
                        @endif

                        <!-- Match Reason -->
                        @if($match->match_reason)
                            <div class="bg-pink-50 border border-pink-200 rounded-lg p-3 mb-3">
                                <p class="text-pink-700 text-sm">
                                    <i class="fas fa-info-circle mr-1"></i>{{ $match->translated_match_reason }}
                                </p>
                            </div>
                        @endif

                        <!-- Match Date -->
                        <p class="text-gray-500 text-xs mb-3">
                            <i class="fas fa-clock mr-1"></i>{{ __('messages.matching.match_date') }} {{ $match->matched_at->format('d/m/Y H:i') }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <a href="{{ route('chat.show', $match->other_user->id) }}" 
                               class="flex-1 bg-pink-500 text-white py-2 px-4 rounded-lg hover:bg-pink-600 transition duration-200 text-center">
                                <i class="fas fa-comment mr-1"></i>{{ __('messages.matching.start_chat') }}
                            </a>
                            
                            <a href="{{ route('profile.view', $match->other_user->id) }}" 
                               class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                                <i class="fas fa-user mr-1"></i>{{ __('messages.matching.view_profile') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-heart-broken text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">{{ __('messages.matching.no_matches_yet') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('messages.matching.start_discovering') }}</p>
            <a href="{{ route('matching.discover') }}" 
               class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-search mr-2"></i>{{ __('messages.matching.discover_people') }}
            </a>
        </div>
    @endif
</div>
@endsection
