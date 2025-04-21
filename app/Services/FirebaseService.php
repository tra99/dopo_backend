<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        // Initialize the Firebase Messaging component
        $this->messaging = (new Factory)
            ->withServiceAccount(storage_path(env('GOOGLE_CREDENTIALS_PATH', 'app/public/dopo-aa2ab-firebase-adminsdk-fbsvc-6c63ebece5.json')))
            ->createMessaging();
    }

    /**
     * Send a notification to the given FCM token.
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array  $data
     *
     * @return mixed
     */
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = [])
    {
        // Create a CloudMessage with a target of type 'token'
        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification(Notification::create($title, $body))
            ->withData($data); // Optional custom data

        // Send the message using the send() method
        return $this->messaging->send($message);
    }
}
