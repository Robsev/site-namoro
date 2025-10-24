<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\MatchingPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remover apenas usuários de teste existentes (example.com)
        User::where('email', 'like', '%@example.com')->delete();
        
        $this->command->info('🗑️ Usuários de teste antigos removidos');

        // Dados de usuários de teste coerentes
        $testUsers = [
            [
                'name' => 'Ana Silva Santos',
                'first_name' => 'Ana',
                'last_name' => 'Silva Santos',
                'email' => 'ana.silva@example.com',
                'gender' => 'female',
                'birth_date' => '1995-03-15',
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'Brasil',
                'neighborhood' => 'Vila Madalena',
                'latitude' => -23.5505,
                'longitude' => -46.6333,
                'bio' => 'Arquiteta apaixonada por design e sustentabilidade. Adoro viajar e conhecer novas culturas.',
                'interests' => ['Arquitetura', 'Sustentabilidade', 'Viagem', 'Arte', 'Música'],
                'hobbies' => ['Desenhar', 'Fotografar', 'Ler', 'Dançar'],
                'personality_traits' => ['Criativa', 'Empática', 'Otimista', 'Determinada'],
                'relationship_goal' => 'friendship',
                'education_level' => 'bachelor',
                'occupation' => 'Arquiteta',
                'smoking' => 'never',
                'drinking' => 'occasionally',
                'exercise_frequency' => 'weekly',
                'looking_for' => 'Pessoas interessantes para conversar sobre arte e cultura',
                'age_min' => 25,
                'age_max' => 40,
                'max_distance' => 30,
                'preferred_genders' => ['male', 'female'],
                'preferred_interests' => ['Arte', 'Cultura', 'Viagem', 'Música'],
            ],
            [
                'name' => 'Carlos Eduardo Oliveira',
                'first_name' => 'Carlos',
                'last_name' => 'Eduardo Oliveira',
                'email' => 'carlos.oliveira@example.com',
                'gender' => 'male',
                'birth_date' => '1988-07-22',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'country' => 'Brasil',
                'neighborhood' => 'Copacabana',
                'latitude' => -22.9068,
                'longitude' => -43.1729,
                'bio' => 'Engenheiro de software que ama tecnologia e esportes. Sempre disposto a ajudar e aprender.',
                'interests' => ['Tecnologia', 'Futebol', 'Programação', 'Cinema', 'Culinária'],
                'hobbies' => ['Jogar futebol', 'Assistir filmes', 'Cozinhar', 'Ler'],
                'personality_traits' => ['Amigável', 'Paciente', 'Humorado', 'Colaborativo'],
                'relationship_goal' => 'romance',
                'education_level' => 'bachelor',
                'occupation' => 'Engenheiro de Software',
                'smoking' => 'never',
                'drinking' => 'occasionally',
                'exercise_frequency' => 'daily',
                'looking_for' => 'Alguém especial para compartilhar a vida',
                'age_min' => 25,
                'age_max' => 35,
                'max_distance' => 50,
                'preferred_genders' => ['female'],
                'preferred_interests' => ['Tecnologia', 'Esportes', 'Cultura', 'Viagem'],
            ],
            [
                'name' => 'Marina Costa Lima',
                'first_name' => 'Marina',
                'last_name' => 'Costa Lima',
                'email' => 'marina.costa@example.com',
                'gender' => 'female',
                'birth_date' => '1992-11-08',
                'city' => 'Belo Horizonte',
                'state' => 'MG',
                'country' => 'Brasil',
                'neighborhood' => 'Savassi',
                'latitude' => -19.9167,
                'longitude' => -43.9345,
                'bio' => 'Psicóloga que trabalha com desenvolvimento pessoal. Adora meditação e natureza.',
                'interests' => ['Psicologia', 'Meditação', 'Natureza', 'Yoga', 'Leitura'],
                'hobbies' => ['Meditar', 'Fazer yoga', 'Caminhar na natureza', 'Escrever'],
                'personality_traits' => ['Calma', 'Reflexiva', 'Empática', 'Sábia'],
                'relationship_goal' => 'friendship',
                'education_level' => 'master',
                'occupation' => 'Psicóloga',
                'smoking' => 'never',
                'drinking' => 'never',
                'exercise_frequency' => 'daily',
                'looking_for' => 'Amizades profundas e significativas',
                'age_min' => 28,
                'age_max' => 45,
                'max_distance' => 40,
                'preferred_genders' => ['male', 'female', 'other'],
                'preferred_interests' => ['Bem-estar', 'Espiritualidade', 'Natureza', 'Arte'],
            ],
            [
                'name' => 'Rafael Santos Pereira',
                'first_name' => 'Rafael',
                'last_name' => 'Santos Pereira',
                'email' => 'rafael.santos@example.com',
                'gender' => 'male',
                'birth_date' => '1990-05-12',
                'city' => 'Porto Alegre',
                'state' => 'RS',
                'country' => 'Brasil',
                'neighborhood' => 'Moinhos de Vento',
                'latitude' => -30.0346,
                'longitude' => -51.2177,
                'bio' => 'Chef de cozinha apaixonado por gastronomia e cultura gaúcha. Sempre experimentando novos sabores.',
                'interests' => ['Gastronomia', 'Cultura Gaúcha', 'Música', 'História', 'Viagem'],
                'hobbies' => ['Cozinhar', 'Tocar violão', 'Ler sobre história', 'Viajar'],
                'personality_traits' => ['Criativo', 'Passional', 'Generoso', 'Aventureiro'],
                'relationship_goal' => 'romance',
                'education_level' => 'bachelor',
                'occupation' => 'Chef de Cozinha',
                'smoking' => 'never',
                'drinking' => 'occasionally',
                'exercise_frequency' => 'weekly',
                'looking_for' => 'Alguém para compartilhar aventuras culinárias',
                'age_min' => 25,
                'age_max' => 40,
                'max_distance' => 60,
                'preferred_genders' => ['female'],
                'preferred_interests' => ['Gastronomia', 'Cultura', 'Música', 'Viagem'],
            ],
            [
                'name' => 'Alex Morgan',
                'first_name' => 'Alex',
                'last_name' => 'Morgan',
                'email' => 'alex.morgan@example.com',
                'gender' => 'other',
                'birth_date' => '1993-09-30',
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'Brasil',
                'neighborhood' => 'Pinheiros',
                'latitude' => -23.5505,
                'longitude' => -46.6333,
                'bio' => 'Artista visual e ativista LGBTQ+. Trabalho com arte digital e causas sociais.',
                'interests' => ['Arte Digital', 'Ativismo', 'LGBTQ+', 'Tecnologia', 'Justiça Social'],
                'hobbies' => ['Criar arte digital', 'Participar de manifestações', 'Ler sobre direitos humanos'],
                'personality_traits' => ['Corajoso', 'Criativo', 'Defensor', 'Inspirador'],
                'relationship_goal' => 'friendship',
                'education_level' => 'bachelor',
                'occupation' => 'Artista Digital',
                'smoking' => 'never',
                'drinking' => 'occasionally',
                'exercise_frequency' => 'weekly',
                'looking_for' => 'Pessoas que compartilham valores de justiça social',
                'age_min' => 22,
                'age_max' => 35,
                'max_distance' => 50,
                'preferred_genders' => ['male', 'female', 'other'],
                'preferred_interests' => ['Arte', 'Ativismo', 'Justiça Social', 'Tecnologia'],
            ],
        ];

        foreach ($testUsers as $userData) {
            // Criar usuário
            $user = User::create([
                'name' => $userData['name'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'remember_token' => \Illuminate\Support\Str::random(10),
                'birth_date' => $userData['birth_date'],
                'gender' => $userData['gender'],
                'city' => $userData['city'],
                'state' => $userData['state'],
                'country' => $userData['country'],
                'neighborhood' => $userData['neighborhood'],
                'latitude' => $userData['latitude'],
                'longitude' => $userData['longitude'],
                'is_verified' => true,
                'is_active' => true,
                'last_seen' => now(),
                'subscription_type' => 'free',
            ]);

            // Criar perfil
            $user->profile()->create([
                'bio' => $userData['bio'],
                'interests' => $userData['interests'],
                'hobbies' => $userData['hobbies'],
                'personality_traits' => $userData['personality_traits'],
                'relationship_goal' => $userData['relationship_goal'],
                'education_level' => $userData['education_level'],
                'occupation' => $userData['occupation'],
                'smoking' => $userData['smoking'],
                'drinking' => $userData['drinking'],
                'exercise_frequency' => $userData['exercise_frequency'],
                'looking_for' => $userData['looking_for'],
                'age_min' => $userData['age_min'],
                'age_max' => $userData['age_max'],
                'max_distance' => $userData['max_distance'],
                'show_distance' => true,
                'show_age' => true,
                'show_online_status' => true,
            ]);

            // Criar preferências de matching
            $user->matchingPreferences()->create([
                'preferred_genders' => $userData['preferred_genders'],
                'min_age' => $userData['age_min'],
                'max_age' => $userData['age_max'],
                'max_distance' => $userData['max_distance'],
                'preferred_interests' => $userData['preferred_interests'],
                'preferred_personality_traits' => $userData['personality_traits'],
                'preferred_education_levels' => ['bachelor', 'master', 'phd'],
                'preferred_relationship_goals' => [$userData['relationship_goal']],
                'smoking_ok' => $userData['smoking'] === 'never' ? false : true,
                'drinking_ok' => $userData['drinking'] !== 'never',
                'online_only' => false,
                'verified_only' => false,
            ]);

            $this->command->info("✅ Usuário de teste criado: {$userData['name']} ({$userData['email']})");
        }

        $this->command->info('🎉 Usuários de teste criados com sucesso!');
        $this->command->info('📧 Todos os emails terminam com @example.com para fácil identificação');
        $this->command->info('🔐 Senha padrão para todos: "password"');
    }
}