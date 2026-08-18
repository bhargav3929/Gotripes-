<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\EsimOrder;
use App\Models\TravelPackage;
use App\Models\UAEActivity;
use Illuminate\Http\Request;

class AgentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $agent = $request->user();

        // Per-service stat cards; null means the service isn't granted and
        // the card is hidden. Content queries are tenant-scoped by
        // CompanyScope and narrowed to this agent's own listings.
        $packageCount = $agent->hasService('tours')
            ? TravelPackage::where('agent_id', $agent->id)->where('isActive', 1)->count()
            : null;

        $activityCount = $agent->hasService('activities')
            ? UAEActivity::where('agent_id', $agent->id)->where('isActive', 1)->count()
            : null;

        $esimOrderCount = $agent->hasService('esim')
            ? EsimOrder::count()
            : null;

        // e-Visa and UAE Visa have no listing of their own to count — they're
        // commission/referral grants, not content an agent creates — so they
        // get a simple info card instead of a stat card. Still need their own
        // flags: without them, an agent granted only one of these two sees
        // the "no services granted" empty state below despite having access.
        $hasEvisa = $agent->hasService('evisa');
        $hasUaeVisa = $agent->hasService('uae_visa');

        return view('agent.dashboard', compact('agent', 'packageCount', 'activityCount', 'esimOrderCount', 'hasEvisa', 'hasUaeVisa'));
    }
}
