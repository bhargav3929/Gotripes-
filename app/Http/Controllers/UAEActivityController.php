<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UAEActivity;

class UAEActivityController extends Controller
{
    /**
     * Display a listing of UAE activities.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all active activities from the database
        $activities = UAEActivity::where('isActive', 1)->get();

        // Pass activities to the view
        return view('uaeactivities', compact('activities'));
    }
}
