<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\Message;

class NotificationService
{
    /**
     * Send notification when users match
     */
    public function notifyMatch(UserMatch $match)
    {
        // Notify both users about the match
        Notification::createMatchNotification(
            $match->user1_id,
            $match->user2_id,
            [
                'match_id' => $match->id,
                'compatibility_score' => $match->compatibility_score,
                'match_reason' => $match->match_reason
            ]
        );

        Notification::createMatchNotification(
            $match->user2_id,
            $match->user1_id,
            [
                'match_id' => $match->id,
                'compatibility_score' => $match->compatibility_score,
                'match_reason' => $match->match_reason
            ]
        );
    }

    /**
     * Send notification when someone likes a user
     */
    public function notifyLike(User $liker, User $likedUser)
    {
        Notification::createLikeNotification(
            $likedUser->id,
            $liker->id
        );
    }

    /**
     * Send notification when someone super likes a user
     */
    public function notifySuperLike(User $liker, User $likedUser)
    {
        Notification::createSuperLikeNotification(
            $likedUser->id,
            $liker->id
        );
    }

    /**
     * Send notification when someone sends a message
     */
    public function notifyMessage(Message $message)
    {
        // Only notify if the message is not from the current user
        if ($message->sender_id !== $message->receiver_id) {
            Notification::createMessageNotification(
                $message->receiver_id,
                $message->sender_id,
                Str::limit($message->message, 50)
            );
        }
    }

    /**
     * Send notification when someone views a profile
     */
    public function notifyProfileView(User $viewer, User $profileOwner)
    {
        // Don't notify if user views their own profile
        if ($viewer->id === $profileOwner->id) {
            return;
        }

        // Check if we already sent a notification recently (within 1 hour)
        $recentNotification = Notification::where('user_id', $profileOwner->id)
            ->where('type', 'profile_view')
            ->where('data->viewer_user_id', $viewer->id)
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if (!$recentNotification) {
            Notification::createProfileViewNotification(
                $profileOwner->id,
                $viewer->id
            );
        }
    }

    /**
     * Send notification for new user registration
     */
    public function notifyNewUser(User $user)
    {
        // This could be used to notify admins or send welcome notifications
        Notification::createNotification(
            $user->id,
            'welcome',
            'Bem-vindo ao Amigos Para Sempre!',
            'Sua conta foi criada com sucesso. Complete seu perfil para começar a encontrar pessoas!',
            ['user_id' => $user->id]
        );
    }

    /**
     * Send notification for profile completion
     */
    public function notifyProfileCompletion(User $user)
    {
        Notification::createNotification(
            $user->id,
            'profile_complete',
            'Perfil Completo!',
            'Parabéns! Seu perfil está completo. Agora você pode começar a descobrir pessoas!',
            ['user_id' => $user->id]
        );
    }

    /**
     * Send notification for subscription changes
     */
    public function notifySubscriptionChange(User $user, $subscriptionType)
    {
        $title = $subscriptionType === 'premium' ? 'Upgrade para Premium!' : 'Assinatura Atualizada';
        $message = $subscriptionType === 'premium' 
            ? 'Parabéns! Você agora é um usuário Premium e tem acesso a recursos exclusivos!'
            : 'Sua assinatura foi atualizada com sucesso.';

        Notification::createNotification(
            $user->id,
            'subscription',
            $title,
            $message,
            ['subscription_type' => $subscriptionType]
        );
    }

    /**
     * Send notification for daily matches
     */
    public function notifyDailyMatches(User $user, $matchesCount)
    {
        if ($matchesCount > 0) {
            Notification::createNotification(
                $user->id,
                'daily_matches',
                'Novos Matches Hoje!',
                "Você tem {$matchesCount} novos matches hoje! Veja quem te curtiu.",
                ['matches_count' => $matchesCount]
            );
        }
    }

    /**
     * Send notification for inactive user
     */
    public function notifyInactiveUser(User $user, $daysInactive)
    {
        Notification::createNotification(
            $user->id,
            'inactive',
            'Estamos com saudades!',
            "Faz {$daysInactive} dias que você não acessa o app. Que tal dar uma olhada nos novos matches?",
            ['days_inactive' => $daysInactive]
        );
    }

    /**
     * Clean up old notifications (older than 30 days)
     */
    public function cleanupOldNotifications()
    {
        Notification::where('created_at', '<', now()->subDays(30))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * Get notification statistics for a user
     */
    public function getNotificationStats(User $user)
    {
        return [
            'total' => $user->notifications()->count(),
            'unread' => $user->unread_notifications_count,
            'by_type' => $user->notifications()
                ->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray()
        ];
    }
}
