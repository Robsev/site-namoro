<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'plan',
        'status',
        'amount',
        'currency',
        'starts_at',
        'ends_at',
        'canceled_at',
        'trial_ends_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->ends_at && 
               $this->ends_at->isFuture();
    }

    /**
     * Check if subscription is in trial.
     */
    public function isOnTrial()
    {
        return $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('ends_at', '>', now());
    }

    /**
     * Scope to get premium subscriptions.
     */
    public function scopePremium($query)
    {
        return $query->whereIn('plan', ['premium_monthly', 'premium_yearly']);
    }
}
