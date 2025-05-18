<?php

namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/json/firebase.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function sendNotificationToTokens(array $tokens, array $notificationData)
    {
        $notification = Notification::create(
            $notificationData['title'] ?? '',
            $notificationData['body'] ?? ''
        );

        $message = CloudMessage::new()
            ->withNotification($notification)
            ->withData($notificationData);

        foreach ($tokens as $token) {
            $this->messaging->sendMulticast($message, [$token]);
        }
    }

    public function sendNotificationToUser($user, array $notificationData)
    {
        $tokens = $user->deviceTokens()->pluck('device_token')->toArray();
        if (!empty($tokens)) {
            $this->sendNotificationToTokens($tokens, $notificationData);
        }
    }
}