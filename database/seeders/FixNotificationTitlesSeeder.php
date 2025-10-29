<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class FixNotificationTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Iniciando correção de títulos e mensagens de notificações...');

        // Buscar notificações que tenham valores padrão incorretos
        $notifications = Notification::where(function($query) {
            $query->where('title', 'Notificação')
                  ->orWhere('message', 'Nova notificação');
        })->get();

        $this->command->info("Encontradas {$notifications->count()} notificações para corrigir.");

        $fixed = 0;
        $skipped = 0;

        foreach ($notifications as $notification) {
            $data = $notification->data ?? [];
            
            // Verificar se o data tem os valores corretos
            $correctTitle = $data['title'] ?? null;
            $correctMessage = $data['message'] ?? null;
            
            // Se tiver os valores corretos no data, atualizar os campos title e message
            if ($correctTitle || $correctMessage) {
                $updateData = [];
                
                if ($correctTitle && ($notification->title === 'Notificação' || empty($notification->title))) {
                    $updateData['title'] = $correctTitle;
                }
                
                if ($correctMessage && ($notification->message === 'Nova notificação' || empty($notification->message))) {
                    $updateData['message'] = $correctMessage;
                }
                
                if (!empty($updateData)) {
                    $notification->update($updateData);
                    $fixed++;
                    $titleLog = isset($updateData['title']) ? $updateData['title'] : 'não alterado';
                    $messageLog = isset($updateData['message']) ? $updateData['message'] : 'não alterado';
                    $this->command->info("Corrigido ID {$notification->id}: title={$titleLog}, message={$messageLog}");
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
                $this->command->warn("Notificação ID {$notification->id} não tem title/message no data, pulando...");
            }
        }

        $this->command->info("Correção concluída! {$fixed} notificações corrigidas, {$skipped} ignoradas.");
    }
}
