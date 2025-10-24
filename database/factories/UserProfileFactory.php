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
        $faker = $this->faker ?? \Faker\Factory::create('pt_BR');
        
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
            'bio' => $faker->paragraph(3),
            'interests' => $faker->randomElements($interests, $faker->numberBetween(3, 8)),
            'hobbies' => $faker->randomElements($hobbies, $faker->numberBetween(2, 6)),
            'personality_traits' => $faker->randomElements($personalityTraits, $faker->numberBetween(3, 7)),
            'relationship_goal' => $faker->randomElement(['friendship', 'romance', 'casual', 'serious', 'marriage']),
            'education_level' => $faker->randomElement(['high_school', 'bachelor', 'master', 'phd', 'other']),
            'occupation' => $faker->jobTitle(),
            'smoking' => $faker->randomElement(['never', 'occasionally', 'regularly', 'prefer_not_to_say']),
            'drinking' => $faker->randomElement(['never', 'occasionally', 'regularly', 'prefer_not_to_say']),
            'exercise_frequency' => $faker->randomElement(['never', 'rarely', 'weekly', 'daily']),
            'looking_for' => $faker->sentence(8),
            'age_min' => $faker->numberBetween(18, 25),
            'age_max' => $faker->numberBetween(30, 50),
            'max_distance' => $faker->numberBetween(10, 100),
            'show_distance' => $faker->boolean(80),
            'show_age' => $faker->boolean(90),
            'show_online_status' => $faker->boolean(70),
        ];
    }
}
