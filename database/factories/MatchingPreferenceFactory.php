<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MatchingPreference>
 */
class MatchingPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $interests = [
            'Música', 'Cinema', 'Leitura', 'Esportes', 'Viagem', 'Culinária',
            'Arte', 'Tecnologia', 'Natureza', 'Fotografia', 'Dança', 'Teatro',
            'Jogos', 'Fitness', 'Meditação', 'Voluntariado', 'Idiomas', 'História'
        ];

        $personalityTraits = [
            'Extrovertido', 'Introvertido', 'Criativo', 'Analítico', 'Empático',
            'Aventuroso', 'Cauteloso', 'Otimista', 'Realista', 'Líder',
            'Colaborativo', 'Independente', 'Compassivo', 'Determinado', 'Flexível'
        ];

        return [
            'preferred_genders' => fake()->randomElements(
                ['male', 'female', 'other', 'prefer_not_to_say'], 
                fake()->numberBetween(1, 3)
            ),
            'min_age' => fake()->numberBetween(18, 25),
            'max_age' => fake()->numberBetween(30, 50),
            'max_distance' => fake()->numberBetween(10, 100),
            'preferred_interests' => fake()->randomElements($interests, fake()->numberBetween(3, 8)),
            'preferred_personality_traits' => fake()->randomElements($personalityTraits, fake()->numberBetween(3, 7)),
            'preferred_education_levels' => fake()->randomElements(
                ['high_school', 'bachelor', 'master', 'phd', 'other'], 
                fake()->numberBetween(1, 4)
            ),
            'preferred_relationship_goals' => fake()->randomElements(
                ['friendship', 'romance', 'casual', 'serious', 'marriage'], 
                fake()->numberBetween(1, 3)
            ),
            'smoking_ok' => fake()->boolean(70),
            'drinking_ok' => fake()->boolean(80),
            'online_only' => fake()->boolean(10),
            'verified_only' => fake()->boolean(20),
        ];
    }
}
