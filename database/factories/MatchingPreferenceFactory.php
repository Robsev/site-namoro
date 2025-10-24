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
            'preferred_genders' => $this->faker->randomElements(
                ['male', 'female', 'other', 'prefer_not_to_say'], 
                $this->faker->numberBetween(1, 3)
            ),
            'min_age' => $this->faker->numberBetween(18, 25),
            'max_age' => $this->faker->numberBetween(30, 50),
            'max_distance' => $this->faker->numberBetween(10, 100),
            'preferred_interests' => $this->faker->randomElements($interests, $this->faker->numberBetween(3, 8)),
            'preferred_personality_traits' => $this->faker->randomElements($personalityTraits, $this->faker->numberBetween(3, 7)),
            'preferred_education_levels' => $this->faker->randomElements(
                ['high_school', 'bachelor', 'master', 'phd', 'other'], 
                $this->faker->numberBetween(1, 4)
            ),
            'preferred_relationship_goals' => $this->faker->randomElements(
                ['friendship', 'romance', 'casual', 'serious', 'marriage'], 
                $this->faker->numberBetween(1, 3)
            ),
            'smoking_ok' => $this->faker->boolean(70),
            'drinking_ok' => $this->faker->boolean(80),
            'online_only' => $this->faker->boolean(10),
            'verified_only' => $this->faker->boolean(20),
        ];
    }
}
