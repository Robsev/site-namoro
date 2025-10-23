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
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_approved' => 'boolean',
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
     * Scope to get only approved photos.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get primary photo.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
