@foreach($potentialMatches as $match)
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200">
        <!-- Profile Photo -->
        <div class="relative">
            @if($match->profile_photo)
                <img src="{{ str_starts_with($match->profile_photo, 'http') ? $match->profile_photo : Storage::url($match->profile_photo) }}" 
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

            <!-- Profile Completeness -->
            <div class="absolute bottom-4 right-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                <i class="fas fa-chart-pie text-blue-500 mr-1"></i>{{ $match->profile_completeness }}%
            </div>

            <!-- Age Badge -->
            @if($match->age)
                <div class="absolute bottom-4 left-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                    {{ $match->age }} {{ __('messages.common.years') }}
                </div>
            @endif

            <!-- Distance Badge -->
            @if($match->distance !== null)
                <div class="absolute top-4 left-4 bg-white bg-opacity-90 rounded-full px-3 py-1 text-sm font-semibold">
                    <i class="fas fa-map-marker-alt text-green-500 mr-1"></i>{{ round($match->distance, 1) }} km
                </div>
            @endif
        </div>

        <!-- Profile Info -->
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                {{ $match->full_name }}
                @if($match->is_verified)
                    <i class="fas fa-check-circle text-blue-500 ml-1" title="{{ __('messages.profile.verified') }}"></i>
                @endif
            </h3>
            
            <p class="text-gray-600 text-sm mb-2">
                <i class="fas fa-map-marker-alt mr-1"></i>{{ $match->location ?? __('messages.matching.location_not_informed') }}
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
                        <span class="text-gray-500 text-xs">+{{ count($match->profile->interests) - 3 }} {{ __('messages.common.more') }}</span>
                    @endif
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-2">
                @if(auth()->user()->hasActivePremiumSubscription())
                    <a href="{{ route('profile.view', $match->id) }}" 
                       class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200 text-center">
                        <i class="fas fa-user mr-1"></i>{{ __('messages.matching.view_profile') }}
                    </a>
                @endif
                
                <button onclick="passUser({{ $match->id }})" 
                        class="flex-1 bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">
                    <i class="fas fa-times mr-1"></i>{{ __('messages.matching.pass') }}
                </button>
                
                <button onclick="likeUser({{ $match->id }})" 
                        class="flex-1 bg-pink-500 text-white py-2 px-4 rounded-lg hover:bg-pink-600 transition duration-200"
                        data-user-id="{{ $match->id }}">
                    <i class="fas fa-heart mr-1"></i>{{ __('messages.matching.like') }}
                </button>
                
                <button onclick="superLikeUser({{ $match->id }})" 
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-200 flex items-center justify-center"
                        title="{{ __('messages.matching.super_like_info') }}">
                    <i class="fas fa-star mr-2"></i>{{ __('messages.matching.super_like') }}
                </button>
            </div>
        </div>
    </div>
@endforeach
