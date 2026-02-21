<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Return active announcements as JSON for the news ticker.
     */
    public function index()
    {
        $announcements = Announcement::where('isActive', true)
            ->orderBy('createdDate', 'desc')
            ->limit(10)
            ->get();

        return response()->json($announcements);
    }
}
