@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-bell text-pink-500 mr-2"></i>{{ __('messages.notifications.title') }}
        </h2>
        <div class="flex items-center space-x-2">
            <button onclick="markAllAsRead()" 
                    class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition duration-200 text-sm">
                <i class="fas fa-check-double mr-1"></i>{{ __('messages.notifications.mark_all_read') }}
            </button>
            <button onclick="refreshNotifications()" 
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 text-sm">
                <i class="fas fa-sync-alt mr-1"></i>{{ __('messages.notifications.refresh') }}
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
                                    'admin_new_user' => 'fas fa-user-plus',
                                    'admin_new_report' => 'fas fa-exclamation-triangle',
                                    'subscription' => 'fas fa-crown',
                                    'profile_complete' => 'fas fa-check-circle',
                                    'daily_matches' => 'fas fa-calendar-heart',
                                    'inactive' => 'fas fa-clock',
                                    'welcome' => 'fas fa-hand-wave',
                                    default => 'fas fa-bell'
                                };
                                $color = match($notification->type) {
                                    'new_match', 'match' => 'text-red-500',
                                    'new_message', 'message' => 'text-blue-500',
                                    'new_like', 'like' => 'text-green-500',
                                    'new_super_like', 'super_like' => 'text-yellow-500',
                                    'photo_moderation' => 'text-purple-500',
                                    'admin_new_user' => 'text-indigo-500',
                                    'admin_new_report' => 'text-orange-500',
                                    'subscription' => 'text-yellow-600',
                                    'profile_complete' => 'text-green-600',
                                    'daily_matches' => 'text-pink-500',
                                    'inactive' => 'text-gray-600',
                                    'welcome' => 'text-blue-600',
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
                                        <i class="fas fa-check mr-1"></i>{{ __('messages.notifications.mark_as_read') }}
                                    </button>
                                @endif
                                
                                @if($notification->type === 'match' && isset($notification->data['match_user_id']))
                                    <a href="{{ route('profile.show', $notification->data['match_user_id']) }}" 
                                       class="text-blue-500 hover:text-blue-700 text-sm">
                                        <i class="fas fa-user mr-1"></i>{{ __('messages.notifications.view_profile') }}
                                    </a>
                                @endif
                                
                                @if($notification->type === 'message' && isset($notification->data['sender_user_id']))
                                    <a href="{{ route('chat.show', $notification->data['sender_user_id']) }}" 
                                       class="text-green-500 hover:text-green-700 text-sm">
                                        <i class="fas fa-comment mr-1"></i>{{ __('messages.notifications.reply') }}
                                    </a>
                                @endif
                                
                                <button onclick="deleteNotification({{ $notification->id }})" 
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash mr-1"></i>{{ __('messages.notifications.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Load More Button -->
        @if($hasMore ?? false)
        <div class="text-center mt-6" id="load-more-container">
            <button onclick="loadMoreNotifications()" 
                    id="load-more-btn"
                    class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition duration-200">
                <i class="fas fa-plus mr-2"></i>{{ __('messages.notifications.load_more') }}
            </button>
        </div>
        @endif
    @else
        <div class="text-center py-12">
            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">{{ __('messages.notifications.no_notifications') }}</h3>
            <p class="text-gray-500">{{ __('messages.notifications.no_notifications_message') }}</p>
        </div>
    @endif
</div>

<!-- JavaScript for notification actions -->
<script>
const notificationTranslations = {
    confirmDelete: '{{ __('messages.notifications.confirm_delete') }}',
    markAsRead: '{{ __('messages.notifications.mark_as_read') }}',
    viewProfile: '{{ __('messages.notifications.view_profile') }}',
    reply: '{{ __('messages.notifications.reply') }}',
    delete: '{{ __('messages.notifications.delete') }}',
    loading: '{{ __('messages.notifications.loading') }}',
    loadMore: '{{ __('messages.notifications.load_more') }}',
    errorLoading: '{{ __('messages.notifications.error_loading') }}'
};

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
    if (!confirm(notificationTranslations.confirmDelete)) {
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

let currentPage = {{ $page ?? 1 }};
let isLoading = false;

function loadMoreNotifications() {
    if (isLoading) return;
    
    isLoading = true;
    const loadMoreBtn = document.getElementById('load-more-btn');
    const originalText = loadMoreBtn.innerHTML;
    
    // Show loading state
    loadMoreBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${notificationTranslations.loading}`;
    loadMoreBtn.disabled = true;
    
    fetch(`/notifications?page=${currentPage + 1}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Append new notifications to the list
            const notificationsList = document.getElementById('notifications-list');
            
            data.notifications.forEach(notification => {
                const notificationHtml = createNotificationHtml(notification);
                notificationsList.insertAdjacentHTML('beforeend', notificationHtml);
            });
            
            currentPage++;
            
            // Hide load more button if no more notifications
            if (!data.hasMore) {
                document.getElementById('load-more-container').style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
        alert(notificationTranslations.errorLoading);
    })
    .finally(() => {
        isLoading = false;
        loadMoreBtn.innerHTML = originalText;
        loadMoreBtn.disabled = false;
    });
}

function createNotificationHtml(notification) {
    const icon = getNotificationIcon(notification.type);
    const color = getNotificationColor(notification.type);
    const isRead = notification.is_read;
    const readClass = isRead ? 'bg-white' : 'bg-pink-50 border-pink-200';
    const boldClass = isRead ? '' : 'font-bold';
    
    return `
        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200 ${readClass}" 
             data-notification-id="${notification.id}">
            <div class="flex items-start">
                <!-- Notification Icon -->
                <div class="flex-shrink-0 mr-4">
                    <div class="w-10 h-10 rounded-full ${color} bg-opacity-20 flex items-center justify-center">
                        <i class="${icon} ${color}"></i>
                    </div>
                </div>

                <!-- Notification Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 ${boldClass}">
                            ${notification.title}
                        </h3>
                        <div class="flex items-center space-x-2">
                            ${!isRead ? '<span class="w-2 h-2 bg-pink-500 rounded-full"></span>' : ''}
                            <span class="text-sm text-gray-500">
                                ${notification.created_at}
                            </span>
                        </div>
                    </div>
                    
                    <p class="text-gray-700 mt-1">${notification.message}</p>
                    
                    <!-- Notification Actions -->
                    <div class="flex items-center space-x-2 mt-3">
                        ${!isRead ? `
                            <button onclick="markAsRead(${notification.id})" 
                                    class="text-pink-500 hover:text-pink-700 text-sm">
                                <i class="fas fa-check mr-1"></i>${notificationTranslations.markAsRead}
                            </button>
                        ` : ''}
                        
                        ${notification.type === 'match' && notification.data && notification.data.match_user_id ? `
                            <a href="/profile/${notification.data.match_user_id}" 
                               class="text-blue-500 hover:text-blue-700 text-sm">
                                <i class="fas fa-user mr-1"></i>${notificationTranslations.viewProfile}
                            </a>
                        ` : ''}
                        
                        ${notification.type === 'message' && notification.data && notification.data.sender_user_id ? `
                            <a href="/chat/${notification.data.sender_user_id}" 
                               class="text-green-500 hover:text-green-700 text-sm">
                                <i class="fas fa-comment mr-1"></i>${notificationTranslations.reply}
                            </a>
                        ` : ''}
                        
                        <button onclick="deleteNotification(${notification.id})" 
                                class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash mr-1"></i>${notificationTranslations.delete}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function getNotificationIcon(type) {
    const icons = {
        'new_match': 'fas fa-heart',
        'new_message': 'fas fa-comment',
        'new_like': 'fas fa-thumbs-up',
        'new_super_like': 'fas fa-star',
        'photo_moderation': 'fas fa-camera',
        'match': 'fas fa-heart',
        'message': 'fas fa-comment',
        'like': 'fas fa-thumbs-up',
        'super_like': 'fas fa-star',
        'profile_view': 'fas fa-eye'
    };
    return icons[type] || 'fas fa-bell';
}

function getNotificationColor(type) {
    const colors = {
        'new_match': 'text-red-500',
        'new_message': 'text-blue-500',
        'new_like': 'text-green-500',
        'new_super_like': 'text-yellow-500',
        'photo_moderation': 'text-purple-500',
        'match': 'text-red-500',
        'message': 'text-blue-500',
        'like': 'text-green-500',
        'super_like': 'text-yellow-500',
        'profile_view': 'text-purple-500'
    };
    return colors[type] || 'text-gray-500';
}
</script>
@endsection
