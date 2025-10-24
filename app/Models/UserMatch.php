<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'user1_id',
        'user2_id',
        'compatibility_score',
        'status',
        'matched_at',
        'responded_at',
        'match_reason',
        'is_super_like',
    ];

    protected function casts(): array
    {
        return [
            'compatibility_score' => 'decimal:2',
            'matched_at' => 'datetime',
            'responded_at' => 'datetime',
            'is_super_like' => 'boolean',
        ];
    }

    /**
     * Get the first user in the match.
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the match.
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get the other user in the match.
     */
    public function getOtherUser($userId)
    {
        return $this->user1_id === $userId ? $this->user2 : $this->user1;
    }

    /**
     * Scope to get matches for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
    }

    /**
     * Scope to get accepted matches.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope to get pending matches.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
