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
        
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->map(function($notification) {
                // Extract title and message from data field for Laravel notifications
                $data = $notification->data ?? [];
                $notification->title = $data['title'] ?? 'Notificação';
                $notification->message = $data['message'] ?? 'Nova notificação';
                $notification->type = $data['type'] ?? $notification->type;
                return $notification;
            });

        $totalNotifications = $user->notifications()->count();
        $hasMore = ($offset + $perPage) < $totalNotifications;

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
                // Extract title and message from data field for Laravel notifications
                $data = $notification->data ?? [];
                $title = $data['title'] ?? 'Notificação';
                $message = $data['message'] ?? 'Nova notificação';
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
            default => 'text-gray-500'
        };
    }
}