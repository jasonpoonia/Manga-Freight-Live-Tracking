<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StarshipitController extends Controller
{
    public function track(Request $request)
    {
        $trackingNumber = $request->input('tracking_number');

        if (empty($trackingNumber)) {
            return response()->json(['error' => 'Tracking number is required.'], 400);
        }

        $apiKey = config('services.starshipit.api_key');
        $subscriptionKey = config('services.starshipit.subscription_key');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'StarShipIT-Api-Key' => $apiKey,
            'Ocp-Apim-Subscription-Key' => $subscriptionKey,
        ])->get('https://api.starshipit.com/api/track', [
            'tracking_number' => $trackingNumber,
        ]);

        if ($response->ok()) {
            return $response->json();
        }

        return response()->json(['error' => 'Failed to retrieve tracking information.'], 500);
    }

    public function getTrackingCredentials()
    {
        return response()->json([
            'starshipit_api_key' => getenv('STARSHIPIT_API_KEY'),
            'starshipit_subscription_key' => getenv('STARSHIPIT_SUBSCRIPTION_KEY'),
        ]);
    }
}
