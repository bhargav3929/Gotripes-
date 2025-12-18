<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomepageAd;

namespace App\Http\Controllers;

use App\Models\HomepageAd;

class HomepageAdsController extends Controller
{
    public function showCarousel()
    {
        $carouselImages = HomepageAd::where('isActive', true)->get();
        return view('banner', compact('carouselImages'));
    }

    public function index()
{
    $carouselImages = HomepageAd::where('isActive', true)->get();
    return view('welcome', compact('carouselImages'));
}


}
