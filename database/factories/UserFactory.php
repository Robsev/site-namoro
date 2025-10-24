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
        $faker = $this->faker ?? \Faker\Factory::create('pt_BR');
        
        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'first_name' => $faker->firstName(),
            'last_name' => $faker->lastName(),
            'birth_date' => $faker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
            'gender' => $faker->randomElement(['male', 'female', 'other', 'prefer_not_to_say']),
            'phone' => $faker->phoneNumber(),
            'location' => $faker->city() . ', ' . $faker->state(),
            'is_verified' => $faker->boolean(30), // 30% chance of being verified
            'is_active' => true,
            'last_seen' => $faker->dateTimeBetween('-7 days', 'now'),
            'subscription_type' => $faker->randomElement(['free', 'premium']),
            'subscription_expires_at' => $faker->optional(0.3)->dateTimeBetween('now', '+1 year'),
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
