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
            'location' => 5,
            'profile_photo' => 5
        ];

        foreach ($basicInfo as $field => $points) {
            $totalPoints += $points;
            if (!empty($this->$field)) {
                $earnedPoints += $points;
            }
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
        $photoCount = $this->photos()->where('is_approved', true)->count();
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
        if (empty($this->first_name)) $missing[] = 'Nome';
        if (empty($this->last_name)) $missing[] = 'Sobrenome';
        if (empty($this->birth_date)) $missing[] = 'Data de nascimento';
        if (empty($this->gender)) $missing[] = 'Gênero';
        if (empty($this->location)) $missing[] = 'Localização';
        
        // Check if user has any photos before asking for profile photo
        $photoCount = $this->photos()->where('is_approved', true)->count();
        if (empty($this->profile_photo) && $photoCount == 0) {
            $missing[] = 'Foto de perfil';
        }

        // Profile Details
        if ($this->profile) {
            if (empty($this->profile->bio)) $missing[] = 'Biografia';
            if (empty($this->profile->relationship_goal)) $missing[] = 'Objetivo de relacionamento';
            if (empty($this->profile->education_level)) $missing[] = 'Nível de educação';
            if (empty($this->profile->smoking)) $missing[] = 'Hábito de fumar';
            if (empty($this->profile->drinking)) $missing[] = 'Hábito de beber';
        } else {
            $missing[] = 'Informações do perfil';
        }

        // Photos - check both profile_photo and gallery photos (including pending)
        $hasProfilePhoto = !empty($this->profile_photo);
        $pendingPhotoCount = $this->photos()->where('is_approved', false)->count();
        $totalPhotos = $photoCount + $pendingPhotoCount + ($hasProfilePhoto ? 1 : 0);
        
        if ($totalPhotos == 0) {
            $missing[] = 'Fotos do perfil';
        } elseif ($totalPhotos < 2) {
            $missing[] = 'Mais fotos (recomendado: 2-3)';
        } elseif ($pendingPhotoCount > 0) {
            $missing[] = 'Aguardando aprovação de ' . $pendingPhotoCount . ' foto(s)';
        }

        // Interests
        $interestCount = $this->interests()->count();
        if ($interestCount < 3) $missing[] = 'Interesses (mínimo: 3)';

        // Psychological Profile
        if (!$this->psychologicalProfile) $missing[] = 'Perfil psicológico';

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
            'excellent' => 'Excelente',
            'good' => 'Bom',
            'fair' => 'Regular',
            'poor' => 'Incompleto',
            'incomplete' => 'Muito Incompleto'
        ];

        return $levels[$this->profile_completeness_level] ?? 'Desconhecido';
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
