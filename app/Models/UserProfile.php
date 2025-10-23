<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'interests',
        'hobbies',
        'personality_traits',
        'relationship_goal',
        'education_level',
        'occupation',
        'smoking',
        'drinking',
        'exercise_frequency',
        'looking_for',
        'age_min',
        'age_max',
        'max_distance',
        'show_distance',
        'show_age',
        'show_online_status',
    ];

    protected function casts(): array
    {
        return [
            'interests' => 'array',
            'hobbies' => 'array',
            'personality_traits' => 'array',
            'show_distance' => 'boolean',
            'show_age' => 'boolean',
            'show_online_status' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
