<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas, não no controller
    }

    /**
     * Show the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['profile', 'photos' => function($query) {
            $query->approved()->orderBy('sort_order');
        }]);

        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load('profile');

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's basic information.
     */
    public function updateBasic(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other,prefer_not_to_say'],
            'phone' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'location' => $request->location,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Informações básicas atualizadas com sucesso!');
    }

    /**
     * Update the user's profile details.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bio' => ['nullable', 'string', 'max:1000'],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'max:100'],
            'hobbies' => ['nullable', 'array'],
            'hobbies.*' => ['string', 'max:100'],
            'personality_traits' => ['nullable', 'array'],
            'personality_traits.*' => ['string', 'max:100'],
            'relationship_goal' => ['nullable', 'in:friendship,romance,casual,serious,marriage'],
            'education_level' => ['nullable', 'in:high_school,bachelor,master,phd,other'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'smoking' => ['nullable', 'in:never,occasionally,regularly,prefer_not_to_say'],
            'drinking' => ['nullable', 'in:never,occasionally,regularly,prefer_not_to_say'],
            'exercise_frequency' => ['nullable', 'in:never,rarely,weekly,daily'],
            'looking_for' => ['nullable', 'string', 'max:1000'],
        ]);

        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $profile->update([
            'bio' => $request->bio,
            'interests' => $request->interests ?? [],
            'hobbies' => $request->hobbies ?? [],
            'personality_traits' => $request->personality_traits ?? [],
            'relationship_goal' => $request->relationship_goal,
            'education_level' => $request->education_level,
            'occupation' => $request->occupation,
            'smoking' => $request->smoking,
            'drinking' => $request->drinking,
            'exercise_frequency' => $request->exercise_frequency,
            'looking_for' => $request->looking_for,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Update privacy settings.
     */
    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'show_distance' => ['boolean'],
            'show_age' => ['boolean'],
            'show_online_status' => ['boolean'],
        ]);

        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

        $profile->update([
            'show_distance' => $request->boolean('show_distance'),
            'show_age' => $request->boolean('show_age'),
            'show_online_status' => $request->boolean('show_online_status'),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Configurações de privacidade atualizadas!');
    }

    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Delete old photo if exists
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        
        $user->update(['profile_photo' => $path]);

        return redirect()->route('profile.edit')->with('success', 'Foto de perfil atualizada com sucesso!');
    }

    /**
     * Update user's geolocation.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        
        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
        ]);

        return redirect()->back()->with('success', 'Localização atualizada com sucesso!');
    }
}
