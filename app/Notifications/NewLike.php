<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLike extends Notification
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
        $channels = ['database'];
        
        // Adicionar e-mail se o usuário tiver habilitado
        if ($notifiable->email_notifications_enabled && $notifiable->email_new_likes) {
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
            ->subject(__('messages.notifications.new_like_subject', ['name' => config('app.name')]))
            ->line(__('messages.notifications.someone_liked_you', ['name' => $this->liker->name]))
            ->line(__('messages.notifications.check_likes_message'))
            ->action(__('messages.notifications.view_who_liked'), route('matching.likes-received'))
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
            'type' => 'new_like',
            'title' => __('messages.notifications.new_like_title'),
            'message' => __('messages.notifications.someone_liked_you', ['name' => $this->liker->name]),
            'data' => [
                'liker_id' => $this->liker->id,
                'liker_name' => $this->liker->name,
                'liker_photo' => $this->liker->profile_photo,
            ],
        ];
    }
}
