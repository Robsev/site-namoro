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
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/user.birthday.read'])
            ->redirect();
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

        // Extract birth date from Google user data
        $birthDate = $this->extractBirthDate($providerUser);

        // Create new user
        $user = User::create([
            'name' => $providerUser->getName(),
            'email' => $providerUser->getEmail(),
            'password' => Hash::make(Str::random(24)), // Random password for OAuth users
            'first_name' => $this->extractFirstName($providerUser->getName()),
            'last_name' => $this->extractLastName($providerUser->getName()),
            'birth_date' => $birthDate,
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
        $avatarUrl = $providerUser->getAvatar();
        \Log::info('OAuth: Updating user from provider', [
            'user_id' => $user->id,
            'email' => $user->email,
            'has_avatar' => !empty($avatarUrl),
            'avatar_url' => $avatarUrl,
            'current_photo' => $user->profile_photo
        ]);

        if ($avatarUrl) {
            // Delete old photo if it exists and is stored locally
            // Also delete if it's an old Google URL (we want to replace with local file)
            if ($user->profile_photo) {
                $isExternalUrl = str_starts_with($user->profile_photo, 'http://') || str_starts_with($user->profile_photo, 'https://');
                
                if (!$isExternalUrl) {
                    // It's a local file, delete it
                    if (Storage::disk('public')->exists($user->profile_photo)) {
                        Storage::disk('public')->delete($user->profile_photo);
                        \Log::info('OAuth: Deleted old local profile photo', ['path' => $user->profile_photo]);
                    }
                } else {
                    // It's an external URL (old Google URL), we'll replace it with local file
                    \Log::info('OAuth: Replacing external URL with local file', ['old_url' => $user->profile_photo]);
                }
            }

            // Download and save new photo
            $profilePhotoPath = $this->downloadAndSaveProfilePhoto($avatarUrl);
            if ($profilePhotoPath) {
                $updates['profile_photo'] = $profilePhotoPath;
                \Log::info('OAuth: Profile photo downloaded and saved', ['path' => $profilePhotoPath]);
            } else {
                \Log::warning('OAuth: Failed to download profile photo', ['url' => $avatarUrl]);
            }
        } else {
            \Log::warning('OAuth: No avatar URL provided by provider');
        }

        if (!$user->is_verified) {
            $updates['is_verified'] = true;
        }

        // Update birth date if not set and available from provider
        if (!$user->birth_date) {
            $birthDate = $this->extractBirthDate($providerUser);
            if ($birthDate) {
                $updates['birth_date'] = $birthDate;
            }
        }

        $updates['last_seen'] = now();

        if (!empty($updates)) {
            $user->update($updates);
            // Refresh the model to ensure attributes are updated
            $user->refresh();
            \Log::info('OAuth: User updated', [
                'updates' => array_keys($updates),
                'new_profile_photo' => $user->profile_photo,
                'profile_photo_url' => $user->profile_photo_url
            ]);
        } else {
            \Log::info('OAuth: No updates needed');
        }
    }

    /**
     * Download profile photo from external URL and save it locally.
     */
    private function downloadAndSaveProfilePhoto($avatarUrl)
    {
        try {
            \Log::info('OAuth: Starting photo download', ['url' => $avatarUrl]);

            // Use cURL for more reliable downloads
            $ch = curl_init($avatarUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($imageContent === false || $httpCode !== 200) {
                \Log::warning('Failed to download profile photo', [
                    'url' => $avatarUrl,
                    'http_code' => $httpCode,
                    'error' => $error
                ]);
                
                // Fallback to file_get_contents if cURL fails
                if (ini_get('allow_url_fopen')) {
                    \Log::info('OAuth: Trying file_get_contents as fallback');
                    $imageContent = @file_get_contents($avatarUrl);
                    if ($imageContent === false) {
                        return null;
                    }
                } else {
                    return null;
                }
            }

            if (empty($imageContent)) {
                \Log::warning('OAuth: Downloaded image is empty', ['url' => $avatarUrl]);
                return null;
            }

            // Get file extension from URL or default to jpg
            $extension = 'jpg';
            $urlPath = parse_url($avatarUrl, PHP_URL_PATH);
            if ($urlPath) {
                $pathInfo = pathinfo($urlPath);
                if (isset($pathInfo['extension']) && in_array(strtolower($pathInfo['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $extension = strtolower($pathInfo['extension']);
                }
            }

            // Generate unique filename
            $filename = 'profile-photos/' . Str::random(40) . '.' . $extension;
            
            // Save to storage
            $saved = Storage::disk('public')->put($filename, $imageContent);
            
            if (!$saved) {
                \Log::error('OAuth: Failed to save photo to storage', ['filename' => $filename]);
                return null;
            }

            \Log::info('OAuth: Photo saved successfully', ['filename' => $filename, 'size' => strlen($imageContent)]);

            // Resize if possible
            $this->resizeProfilePhoto($filename);

            return $filename;
        } catch (\Exception $e) {
            \Log::error('OAuth: Error downloading profile photo', [
                'url' => $avatarUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    /**
     * Extract birth date from Google OAuth user data.
     * 
     * @param \Laravel\Socialite\Two\User $providerUser
     * @return string|null Date in Y-m-d format or null if not available
     */
    private function extractBirthDate($providerUser)
    {
        try {
            // Try to get birth date from user data array
            $userData = $providerUser->user ?? [];
            
            // Check if birthday is in user data (format: YYYY-MM-DD or MM-DD)
            if (isset($userData['birthday'])) {
                $birthday = $userData['birthday'];
                
                // If format is YYYY-MM-DD, use it directly
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthday)) {
                    return $birthday;
                }
                
                // If format is MM-DD, we need to get year from People API
                if (preg_match('/^\d{2}-\d{2}$/', $birthday)) {
                    // Try to get full date from People API
                    return $this->getBirthDateFromPeopleApi($providerUser);
                }
            }

            // Try to get from People API directly
            return $this->getBirthDateFromPeopleApi($providerUser);
            
        } catch (\Exception $e) {
            \Log::warning('OAuth: Failed to extract birth date', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get birth date from Google People API.
     * 
     * @param \Laravel\Socialite\Two\User $providerUser
     * @return string|null Date in Y-m-d format or null if not available
     */
    private function getBirthDateFromPeopleApi($providerUser)
    {
        try {
            // Try different ways to get the token
            $token = $providerUser->token ?? 
                     (method_exists($providerUser, 'getToken') ? $providerUser->getToken() : null) ??
                     (isset($providerUser->accessTokenResponseBody['access_token']) ? $providerUser->accessTokenResponseBody['access_token'] : null);
            
            if (!$token) {
                \Log::warning('OAuth: No access token available for People API');
                return null;
            }

            // Make request to People API
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://people.googleapis.com/v1/people/me?personFields=birthdays', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['birthdays']) && is_array($data['birthdays'])) {
                foreach ($data['birthdays'] as $birthday) {
                    if (isset($birthday['date'])) {
                        $date = $birthday['date'];
                        
                        // Google returns year, month, day separately
                        $year = $date['year'] ?? null;
                        $month = $date['month'] ?? null;
                        $day = $date['day'] ?? null;
                        
                        if ($year && $month && $day) {
                            return sprintf('%04d-%02d-%02d', $year, $month, $day);
                        }
                        
                        // If no year, use current year (but this is not ideal)
                        if ($month && $day) {
                            \Log::info('OAuth: Birthday without year, using current year', [
                                'month' => $month,
                                'day' => $day
                            ]);
                            return sprintf('%04d-%02d-%02d', date('Y'), $month, $day);
                        }
                    }
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            \Log::warning('OAuth: Failed to get birth date from People API', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
