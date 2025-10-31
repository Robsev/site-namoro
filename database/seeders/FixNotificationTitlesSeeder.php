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
                  ->orWhere('title', '')
                  ->orWhereNull('title')
                  ->orWhere('message', 'Nova notificação')
                  ->orWhere('message', '')
                  ->orWhereNull('message');
        })->get();

        $this->command->info("Encontradas {$notifications->count()} notificações para corrigir.");

        $fixed = 0;
        $skipped = 0;

        foreach ($notifications as $notification) {
            $data = $notification->data ?? [];
            
            // Verificar se o data tem os valores corretos
            $correctTitle = $data['title'] ?? null;
            $correctMessage = $data['message'] ?? null;
            $correctType = $data['type'] ?? null;
            
            // Se tiver os valores corretos no data, atualizar os campos
            if ($correctTitle || $correctMessage || $correctType) {
                $updateData = [];
                
                if ($correctTitle && ($notification->title === 'Notificação' || empty($notification->title) || is_null($notification->title))) {
                    $updateData['title'] = $correctTitle;
                }
                
                if ($correctMessage && ($notification->message === 'Nova notificação' || empty($notification->message) || is_null($notification->message))) {
                    $updateData['message'] = $correctMessage;
                }
                
                // Atualizar type se necessário (especialmente para notificações do Laravel como App\Notifications\NewSuperLike)
                if ($correctType && $notification->type !== $correctType) {
                    // Se o type atual é uma classe (App\Notifications\NewSuperLike), substituir pelo tipo do data
                    if (strpos($notification->type, 'App\\Notifications\\') === 0 || strpos($notification->type, 'App/Notifications/') === 0) {
                        $updateData['type'] = $correctType;
                    }
                }
                
                if (!empty($updateData)) {
                    $notification->update($updateData);
                    $fixed++;
                    $changes = [];
                    if (isset($updateData['title'])) $changes[] = "title='{$updateData['title']}'";
                    if (isset($updateData['message'])) $changes[] = "message='{$updateData['message']}'";
                    if (isset($updateData['type'])) $changes[] = "type='{$updateData['type']}'";
                    $this->command->info("Corrigido ID {$notification->id}: " . implode(', ', $changes));
                } else {
                    $skipped++;
                }
            } else {
                $skipped++;
                $this->command->warn("Notificação ID {$notification->id} não tem title/message/type no data, pulando...");
            }
        }

        $this->command->info("Correção concluída! {$fixed} notificações corrigidas, {$skipped} ignoradas.");
    }
}
