<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEActivity;

class UAEActivityController extends Controller
{
    public function index()
    {
        $all = UAEActivity::where('isActive', 1)->get();

        // Normalise null/empty country → UAE
        $grouped = $all->groupBy(fn($a) => $a->country ?: 'United Arab Emirates');

        // Sort so UAE appears first, then alphabetical
        $sorted = $grouped->sortKeysUsing(function ($a, $b) {
            if ($a === 'United Arab Emirates') return -1;
            if ($b === 'United Arab Emirates') return 1;
            return strcmp($a, $b);
        });

        $countries    = $sorted->keys()->values()->toArray();
        $countryCount = count($countries);
        $flagMap      = config('countries');

        return view('uaeactivities', compact('sorted', 'countries', 'countryCount', 'flagMap'));
    }
}
