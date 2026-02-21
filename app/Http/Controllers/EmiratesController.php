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
        $emiratesID = $request->get('emiratesID'); // Get from query parameter
        
        if ($emiratesID) {
            // Show activities for specific emirate
            $emirate = Emirates::where('emiratesID', $emiratesID)
                              ->where('isActive', 1)
                              ->firstOrFail();
            
            // Get activities for this emirate (🎯 ADDED: sorted by latest created date first)
            $activities = UAEActivity::with('emirate')
                                    ->where('emiratesID', $emiratesID)
                                    ->where('isActive', 1)
                                    ->orderBy('createdDate', 'DESC')  // 🎯 THIS LINE ADDED
                                    ->get();
            
            $emirates = null; // Not needed when showing activities
            
            return view('Emirates', compact('emirate', 'activities', 'emirates'));
        } else {
            // Show all emirates selection
            $emirates = Emirates::getEmiratesWithActivityCount();
            $emirate = null; // Not needed when showing emirates list
            $activities = collect(); // Empty collection
            
            return view('Emirates', compact('emirates', 'emirate', 'activities'));
        }
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
