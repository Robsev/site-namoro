<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Gerar gênero primeiro para garantir coerência
        $gender = $this->faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']);
        
        // Gerar nome baseado no gênero
        $firstName = $this->faker->firstName($gender === 'male' ? 'male' : ($gender === 'female' ? 'female' : null));
        $lastName = $this->faker->lastName();
        $fullName = $firstName . ' ' . $lastName;
        
        return [
            'name' => $fullName,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_date' => $this->faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
            'gender' => $gender,
            'location' => $this->faker->city() . ', ' . $this->faker->state(),
            'is_verified' => $this->faker->boolean(30), // 30% chance of being verified
            'is_active' => true,
            'last_seen' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'subscription_type' => $this->faker->randomElement(['free', 'premium']),
            'subscription_expires_at' => $this->faker->optional(0.3)->dateTimeBetween('now', '+1 year'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
