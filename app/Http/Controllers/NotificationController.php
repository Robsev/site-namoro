<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all notifications for the current user
     */
    public function index()
    {
        $user = Auth::user();
        
        $notifications = $user->notifications()
            ->recent(50)
            ->get();

        return view('notifications.index', compact('notifications'));
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
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data
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
}