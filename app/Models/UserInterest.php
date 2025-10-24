<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'interest_category_id',
        'interest_value',
        'preference_level',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'preference_level' => 'integer',
            'is_public' => 'boolean',
        ];
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interestCategory()
    {
        return $this->belongsTo(InterestCategory::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('interest_category_id', $categoryId);
    }

    public function scopeByPreferenceLevel($query, $level)
    {
        return $query->where('preference_level', $level);
    }
}
