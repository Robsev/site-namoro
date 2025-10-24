<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    /**
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = $this->findOrCreateUser($googleUser, 'google');
            
            Auth::login($user);
            
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Erro ao fazer login com Google: ' . $e->getMessage());
        }
    }


    /**
     * Find or create user from OAuth provider.
     */
    private function findOrCreateUser($providerUser, $provider)
    {
        // Try to find existing user by email
        $user = User::where('email', $providerUser->getEmail())->first();

        if ($user) {
            // Update OAuth provider info if needed
            $this->updateUserFromProvider($user, $providerUser, $provider);
            return $user;
        }

        // Create new user
        $user = User::create([
            'name' => $providerUser->getName(),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random(24)), // Random password for OAuth users
            'first_name' => $this->extractFirstName($providerUser->getName()),
            'last_name' => $this->extractLastName($providerUser->getName()),
            'profile_photo' => $providerUser->getAvatar(),
            'is_verified' => true, // OAuth users are considered verified
            'is_active' => true,
            'last_seen' => now(),
            'subscription_type' => 'free',
        ]);

        // Create default profile
        $user->profile()->create([
            'bio' => 'Usuário conectado via ' . ucfirst($provider),
            'relationship_goal' => 'friendship',
            'show_distance' => true,
            'show_age' => true,
            'show_online_status' => true,
        ]);

        // Create default matching preferences
        $user->matchingPreferences()->create([
            'min_age' => 18,
            'max_age' => 100,
            'max_distance' => 50,
            'smoking_ok' => true,
            'drinking_ok' => true,
            'online_only' => false,
            'verified_only' => false,
        ]);

        return $user;
    }

    /**
     * Update user information from OAuth provider.
     */
    private function updateUserFromProvider($user, $providerUser, $provider)
    {
        $updates = [];

        if (!$user->profile_photo && $providerUser->getAvatar()) {
            $updates['profile_photo'] = $providerUser->getAvatar();
        }

        if (!$user->is_verified) {
            $updates['is_verified'] = true;
        }

        $updates['last_seen'] = now();

        if (!empty($updates)) {
            $user->update($updates);
        }
    }

    /**
     * Extract first name from full name.
     */
    private function extractFirstName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return $parts[0] ?? '';
    }

    /**
     * Extract last name from full name.
     */
    private function extractLastName($fullName)
    {
        $parts = explode(' ', trim($fullName));
        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }
}
