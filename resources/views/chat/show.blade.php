@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md max-h-[80vh] h-[70vh] sm:h-[75vh] md:h-[80vh] flex flex-col">
    <!-- Chat Header -->
    <div class="bg-orange-500 text-white p-4 rounded-t-lg flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('matching.matches') }}" class="mr-4 text-white hover:text-orange-200">
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
                <p class="text-sm text-orange-200">
                    @if($user->last_seen && $user->last_seen->diffInMinutes(now()) < 30)
                        <i class="fas fa-circle text-green-400 mr-1"></i>Online
                    @else
                        <i class="fas fa-clock mr-1"></i>Visto {{ $user->last_seen ? $user->last_seen->diffForHumans() : 'nunca' }}
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('profile.show', $user->id) }}" class="text-white hover:text-orange-200" title="Ver Perfil">
                <i class="fas fa-user"></i>
            </a>
            <button id="chat-options-button" onclick="toggleChatOptions()" class="text-white hover:text-orange-200" title="Opções">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
    </div>

    <!-- Chat Options Menu -->
    <div id="chat-options-menu" class="hidden absolute top-16 right-4 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 min-w-48">
        <button onclick="searchMessages()" class="w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="fas fa-search mr-3 text-gray-500"></i>
            Buscar nas mensagens
        </button>
        <button onclick="clearChat()" class="w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="fas fa-trash mr-3 text-red-500"></i>
            Limpar histórico
        </button>
        <button onclick="archiveChat()" class="w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="fas fa-archive mr-3 text-blue-500"></i>
            Arquivar conversa
        </button>
        <hr class="my-2">
        <button onclick="reportUser()" class="w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="fas fa-flag mr-3 text-yellow-500"></i>
            Denunciar usuário
        </button>
        <button onclick="blockUser()" class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-50 flex items-center">
            <i class="fas fa-ban mr-3 text-red-500"></i>
            Bloquear usuário
        </button>
    </div>

    <!-- Messages Container -->
    <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        @foreach($messages as $message)
            <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
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
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-orange-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
                                {{ $message->message }}
                            </div>
                        @elseif($message->message_type === 'image')
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-orange-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
                                <img src="{{ Storage::url($message->attachment_path) }}" alt="Imagem" class="max-w-full h-auto rounded cursor-pointer" onclick="openImageModal('{{ Storage::url($message->attachment_path) }}')">
                                @if($message->message)
                                    <p class="mt-2">{{ $message->message }}</p>
                                @endif
                            </div>
                        @elseif($message->message_type === 'audio')
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-orange-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-microphone text-lg"></i>
                                    <audio controls class="flex-1" style="max-width: 250px;" controlsList="nodownload">
                                        <source src="{{ Storage::url($message->attachment_path) }}" type="audio/webm">
                                        <source src="{{ Storage::url($message->attachment_path) }}" type="audio/mp3">
                                        <source src="{{ Storage::url($message->attachment_path) }}" type="audio/wav">
                                        Seu navegador não suporta áudio.
                                    </audio>
                                </div>
                                @if($message->message)
                                    <p class="mt-2 text-xs">{{ $message->message }}</p>
                                @endif
                            </div>
                        @else
                            <div class="px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-orange-500 text-white' : 'bg-white text-gray-800' }} shadow-sm">
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
    <div class="bg-white border-t border-gray-200 p-2 md:p-4">
        <form id="message-form" class="flex items-end space-x-2" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="message_type" value="text">
            
            <!-- Attachments Buttons (Images and Audio) -->
            <div class="flex flex-col space-y-2">
                <!-- Image Upload Button -->
                <div class="relative">
                    <input type="file" 
                           id="image-input" 
                           name="image" 
                           accept="image/*" 
                           class="hidden">
                    <button type="button" 
                            id="image-button"
                            class="text-gray-500 hover:text-orange-500 text-lg"
                            title="Enviar imagem">
                        <i class="fas fa-image"></i>
                    </button>
                </div>
                
                <!-- Audio Recording Button -->
                <button type="button" 
                        id="audio-button"
                        class="text-gray-500 hover:text-pink-500 text-lg"
                        title="Gravar áudio">
                    <i class="fas fa-microphone" id="mic-icon"></i>
                </button>
            </div>
            
            <!-- Message Input -->
            <input type="text" 
                   name="message" 
                   id="message-input" 
                   placeholder="Digite sua mensagem..." 
                   class="flex-1 border border-gray-300 rounded-lg px-2 md:px-4 py-2 text-sm md:text-base focus:outline-none focus:border-orange-500"
                   maxlength="1000">
            
            <!-- Send Button -->
            <button type="submit" 
                    class="bg-orange-500 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200 disabled:opacity-50 text-sm md:text-base"
                    id="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
        
        <!-- Audio Recording Indicator -->
        <div id="audio-recording" class="hidden mt-2 bg-orange-100 border border-orange-300 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-orange-700" id="recording-time">0:00</span>
                </div>
                <button type="button" 
                        id="stop-recording"
                        class="text-red-600 hover:text-red-800 text-sm font-medium"
                        onclick="stopAudioRecording()">
                    <i class="fas fa-stop mr-1"></i>Parar
                </button>
            </div>
        </div>
        
        <!-- Audio Preview -->
        <div id="audio-preview" class="hidden mt-2 bg-orange-50 border border-orange-200 rounded-lg p-3">
            <p class="text-sm text-orange-700 mb-2"><i class="fas fa-microphone mr-2"></i>Áudio gravado</p>
            <audio id="recorded-audio" controls class="w-full mb-2"></audio>
            <div class="flex gap-2">
                <button type="button" 
                        id="send-audio-button"
                        onclick="sendAudioMessage()" 
                        class="flex-1 bg-orange-500 text-white px-3 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                    <i class="fas fa-paper-plane mr-1"></i>Enviar Áudio
                </button>
                <button type="button" 
                        onclick="discardAudio()" 
                        class="text-red-600 hover:text-red-800 px-3 py-2 text-sm font-medium">
                    <i class="fas fa-times mr-1"></i>Descartar
                </button>
            </div>
        </div>
        
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

// Audio recording variables
let mediaRecorder = null;
let audioChunks = [];
let recordedBlob = null;
let recordingTimer = null;
let recordingSeconds = 0;
let isSendingAudio = false; // Flag to prevent double-sending

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

// Audio button click handler
document.getElementById('audio-button').addEventListener('click', function() {
    console.log('Audio button clicked');
    toggleAudioRecording();
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
    // Check if message already exists to prevent duplicates
    if (document.querySelector(`[data-message-id="${message.id}"]`)) {
        console.log('Message already exists, skipping:', message.id);
        return;
    }
    
    const container = document.getElementById('messages-container');
    const messageDiv = document.createElement('div');
    
    const isOwnMessage = message.sender_id === {{ Auth::id() }};
    const messageClass = isOwnMessage ? 'justify-end' : 'justify-start';
    
    let messageContent = '';
    
    if (message.message_type === 'image' && message.attachment_path) {
        // Image message
        messageContent = `
            <div class="px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-orange-500 text-white' : 'bg-white text-gray-800'} shadow-sm">
                <img src="/storage/${message.attachment_path}" 
                     alt="Imagem enviada" 
                     class="max-w-full h-auto rounded cursor-pointer"
                     onclick="openImageModal('/storage/${message.attachment_path}')">
                ${message.message ? `<p class="mt-2">${message.message}</p>` : ''}
            </div>
        `;
    } else if (message.message_type === 'audio' && message.attachment_path) {
        // Audio message - download disabled for privacy
        messageContent = `
            <div class="px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-orange-500 text-white' : 'bg-white text-gray-800'} shadow-sm">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-microphone text-lg"></i>
                    <audio controls class="flex-1" style="max-width: 250px;" controlsList="nodownload">
                        <source src="/storage/${message.attachment_path}" type="audio/webm">
                        <source src="/storage/${message.attachment_path}" type="audio/mp3">
                        <source src="/storage/${message.attachment_path}" type="audio/wav">
                        Seu navegador não suporta áudio.
                    </audio>
                </div>
                ${message.message ? `<p class="mt-2 text-xs">${message.message}</p>` : ''}
            </div>
        `;
    } else {
        // Text message
        messageContent = `
            <div class="px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-orange-500 text-white' : 'bg-white text-gray-800'} shadow-sm">
                ${message.message}
            </div>
        `;
    }
    
    messageDiv.className = `flex ${messageClass}`;
    messageDiv.setAttribute('data-message-id', message.id);
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
    
    console.log('Message added to chat:', message.id);
}

// Check for new messages periodically
setInterval(function() {
    fetch(`/chat/messages/{{ $user->id }}?last_message_id=${lastMessageId}`)
        .then(response => {
            if (!response.ok) {
                // Server is down or returning error
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    addMessageToChat(message);
                    lastMessageId = message.id;
                });
            }
        })
        .catch(error => {
            // Silently ignore errors to prevent console spam
            // Only log if it's not a server unavailable error
            if (!error.message.includes('503') && !error.message.includes('Unexpected token')) {
                console.error('Error checking messages:', error);
            }
        });
}, 3000); // Check every 3 seconds

// Toggle attachment options
function toggleAttachmentOptions() {
    const options = document.getElementById('attachment-options');
    options.classList.toggle('hidden');
}

// Toggle chat options
function toggleChatOptions() {
    const optionsMenu = document.getElementById('chat-options-menu');
    if (optionsMenu) {
        optionsMenu.classList.toggle('hidden');
    }
}

// Close options menu when clicking outside
document.addEventListener('click', function(event) {
    const optionsMenu = document.getElementById('chat-options-menu');
    const optionsButton = document.getElementById('chat-options-button');
    
    if (optionsMenu && !optionsMenu.contains(event.target) && !optionsButton.contains(event.target)) {
        optionsMenu.classList.add('hidden');
    }
});

// Chat options functions
function clearChat() {
    if (confirm('Tem certeza que deseja limpar o histórico desta conversa? Esta ação não pode ser desfeita.')) {
        // Implement clear chat functionality
        fetch(`/chat/clear/{{ $user->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear messages from UI
                const messagesContainer = document.getElementById('messages');
                if (messagesContainer) {
                    messagesContainer.innerHTML = '';
                }
                showNotification('Histórico da conversa limpo com sucesso!', 'success');
            } else {
                showNotification('Erro ao limpar o histórico da conversa.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao limpar o histórico da conversa.', 'error');
        });
    }
}

function blockUser() {
    if (confirm('Tem certeza que deseja bloquear este usuário? Vocês não poderão mais se comunicar.')) {
        // Implement block user functionality
        fetch(`/chat/block/{{ $user->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Usuário bloqueado com sucesso!', 'success');
                // Redirect to conversations list
                setTimeout(() => {
                    window.location.href = '/chat';
                }, 1500);
            } else {
                showNotification('Erro ao bloquear usuário.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao bloquear usuário.', 'error');
        });
    }
}

function reportUser() {
    const reason = prompt('Por favor, descreva o motivo da denúncia:');
    if (reason && reason.trim()) {
        // Implement report user functionality
        fetch(`/chat/report/{{ $user->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Denúncia enviada com sucesso! Nossa equipe analisará o caso.', 'success');
            } else {
                showNotification('Erro ao enviar denúncia.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erro ao enviar denúncia.', 'error');
        });
    }
}

function archiveChat() {
    // Implement archive chat functionality
    fetch(`/chat/archive/{{ $user->id }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Conversa arquivada com sucesso!', 'success');
            // Redirect to conversations list
            setTimeout(() => {
                window.location.href = '/chat';
            }, 1500);
        } else {
            showNotification('Erro ao arquivar conversa.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erro ao arquivar conversa.', 'error');
    });
}

function searchMessages() {
    const searchTerm = prompt('Digite o termo para buscar nas mensagens:');
    if (searchTerm && searchTerm.trim()) {
        // Implement search functionality
        const messages = document.querySelectorAll('.message');
        let foundCount = 0;
        
        messages.forEach(message => {
            const messageText = message.textContent.toLowerCase();
            const searchLower = searchTerm.toLowerCase();
            
            if (messageText.includes(searchLower)) {
                message.style.backgroundColor = '#fef3c7';
                message.scrollIntoView({ behavior: 'smooth', block: 'center' });
                foundCount++;
            } else {
                message.style.backgroundColor = '';
            }
        });
        
        if (foundCount > 0) {
            showNotification(`${foundCount} mensagem(ns) encontrada(s)!`, 'success');
        } else {
            showNotification('Nenhuma mensagem encontrada.', 'info');
        }
    }
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Toggle audio recording
async function toggleAudioRecording() {
    console.log('toggleAudioRecording called', { mediaRecorder, hasGetUserMedia: !!navigator.mediaDevices });
    
    if (!mediaRecorder || (mediaRecorder && mediaRecorder.state === 'inactive')) {
        try {
            console.log('Requesting microphone access...');
            // Request microphone access
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            console.log('Microphone access granted');
            
            // Create MediaRecorder
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            // Handle data available
            mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    audioChunks.push(event.data);
                }
            };
            
            // Handle recording stop
            mediaRecorder.onstop = () => {
                console.log('Recording stopped, audioChunks:', audioChunks.length);
                recordedBlob = new Blob(audioChunks, { type: 'audio/webm' });
                console.log('recordedBlob created:', recordedBlob.size, 'bytes');
                const audioUrl = URL.createObjectURL(recordedBlob);
                
                // Show audio preview
                const audioPreview = document.getElementById('audio-preview');
                const audioElement = document.getElementById('recorded-audio');
                const sendAudioButton = document.getElementById('send-audio-button');
                
                if (audioElement) audioElement.src = audioUrl;
                if (audioPreview) audioPreview.classList.remove('hidden');
                if (sendAudioButton) sendAudioButton.disabled = false;
                
                console.log('Audio preview shown, blob size:', recordedBlob.size);
                
                // Stop all tracks
                stream.getTracks().forEach(track => track.stop());
            };
            
            // Start recording
            mediaRecorder.start();
            
            // Show recording indicator
            document.getElementById('audio-recording').classList.remove('hidden');
            document.getElementById('mic-icon').classList.remove('fa-microphone');
            document.getElementById('mic-icon').classList.add('fa-microphone-slash', 'text-red-500');
            document.getElementById('audio-button').disabled = true;
            
            // Start timer
            recordingSeconds = 0;
            recordingTimer = setInterval(() => {
                recordingSeconds++;
                const minutes = Math.floor(recordingSeconds / 60);
                const seconds = recordingSeconds % 60;
                document.getElementById('recording-time').textContent = 
                    `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }, 1000);
            
        } catch (error) {
            console.error('Error accessing microphone:', error);
            alert('Não foi possível acessar o microfone. Verifique as permissões do navegador.');
        }
    } else {
        stopAudioRecording();
    }
}

// Stop audio recording
function stopAudioRecording() {
    if (mediaRecorder && mediaRecorder.state && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        
        // Hide recording indicator and show preview
        document.getElementById('audio-recording').classList.add('hidden');
        document.getElementById('mic-icon').classList.add('fa-microphone');
        document.getElementById('mic-icon').classList.remove('fa-microphone-slash', 'text-red-500');
        document.getElementById('audio-button').disabled = false;
        
        // Stop timer
        if (recordingTimer) {
            clearInterval(recordingTimer);
            recordingTimer = null;
        }
    }
}

// Send audio message
async function sendAudioMessage() {
    console.log('sendAudioMessage called', { recordedBlob, hasBlob: !!recordedBlob, blobSize: recordedBlob?.size, isSendingAudio });
    
    // Prevent double-sending
    if (isSendingAudio) {
        console.log('Already sending audio, ignoring duplicate click');
        return;
    }
    
    if (!recordedBlob) {
        alert('Nenhum áudio gravado.');
        return;
    }
    
    // Set flag to prevent duplicate submissions
    isSendingAudio = true;
    
    // Disable the send audio button
    const sendAudioButton = document.getElementById('send-audio-button');
    if (sendAudioButton) {
        sendAudioButton.disabled = true;
        sendAudioButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Enviando...';
    }
    
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('audio', recordedBlob, 'voice-message.webm');
    formData.append('message_type', 'audio');
    
    console.log('Sending audio to:', `/chat/send/{{ $user->id }}`);
    console.log('FormData entries:', Array.from(formData.entries()));
    
    try {
        const response = await fetch(`/chat/send/{{ $user->id }}`, {
            method: 'POST',
            body: formData
        });
        
        console.log('Response received:', response.status, response.statusText);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            // Add message to chat interface
            addMessageToChat(data.message);
            
            // Hide audio preview and reset
            document.getElementById('audio-preview').classList.add('hidden');
            document.getElementById('audio-recording').classList.add('hidden');
            recordedBlob = null;
            audioChunks = [];
            lastMessageId = data.message.id;
            
            // Scroll to bottom
            scrollToBottom();
            
            showNotification('Áudio enviado com sucesso!', 'success');
        } else {
            showNotification('Erro ao enviar áudio.', 'error');
        }
    } catch (error) {
        console.error('Error sending audio:', error);
        showNotification('Erro ao enviar áudio.', 'error');
    } finally {
        // Re-enable the send audio button
        isSendingAudio = false;
        if (sendAudioButton) {
            sendAudioButton.disabled = false;
            sendAudioButton.innerHTML = '<i class="fas fa-paper-plane mr-1"></i>Enviar Áudio';
        }
    }
}

// Discard audio
function discardAudio() {
    recordedBlob = null;
    audioChunks = [];
    document.getElementById('audio-preview').classList.add('hidden');
    document.getElementById('recorded-audio').src = '';
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

// Update last_seen periodically while in chat
setInterval(function() {
    fetch('/api/update-last-seen', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        }
    }).catch(error => {
        // Silently ignore 503 errors to prevent console spam
        if (!error.message || !error.message.includes('503')) {
            console.log('Last seen update failed:', error);
        }
    });
}, 30000); // Update every 30 seconds

// Scroll to bottom on page load
window.addEventListener('load', scrollToBottom);

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 bg-${type === 'success' ? 'green' : type === 'error' ? 'red' : 'blue'}-500 text-white px-6 py-3 rounded-lg shadow-lg z-50`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transition = 'opacity 0.5s';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}
</script>
@endsection
