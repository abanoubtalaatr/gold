<?php

namespace App\Services\Firebase;

use App\Services\Firebase\FirebaseService;

trait PushNotification
{
    public function sendNotification($user, $notification_data)
    {
        $firebase = new FirebaseService();
        return $firebase->sendNotificationToUser($user, $notification_data);
    }

    public function sendNotifications($notification_data, $tokens)
    {
        if (count($tokens) > 0) {
            $firebase = new FirebaseService();
            return $firebase->sendNotificationToTokens($tokens, $notification_data);
        }
        return 0;
    }
}
