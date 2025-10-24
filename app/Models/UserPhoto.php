<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo_path',
        'photo_url',
        'is_primary',
        'sort_order',
        'is_approved',
        'rejection_reason',
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'moderated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the photo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the moderator who reviewed this photo.
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Scope to get only approved photos.
     */
    public function scopeApproved($query)
    {
        return $query->where('moderation_status', 'approved');
    }

    /**
     * Scope to get pending photos.
     */
    public function scopePending($query)
    {
        return $query->where('moderation_status', 'pending');
    }

    /**
     * Scope to get rejected photos.
     */
    public function scopeRejected($query)
    {
        return $query->where('moderation_status', 'rejected');
    }

    /**
     * Scope to get primary photo.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
