<?php

namespace App\Http\Controllers;

use App\Models\MatchingPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferencesController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Show matching preferences form.
     */
    public function edit()
    {
        $user = Auth::user();
        $preferences = $user->matchingPreferences;

        return view('preferences.edit', compact('preferences'));
    }

    /**
     * Update matching preferences.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'preferred_genders' => ['nullable', 'array'],
            'preferred_genders.*' => ['in:male,female,other,prefer_not_to_say'],
            'min_age' => ['required', 'integer', 'min:18', 'max:100'],
            'max_age' => ['required', 'integer', 'min:18', 'max:100', 'gte:min_age'],
            'enable_geographic_matching' => ['boolean'],
            'max_distance' => ['required_if:enable_geographic_matching,true', 'integer', 'min:1', 'max:1000'],
            'preferred_interests' => ['nullable', 'array'],
            'preferred_interests.*' => ['string', 'max:100'],
            'preferred_personality_traits' => ['nullable', 'array'],
            'preferred_personality_traits.*' => ['string', 'max:100'],
            'preferred_education_levels' => ['nullable', 'array'],
            'preferred_education_levels.*' => ['in:high_school,bachelor,master,phd,other'],
            'preferred_relationship_goals' => ['nullable', 'array'],
            'preferred_relationship_goals.*' => ['in:friendship,romance,casual,serious,marriage'],
            'smoking_ok' => ['boolean'],
            'drinking_ok' => ['boolean'],
            'online_only' => ['boolean'],
            'verified_only' => ['boolean'],
            'photos_only' => ['boolean'],
            'complete_profiles_only' => ['boolean'],
        ]);

        $preferences = $user->matchingPreferences ?? new MatchingPreference(['user_id' => $user->id]);

        // Prepare update data
        $updateData = [
            'preferred_genders' => $request->preferred_genders ?? [],
            'min_age' => $request->min_age,
            'max_age' => $request->max_age,
            'enable_geographic_matching' => $request->boolean('enable_geographic_matching'),
            'preferred_interests' => $request->preferred_interests ?? [],
            'preferred_personality_traits' => $request->preferred_personality_traits ?? [],
            'preferred_education_levels' => $request->preferred_education_levels ?? [],
            'preferred_relationship_goals' => $request->preferred_relationship_goals ?? [],
            'smoking_ok' => $request->boolean('smoking_ok'),
            'drinking_ok' => $request->boolean('drinking_ok'),
            'online_only' => $request->boolean('online_only'),
            'verified_only' => $request->boolean('verified_only'),
            'photos_only' => $request->boolean('photos_only'),
            'complete_profiles_only' => $request->boolean('complete_profiles_only'),
        ];

        // Only update max_distance if geographic matching is enabled
        if ($request->boolean('enable_geographic_matching')) {
            $updateData['max_distance'] = $request->max_distance;
        }

        $preferences->update($updateData);

        return redirect()->route('preferences.edit')->with('success', 'Preferências de matching atualizadas com sucesso!');
    }
}
