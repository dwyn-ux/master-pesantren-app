<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public function __construct(
        protected FirebaseNotificationService $firebase,
    ) {}

    /**
     * Kirim notifikasi ke 1 user: log ke DB + push FCM (kalau ada token).
     */
    public function send(
        User $user,
        string $type,
        string $title,
        string $body,
        array $data = [],
        ?string $actionUrl = null,
    ): AppNotification {
        $notification = AppNotification::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'data'       => $data,
            'action_url' => $actionUrl,
            'channel'    => 'in_app',
        ]);

        if ($user->fcm_token) {
            $fcmData = array_merge($data, [
                'notification_id' => (string) $notification->id,
                'type'            => $type,
            ]);
            if ($actionUrl) {
                $fcmData['action_url'] = $actionUrl;
            }

            $sent = $this->firebase->sendToUser($user, $title, $body, $fcmData);
            if ($sent) {
                $notification->update([
                    'channel'  => 'fcm',
                    'sent_at'  => now(),
                ]);
            }
        }

        return $notification;
    }

    /**
     * Kirim ke banyak user sekaligus.
     */
    public function sendToMany(
        iterable $users,
        string $type,
        string $title,
        string $body,
        array $data = [],
        ?string $actionUrl = null,
    ): int {
        $count = 0;
        foreach ($users as $user) {
            if ($user instanceof User) {
                $this->send($user, $type, $title, $body, $data, $actionUrl);
                $count++;
            }
        }
        return $count;
    }
}
