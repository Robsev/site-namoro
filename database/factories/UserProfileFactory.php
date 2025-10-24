<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
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

        $hobbies = [
            'Tocar instrumento', 'Pintar', 'Correr', 'Cozinhar', 'Ler livros',
            'Assistir séries', 'Jogar futebol', 'Fazer yoga', 'Viajar', 'Fotografar',
            'Dançar', 'Jogar videogame', 'Fazer artesanato', 'Caminhar', 'Nadar'
        ];

        $personalityTraits = [
            'Extrovertido', 'Introvertido', 'Criativo', 'Analítico', 'Empático',
            'Aventuroso', 'Cauteloso', 'Otimista', 'Realista', 'Líder',
            'Colaborativo', 'Independente', 'Compassivo', 'Determinado', 'Flexível'
        ];

        return [
            'bio' => fake()->paragraph(3),
            'interests' => fake()->randomElements($interests, fake()->numberBetween(3, 8)),
            'hobbies' => fake()->randomElements($hobbies, fake()->numberBetween(2, 6)),
            'personality_traits' => fake()->randomElements($personalityTraits, fake()->numberBetween(3, 7)),
            'relationship_goal' => fake()->randomElement(['friendship', 'romance', 'casual', 'serious', 'marriage']),
            'education_level' => fake()->randomElement(['high_school', 'bachelor', 'master', 'phd', 'other']),
            'occupation' => fake()->jobTitle(),
            'smoking' => fake()->randomElement(['never', 'occasionally', 'regularly', 'prefer_not_to_say']),
            'drinking' => fake()->randomElement(['never', 'occasionally', 'regularly', 'prefer_not_to_say']),
            'exercise_frequency' => fake()->randomElement(['never', 'rarely', 'weekly', 'daily']),
            'looking_for' => fake()->sentence(8),
            'age_min' => fake()->numberBetween(18, 25),
            'age_max' => fake()->numberBetween(30, 50),
            'max_distance' => fake()->numberBetween(10, 100),
            'show_distance' => fake()->boolean(80),
            'show_age' => fake()->boolean(90),
            'show_online_status' => fake()->boolean(70),
        ];
    }
}
