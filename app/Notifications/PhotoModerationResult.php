<?php

namespace App\Notifications;

use App\Models\UserPhoto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PhotoModerationResult extends Notification
{
    use Queueable;

    protected $photo;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserPhoto $photo, string $status)
    {
        $this->photo = $photo;
        $this->status = $status;
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
        $message = new MailMessage();
        
        if ($this->status === 'approved') {
            $message->subject('Foto Aprovada - Amigos Para Sempre')
                    ->line('Sua foto foi aprovada e está agora visível no seu perfil!')
                    ->line('Obrigado por usar o Amigos Para Sempre.');
        } else {
            $message->subject('Foto Rejeitada - Amigos Para Sempre')
                    ->line('Infelizmente, sua foto não foi aprovada.')
                    ->line('Motivo: ' . ($this->photo->moderation_notes ?? 'Não especificado'))
                    ->line('Por favor, envie uma nova foto seguindo nossas diretrizes da comunidade.');
        }
        
        return $message->action('Ver Perfil', route('profile.show'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'photo_moderation',
            'title' => $this->status === 'approved' 
                ? 'Foto Aprovada'
                : 'Foto Rejeitada',
            'message' => $this->status === 'approved' 
                ? 'Sua foto foi aprovada e está visível no seu perfil!'
                : 'Sua foto foi rejeitada. Motivo: ' . ($this->photo->moderation_notes ?? 'Não especificado'),
            'photo_id' => $this->photo->id,
            'status' => $this->status,
        ];
    }
}
