<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        // Download and save profile photo from provider
        $profilePhotoPath = null;
        if ($providerUser->getAvatar()) {
            $profilePhotoPath = $this->downloadAndSaveProfilePhoto($providerUser->getAvatar());
        }

        // Create new user
        $user = User::create([
            'name' => $providerUser->getName(),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random(24)), // Random password for OAuth users
            'first_name' => $this->extractFirstName($providerUser->getName()),
            'last_name' => $this->extractLastName($providerUser->getName()),
            'profile_photo' => $profilePhotoPath,
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

        // Always update profile photo from Google if available
        if ($providerUser->getAvatar()) {
            // Delete old photo if it exists and is stored locally
            if ($user->profile_photo && !str_starts_with($user->profile_photo, 'http://') && !str_starts_with($user->profile_photo, 'https://')) {
                if (Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
            }

            // Download and save new photo
            $profilePhotoPath = $this->downloadAndSaveProfilePhoto($providerUser->getAvatar());
            if ($profilePhotoPath) {
                $updates['profile_photo'] = $profilePhotoPath;
            }
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
     * Download profile photo from external URL and save it locally.
     */
    private function downloadAndSaveProfilePhoto($avatarUrl)
    {
        try {
            // Download the image
            $imageContent = file_get_contents($avatarUrl);
            
            if ($imageContent === false) {
                \Log::warning('Failed to download profile photo from: ' . $avatarUrl);
                return null;
            }

            // Get file extension from URL or default to jpg
            $extension = 'jpg';
            $urlPath = parse_url($avatarUrl, PHP_URL_PATH);
            if ($urlPath) {
                $pathInfo = pathinfo($urlPath);
                if (isset($pathInfo['extension']) && in_array(strtolower($pathInfo['extension']), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $extension = strtolower($pathInfo['extension']);
                }
            }

            // Generate unique filename
            $filename = 'profile-photos/' . Str::random(40) . '.' . $extension;
            
            // Save to storage
            Storage::disk('public')->put($filename, $imageContent);

            // Resize if possible
            $this->resizeProfilePhoto($filename);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('Error downloading profile photo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Resize profile photo to optimize storage.
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
