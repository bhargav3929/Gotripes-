<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiProxyController extends Controller
{
    public function getPrices(Request $request)
    {
        // Get all query parameters from the frontend request
        $query = $request->query();
        
        // External API URL base
        $apiBaseUrl = 'https://api.travelpayouts.com/aviasales/v3/prices_for_dates';

        // Call the external API with the query parameters
        $response = Http::get($apiBaseUrl, $query);

        // Return the response body and status back to the frontend
        return response($response->body(), $response->status())
            ->header('Content-Type', 'application/json');
    }
}
