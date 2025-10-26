<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Combine birth_day, birth_month, and birth_year into birth_date if they exist
        if ($request->birth_day && $request->birth_month && $request->birth_year) {
            $request->merge([
                'birth_date' => $request->birth_year . '-' . str_pad($request->birth_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($request->birth_day, 2, '0', STR_PAD_LEFT)
            ]);
        }
        
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other,prefer_not_to_say'],
            'terms' => ['required', 'accepted'],
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'is_verified' => false,
            'is_active' => true,
            'last_seen' => now(),
            'subscription_type' => 'free',
        ]);

        // Create default profile
        $user->profile()->create([
            'bio' => 'Olá! Sou novo aqui e estou procurando fazer novas amizades.',
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

        // Send verification email
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', 'Conta criada com sucesso! Por favor, verifique seu e-mail.');
    }
}
