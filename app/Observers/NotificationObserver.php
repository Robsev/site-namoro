<?php

namespace App\Observers;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        try {
            $data = $notification->data ?? [];
            
            // Update title and message from data if they are generic/default values
            $needsUpdate = false;
            $updates = [];
            
            if (($notification->title === 'Notificação' || empty($notification->title)) && !empty($data['title'])) {
                $updates['title'] = $data['title'];
                $needsUpdate = true;
            }
            
            if (($notification->message === 'Nova notificação' || empty($notification->message)) && !empty($data['message'])) {
                $updates['message'] = $data['message'];
                $needsUpdate = true;
            }
            
            // Also update type from data if needed
            if (!empty($data['type']) && $notification->type !== $data['type']) {
                $updates['type'] = $data['type'];
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $notification->update($updates);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update notification title/message in observer', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id ?? null
            ]);
        }
    }
}

