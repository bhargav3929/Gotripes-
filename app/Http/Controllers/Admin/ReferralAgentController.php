<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralAgent;
use App\Models\ReferralTracking;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ReferralAgentController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display dashboard with stats
     */
    public function dashboard()
    {
        $stats = $this->referralService->getAdminDashboardStats();
        $topAgents = $this->referralService->getTopAgents(5, 'month');

        $recentOrders = ReferralTracking::with('referralAgent')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.referrals.dashboard', compact('stats', 'topAgents', 'recentOrders'));
    }

    /**
     * Display a listing of agents
     */
    public function index(Request $request)
    {
        $query = ReferralAgent::query();

        // Search
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $agents = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.referrals.agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new agent
     */
    public function create()
    {
        $settings = \App\Models\ReferralSetting::getSettings();
        return view('admin.referrals.agents.create', compact('settings'));
    }

    /**
     * Store a newly created agent
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:referral_agents,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'referral_code' => 'nullable|string|max:50|unique:referral_agents,referral_code|alpha_dash',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:1000',
        ]);

        $settings = \App\Models\ReferralSetting::getSettings();

        // Fall back to global defaults when the admin hasn't overridden them
        if (empty($validated['commission_type'])) {
            $validated['commission_type'] = $settings->commission_type;
        }
        if (!isset($validated['commission_value']) || $validated['commission_value'] === '') {
            $validated['commission_value'] = $settings->commission_value;
        }

        // Generate referral code if not provided
        if (empty($validated['referral_code'])) {
            $validated['referral_code'] = ReferralAgent::generateCodeFromName($validated['name']);
        }

        $validated['password'] = Hash::make($validated['password']);

        ReferralAgent::create($validated);

        return redirect()->route('admin.referrals.agents.index')->with([
            'message' => 'Referral agent created successfully!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified agent
     */
    public function show(ReferralAgent $agent)
    {
        $stats = $this->referralService->getAgentStats($agent);

        $recentOrders = $agent->referralTracking()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentClicks = $agent->clicks()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.referrals.agents.show', compact('agent', 'stats', 'recentOrders', 'recentClicks'));
    }

    /**
     * Show the form for editing the specified agent
     */
    public function edit(ReferralAgent $agent)
    {
        return view('admin.referrals.agents.edit', compact('agent'));
    }

    /**
     * Update the specified agent
     */
    public function update(Request $request, ReferralAgent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('referral_agents')->ignore($agent->id)],
            'password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'referral_code' => ['nullable', 'string', 'max:50', 'alpha_dash', Rule::unique('referral_agents')->ignore($agent->id)],
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $agent->update($validated);

        return redirect()->route('admin.referrals.agents.index')->with([
            'message' => 'Referral agent updated successfully!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified agent
     */
    public function destroy(ReferralAgent $agent)
    {
        $agent->delete();

        return redirect()->route('admin.referrals.agents.index')->with([
            'message' => 'Referral agent deleted successfully!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Toggle agent status
     */
    public function toggleStatus(ReferralAgent $agent)
    {
        $agent->status = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->save();

        return response()->json([
            'success' => true,
            'status' => $agent->status,
            'message' => 'Agent status updated successfully!'
        ]);
    }

    /**
     * Regenerate referral code
     */
    public function regenerateCode(ReferralAgent $agent)
    {
        $agent->referral_code = ReferralAgent::generateUniqueCode();
        $agent->save();

        return response()->json([
            'success' => true,
            'referral_code' => $agent->referral_code,
            'referral_url' => $agent->referral_url,
            'message' => 'Referral code regenerated successfully!'
        ]);
    }
}
