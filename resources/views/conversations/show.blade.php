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
                                @if($message->message_type === 'image' && $message->attachment_path)
                                    <!-- Image Message -->
                                    <img src="{{ Storage::url($message->attachment_path) }}" 
                                         alt="Imagem enviada" 
                                         class="max-w-full h-auto rounded cursor-pointer"
                                         onclick="openImageModal('{{ Storage::url($message->attachment_path) }}')">
                                    @if($message->message)
                                        <p class="text-sm mt-2">{{ $message->message }}</p>
                                    @endif
                                @else
                                    <!-- Text Message -->
                                    <p class="text-sm">{{ $message->message }}</p>
                                @endif
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
            <form id="messageForm" class="flex items-center space-x-4" enctype="multipart/form-data">
                @csrf
                <div class="flex-1">
                    <input type="text" 
                           name="message" 
                           id="messageInput"
                           placeholder="Digite sua mensagem..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           maxlength="1000">
                </div>
                
                <!-- Image Upload Button -->
                <div class="relative">
                    <input type="file" 
                           id="imageInput" 
                           name="image" 
                           accept="image/*" 
                           class="hidden">
                    <button type="button" 
                            id="imageButton"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200"
                            title="Enviar imagem">
                        <i class="fas fa-image"></i>
                    </button>
                </div>
                
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50"
                        id="sendButton">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar
                </button>
            </form>
            
            <!-- Image Preview -->
            <div id="imagePreview" class="mt-3 hidden">
                <div class="flex items-center space-x-3 bg-gray-100 p-3 rounded-lg">
                    <img id="previewImg" src="" alt="Preview" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600" id="previewText">Imagem selecionada</p>
                        <p class="text-xs text-gray-500" id="previewSize"></p>
                    </div>
                    <button type="button" id="removeImage" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Chat JavaScript carregado!');
    
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    const sendButton = document.getElementById('sendButton');
    const imageInput = document.getElementById('imageInput');
    const imageButton = document.getElementById('imageButton');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const previewText = document.getElementById('previewText');
    const previewSize = document.getElementById('previewSize');
    const removeImage = document.getElementById('removeImage');

    // Auto-scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Scroll to bottom on load
    scrollToBottom();

    // Image upload handling
    imageButton.addEventListener('click', function() {
        imageInput.click();
    });

    imageInput.addEventListener('change', function(e) {
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
                previewImg.src = e.target.result;
                previewText.textContent = file.name;
                previewSize.textContent = formatFileSize(file.size);
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove image
    removeImage.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.classList.add('hidden');
    });

    // Handle form submission - SIMPLIFIED APPROACH
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        const hasImage = imageInput.files.length > 0;
        
        if (!message && !hasImage) {
            return;
        }

        // Disable form
        sendButton.disabled = true;
        messageInput.disabled = true;
        imageButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';

        // Create FormData for traditional form submission
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('message', message);
        
        if (hasImage) {
            formData.append('image', imageInput.files[0]);
            formData.append('message_type', 'image');
        } else {
            formData.append('message_type', 'text');
        }

        // Send message via fetch with FormData
        fetch(`{{ route('conversations.send-message', $conversation) }}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => {
            console.log('📡 Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📨 Response data:', data);
            
            if (data.success) {
                // Add message to UI immediately
                addMessageToUI(data.message);
                messageInput.value = '';
                imageInput.value = '';
                imagePreview.classList.add('hidden');
                scrollToBottom();
            } else {
                alert('Erro: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('❌ Erro:', error);
            alert('Erro ao enviar mensagem. Tente novamente.');
        })
        .finally(() => {
            // Re-enable form
            sendButton.disabled = false;
            messageInput.disabled = false;
            imageButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Enviar';
            messageInput.focus();
        });
    });

    // Add message to UI
    function addMessageToUI(message) {
        const currentUserId = {{ auth()->id() }};
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${message.sender_id === currentUserId ? 'justify-end' : 'justify-start'}`;
        
        const now = new Date();
        const timeString = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        
        let messageContent = '';
        
        if (message.message_type === 'image' && message.attachment_path) {
            // Image message
            messageContent = `
                <div class="px-4 py-2 rounded-lg ${message.sender_id === currentUserId 
                    ? 'bg-blue-500 text-white' 
                    : 'bg-gray-200 text-gray-900'}">
                    <img src="/storage/${message.attachment_path}" 
                         alt="Imagem enviada" 
                         class="max-w-full h-auto rounded cursor-pointer"
                         onclick="openImageModal('/storage/${message.attachment_path}')">
                    ${message.message ? `<p class="text-sm mt-2">${message.message}</p>` : ''}
                </div>
            `;
        } else {
            // Text message
            messageContent = `
                <div class="px-4 py-2 rounded-lg ${message.sender_id === currentUserId 
                    ? 'bg-blue-500 text-white' 
                    : 'bg-gray-200 text-gray-900'}">
                    <p class="text-sm">${message.message}</p>
                </div>
            `;
        }
        
        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                ${messageContent}
                <div class="mt-1 text-xs text-gray-500 ${message.sender_id === currentUserId ? 'text-right' : 'text-left'}">
                    ${timeString}
                    ${message.sender_id === currentUserId ? '<i class="fas fa-check text-gray-400 ml-1"></i>' : ''}
                </div>
            </div>
        `;
        
        messagesContainer.querySelector('.space-y-4').appendChild(messageDiv);
        console.log('✅ Mensagem adicionada à UI:', message);
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

    // Focus input on load
    messageInput.focus();
    
    console.log('✅ Chat inicializado com sucesso!');
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
