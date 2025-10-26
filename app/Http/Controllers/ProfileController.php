<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
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
            'exercise_frequency' => ['nullable', 'in:never,rarely,weekly,daily,several_times_week,monthly,prefer_not_to_say'],
            'looking_for' => ['nullable', 'string', 'max:1000'],
            // New lifestyle fields
            'has_children' => ['nullable', 'in:yes,no,prefer_not_to_say'],
            'wants_children' => ['nullable', 'in:yes,no,maybe,prefer_not_to_say'],
            'body_type' => ['nullable', 'in:slim,athletic,average,curvy,plus_size,muscular,prefer_not_to_say'],
            'height' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight' => ['nullable', 'integer', 'min:30', 'max:200'],
            'diet_type' => ['nullable', 'in:omnivore,vegetarian,vegan,pescatarian,keto,paleo,other,prefer_not_to_say'],
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
            // New lifestyle fields
            'has_children' => $request->has_children,
            'wants_children' => $request->wants_children,
            'body_type' => $request->body_type,
            'height' => $request->height,
            'weight' => $request->weight,
            'diet_type' => $request->diet_type,
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
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB max
        ]);

        // Delete old photo if exists
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        
        // Resize and optimize image
        $this->resizeProfilePhoto($path);

        $user->update([
            'profile_photo' => $path,
        ]);

        return redirect()->back()->with('success', 'Foto de perfil atualizada com sucesso!');
    }

    /**
     * Update user's geolocation.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        
        $user->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'neighborhood' => $request->neighborhood,
        ]);

        return redirect()->back()->with('success', 'Localização atualizada com sucesso!');
    }

    /**
     * Remove user profile photo.
     */
    public function removePhoto(Request $request)
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update([
            'profile_photo' => null,
        ]);

        return redirect()->back()->with('success', 'Foto de perfil removida com sucesso!');
    }

    /**
     * Resize and optimize profile photo.
     */
    private function resizeProfilePhoto($path)
    {
        try {
            // Check if Intervention Image is available
            if (!class_exists('Intervention\Image\ImageManager')) {
                \Log::info('Intervention Image not available, skipping photo resizing');
                return;
            }

            $fullPath = Storage::disk('public')->path($path);
            
            // Use Intervention Image v3 syntax
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($fullPath);
            
            // Resize to 400x400 maintaining aspect ratio
            $image->cover(400, 400);
            
            // Optimize quality and save
            $image->toJpeg(85)->save($fullPath);
            
            \Log::info('Profile photo resized successfully');
            
        } catch (\Exception $e) {
            \Log::warning('Failed to resize profile photo: ' . $e->getMessage());
            // Continue without resizing if intervention/image is not available
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não confere.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso!'
        ]);
    }
}
