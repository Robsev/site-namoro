<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Get all notifications for the current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Buscar todas as notificações (não filtrar por lidas/não lidas)
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function($notification) {
                // Use the actual title and message from the notification, not from data
                // Only fallback to data if title/message are empty
                $data = $notification->data ?? [];
                $notification->title = $notification->title ?: ($data['title'] ?? 'Notificação');
                $notification->message = $notification->message ?: ($data['message'] ?? 'Nova notificação');
                $notification->type = $data['type'] ?? $notification->type;
                
                // Garantir que is_read seja calculado corretamente
                // Se for NULL, considerar como false (não lido)
                if ($notification->is_read === null) {
                    $notification->is_read = false;
                }
                
                return $notification;
            });

        $totalNotifications = $user->notifications()->count();
        $unreadCount = $user->notifications()->unread()->count();
        $hasMore = ($offset + $perPage) < $totalNotifications;
        
        // Log para debug (apenas em desenvolvimento)
        if (config('app.debug')) {
            \Log::info('Notifications loaded', [
                'user_id' => $user->id,
                'total' => $totalNotifications,
                'unread_count' => $unreadCount,
                'loaded_count' => $notifications->count(),
                'page' => $page
            ]);
        }

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'hasMore' => $hasMore,
                'currentPage' => $page,
                'total' => $totalNotifications
            ]);
        }

        return view('notifications.index', compact('notifications', 'hasMore', 'page'));
    }

    /**
     * Get unread notifications count (AJAX)
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = $user->unread_notifications_count;

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        $user = Auth::user();
        
        // Ensure user can only mark their own notifications as read
        if ($notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        $user->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        
        // Ensure user can only delete their own notifications
        if ($notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get recent notifications (AJAX)
     */
    public function recent()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->recent(10)
            ->get()
            ->map(function($notification) {
                // Use the actual title and message from the notification, not from data
                // Only fallback to data if title/message are empty
                $data = $notification->data ?? [];
                $title = $notification->title ?: ($data['title'] ?? 'Notificação');
                $message = $notification->message ?: ($data['message'] ?? 'Nova notificação');
                $type = $data['type'] ?? $notification->type;
                
                return [
                    'id' => $notification->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'icon' => $this->getNotificationIcon($type),
                    'color' => $this->getNotificationColor($type),
                    'is_read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $data
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Get notifications by type
     */
    public function byType($type)
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->ofType($type)
            ->recent(20)
            ->get();

        return view('notifications.by-type', compact('notifications', 'type'));
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($type)
    {
        return match($type) {
            'new_match' => 'fas fa-heart',
            'new_message' => 'fas fa-comment',
            'new_like' => 'fas fa-thumbs-up',
            'new_super_like' => 'fas fa-star',
            'photo_moderation' => 'fas fa-camera',
            'match' => 'fas fa-heart',
            'message' => 'fas fa-comment',
            'like' => 'fas fa-thumbs-up',
            'super_like' => 'fas fa-star',
            'profile_view' => 'fas fa-eye',
            'admin_new_user' => 'fas fa-user-plus',
            'admin_new_report' => 'fas fa-exclamation-triangle',
            'subscription' => 'fas fa-crown',
            'profile_complete' => 'fas fa-check-circle',
            'daily_matches' => 'fas fa-calendar-heart',
            'inactive' => 'fas fa-clock',
            'welcome' => 'fas fa-hand-wave',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor($type)
    {
        return match($type) {
            'new_match', 'match' => 'text-red-500',
            'new_message', 'message' => 'text-blue-500',
            'new_like', 'like' => 'text-green-500',
            'new_super_like', 'super_like' => 'text-yellow-500',
            'photo_moderation' => 'text-purple-500',
            'profile_view' => 'text-purple-500',
            'admin_new_user' => 'text-indigo-500',
            'admin_new_report' => 'text-orange-500',
            'subscription' => 'text-yellow-600',
            'profile_complete' => 'text-green-600',
            'daily_matches' => 'text-pink-500',
            'inactive' => 'text-gray-600',
            'welcome' => 'text-blue-600',
            default => 'text-gray-500'
        };
    }
}