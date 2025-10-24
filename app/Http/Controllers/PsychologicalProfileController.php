<?php

namespace App\Http\Controllers;

use App\Models\PsychologicalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PsychologicalProfileController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas
    }

    /**
     * Mostrar questionário de perfil psicológico
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->psychologicalProfile;
        
        return view('psychological-profile.index', compact('profile'));
    }

    /**
     * Salvar respostas do questionário
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'required|integer|min:1|max:5',
        ]);

        // Calcular scores baseado nas respostas
        $scores = $this->calculateScores($request->responses);
        
        // Criar ou atualizar perfil psicológico
        $profile = $user->psychologicalProfile()->updateOrCreate(
            ['user_id' => $user->id],
            array_merge($scores, [
                'questionnaire_responses' => $request->responses,
                'completed_at' => now(),
                'is_public' => $request->boolean('is_public', false),
            ])
        );

        return redirect()->back()->with('success', 'Perfil psicológico atualizado com sucesso!');
    }

    /**
     * Calcular scores do Big Five baseado nas respostas
     */
    private function calculateScores($responses)
    {
        // Mapear respostas para scores (1-5)
        $scores = [
            'openness' => 3.0,
            'conscientiousness' => 3.0,
            'extraversion' => 3.0,
            'agreeableness' => 3.0,
            'neuroticism' => 3.0,
            'attachment_style' => 3.0,
            'communication_style' => 3.0,
            'conflict_resolution' => 3.0,
            'family_importance' => 3.0,
            'career_importance' => 3.0,
            'adventure_seeking' => 3.0,
            'stability_preference' => 3.0,
            'social_connection' => 3.0,
            'social_activities' => 3.0,
            'introspective_activities' => 3.0,
            'active_lifestyle' => 3.0,
            'creative_activities' => 3.0,
        ];

        // Mapear perguntas para dimensões (exemplo simplificado)
        $questionMapping = [
            // Big Five - Openness (perguntas 1-3)
            1 => 'openness', 2 => 'openness', 3 => 'openness',
            // Big Five - Conscientiousness (perguntas 4-6)
            4 => 'conscientiousness', 5 => 'conscientiousness', 6 => 'conscientiousness',
            // Big Five - Extraversion (perguntas 7-9)
            7 => 'extraversion', 8 => 'extraversion', 9 => 'extraversion',
            // Big Five - Agreeableness (perguntas 10-12)
            10 => 'agreeableness', 11 => 'agreeableness', 12 => 'agreeableness',
            // Big Five - Neuroticism (perguntas 13-15)
            13 => 'neuroticism', 14 => 'neuroticism', 15 => 'neuroticism',
            // Estilo de relacionamento (perguntas 16-18)
            16 => 'attachment_style', 17 => 'communication_style', 18 => 'conflict_resolution',
            // Valores pessoais (perguntas 19-23)
            19 => 'family_importance', 20 => 'career_importance', 21 => 'adventure_seeking',
            22 => 'stability_preference', 23 => 'social_connection',
            // Preferências de lazer (perguntas 24-27)
            24 => 'social_activities', 25 => 'introspective_activities',
            26 => 'active_lifestyle', 27 => 'creative_activities',
        ];

        // Calcular médias para cada dimensão
        $dimensionScores = [];
        foreach ($questionMapping as $question => $dimension) {
            if (isset($responses[$question])) {
                $dimensionScores[$dimension][] = $responses[$question];
            }
        }

        // Calcular média para cada dimensão
        foreach ($dimensionScores as $dimension => $values) {
            $scores[$dimension] = round(array_sum($values) / count($values), 2);
        }

        return $scores;
    }

    /**
     * Mostrar perfil psicológico do usuário
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->psychologicalProfile;
        
        if (!$profile) {
            return redirect()->route('psychological-profile.index')
                ->with('info', 'Complete o questionário para ver seu perfil psicológico.');
        }

        return view('psychological-profile.show', compact('profile'));
    }

    /**
     * API: Obter perfil psicológico
     */
    public function getProfile()
    {
        $user = Auth::user();
        $profile = $user->psychologicalProfile;
        
        if (!$profile) {
            return response()->json(['error' => 'Perfil não encontrado'], 404);
        }

        return response()->json([
            'big_five' => $profile->getBigFiveScores(),
            'personality_type' => $profile->getPersonalityType(),
            'completed_at' => $profile->completed_at,
        ]);
    }

    /**
     * Calcular compatibilidade entre perfis psicológicos
     */
    public function calculateCompatibility($userId1, $userId2)
    {
        $profile1 = PsychologicalProfile::where('user_id', $userId1)->first();
        $profile2 = PsychologicalProfile::where('user_id', $userId2)->first();
        
        if (!$profile1 || !$profile2) {
            return 0;
        }

        return $profile1->calculateCompatibilityWith($profile2);
    }
}
