<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Create a new notification
     */
    public static function createNotification($userId, $type, $title, $message, $data = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create match notification
     */
    public static function createMatchNotification($userId, $matchUserId, $matchData = [])
    {
        return self::createNotification(
            $userId,
            'match',
            'Novo Match!',
            "Você tem um novo match!",
            array_merge(['match_user_id' => $matchUserId], $matchData)
        );
    }

    /**
     * Create message notification
     */
    public static function createMessageNotification($userId, $senderUserId, $messagePreview = '')
    {
        return self::createNotification(
            $userId,
            'message',
            'Nova Mensagem',
            $messagePreview ?: "Você recebeu uma nova mensagem!",
            ['sender_user_id' => $senderUserId]
        );
    }

    /**
     * Create like notification
     */
    public static function createLikeNotification($userId, $likerUserId)
    {
        return self::createNotification(
            $userId,
            'like',
            'Alguém te curtiu!',
            "Alguém te curtiu! Veja quem foi.",
            ['liker_user_id' => $likerUserId]
        );
    }

    /**
     * Create super like notification
     */
    public static function createSuperLikeNotification($userId, $likerUserId)
    {
        return self::createNotification(
            $userId,
            'super_like',
            'Super Like!',
            "Alguém te deu um Super Like!",
            ['liker_user_id' => $likerUserId]
        );
    }

    /**
     * Create profile view notification
     */
    public static function createProfileViewNotification($userId, $viewerUserId)
    {
        return self::createNotification(
            $userId,
            'profile_view',
            'Seu perfil foi visualizado',
            "Alguém visualizou seu perfil!",
            ['viewer_user_id' => $viewerUserId]
        );
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
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
    public function getColorAttribute()
    {
        return match($this->type) {
            'match' => 'text-red-500',
            'message' => 'text-blue-500',
            'like' => 'text-green-500',
            'super_like' => 'text-yellow-500',
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