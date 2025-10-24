<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'phone',
        'location',
        'profile_photo',
        'is_verified',
        'is_active',
        'last_seen',
        'subscription_type',
        'subscription_expires_at',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'neighborhood',
        'district',
        'county',
        'road',
        'house_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'last_seen' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's photos.
     */
    public function photos()
    {
        return $this->hasMany(UserPhoto::class);
    }

    /**
     * Get the user's matching preferences.
     */
    public function matchingPreferences()
    {
        return $this->hasOne(MatchingPreference::class);
    }

    /**
     * Get the user's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get matches where this user is user1.
     */
    public function matchesAsUser1()
    {
        return $this->hasMany(UserMatch::class, 'user1_id');
    }

    /**
     * Get matches where this user is user2.
     */
    public function matchesAsUser2()
    {
        return $this->hasMany(UserMatch::class, 'user2_id');
    }

    /**
     * Get all matches for this user.
     */
    public function matches()
    {
        return UserMatch::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id);
    }

    /**
     * Get messages sent by this user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get all messages for this user (sent and received).
     */
    public function messages()
    {
        return Message::where('sender_id', $this->id)
            ->orWhere('receiver_id', $this->id);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Calculate distance from another user in kilometers
     */
    public function distanceFrom($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude || !$latitude || !$longitude) {
            return null;
        }
        
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $latDiff = deg2rad($latitude - $this->latitude);
        $lonDiff = deg2rad($longitude - $this->longitude);
        
        $a = sin($latDiff/2) * sin($latDiff/2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDiff/2) * sin($lonDiff/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Get formatted location string
     */
    public function getFormattedLocationAttribute()
    {
        $parts = array_filter([$this->neighborhood, $this->city, $this->state, $this->country]);
        return implode(', ', $parts) ?: $this->location;
    }

    /**
     * Get detailed location string
     */
    public function getDetailedLocationAttribute()
    {
        $parts = array_filter([
            $this->neighborhood,
            $this->district,
            $this->city,
            $this->state,
            $this->country
        ]);
        return implode(', ', $parts) ?: $this->location;
    }

    /**
     * Check if user has geolocation data
     */
    public function hasGeolocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get the user's age.
     */
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Check if user has active premium subscription.
     */
    public function hasActivePremiumSubscription()
    {
        return $this->subscription_type === 'premium' && 
               $this->subscription_expires_at && 
               $this->subscription_expires_at->isFuture();
    }
}
