<?php

namespace App\Http\Controllers;

use App\Services\GsmTaskApiService;
use Exception;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MyFastwayController extends Controller
{
    public function track(Request $request)
    {
        $trackingNumber = $request->input('tracking_number');

        if (empty($trackingNumber)) {
            return response()->json(['error' => 'Tracking number is required.'], 400);
        }

        $token = $this->getMyFastwayToken();

        if (!$token) {
            return response()->json(['error' => 'Failed to obtain access token.'], 500);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->get("https://api.fastway.org/v1/track/label/{$trackingNumber}");

        if ($response->ok()) {
            return $response->json();
        }

        return response()->json(['error' => 'Failed to retrieve tracking information.'], 500);
    }

    private function getMyFastwayToken()
    {
        $clientId = getenv('MYFASTWAY_CLIENT_ID');
        $clientSecret = getenv('MYFASTWAY_CLIENT_SECRET');
        $scope = getenv('MYFASTWAY_SCOPE');
        $tokenUrl = getenv('MYFASTWAY_TOKEN_URL');

        $response = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => $scope,
        ]);

        if ($response->ok()) {
            return $response->json()['access_token'];
        }

        return null;
    }
}