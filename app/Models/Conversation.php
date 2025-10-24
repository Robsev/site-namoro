<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'last_message_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the first user in the conversation.
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the conversation.
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message in this conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Get the other user in the conversation.
     */
    public function getOtherUser($userId)
    {
        return $this->user1_id === $userId ? $this->user2 : $this->user1;
    }

    /**
     * Check if a user is part of this conversation.
     */
    public function hasUser($userId)
    {
        return $this->user1_id === $userId || $this->user2_id === $userId;
    }

    /**
     * Scope to get conversations for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user1_id', $userId)
              ->orWhere('user2_id', $userId);
        });
    }

    /**
     * Scope to get active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get conversations with recent messages.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('last_message_at', '>=', now()->subDays($days));
    }

    /**
     * Get unread message count for a user.
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all messages as read for a user.
     */
    public function markAsReadForUser($userId)
    {
        $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}