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
        $channels = ['database'];
        
        // Adicionar e-mail se o usuário tiver habilitado
        if ($notifiable->email_notifications_enabled && $notifiable->email_new_matches) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.notifications.new_match_subject', ['name' => config('app.name')]))
            ->line(__('messages.notifications.new_match_message', ['name' => $this->matchUser->name]))
            ->line($this->matchData['match_reason'] ?? __('messages.matching.good_match'))
            ->action(__('messages.notifications.view_conversation'), 
                !empty($this->matchData['conversation_id']) 
                    ? route('conversations.show', $this->matchData['conversation_id'])
                    : route('chat.show', $this->matchUser->id))
            ->line(__('messages.notifications.thanks'));
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
            'title' => __('messages.notifications.new_match_title'),
            'message' => __('messages.notifications.new_match_message', ['name' => $this->matchUser->name]),
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
