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
        <form id="message-form" class="flex items-center space-x-2" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="message_type" value="text">
            
            <!-- Image Upload Button -->
            <div class="relative">
                <input type="file" 
                       id="image-input" 
                       name="image" 
                       accept="image/*" 
                       class="hidden">
                <button type="button" 
                        id="image-button"
                        class="text-gray-500 hover:text-pink-500"
                        title="Enviar imagem">
                    <i class="fas fa-image"></i>
                </button>
            </div>
            
            <!-- Message Input -->
            <input type="text" 
                   name="message" 
                   id="message-input" 
                   placeholder="Digite sua mensagem..." 
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:border-pink-500"
                   maxlength="1000">
            
            <!-- Send Button -->
            <button type="submit" 
                    class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200 disabled:opacity-50"
                    id="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        
        <!-- Image Preview -->
        <div id="image-preview" class="mt-3 hidden">
            <div class="flex items-center space-x-3 bg-gray-100 p-3 rounded-lg">
                <img id="preview-img" src="" alt="Preview" class="w-16 h-16 object-cover rounded">
                <div class="flex-1">
                    <p class="text-sm text-gray-600" id="preview-text">Imagem selecionada</p>
                    <p class="text-xs text-gray-500" id="preview-size"></p>
                </div>
                <button type="button" id="remove-image" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
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

// Image upload handling
document.getElementById('image-button').addEventListener('click', function() {
    document.getElementById('image-input').click();
});

document.getElementById('image-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecione apenas imagens.');
            return;
        }
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 5MB.');
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-text').textContent = file.name;
            document.getElementById('preview-size').textContent = formatFileSize(file.size);
            document.getElementById('image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

// Remove image
document.getElementById('remove-image').addEventListener('click', function() {
    document.getElementById('image-input').value = '';
    document.getElementById('image-preview').classList.add('hidden');
});

// Send message
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    const hasImage = document.getElementById('image-input').files.length > 0;
    
    if (!message && !hasImage) return;
    
    // Disable form
    const sendButton = document.getElementById('send-button');
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('message', message);
    
    if (hasImage) {
        formData.append('image', document.getElementById('image-input').files[0]);
        formData.append('message_type', 'image');
    } else {
        formData.append('message_type', 'text');
    }
    
    fetch(`/chat/send/{{ $user->id }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addMessageToChat(data.message);
            messageInput.value = '';
            document.getElementById('image-input').value = '';
            document.getElementById('image-preview').classList.add('hidden');
            lastMessageId = data.message.id;
        } else {
            alert(data.error || 'Erro ao enviar mensagem');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao enviar mensagem');
    })
    .finally(() => {
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
    });
});

// Add message to chat
function addMessageToChat(message) {
    const container = document.getElementById('messages-container');
    const messageDiv = document.createElement('div');
    
    const isOwnMessage = message.sender_id === {{ Auth::id() }};
    const messageClass = isOwnMessage ? 'justify-end' : 'justify-start';
    
    let messageContent = '';
    
    if (message.message_type === 'image' && message.attachment_path) {
        // Image message
        messageContent = `
            <div class="px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-pink-500 text-white' : 'bg-white text-gray-800'} shadow-sm">
                <img src="/storage/${message.attachment_path}" 
                     alt="Imagem enviada" 
                     class="max-w-full h-auto rounded cursor-pointer"
                     onclick="openImageModal('/storage/${message.attachment_path}')">
                ${message.message ? `<p class="mt-2">${message.message}</p>` : ''}
            </div>
        `;
    } else {
        // Text message
        messageContent = `
            <div class="px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-pink-500 text-white' : 'bg-white text-gray-800'} shadow-sm">
                ${message.message}
            </div>
        `;
    }
    
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
                ${messageContent}
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

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Open image in modal
function openImageModal(imageSrc) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="max-w-4xl max-h-full p-4">
            <img src="${imageSrc}" alt="Imagem" class="max-w-full max-h-full object-contain rounded">
            <button onclick="this.parentElement.parentElement.remove()" 
                    class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.body.appendChild(modal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Scroll to bottom on page load
window.addEventListener('load', scrollToBottom);
</script>
@endsection
