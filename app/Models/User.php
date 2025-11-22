<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            // Set default email preferences
            if (is_null($user->email_notifications_enabled)) {
                $user->email_notifications_enabled = true;
            }
            if (is_null($user->email_new_matches)) {
                $user->email_new_matches = true;
            }
            if (is_null($user->email_new_likes)) {
                $user->email_new_likes = true;
            }
            if (is_null($user->email_new_messages)) {
                $user->email_new_messages = false; // Default false to avoid email spam
            }
            if (is_null($user->email_photo_approvals)) {
                $user->email_photo_approvals = true;
            }
            if (is_null($user->email_marketing)) {
                $user->email_marketing = false;
            }
        });
    }

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
        'is_admin',
        'last_seen',
        'subscription_type',
        'subscription_expires_at',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'neighborhood',
        'email_notifications_enabled',
        'email_new_matches',
        'email_new_likes',
        'email_new_messages',
        'email_photo_approvals',
        'email_marketing',
        'preferred_language',
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
            'is_admin' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'email_notifications_enabled' => 'boolean',
            'email_new_matches' => 'boolean',
            'email_new_likes' => 'boolean',
            'email_new_messages' => 'boolean',
            'email_photo_approvals' => 'boolean',
            'email_marketing' => 'boolean',
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
     * Get the user's interests.
     */
    public function interests()
    {
        return $this->hasMany(UserInterest::class);
    }

    /**
     * Get the user's psychological profile.
     */
    public function psychologicalProfile()
    {
        return $this->hasOne(PsychologicalProfile::class);
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
     * Users blocked by this user
     */
    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'user_id', 'blocked_user_id')
                    ->withTimestamps();
    }

    /**
     * Users who blocked this user
     */
    public function blockedBy()
    {
        return $this->belongsToMany(User::class, 'user_blocks', 'blocked_user_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Reports made by this user
     */
    public function reports()
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    /**
     * Reports made against this user
     */
    public function reportedBy()
    {
        return $this->hasMany(UserReport::class, 'reported_user_id');
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
     * Calculate profile completeness percentage
     */
    public function getProfileCompletenessAttribute()
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        // Basic Information (30 points)
        $basicInfo = [
            'first_name' => 5,
            'last_name' => 5,
            'birth_date' => 5,
            'gender' => 5,
            'profile_photo' => 5
        ];

        foreach ($basicInfo as $field => $points) {
            $totalPoints += $points;
            if (!empty($this->$field)) {
                $earnedPoints += $points;
            }
        }
        
        // Location: verificar location string OU latitude/longitude (geolocalização)
        $totalPoints += 5;
        if (!empty($this->location) || (!is_null($this->latitude) && !is_null($this->longitude))) {
            $earnedPoints += 5;
        }

        // Profile Details (25 points)
        if ($this->profile) {
            $profileDetails = [
                'bio' => 10,
                'relationship_goal' => 5,
                'education_level' => 5,
                'smoking' => 2.5,
                'drinking' => 2.5
            ];

            foreach ($profileDetails as $field => $points) {
                $totalPoints += $points;
                if (!empty($this->profile->$field)) {
                    $earnedPoints += $points;
                }
            }
        } else {
            $totalPoints += 25; // Add points even if profile doesn't exist
        }

        // Photos (20 points)
        $photoCount = $this->photos()->where('moderation_status', 'approved')->count();
        $totalPoints += 20;
        if ($photoCount >= 1) $earnedPoints += 10;
        if ($photoCount >= 2) $earnedPoints += 5;
        if ($photoCount >= 3) $earnedPoints += 5;

        // Interests (15 points)
        $interestCount = $this->interests()->count();
        $totalPoints += 15;
        if ($interestCount >= 3) $earnedPoints += 10;
        if ($interestCount >= 5) $earnedPoints += 5;

        // Psychological Profile (10 points)
        $totalPoints += 10;
        if ($this->psychologicalProfile) {
            $earnedPoints += 10;
        }

        return $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
    }

    /**
     * Get missing profile fields
     */
    public function getMissingProfileFieldsAttribute()
    {
        $missing = [];

        // Basic Information
        if (empty($this->first_name)) $missing[] = __('messages.profile.missing.first_name');
        if (empty($this->last_name)) $missing[] = __('messages.profile.missing.last_name');
        if (empty($this->birth_date)) $missing[] = __('messages.profile.missing.birth_date');
        if (empty($this->gender)) $missing[] = __('messages.profile.missing.gender');
        // Verificar location string OU latitude/longitude (geolocalização)
        if (empty($this->location) && (is_null($this->latitude) || is_null($this->longitude))) {
            $missing[] = __('messages.profile.missing.location');
        }
        
        // Check if user has any photos before asking for profile photo
        $photoCount = $this->photos()->where('moderation_status', 'approved')->count();
        if (empty($this->profile_photo) && $photoCount == 0) {
            $missing[] = __('messages.profile.missing.profile_photo');
        }

        // Profile Details
        if ($this->profile) {
            if (empty($this->profile->bio)) $missing[] = __('messages.profile.missing.bio');
            if (empty($this->profile->relationship_goal)) $missing[] = __('messages.profile.missing.relationship_goal');
            if (empty($this->profile->education_level)) $missing[] = __('messages.profile.missing.education_level');
            if (empty($this->profile->smoking)) $missing[] = __('messages.profile.missing.smoking');
            if (empty($this->profile->drinking)) $missing[] = __('messages.profile.missing.drinking');
        } else {
            $missing[] = __('messages.profile.missing.profile_info');
        }

        // Photos - check both profile_photo and gallery photos (including pending)
        $hasProfilePhoto = !empty($this->profile_photo);
        $pendingPhotoCount = $this->photos()->where('moderation_status', 'pending')->count();
        $totalPhotos = $photoCount + $pendingPhotoCount + ($hasProfilePhoto ? 1 : 0);
        
        if ($totalPhotos == 0) {
            $missing[] = __('messages.profile.missing.photos');
        } elseif ($totalPhotos < 2) {
            $missing[] = __('messages.profile.missing.more_photos');
        } elseif ($pendingPhotoCount > 0) {
            $missing[] = __('messages.profile.missing.photos_pending', ['count' => $pendingPhotoCount]);
        }

        // Interests
        $interestCount = $this->interests()->count();
        if ($interestCount < 3) $missing[] = __('messages.profile.missing.interests');

        // Psychological Profile
        if (!$this->psychologicalProfile) $missing[] = __('messages.profile.missing.psychological_profile');

        return $missing;
    }

    /**
     * Get profile completeness level
     */
    public function getProfileCompletenessLevelAttribute()
    {
        $percentage = $this->profile_completeness;

        if ($percentage >= 90) return 'excellent';
        if ($percentage >= 75) return 'good';
        if ($percentage >= 50) return 'fair';
        if ($percentage >= 25) return 'poor';
        return 'incomplete';
    }

    /**
     * Get profile completeness level label
     */
    public function getProfileCompletenessLabelAttribute()
    {
        $levels = [
            'excellent' => __('messages.profile.completeness_level.excellent'),
            'good' => __('messages.profile.completeness_level.good'),
            'fair' => __('messages.profile.completeness_level.fair'),
            'poor' => __('messages.profile.completeness_level.poor'),
            'incomplete' => __('messages.profile.completeness_level.incomplete')
        ];

        return $levels[$this->profile_completeness_level] ?? __('messages.profile.completeness_level.unknown');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the profile photo URL.
     * Returns the URL directly if it's an external URL (e.g., from Google OAuth),
     * otherwise returns the Storage URL.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo) {
            return null;
        }

        // If it's an external URL (starts with http), return it directly
        if (str_starts_with($this->profile_photo, 'http://') || str_starts_with($this->profile_photo, 'https://')) {
            return $this->profile_photo;
        }

        // Check if file exists in storage
        $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_photo);
        
        if (!$exists) {
            \Log::warning('Profile photo file not found in storage', [
                'user_id' => $this->id,
                'path' => $this->profile_photo,
                'full_path' => \Illuminate\Support\Facades\Storage::disk('public')->path($this->profile_photo)
            ]);
            return null;
        }

        // Generate Storage URL using our custom route
        // This works even without the storage:link command
        $storageUrl = route('storage.serve', ['path' => $this->profile_photo]);

        \Log::debug('Profile photo URL generated', [
            'user_id' => $this->id,
            'path' => $this->profile_photo,
            'url' => $storageUrl
        ]);

        return $storageUrl;
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
    
    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }
}
