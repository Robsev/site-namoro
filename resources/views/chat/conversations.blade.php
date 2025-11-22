@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-comments text-pink-500 mr-2"></i>{{ __('messages.chat.title') }}
    </h2>

    @if($conversations->count() > 0)
        <div class="space-y-4">
            @foreach($conversations as $conversation)
                <a href="{{ route('chat.show', $conversation['user']->id) }}" 
                   class="block bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                    <div class="flex items-center">
                        <!-- Profile Photo -->
                        <div class="flex-shrink-0 mr-4">
                            @if($conversation['user']->profile_photo)
                                <img src="{{ $conversation['user']->profile_photo_url }}" 
                                     alt="{{ $conversation['user']->full_name }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-100 to-purple-100 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Conversation Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 truncate">
                                    {{ $conversation['user']->full_name }}
                                    @if($conversation['user']->is_verified)
                                        <i class="fas fa-check-circle text-blue-500 ml-1" title="{{ __('messages.chat.verified') }}"></i>
                                    @endif
                                </h3>
                                <div class="flex items-center space-x-2">
                                    @if($conversation['unread_count'] > 0)
                                        <span class="bg-pink-500 text-white text-xs px-2 py-1 rounded-full">
                                            {{ $conversation['unread_count'] }}
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500">
                                        {{ $conversation['updated_at']->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 text-sm mt-1 truncate">
                                @if($conversation['last_message'])
                                    @if($conversation['last_message']->message_type === 'text')
                                        {{ Str::limit($conversation['last_message']->message, 60) }}
                                    @elseif($conversation['last_message']->message_type === 'image')
                                        <i class="fas fa-image mr-1"></i>{{ __('messages.chat.image') }}
                                    @else
                                        <i class="fas fa-file mr-1"></i>{{ __('messages.chat.file_message') }}
                                    @endif
                                @else
                                    {{ __('messages.chat.no_message_yet') }}
                                @endif
                            </p>
                        </div>

                        <!-- Online Status -->
                        <div class="flex-shrink-0 ml-4">
                            @if($conversation['user']->last_seen && $conversation['user']->last_seen->diffInMinutes(now()) < 30)
                                <div class="w-3 h-3 bg-green-500 rounded-full" title="{{ __('messages.chat.online') }}"></div>
                            @else
                                <div class="w-3 h-3 bg-gray-300 rounded-full" title="{{ __('messages.chat.offline') }}"></div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">{{ __('messages.chat.no_conversations') }}</h3>
            <p class="text-gray-500 mb-6">{{ __('messages.chat.start_conversing') }}</p>
            <a href="{{ route('matching.matches') }}" 
               class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-heart mr-2"></i>{{ __('messages.chat.view_matches') }}
            </a>
        </div>
    @endif
</div>
@endsection
