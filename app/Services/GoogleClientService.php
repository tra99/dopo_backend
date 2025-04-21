<?php

namespace App\Services;

use Google\Exception;
use Google_Client;

class GoogleClientService
{
    protected $client;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/public/dopo-aa2ab-firebase-adminsdk-fbsvc-877002f9b4.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function getAccessToken()
    {
        $this->client->fetchAccessTokenWithAssertion();
        return $this->client->getAccessToken()['access_token'];
    }

    /**
     * @throws Exception
     */
    public function token()
    {
        $credentialsPath = storage_path('app/public/dopo-aa2ab-firebase-adminsdk-fbsvc-877002f9b4.json'); // Path to your service account file

        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }
}
