<?php

namespace App\Http\Controllers;

use App\Models\HomepageAd;

class HomepageAdsController extends Controller
{
    public function showCarousel()
    {
        $carouselImages = HomepageAd::where('isActive', true)
            ->orderBy('slotOrder', 'asc')
            ->limit(6)
            ->get();
        return view('banner', compact('carouselImages'));
    }

    public function index()
    {
        $carouselImages = HomepageAd::where('isActive', true)
            ->orderBy('slotOrder', 'asc')
            ->limit(6)
            ->get();
        return view('welcome', compact('carouselImages'));
    }
}
