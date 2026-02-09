<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\HomepageAd;
use App\Models\Announcement;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $adCount = HomepageAd::where('isActive', 1)->count();
        $announcementCount = Announcement::where('isActive', 1)->count();

        return view('manager.dashboard', compact('adCount', 'announcementCount'));
    }
}
