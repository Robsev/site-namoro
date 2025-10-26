<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterestCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'options',
        'max_selections',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_active' => 'boolean',
            'max_selections' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    // Relacionamentos
    public function userInterests()
    {
        return $this->hasMany(UserInterest::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the translated name of the category
     */
    public function getTranslatedNameAttribute(): string
    {
        return __('messages.interests.category.' . $this->slug);
    }

    /**
     * Get the translated description of the category
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        return __('messages.interests.description.' . $this->slug);
    }

    /**
     * Get translated options
     */
    public function getTranslatedOptionsAttribute(): array
    {
        $translated = [];
        foreach ($this->options as $option) {
            $key = strtolower(str_replace([' ', '/', '-', '&'], '_', $option));
            $translated[] = [
                'value' => $option,
                'label' => __('messages.interests.option.' . $key, [], $option)
            ];
        }
        return $translated;
    }
}
