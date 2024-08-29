<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class StarshipitApiService
{
    const API_URL = "https://api.starshipit.com/api";
    static array $endpoints = [
        "track" => "/track",
        // Add other endpoints as needed
    ];
    private string $apiKey;
    private string $subscriptionKey;

    public function __construct()
    {
        $this->apiKey = getenv('STARSHIPIT_API_KEY');
        $this->subscriptionKey = getenv('STARSHIPIT_SUBSCRIPTION_KEY');
    }

    private function getApiResponse($endpoint, $params = [], $method = 'get')
    {
        $client = Http::withHeaders([
            'Content-Type' => 'application/json',
            'StarShipIT-Api-Key' => $this->apiKey,
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ]);

        $response = match ($method) {
            'post' => $client->post(self::API_URL . $endpoint, $params),
            default => $client->get(self::API_URL . $endpoint, $params),
        };

        if ($response->ok()) {
            return $response->json();
        }

        throw new Exception("Failed to retrieve data from Starshipit API.");
    }

    public function getTrackingDetails($trackingNumber = null, $orderNumber = null)
    {
        $endpoint = self::$endpoints['track'];
        $params = [];

        if ($trackingNumber) {
            $params['tracking_number'] = $trackingNumber;
        } elseif ($orderNumber) {
            $params['order_number'] = $orderNumber;
        } else {
            throw new Exception("Either tracking number or order number is required.");
        }

        return $this->getApiResponse($endpoint, $params);
    }
}