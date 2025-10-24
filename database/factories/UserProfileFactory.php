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
            'bio' => $this->faker->paragraph(3),
            'interests' => $this->faker->randomElements($interests, $this->faker->numberBetween(3, 8)),
            'hobbies' => $this->faker->randomElements($hobbies, $this->faker->numberBetween(2, 6)),
            'personality_traits' => $this->faker->randomElements($personalityTraits, $this->faker->numberBetween(3, 7)),
            'relationship_goal' => $this->faker->randomElement(['friendship', 'romance', 'casual', 'serious', 'marriage']),
            'education_level' => $this->faker->randomElement(['high_school', 'bachelor', 'master', 'phd', 'other']),
            'occupation' => $this->faker->jobTitle(),
            'smoking' => $this->faker->randomElement(['never', 'occasionally', 'regularly', 'prefer_not_to_say']),
            'drinking' => $this->faker->randomElement(['never', 'occasionally', 'regularly', 'prefer_not_to_say']),
            'exercise_frequency' => $this->faker->randomElement(['never', 'rarely', 'weekly', 'daily']),
            'looking_for' => $this->faker->sentence(8),
            'age_min' => $this->faker->numberBetween(18, 25),
            'age_max' => $this->faker->numberBetween(30, 50),
            'max_distance' => $this->faker->numberBetween(10, 100),
            'show_distance' => $this->faker->boolean(80),
            'show_age' => $this->faker->boolean(90),
            'show_online_status' => $this->faker->boolean(70),
        ];
    }
}
