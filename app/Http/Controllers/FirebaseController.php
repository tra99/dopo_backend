<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log; // Include Log facade

class FirebaseController extends Controller
{

    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send a test notification to a fixed FCM token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestNotification()
    {
        // Replace this with your target FCM token.
        $fcmToken = 'c6ZVxE7NIOuhpGLsTJXN3J:APA91bGBBCrS5hbKWUCm9X6SjFMqMM98tuidDIbBB66LUE0jXB3xo8nWIBb_2QxWIK9-s6MOaOEswG0eVjwcv2v5DK2h3YRnKDiqVzb7XrX5L_Hr9nCUtKo';

        // Define the notification title and body.
        $title = 'Holiday';
        $body = 'Today is 7 January';

        // Optional: additional custom data payload.
        $data = [
            'example_key' => 'example_value',
        ];

        // Send the notification using the Firebase service.
        $result = $this->firebaseService->sendNotification($fcmToken, $title, $body, $data);

        return response()->json($result);
    }


    //     private function getFireBaseAccessToken()
//     {
//         $filepath = public_path(env('GOOGLE_CREDENTIALS_PATH'));
//         $scopeUrl = env('FIREBASE_SCOPE_MESSAGE_URL', 'https://www.googleapis.com/auth/firebase.messaging');
//         dd($filepath);
//         $client = new GoogleClient();
//         $client->setAuthConfig($filepath);
//         $client->addScope($scopeUrl);
//         $response = $client->fetchAccessTokenWithAssertion();
//         return $response['access_token'] ?? false;
//     }
}
