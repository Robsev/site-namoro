<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchingPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preferred_genders',
        'min_age',
        'max_age',
        'max_distance',
        'enable_geographic_matching',
        'preferred_interests',
        'preferred_personality_traits',
        'preferred_education_levels',
        'preferred_relationship_goals',
        'smoking_ok',
        'drinking_ok',
        'online_only',
        'verified_only',
    ];

    protected function casts(): array
    {
        return [
            'preferred_genders' => 'array',
            'preferred_interests' => 'array',
            'preferred_personality_traits' => 'array',
            'preferred_education_levels' => 'array',
            'preferred_relationship_goals' => 'array',
            'enable_geographic_matching' => 'boolean',
            'smoking_ok' => 'boolean',
            'drinking_ok' => 'boolean',
            'online_only' => 'boolean',
            'verified_only' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the preferences.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
