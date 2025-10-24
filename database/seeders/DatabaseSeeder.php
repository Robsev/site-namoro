<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário admin
        $admin = User::factory()->create([
            'name' => 'Admin Amigos Para Sempre',
            'email' => 'admin@amigosparasempre.com',
            'first_name' => 'Admin',
            'last_name' => 'Sistema',
            'is_verified' => true,
            'subscription_type' => 'premium',
        ]);

        // Criar perfil para o admin
        $admin->profile()->create([
            'bio' => 'Administrador do sistema Amigos Para Sempre. Aqui para ajudar você a encontrar suas amizades perfeitas!',
            'interests' => ['Tecnologia', 'Comunidade', 'Ajudar pessoas', 'Inovação'],
            'hobbies' => ['Programar', 'Ler', 'Viajar', 'Conhecer pessoas'],
            'personality_traits' => ['Empático', 'Líder', 'Compassivo', 'Determinado'],
            'relationship_goal' => 'friendship',
            'education_level' => 'master',
            'occupation' => 'Desenvolvedor de Software',
            'smoking' => 'never',
            'drinking' => 'occasionally',
            'exercise_frequency' => 'weekly',
            'looking_for' => 'Pessoas interessantes para conversar e compartilhar experiências',
            'age_min' => 18,
            'age_max' => 60,
            'max_distance' => 50,
            'show_distance' => true,
            'show_age' => true,
            'show_online_status' => true,
        ]);

        // Criar preferências de matching para o admin
        $admin->matchingPreferences()->create([
            'preferred_genders' => ['male', 'female', 'other'],
            'min_age' => 18,
            'max_age' => 60,
            'max_distance' => 100,
            'preferred_interests' => ['Tecnologia', 'Comunidade', 'Ajudar pessoas', 'Inovação', 'Cultura'],
            'preferred_personality_traits' => ['Empático', 'Criativo', 'Otimista', 'Colaborativo'],
            'preferred_education_levels' => ['bachelor', 'master', 'phd'],
            'preferred_relationship_goals' => ['friendship', 'romance'],
            'smoking_ok' => false,
            'drinking_ok' => true,
            'online_only' => false,
            'verified_only' => false,
        ]);

        // Criar usuários de exemplo
        $users = User::factory(50)->create();

        // Criar perfis e preferências para cada usuário
        foreach ($users as $user) {
            $user->profile()->create();
            $user->matchingPreferences()->create();
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('👤 Admin user created: admin@amigosparasempre.com');
        $this->command->info('👥 ' . $users->count() . ' example users created');
    }
}
