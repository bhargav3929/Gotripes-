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

        $isUae = fn($c) => strtolower(trim((string) $c)) === 'united arab emirates';

        // ─── 2+ countries, none chosen yet → flag-card picker ───────────────
        // Visitor clicks a flag, which opens that country's dedicated page.
        if ($countryCount >= 2 && !$country) {
            return view('activities-countries', compact('countries'));
        }

        // ─── A specific non-UAE country chosen → its dedicated by-country page
        if ($country && !$isUae($country)) {
            $activities = UAEActivity::publicVisible()
                ->with('emirate')
                ->where('isActive', 1)
                ->where('country', $country)
                ->orderBy('createdDate', 'DESC')
                ->get();
            return view('activities-by-country', compact('country', 'activities', 'countries'));
        }

        // ─── Exactly one country and it is NOT the UAE → straight to its page
        if (!$country && $countryCount === 1 && !$isUae($countries->first()['country'])) {
            $only       = $countries->first()['country'];
            $activities = UAEActivity::publicVisible()
                ->with('emirate')
                ->where('isActive', 1)
                ->where('country', $only)
                ->orderBy('createdDate', 'DESC')
                ->get();
            return view('activities-by-country', ['country' => $only, 'activities' => $activities, 'countries' => $countries]);
        }

        // ─── UAE (default, explicitly chosen, or the only country) → emirates grid
        $emirates   = Emirates::getEmiratesWithActivityCount();
        $emirate    = null;
        $activities = collect();

        return view('Emirates', compact('emirates', 'emirate', 'activities'));
    }

    public function showBySlug($slug)
    {
        $name = str_replace('-', ' ', $slug);

        $emirate = Emirates::where('isActive', 1)
                          ->whereRaw('LOWER(emiratesName) = ?', [strtolower($name)])
                          ->firstOrFail();

        $activities = UAEActivity::publicVisible()
                                ->with('emirate')
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
