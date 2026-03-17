<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
   public function select2Search(Request $request)
{
    Log::error('deepak');
    $term = strtolower($request->input('term', ''));
    $cities = json_decode(Storage::get('data/cities.json'), true);
    $airports = json_decode(Storage::get('data/airports.json'), true);

    $results = [];

    foreach ($cities as $city) {
        $name = strtolower($city['name_translations']['en'] ?? $city['name']);
        if (str_contains($name, $term)) {
            $results[] = [
                'id' => $city['code'],
                'text' => "{$city['name_translations']['en']}, {$city['country_code']} ({$city['code']})"
            ];
        }
    }

    foreach ($airports as $airport) {
        $name = strtolower($airport['name'] ?? '');
        if (str_contains($name, $term)) {
            $results[] = [
                'id' => $airport['code'],
                'text' => "{$airport['name']}, {$airport['country_code']} ({$airport['code']})"
            ];
        }
    }

    return response()->json(['results' => array_slice($results, 0, 15)]);
}


}

