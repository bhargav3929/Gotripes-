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
        }

        // Country gate: show a country picker first ONLY when activities exist in
        // more than one country. With a single country (e.g. UAE only) keep the
        // original behaviour and go straight to the emirates selection.
        $country   = $request->get('country');
        $countries = \App\Models\UAEActivity::countriesWithActivities();

        if ($countries->count() > 1) {
            // Country picker first.
            if (!$country) {
                return view('activities-countries', compact('countries'));
            }
            // UAE keeps its emirate-based flow; other countries show a flat list
            // of that country's activities (they have no UAE emirate structure).
            if ($country !== 'United Arab Emirates') {
                $activities = \App\Models\UAEActivity::with('emirate')
                    ->where('isActive', 1)
                    ->where('country', $country)
                    ->orderBy('createdDate', 'DESC')
                    ->get();

                return view('activities-by-country', compact('country', 'activities'));
            }
        }

        // Single country (UAE only) OR UAE selected → original emirates selection.
        $emirates   = Emirates::getEmiratesWithActivityCount();
        $emirate    = null;
        $activities = collect();

        return view('Emirates', compact('emirates', 'emirate', 'activities'));
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
