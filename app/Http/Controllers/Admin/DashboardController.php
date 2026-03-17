<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_users' => 0,
            'total_activities' => \App\Models\UAEActivity::where('isActive', 1)->count(),
            'total_announcements' => \App\Models\Announcement::where('isActive', 1)->count(),
            'total_travel_packages' => \App\Models\TravelPackage::where('isActive', 1)->count(),
            'total_umrah_packages' => \App\Models\UmrahPackage::where('isActive', 1)->count(),
        ];

        if ($user->isAdmin()) {
            $stats['total_users'] = \App\Models\User::count();
        }

        return view('admin.dashboard', compact('stats'));
    }
}
