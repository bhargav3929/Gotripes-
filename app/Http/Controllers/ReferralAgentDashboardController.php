<?php

namespace App\Http\Controllers;

use App\Models\ReferralAgent;
use App\Models\ReferralTracking;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReferralAgentDashboardController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::guard('referral_agent')->check()) {
            return redirect()->route('referral.dashboard');
        }
        return view('referral.auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $agent = ReferralAgent::where('email', $credentials['email'])->first();

        if (!$agent) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        if ($agent->status !== 'active') {
            return back()->withErrors(['email' => 'Your account is not active. Please contact support.'])->withInput();
        }

        if (!Hash::check($credentials['password'], $agent->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        Auth::guard('referral_agent')->login($agent, $request->boolean('remember'));

        $agent->update(['last_login_at' => now()]);

        return redirect()->intended(route('referral.dashboard'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('referral_agent')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('referral.login');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $agent = Auth::guard('referral_agent')->user();
        $stats = $this->referralService->getAgentStats($agent);

        // Monthly stats for chart
        $monthlyStats = $this->getMonthlyStats($agent);

        // Recent orders
        $recentOrders = $agent->referralTracking()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('referral.dashboard', compact('agent', 'stats', 'monthlyStats', 'recentOrders'));
    }

    /**
     * Show orders list
     */
    public function orders(Request $request)
    {
        $agent = Auth::guard('referral_agent')->user();

        $query = $agent->referralTracking();

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('referral.orders', compact('agent', 'orders'));
    }

    /**
     * Show earnings breakdown
     */
    public function earnings(Request $request)
    {
        $agent = Auth::guard('referral_agent')->user();

        $period = $request->get('period', 'month');
        $stats = $this->referralService->getAgentStats($agent, $period);

        // Earnings by status
        $earningsByStatus = [
            'pending' => $agent->referralTracking()->pending()->sum('commission_amount'),
            'approved' => $agent->referralTracking()->approved()->sum('commission_amount'),
            'paid' => $agent->referralTracking()->paid()->sum('commission_amount'),
        ];

        // Monthly earnings
        $monthlyEarnings = $this->getMonthlyEarnings($agent);

        return view('referral.earnings', compact('agent', 'stats', 'earningsByStatus', 'monthlyEarnings', 'period'));
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        $agent = Auth::guard('referral_agent')->user();
        return view('referral.profile', compact('agent'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $agent = Auth::guard('referral_agent')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Verify current password if changing password
        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $agent->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $agent->password = Hash::make($validated['new_password']);
        }

        $agent->name = $validated['name'];
        $agent->phone = $validated['phone'] ?? $agent->phone;
        $agent->country = $validated['country'] ?? $agent->country;
        $agent->save();

        return back()->with([
            'message' => 'Profile updated successfully!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Get monthly stats for last 6 months
     */
    protected function getMonthlyStats(ReferralAgent $agent): array
    {
        $stats = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');

            $orders = $agent->referralTracking()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->whereIn('status', ['approved', 'paid']);

            $clicks = $agent->clicks()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);

            $stats[] = [
                'month' => $month,
                'orders' => (clone $orders)->count(),
                'commission' => (clone $orders)->sum('commission_amount'),
                'clicks' => $clicks->count(),
            ];
        }

        return $stats;
    }

    /**
     * Get monthly earnings for last 12 months
     */
    protected function getMonthlyEarnings(ReferralAgent $agent): array
    {
        $earnings = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');

            $amount = $agent->referralTracking()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->whereIn('status', ['approved', 'paid'])
                ->sum('commission_amount');

            $earnings[] = [
                'month' => $month,
                'amount' => (float) $amount,
            ];
        }

        return $earnings;
    }
}
