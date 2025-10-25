<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMatch extends Notification
{
    use Queueable;

    protected $matchUser;
    protected $matchData;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $matchUser, array $matchData = [])
    {
        $this->matchUser = $matchUser;
        $this->matchData = $matchData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Novo Match! - Amigos Para Sempre')
            ->line("Parabéns! Você tem um novo match com {$this->matchUser->name}!")
            ->line($this->matchData['match_reason'] ?? 'Vocês podem ser uma boa combinação!')
            ->action('Ver Conversa', route('conversations.show', $this->matchData['conversation_id'] ?? ''))
            ->line('Obrigado por usar o Amigos Para Sempre!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_match',
            'title' => '🎉 Novo Match!',
            'message' => "Você tem um novo match com {$this->matchUser->name}!",
            'data' => [
                'match_user_id' => $this->matchUser->id,
                'match_user_name' => $this->matchUser->name,
                'compatibility_score' => $this->matchData['compatibility_score'] ?? null,
                'match_reason' => $this->matchData['match_reason'] ?? null,
                'conversation_id' => $this->matchData['conversation_id'] ?? null,
            ],
        ];
    }
}
