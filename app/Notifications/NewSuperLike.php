<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSuperLike extends Notification
{
    use Queueable;

    protected $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $liker)
    {
        $this->liker = $liker;
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
            ->subject('⭐ Super Like! - Amigos Para Sempre')
            ->line("{$this->liker->name} te deu um Super Like!")
            ->line('Isso significa que você chamou muito a atenção!')
            ->action('Ver Quem Te Curtiu', route('matching.likes-received'))
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
            'type' => 'new_super_like',
            'title' => '⭐ Super Like!',
            'message' => "{$this->liker->name} te deu um Super Like!",
            'data' => [
                'liker_id' => $this->liker->id,
                'liker_name' => $this->liker->name,
                'liker_photo' => $this->liker->profile_photo,
            ],
        ];
    }
}
