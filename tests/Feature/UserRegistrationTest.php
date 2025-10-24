<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test user can register with valid data
     */
    public function test_user_can_register_with_valid_data()
    {
        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'phone' => '11999999999',
            'location' => 'São Paulo, SP',
            'terms' => true
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'first_name' => 'João',
            'last_name' => 'Silva'
        ]);
    }

    /**
     * Test user registration fails with invalid email
     */
    public function test_user_registration_fails_with_invalid_email()
    {
        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'terms' => true
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-email'
        ]);
    }

    /**
     * Test user registration fails with mismatched passwords
     */
    public function test_user_registration_fails_with_mismatched_passwords()
    {
        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'terms' => true
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'joao@example.com'
        ]);
    }

    /**
     * Test user registration fails without accepting terms
     */
    public function test_user_registration_fails_without_accepting_terms()
    {
        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'terms' => false
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('terms');
        $this->assertDatabaseMissing('users', [
            'email' => 'joao@example.com'
        ]);
    }

    /**
     * Test user registration creates default profile and preferences
     */
    public function test_user_registration_creates_default_profile_and_preferences()
    {
        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'terms' => true
        ];

        $response = $this->post('/register', $userData);

        $user = User::where('email', 'joao@example.com')->first();
        
        $this->assertNotNull($user->profile);
        $this->assertNotNull($user->matchingPreferences);
        $this->assertEquals('free', $user->subscription_type);
    }

    /**
     * Test user cannot register with existing email
     */
    public function test_user_cannot_register_with_existing_email()
    {
        // Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1990-01-01',
            'gender' => 'male',
            'terms' => true
        ];

        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test user registration with OAuth data
     */
    public function test_user_registration_with_oauth_data()
    {
        $oauthData = [
            'name' => 'João Silva',
            'email' => 'joao@gmail.com',
            'provider' => 'google',
            'provider_id' => 'google_123'
        ];

        $response = $this->post('/auth/google/callback', $oauthData);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@gmail.com',
            'first_name' => 'João',
            'last_name' => 'Silva'
        ]);
    }
}