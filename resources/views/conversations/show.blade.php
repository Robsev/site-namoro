@extends('layouts.profile')

@section('title', 'Conversa com ' . $otherUser->name . ' - Amigos Para Sempre')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('conversations.index') }}" 
                       class="text-gray-500 hover:text-gray-700 transition duration-200">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    
                    <!-- User Info -->
                    <div class="flex items-center space-x-3">
                        @if($otherUser->profile_photo)
                            <img src="{{ Storage::url($otherUser->profile_photo) }}" 
                                 alt="{{ $otherUser->name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        @endif
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">{{ $otherUser->name }}</h2>
                            <p class="text-sm text-gray-500">
                                @if($otherUser->is_verified)
                                    <i class="fas fa-check-circle text-blue-500 mr-1"></i>
                                @endif
                                Online
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <button onclick="archiveConversation({{ $conversation->id }})"
                            class="text-gray-500 hover:text-gray-700 transition duration-200"
                            title="Arquivar conversa">
                        <i class="fas fa-archive"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="bg-white h-96 overflow-y-auto" id="messagesContainer">
            <div class="p-6 space-y-4">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <!-- Message Bubble -->
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() 
                                ? 'bg-blue-500 text-white' 
                                : 'bg-gray-200 text-gray-900' }}">
                                <p class="text-sm">{{ $message->message }}</p>
                            </div>
                            
                            <!-- Message Info -->
                            <div class="mt-1 text-xs text-gray-500 {{ $message->sender_id === auth()->id() ? 'text-right' : 'text-left' }}">
                                {{ $message->created_at->format('H:i') }}
                                @if($message->sender_id === auth()->id())
                                    @if($message->is_read)
                                        <i class="fas fa-check-double text-blue-500 ml-1"></i>
                                    @else
                                        <i class="fas fa-check text-gray-400 ml-1"></i>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-comment text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Nenhuma mensagem ainda. Comece a conversa!</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Message Input -->
        <div class="bg-white border-t border-gray-200 px-6 py-4">
            <form id="messageForm" class="flex items-center space-x-4">
                @csrf
                <div class="flex-1">
                    <input type="text" 
                           name="message" 
                           id="messageInput"
                           placeholder="Digite sua mensagem..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           maxlength="1000"
                           required>
                </div>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50"
                        id="sendButton">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    const sendButton = document.getElementById('sendButton');

    // Auto-scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Scroll to bottom on load
    scrollToBottom();

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable button and input
        sendButton.disabled = true;
        messageInput.disabled = true;

        // Send message via AJAX
        fetch(`{{ route('conversations.send-message', $conversation) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                message: message,
                message_type: 'text'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add message to UI
                addMessageToUI(data.message);
                messageInput.value = '';
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao enviar mensagem. Tente novamente.');
        })
        .finally(() => {
            // Re-enable button and input
            sendButton.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Add message to UI
    function addMessageToUI(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${message.sender_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'}`;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        
        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                <div class="px-4 py-2 rounded-lg ${message.sender_id === {{ auth()->id() }} 
                    ? 'bg-blue-500 text-white' 
                    : 'bg-gray-200 text-gray-900'}">
                    <p class="text-sm">${message.message}</p>
                </div>
                <div class="mt-1 text-xs text-gray-500 ${message.sender_id === {{ auth()->id() }} ? 'text-right' : 'text-left'}">
                    ${timeString}
                    ${message.sender_id === {{ auth()->id() }} ? '<i class="fas fa-check text-gray-400 ml-1"></i>' : ''}
                </div>
            </div>
        `;
        
        messagesContainer.querySelector('.space-y-4').appendChild(messageDiv);
    }

    // Focus input on load
    messageInput.focus();
});

function archiveConversation(conversationId) {
    if (confirm('Tem certeza que deseja arquivar esta conversa?')) {
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
                window.location.href = '{{ route("conversations.index") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endsection
