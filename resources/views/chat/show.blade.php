@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md h-screen flex flex-col">
    <!-- Chat Header -->
    <div class="bg-pink-500 text-white p-4 rounded-t-lg flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('matching.matches') }}" class="mr-4 text-white hover:text-pink-200">
                <i class="fas fa-arrow-left"></i>
            </a>
            @if($user->profile_photo)
                <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->full_name }}" class="w-10 h-10 rounded-full mr-3">
            @else
                <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
            @endif
            <div>
                <h3 class="font-semibold">{{ $user->full_name }}</h3>
                <p class="text-sm text-pink-200">
                    @if($user->last_seen && $user->last_seen->diffInMinutes(now()) < 30)
                        <i class="fas fa-circle text-green-400 mr-1"></i>Online
                    @else
                        <i class="fas fa-clock mr-1"></i>Visto {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'nunca' }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('profile.show', $user->id) }}" class="text-white hover:text-pink-200" title="Ver Perfil">
                <i class="fas fa-user"></i>
            </a>
            <button onclick="toggleChatOptions()" class="text-white hover:text-pink-200" title="Opções">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
    </div>

    <!-- Messages Container -->
    <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        @foreach($messages as $message)
            <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-xs lg:max-w-md">
                    @if($message->sender_id !== Auth::id())
                        <div class="flex items-center mb-1">
                            @if($message->sender->profile_photo)
                                <img src="{{ Storage::url($message->sender->profile_photo) }}" alt="{{ $message->sender->full_name }}" class="w-6 h-6 rounded-full mr-2">
                            @else
                                <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-xs text-gray-600"></i>
                                </div>
                            @endif
                            <span class="text-xs text-gray-500">{{ $message->sender->first_name }}</span>
                        </div>
                    @endif
                    
                    <div class="relative">
                        @if($message->message_type === 'text')
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-pink-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
                                {{ $message->message }}
                            </div>
                        @elseif($message->message_type === 'image')
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-pink-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
                                <img src="{{ Storage::url($message->attachment_path) }}" alt="Imagem" class="max-w-full h-auto rounded">
                                @if($message->message)
                                    <p class="mt-2">{{ $message->message }}</p>
                                @endif
                            </div>
                        @else
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-pink-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
                                <div class="flex items-center">
                                    <i class="fas fa-file mr-2"></i>
                                    <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="underline">
                                        {{ basename($message->attachment_path) }}
                                    </a>
                                </div>
                                @if($message->message)
                                    <p class="mt-2">{{ $message->message }}</p>
                                @endif
                            </div>
                        @endif
                        
                        <div class="text-xs text-gray-500 mt-1 {{ $message->sender_id === Auth::id() ? 'text-right' : 'text-left' }}">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->sender_id === Auth::id())
                                @if($message->is_read)
                                    <i class="fas fa-check-double text-blue-500 ml-1"></i>
                                @else
                                    <i class="fas fa-check text-gray-400 ml-1"></i>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Message Input -->
    <div class="bg-white border-t border-gray-200 p-4">
        <form id="message-form" class="flex items-center space-x-2">
            @csrf
            <input type="hidden" name="message_type" value="text">
            
            <!-- Attachment Button -->
            <button type="button" onclick="toggleAttachmentOptions()" class="text-gray-500 hover:text-pink-500">
                <i class="fas fa-paperclip"></i>
            </button>
            
            <!-- Message Input -->
            <input type="text" 
                   name="message" 
                   id="message-input" 
                   placeholder="Digite sua mensagem..." 
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-pink-500"
                   maxlength="1000"
                   required>
            
            <!-- Send Button -->
            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        
        <!-- Attachment Options (Hidden by default) -->
        <div id="attachment-options" class="hidden mt-2 p-2 bg-gray-100 rounded-lg">
            <input type="file" id="file-input" name="attachment" accept="image/*,.pdf,.doc,.docx" class="hidden">
            <button type="button" onclick="document.getElementById('file-input').click()" class="text-sm text-gray-600 hover:text-pink-500">
                <i class="fas fa-image mr-1"></i>Imagem
            </button>
            <button type="button" onclick="document.getElementById('file-input').click()" class="text-sm text-gray-600 hover:text-pink-500 ml-4">
                <i class="fas fa-file mr-1"></i>Arquivo
            </button>
        </div>
    </div>
</div>

<!-- JavaScript for real-time chat -->
<script>
let lastMessageId = {{ $messages->last() ? $messages->last()->id : 0 }};
let isTyping = false;

// Auto-scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
}

// Send message
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    const formData = new FormData(this);
    
    fetch(`/chat/send/{{ $user->id }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessageToChat(data.message);
            messageInput.value = '';
            lastMessageId = data.message.id;
        } else {
            alert(data.error || 'Erro ao enviar mensagem');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao enviar mensagem');
    });
});

// Add message to chat
function addMessageToChat(message) {
    const container = document.getElementById('messages-container');
    const messageDiv = document.createElement('div');
    
    const isOwnMessage = message.sender_id === {{ Auth::id() }};
    const messageClass = isOwnMessage ? 'justify-end' : 'justify-start';
    
    messageDiv.className = `flex ${messageClass}`;
    messageDiv.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            ${!isOwnMessage ? `
                <div class="flex items-center mb-1">
                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                        <i class="fas fa-user text-xs text-gray-600"></i>
                    </div>
                    <span class="text-xs text-gray-500">${message.sender.first_name}</span>
                </div>
            ` : ''}
            
            <div class="relative">
                <div class="px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-pink-500 text-white' : 'bg-white text-gray-800'} shadow-sm">
                    ${message.message}
                </div>
                <div class="text-xs text-gray-500 mt-1 ${isOwnMessage ? 'text-right' : 'text-left'}">
                    ${new Date(message.created_at).toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'})}
                    ${isOwnMessage ? '<i class="fas fa-check text-gray-400 ml-1"></i>' : ''}
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(messageDiv);
    scrollToBottom();
}

// Check for new messages periodically
setInterval(function() {
    fetch(`/chat/messages/{{ $user->id }}?last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    addMessageToChat(message);
                    lastMessageId = message.id;
                });
            }
        })
        .catch(error => console.error('Error checking messages:', error));
}, 3000); // Check every 3 seconds

// Toggle attachment options
function toggleAttachmentOptions() {
    const options = document.getElementById('attachment-options');
    options.classList.toggle('hidden');
}

// Handle file input
document.getElementById('file-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('message', '');
        formData.append('attachment', file);
        formData.append('message_type', file.type.startsWith('image/') ? 'image' : 'file');
        
        fetch(`/chat/send/{{ $user->id }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessageToChat(data.message);
                lastMessageId = data.message.id;
            } else {
                alert(data.error || 'Erro ao enviar arquivo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao enviar arquivo');
        });
    }
});

// Toggle chat options
function toggleChatOptions() {
    // Implement chat options menu
    alert('Opções do chat em desenvolvimento');
}

// Scroll to bottom on page load
window.addEventListener('load', scrollToBottom);
</script>
@endsection
