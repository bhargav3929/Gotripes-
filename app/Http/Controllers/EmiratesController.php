<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emirates;
use App\Models\UAEActivity;
use Illuminate\Support\Str;

class EmiratesController extends Controller
{
    public function index(Request $request)
    {
        $emiratesID = $request->get('emiratesID');

        if ($emiratesID) {
            $emirate = Emirates::where('emiratesID', $emiratesID)->where('isActive', 1)->first();
            if ($emirate) {
                return redirect()->route('emirates.show', ['slug' => Str::slug($emirate->emiratesName)], 301);
            }
            abort(404);
        }

        $country      = $request->get('country');
        $countries    = UAEActivity::countriesWithActivities();
        $countryCount = $countries->count();

        // ─── 3+ countries: dedicated country-grid picker ───────────────────
        if ($countryCount > 2) {
            if (!$country) {
                return view('activities-countries', compact('countries'));
            }
            if ($country !== 'United Arab Emirates') {
                $activities = UAEActivity::with('emirate')
                    ->where('isActive', 1)
                    ->where('country', $country)
                    ->orderBy('createdDate', 'DESC')
                    ->get();
                return view('activities-by-country', compact('country', 'activities', 'countries'));
            }
            // UAE selected → fall through to Emirates grid
        }

        $emirates   = Emirates::getEmiratesWithActivityCount();
        $emirate    = null;
        $activities = collect();

        // ─── 2 countries: inline tabs ───────────────────────────────────────
        $otherCountry    = null;
        $otherActivities = collect();

        if ($countryCount === 2) {
            $otherData = $countries->first(fn($c) => $c['country'] !== 'United Arab Emirates');
            if ($otherData) {
                $otherCountry    = $otherData['country'];
                $otherActivities = UAEActivity::with('emirate')
                    ->where('isActive', 1)
                    ->where('country', $otherCountry)
                    ->orderBy('createdDate', 'DESC')
                    ->get();
            }
        }

        return view('Emirates', compact('emirates', 'emirate', 'activities', 'otherCountry', 'otherActivities'));
    }

    public function showBySlug($slug)
    {
        $name = str_replace('-', ' ', $slug);

        $emirate = Emirates::where('isActive', 1)
                          ->whereRaw('LOWER(emiratesName) = ?', [strtolower($name)])
                          ->firstOrFail();

        $activities = UAEActivity::with('emirate')
                                ->where('emiratesID', $emirate->emiratesID)
                                ->where('isActive', 1)
                                ->orderBy('createdDate', 'DESC')
                                ->get();

        $emirates        = null;
        $otherCountry    = null;
        $otherActivities = collect();

        return view('Emirates', compact('emirate', 'activities', 'emirates', 'otherCountry', 'otherActivities'));
    }

    public function getEmiratesJson()
    {
        $emirates = Emirates::where('isActive', 1)
                           ->select('emiratesID', 'emiratesName')
                           ->orderBy('emiratesName')
                           ->get();

        return response()->json($emirates);
    }
}
