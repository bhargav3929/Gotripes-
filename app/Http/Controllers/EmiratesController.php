<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emirates;
use App\Models\UAEActivity;

class EmiratesController extends Controller
{
    /**
     * Display Emirates selection page or activities for specific emirate
     */
    public function index(Request $request)
    {
        $emiratesID = $request->get('emiratesID');

        if ($emiratesID) {
            // Redirect old ?emiratesID= URLs to clean /activities/slug URLs
            $emirate = Emirates::where('emiratesID', $emiratesID)
                              ->where('isActive', 1)
                              ->first();

            if ($emirate) {
                return redirect()->route('emirates.show', [
                    'slug' => \Illuminate\Support\Str::slug($emirate->emiratesName)
                ], 301);
            }

            abort(404);
        } else {
            // Show all emirates selection
            $emirates = Emirates::getEmiratesWithActivityCount();
            $emirate = null; // Not needed when showing emirates list
            $activities = collect(); // Empty collection
            
            return view('Emirates', compact('emirates', 'emirate', 'activities'));
        }
    }

    /**
     * Display activities for a specific emirate using URL slug
     * e.g. /activities/abu-dhabi, /activities/dubai
     */
    public function showBySlug($slug)
    {
        // Convert slug back to name: "abu-dhabi" → "Abu Dhabi"
        $name = str_replace('-', ' ', $slug);

        $emirate = Emirates::where('isActive', 1)
                          ->whereRaw('LOWER(emiratesName) = ?', [strtolower($name)])
                          ->firstOrFail();

        $activities = UAEActivity::with('emirate')
                                ->where('emiratesID', $emirate->emiratesID)
                                ->where('isActive', 1)
                                ->orderBy('createdDate', 'DESC')
                                ->get();

        $emirates = null;

        return view('Emirates', compact('emirate', 'activities', 'emirates'));
    }

    /**
     * API endpoint to get emirates for select dropdown
     */
    public function getEmiratesJson()
    {
        $emirates = Emirates::where('isActive', 1)
                           ->select('emiratesID', 'emiratesName')
                           ->orderBy('emiratesName')
                           ->get();
        
        return response()->json($emirates);
    }
}
