<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PurgeTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Iniciando remoção segura de usuários de teste (example.com/.net/.org)...');

        DB::transaction(function () {
            // Selecionar usuários com domínio example.*
            $testUsers = User::query()
                ->where(function ($q) {
                    $q->where('email', 'like', '%@example.com')
                      ->orWhere('email', 'like', '%@example.net')
                      ->orWhere('email', 'like', '%@example.org');
                })
                ->get(['id', 'email']);

            if ($testUsers->isEmpty()) {
                $this->command->info('Nenhum usuário de teste encontrado.');
                return;
            }

            $userIds = $testUsers->pluck('id')->all();
            $this->command->warn('Usuários de teste encontrados: ' . count($userIds));

            // Apagar dados relacionados em ordem segura para FKs
            // Mensagens (sender/receiver)
            DB::table('messages')->whereIn('sender_id', $userIds)->delete();
            DB::table('messages')->whereIn('receiver_id', $userIds)->delete();

            // Conversas (user1/user2) - mensagens já removidas acima
            DB::table('conversations')->whereIn('user1_id', $userIds)->delete();
            DB::table('conversations')->whereIn('user2_id', $userIds)->delete();

            // Matches (user1/user2)
            DB::table('user_matches')->whereIn('user1_id', $userIds)->delete();
            DB::table('user_matches')->whereIn('user2_id', $userIds)->delete();

            // Fotos
            DB::table('user_photos')->whereIn('user_id', $userIds)->delete();

            // Interesses (pivot)
            DB::table('user_interests')->whereIn('user_id', $userIds)->delete();

            // Preferências de matching
            DB::table('matching_preferences')->whereIn('user_id', $userIds)->delete();

            // Perfil psicológico
            DB::table('psychological_profiles')->whereIn('user_id', $userIds)->delete();

            // Notificações
            DB::table('notifications')->whereIn('user_id', $userIds)->delete();

            // Relatórios (reporter / reported)
            DB::table('user_reports')->whereIn('reporter_id', $userIds)->delete();
            DB::table('user_reports')->whereIn('reported_user_id', $userIds)->delete();

            // Bloqueios (pivot user_blocks)
            DB::table('user_blocks')->whereIn('user_id', $userIds)->delete();
            DB::table('user_blocks')->whereIn('blocked_user_id', $userIds)->delete();

            // Assinaturas
            DB::table('subscriptions')->whereIn('user_id', $userIds)->delete();

            // Perfis de usuário (se houver tabela user_profiles)
            if (DB::getSchemaBuilder()->hasTable('user_profiles')) {
                DB::table('user_profiles')->whereIn('user_id', $userIds)->delete();
            }

            // Sessões
            DB::table('sessions')->whereIn('user_id', $userIds)->delete();

            // Finalmente os usuários
            DB::table('users')->whereIn('id', $userIds)->delete();

            $this->command->info('Remoção concluída com sucesso.');
        });
    }
}
