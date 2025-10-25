@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-bell text-pink-500 mr-2"></i>Notificações
        </h2>
        <div class="flex items-center space-x-2">
            <button onclick="markAllAsRead()" 
                    class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200 text-sm">
                <i class="fas fa-check-double mr-1"></i>Marcar Todas como Lidas
            </button>
            <button onclick="refreshNotifications()" 
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-sm">
                <i class="fas fa-sync-alt mr-1"></i>Atualizar
            </button>
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="space-y-4" id="notifications-list">
            @foreach($notifications as $notification)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 {{ !$notification->is_read ? 'bg-pink-50 border-pink-200' : 'bg-white' }}" 
                     data-notification-id="{{ $notification->id }}">
                    <div class="flex items-start">
                        <!-- Notification Icon -->
                        <div class="flex-shrink-0 mr-4">
                            @php
                                $icon = match($notification->type) {
                                    'new_match', 'match' => 'fas fa-heart',
                                    'new_message', 'message' => 'fas fa-comment',
                                    'new_like', 'like' => 'fas fa-thumbs-up',
                                    'new_super_like', 'super_like' => 'fas fa-star',
                                    'photo_moderation' => 'fas fa-camera',
                                    default => 'fas fa-bell'
                                };
                                $color = match($notification->type) {
                                    'new_match', 'match' => 'text-red-500',
                                    'new_message', 'message' => 'text-blue-500',
                                    'new_like', 'like' => 'text-green-500',
                                    'new_super_like', 'super_like' => 'text-yellow-500',
                                    'photo_moderation' => 'text-purple-500',
                                    default => 'text-gray-500'
                                };
                            @endphp
                            <div class="w-10 h-10 rounded-full {{ $color }} bg-opacity-20 flex items-center justify-center">
                                <i class="{{ $icon }} {{ $color }}"></i>
                            </div>
                        </div>

                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-800 {{ !$notification->is_read ? 'font-bold' : '' }}">
                                    {{ $notification->title }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    @if(!$notification->is_read)
                                        <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                                    @endif
                                    <span class="text-sm text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            
                            <p class="text-gray-700 mt-1">{{ $notification->message }}</p>
                            
                            <!-- Notification Actions -->
                            <div class="flex items-center space-x-2 mt-3">
                                @if(!$notification->is_read)
                                    <button onclick="markAsRead({{ $notification->id }})" 
                                            class="text-pink-500 hover:text-pink-700 text-sm">
                                        <i class="fas fa-check mr-1"></i>Marcar como Lida
                                    </button>
                                @endif
                                
                                @if($notification->type === 'match' && isset($notification->data['match_user_id']))
                                    <a href="{{ route('profile.show', $notification->data['match_user_id']) }}" 
                                       class="text-blue-500 hover:text-blue-700 text-sm">
                                        <i class="fas fa-user mr-1"></i>Ver Perfil
                                    </a>
                                @endif
                                
                                @if($notification->type === 'message' && isset($notification->data['sender_user_id']))
                                    <a href="{{ route('chat.show', $notification->data['sender_user_id']) }}" 
                                       class="text-green-500 hover:text-green-700 text-sm">
                                        <i class="fas fa-comment mr-1"></i>Responder
                                    </a>
                                @endif
                                
                                <button onclick="deleteNotification({{ $notification->id }})" 
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash mr-1"></i>Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-6">
            <button onclick="loadMoreNotifications()" 
                    class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-plus mr-2"></i>Carregar Mais Notificações
            </button>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma notificação</h3>
            <p class="text-gray-500">Você não tem notificações no momento.</p>
        </div>
    @endif
</div>

<!-- JavaScript for notification actions -->
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
            notification.classList.remove('bg-pink-50', 'border-pink-200');
            notification.classList.add('bg-white');
            
            // Remove unread indicator
            const unreadDot = notification.querySelector('.bg-pink-500');
            if (unreadDot) {
                unreadDot.remove();
            }
            
            // Update button
            const markAsReadBtn = notification.querySelector('button[onclick*="markAsRead"]');
            if (markAsReadBtn) {
                markAsReadBtn.remove();
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated notifications
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteNotification(notificationId) {
    if (!confirm('Tem certeza que deseja excluir esta notificação?')) {
        return;
    }

    fetch(`/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
            notification.remove();
        }
    })
    .catch(error => console.error('Error:', error));
}

function refreshNotifications() {
    location.reload();
}

function loadMoreNotifications() {
    // This would implement pagination
    alert('Funcionalidade de paginação em desenvolvimento');
}
</script>
@endsection
