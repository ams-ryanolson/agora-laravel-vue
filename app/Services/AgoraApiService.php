<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class AgoraApiService
{
    protected $client;

    public function __construct()
    {
        $uuid = Uuid::uuid4()->toString();
        $customerKey = 'a568a8e4828e4ee390d83a9cbe8264dd';
        $customerSecret = 'df3557d8ce1d40429750202066ca5269';

        $plainCredentials = $customerKey . ':' . $customerSecret;
        $encodedCredentials = base64_encode($plainCredentials);

        $this->client = new Client([
            'base_uri' => 'https://api.agora.io/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $encodedCredentials,
                'X-Request-ID' => $uuid,
            ],
        ]);
    }

    public function createMediaGateway($userId, $region, $appId)
    {
        $response = $this->client->post("{$region}/v1/projects/{$appId}/rtls/ingress/streamkeys", [
            'json' => [
                'settings' => [
                    "channel" => $userId,
                    "uid" => $userId,
                    "expiresAfter" => 30,
                ]
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to create media gateway');
        }

        // Handle the response as needed
        return $response->getBody()->getContents();
    }

//    public function createMediaGateway($userId, $region, $appId)
//    {
//        $uuid = Uuid::uuid4()->toString();
//
//        $response = Http::withHeaders([
//            'Content-Type' => 'application/json',
//            'Authorization' => 'Basic ' . 'YTU2OGE4ZTQ4MjhlNGVlMzkwZDgzYTljYmU4MjY0ZGQ6ZGYzNTU3ZDhjZTFkNDA0Mjk3NTAyMDIwNjZjYTUyNjk=', // Replace 'YOUR_API_KEY_HERE' with your actual API key
//            'X-Request-ID' => $uuid,
//        ])->post("https://api.agora.io/na/v1/projects/57c98d4cccdd40139fa2bafc51704530/rtls/ingress/streamkeys", [
//            'settings' => [
//                'channel' => $userId,
//                'uid' => $userId,
//                'expiresAfter' => 300,
//            ],
//        ]);
//
//        dd($response->getBody()->getContents());
//
//        // Handle the response as needed
//        return $response->getBody()->getContents();
//    }
}
