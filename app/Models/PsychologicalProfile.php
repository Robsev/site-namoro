<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PsychologicalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'openness',
        'conscientiousness',
        'extraversion',
        'agreeableness',
        'neuroticism',
        'attachment_style',
        'communication_style',
        'conflict_resolution',
        'family_importance',
        'career_importance',
        'adventure_seeking',
        'stability_preference',
        'social_connection',
        'social_activities',
        'introspective_activities',
        'active_lifestyle',
        'creative_activities',
        'questionnaire_responses',
        'completed_at',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'openness' => 'decimal:2',
            'conscientiousness' => 'decimal:2',
            'extraversion' => 'decimal:2',
            'agreeableness' => 'decimal:2',
            'neuroticism' => 'decimal:2',
            'attachment_style' => 'decimal:2',
            'communication_style' => 'decimal:2',
            'conflict_resolution' => 'decimal:2',
            'family_importance' => 'decimal:2',
            'career_importance' => 'decimal:2',
            'adventure_seeking' => 'decimal:2',
            'stability_preference' => 'decimal:2',
            'social_connection' => 'decimal:2',
            'social_activities' => 'decimal:2',
            'introspective_activities' => 'decimal:2',
            'active_lifestyle' => 'decimal:2',
            'creative_activities' => 'decimal:2',
            'questionnaire_responses' => 'array',
            'completed_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Métodos de análise
    public function getBigFiveScores()
    {
        return [
            'openness' => $this->openness,
            'conscientiousness' => $this->conscientiousness,
            'extraversion' => $this->extraversion,
            'agreeableness' => $this->agreeableness,
            'neuroticism' => $this->neuroticism,
        ];
    }

    public function getPersonalityType()
    {
        $scores = $this->getBigFiveScores();
        
        // Lógica simples para determinar tipo de personalidade
        $high = array_filter($scores, fn($score) => $score >= 4.0);
        $low = array_filter($scores, fn($score) => $score <= 2.0);
        
        if (count($high) >= 3) {
            return 'extroverted_creative';
        } elseif (count($low) >= 3) {
            return 'introverted_stable';
        } else {
            return 'balanced';
        }
    }

    public function calculateCompatibilityWith(PsychologicalProfile $other)
    {
        $scores1 = $this->getBigFiveScores();
        $scores2 = $other->getBigFiveScores();
        
        $totalDiff = 0;
        foreach ($scores1 as $trait => $score1) {
            $score2 = $scores2[$trait];
            $totalDiff += abs($score1 - $score2);
        }
        
        // Converter diferença em compatibilidade (0-100)
        $maxDiff = 20; // Máxima diferença possível (5-1)*4 traits
        $compatibility = max(0, 100 - ($totalDiff / $maxDiff) * 100);
        
        return round($compatibility, 2);
    }
}
