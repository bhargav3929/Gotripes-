<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\EsimOrder;
use App\Models\ReferralAgent;
use App\Models\ReferralTracking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $company = app('current_company');

        if (!$company) {
            return redirect('/')->with('error', 'Company not found.');
        }

        $stats = [
            'total_orders' => EsimOrder::count(),
            'total_revenue' => EsimOrder::where('status', 'completed')->sum('total_amount'),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_agents' => ReferralAgent::count(),
            'pending_commissions' => ReferralTracking::where('status', 'pending')->sum('commission_amount'),
            'today_orders' => EsimOrder::whereDate('created_at', today())->count(),
            'today_revenue' => EsimOrder::whereDate('created_at', today())
                ->where('status', 'completed')
                ->sum('total_amount'),
        ];

        $recentOrders = EsimOrder::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $topAgents = ReferralAgent::orderByDesc('total_earnings')
            ->limit(5)
            ->get();

        return view('client.dashboard', compact('company', 'stats', 'recentOrders', 'topAgents'));
    }

    public function orders(Request $request)
    {
        $query = EsimOrder::with('user', 'referralAgent');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_email', 'like', "%{$request->search}%")
                  ->orWhere('order_reference', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('client.orders.index', compact('orders'));
    }

    public function showOrder(EsimOrder $order)
    {
        $order->load('user', 'referralAgent');
        return view('client.orders.show', compact('order'));
    }

    public function branding()
    {
        $company = app('current_company');
        return view('client.branding', compact('company'));
    }

    public function updateBranding(Request $request)
    {
        $company = app('current_company');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
        ]);

        $company->name = $validated['name'];
        $company->email = $validated['email'];
        $company->phone = $validated['phone'];
        $company->primary_color = $validated['primary_color'];
        $company->secondary_color = $validated['secondary_color'];

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company-logos', 'public');
            $company->logo = $path;
        }

        $company->save();

        return back()->with('success', 'Branding updated successfully.');
    }

    public function settings()
    {
        $company = app('current_company');
        return view('client.settings', compact('company'));
    }

    public function updateSettings(Request $request)
    {
        $company = app('current_company');

        $validated = $request->validate([
            'currency' => 'required|string|max:3',
            'timezone' => 'required|string',
            'markup_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $company->currency = $validated['currency'];
        $company->timezone = $validated['timezone'];
        $company->markup_percentage = $validated['markup_percentage'];
        $company->save();

        return back()->with('success', 'Settings updated successfully.');
    }

    public function analytics(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->subMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $stats = [
            'revenue' => EsimOrder::whereBetween('created_at', [$from, $to])
                ->where('status', 'completed')
                ->sum('total_amount'),
            'orders' => EsimOrder::whereBetween('created_at', [$from, $to])->count(),
            'avg_order_value' => EsimOrder::whereBetween('created_at', [$from, $to])
                ->where('status', 'completed')
                ->avg('total_amount') ?? 0,
            'conversion_rate' => 0,
        ];

        $dailyRevenue = EsimOrder::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topCountries = EsimOrder::selectRaw('country_name, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->groupBy('country_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('client.analytics', compact('stats', 'dailyRevenue', 'topCountries'));
    }
}
