@extends('layouts.profile')

@section('title', 'Conversas - Sintonia de Amor')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-comments text-blue-500 mr-3"></i>
                        {{ __('messages.chat.title') }}
                    </h1>
                    <p class="mt-2 text-gray-600">{{ __('messages.chat.subtitle') }}</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('matching.discover') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>
                        {{ __('messages.matching.discover_people') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="bg-white rounded-lg shadow">
            @if($conversations->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                        @php
                            $otherUser = $conversation->getOtherUser(auth()->id());
                            $unreadCount = $conversation->getUnreadCountForUser(auth()->id());
                            $latestMessage = $conversation->latestMessage;
                        @endphp
                        
                        <div class="p-6 hover:bg-gray-50 transition duration-200 cursor-pointer"
                             onclick="window.location.href='{{ route('conversations.show', $conversation) }}'">
                            <div class="flex items-center space-x-4">
                                <!-- User Avatar -->
                                <div class="flex-shrink-0">
                                    @if($otherUser->profile_photo)
                                        <img src="{{ $otherUser->profile_photo_url }}" 
                                             alt="{{ $otherUser->name }}"
                                             class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Conversation Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-lg font-medium text-gray-900 truncate">
                                                {{ $otherUser->name }}
                                            </h3>
                                            @if($otherUser->is_verified)
                                                <i class="fas fa-check-circle text-blue-500 text-sm"></i>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($latestMessage)
                                                <span class="text-sm text-gray-500">
                                                    {{ $latestMessage->created_at->diffForHumans() }}
                                                </span>
                                            @endif
                                            @if($unreadCount > 0)
                                                <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                    {{ $unreadCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($latestMessage)
                                        <p class="text-sm text-gray-600 truncate mt-1">
                                            @if($latestMessage->sender_id === auth()->id())
                                                <span class="text-gray-500">{{ __('messages.chat.you') }}:</span>
                                            @endif
                                            {{ $latestMessage->message }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500 italic">{{ __('messages.chat.no_messages_yet') }}</p>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    <button onclick="event.stopPropagation(); archiveConversation({{ $conversation->id }})"
                                            class="text-gray-400 hover:text-gray-600 transition duration-200"
                                            title="{{ __('messages.chat.archive_chat') }}">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $conversations->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-comments text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('messages.chat.no_conversations_yet') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('messages.chat.start_conversing') }}</p>
                    <a href="{{ route('matching.discover') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>
                        {{ __('messages.matching.discover_people') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function archiveConversation(conversationId) {
    if (confirm('{{ __('messages.chat.confirm_archive') }}')) {
        fetch(`/conversations/${conversationId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endsection
