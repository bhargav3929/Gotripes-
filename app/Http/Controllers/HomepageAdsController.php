<?php

namespace App\Http\Controllers;

use App\Models\HomepageAd;
use App\Models\TravelPackage;

class HomepageAdsController extends Controller
{
    public function showCarousel()
    {
        $adSlots = HomepageAd::getGroupedBySlot(5);
        // Keep backward compat: flatten for any old usage
        $carouselImages = HomepageAd::where('isActive', true)
            ->orderBy('slotOrder', 'asc')
            ->orderBy('displayOrder', 'asc')
            ->get();
        return view('banner', compact('carouselImages', 'adSlots'));
    }

    public function index()
    {
        $adSlots = HomepageAd::getGroupedBySlot(5);
        $carouselImages = HomepageAd::where('isActive', true)
            ->orderBy('slotOrder', 'asc')
            ->orderBy('displayOrder', 'asc')
            ->get();
        // Bookable tour packages first, so a visitor can go straight into the wizard.
        $tourPackages = TravelPackage::where('isActive', 1)
            ->with(['rates', 'landRates', 'hotels'])
            ->orderByRaw("CASE WHEN package_type = 'wizard' THEN 0 WHEN package_type = 'purchase' THEN 1 ELSE 2 END")
            ->orderByDesc('createdDate')
            ->take(8)
            ->get()
            ->sortBy(fn ($p) => $p->isBookableOnline() ? 0 : 1)
            ->values();

        return view('welcome', compact('carouselImages', 'adSlots', 'tourPackages'));
    }
}
