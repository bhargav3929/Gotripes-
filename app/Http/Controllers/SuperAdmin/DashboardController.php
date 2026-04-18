<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'total_users' => User::whereNotNull('company_id')->count(),
            'total_revenue' => Company::sum('total_revenue'),
            'total_orders' => Company::sum('total_orders'),
            'trial_companies' => Company::where('plan', 'trial')->count(),
            'paid_companies' => Company::whereIn('plan', ['basic', 'pro', 'enterprise'])->count(),
            'expiring_soon' => Company::where('subscription_ends_at', '<=', now()->addDays(7))
                                       ->where('subscription_ends_at', '>', now())
                                       ->count(),
        ];

        $recentCompanies = Company::with('owner')
            ->latest()
            ->take(10)
            ->get();

        $topCompanies = Company::orderBy('total_revenue', 'desc')
            ->take(5)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentCompanies', 'topCompanies'));
    }
}
