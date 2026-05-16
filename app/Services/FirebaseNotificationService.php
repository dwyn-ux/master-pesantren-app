<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $firebaseCredentials = base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase_credentials.json'));

        if (file_exists($firebaseCredentials)) {
            $factory = (new Factory)->withServiceAccount($firebaseCredentials);
            $this->messaging = $factory->createMessaging();
        }
    }

    public function sendToUser($user, $title, $body, $data = [])
    {
        if (!$this->messaging || !$user->fcm_token) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $androidConfig = AndroidConfig::fromArray([
                'notification' => [
                    'channel_id' => 'fcm_default_channel',
                    'sound' => 'default',
                ],
            ]);

            $message = CloudMessage::new()
                ->withToken($user->fcm_token)
                ->withNotification($notification)
                ->withAndroidConfig($androidConfig)
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            \Log::error('Firebase Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
