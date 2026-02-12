<?php

namespace App\Http\Controllers;

use App\Models\HomepageAd;

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
        $adSlots = \App\Models\HomepageAd::getGroupedBySlot(5);
        $carouselImages = \App\Models\HomepageAd::where('isActive', true)
            ->orderBy('slotOrder', 'asc')
            ->orderBy('displayOrder', 'asc')
            ->get();
            
        $tickerItems = \App\Models\Announcement::where('isActive', true)
                      ->orderBy('AnnouncementImportance', 'desc')
                      ->orderBy('createdDate', 'desc')
                      ->get();
                      
        return view('welcome', compact('carouselImages', 'adSlots', 'tickerItems'));
    }
}
