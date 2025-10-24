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
        $faker = $this->faker ?? \Faker\Factory::create('pt_BR');
        
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
            'preferred_genders' => $faker->randomElements(
                ['male', 'female', 'other', 'prefer_not_to_say'], 
                $faker->numberBetween(1, 3)
            ),
            'min_age' => $faker->numberBetween(18, 25),
            'max_age' => $faker->numberBetween(30, 50),
            'max_distance' => $faker->numberBetween(10, 100),
            'preferred_interests' => $faker->randomElements($interests, $faker->numberBetween(3, 8)),
            'preferred_personality_traits' => $faker->randomElements($personalityTraits, $faker->numberBetween(3, 7)),
            'preferred_education_levels' => $faker->randomElements(
                ['high_school', 'bachelor', 'master', 'phd', 'other'], 
                $faker->numberBetween(1, 4)
            ),
            'preferred_relationship_goals' => $faker->randomElements(
                ['friendship', 'romance', 'casual', 'serious', 'marriage'], 
                $faker->numberBetween(1, 3)
            ),
            'smoking_ok' => $faker->boolean(70),
            'drinking_ok' => $faker->boolean(80),
            'online_only' => $faker->boolean(10),
            'verified_only' => $faker->boolean(20),
        ];
    }
}
