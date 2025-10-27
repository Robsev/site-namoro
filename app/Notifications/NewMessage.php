<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
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
        if ($notifiable->email_notifications_enabled && $notifiable->email_new_messages) {
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
            ->subject(__('messages.notifications.new_message_subject', ['name' => $this->message->sender->name]))
            ->line(__('messages.notifications.new_message_from', ['name' => $this->message->sender->name]))
            ->line(__('messages.notifications.message') . ': ' . $this->message->message)
            ->action(__('messages.notifications.view_conversation'), route('conversations.show', $this->message->conversation))
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
            'type' => 'new_message',
            'title' => __('messages.notifications.new_message_title'),
            'message' => __('messages.notifications.new_message_from', ['name' => $this->message->sender->name]),
            'data' => [
                'conversation_id' => $this->message->conversation_id,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $this->message->sender->name,
                'message_preview' => \Str::limit($this->message->message, 50),
            ],
        ];
    }
}