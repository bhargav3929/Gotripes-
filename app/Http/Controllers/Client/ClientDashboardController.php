<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\EsimOrder;
use App\Models\ReferralAgent;
use App\Models\ReferralTracking;
use App\Models\User;
use App\Models\UAEVApplication;
use App\Models\ActivityBooking;
use App\Models\NomodTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClientDashboardController extends Controller
{
    private function safeCount($model)
    {
        try {
            return $model::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeQuery($callback, $default = 0)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function index()
    {
        $company = app('current_company');

        if (!$company) {
            return redirect('/')->with('error', 'Company not found.');
        }

        $stats = [
            // eSIM Stats
            'esim_orders' => $this->safeQuery(fn() => EsimOrder::count()),
            'esim_revenue' => $this->safeQuery(fn() => EsimOrder::where('payment_status', 'paid')->sum('selling_price')),

            // Visa Stats
            'visa_applications' => $this->safeQuery(fn() => UAEVApplication::count()),

            // Activity Stats
            'activity_bookings' => $this->safeQuery(fn() => ActivityBooking::count()),

            // Flight/Hotel Stats
            'flight_hotel_bookings' => $this->safeQuery(fn() => NomodTransaction::where('status', 'completed')->count()),
            'flight_hotel_revenue' => $this->safeQuery(fn() => NomodTransaction::where('status', 'completed')->sum('amount')),

            // Agent Stats
            'total_agents' => $this->safeQuery(fn() => ReferralAgent::count()),
            'pending_commissions' => $this->safeQuery(fn() => ReferralTracking::where('status', 'pending')->sum('commission_amount')),

            // Today Stats
            'today_orders' => $this->safeQuery(fn() => EsimOrder::whereDate('created_at', today())->count()),
            'today_revenue' => $this->safeQuery(fn() => EsimOrder::whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('selling_price')),
        ];

        $recentOrders = $this->safeQuery(fn() => EsimOrder::orderBy('created_at', 'desc')->limit(5)->get(), collect());

        $recentVisas = $this->safeQuery(fn() => UAEVApplication::orderBy('id', 'desc')->limit(5)->get(), collect());

        $recentActivities = $this->safeQuery(fn() => ActivityBooking::orderBy('id', 'desc')->limit(5)->get(), collect());

        $topAgents = $this->safeQuery(fn() => ReferralAgent::orderByDesc('total_earnings')->limit(5)->get(), collect());

        return view('client.dashboard', compact(
            'company', 'stats', 'recentOrders', 'recentVisas', 'recentActivities', 'topAgents'
        ));
    }

    // eSIM Orders
    public function orders(Request $request)
    {
        $query = EsimOrder::with('referralAgent');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', "%{$request->search}%")
                  ->orWhere('customer_email', 'like', "%{$request->search}%")
                  ->orWhere('order_reference', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('payment_status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('client.orders.index', compact('orders'));
    }

    public function showOrder(EsimOrder $order)
    {
        $order->load('referralAgent');
        return view('client.orders.show', compact('order'));
    }

    // Visa Applications
    public function visaApplications(Request $request)
    {
        try {
            $query = UAEVApplication::query();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('UAEV_first_name', 'like', "%{$request->search}%")
                      ->orWhere('UAEV_last_name', 'like', "%{$request->search}%")
                      ->orWhere('UAEV_email', 'like', "%{$request->search}%");
                });
            }

            $visas = $query->orderBy('id', 'desc')->paginate(20);
        } catch (\Exception $e) {
            $visas = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('client.visa.index', compact('visas'));
    }

    // Activity Bookings
    public function activityBookings(Request $request)
    {
        try {
            $query = ActivityBooking::query();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%");
                });
            }

            $bookings = $query->orderBy('id', 'desc')->paginate(20);
        } catch (\Exception $e) {
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('client.activities.index', compact('bookings'));
    }

    // Flight & Hotel Bookings
    public function flightHotelBookings(Request $request)
    {
        try {
            $query = NomodTransaction::query();

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('order_id', 'like', "%{$request->search}%")
                      ->orWhere('checkout_id', 'like', "%{$request->search}%");
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->type) {
                $query->where('booking_type', $request->type);
            }

            $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        } catch (\Exception $e) {
            $bookings = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('client.flights-hotels.index', compact('bookings'));
    }

    // Branding
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

    // Settings
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

    // Analytics
    public function analytics(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from) : now()->subMonth();
        $to = $request->to ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $stats = [
            'esim_revenue' => $this->safeQuery(fn() => EsimOrder::whereBetween('created_at', [$from, $to])
                ->where('payment_status', 'paid')
                ->sum('selling_price')),
            'esim_orders' => $this->safeQuery(fn() => EsimOrder::whereBetween('created_at', [$from, $to])->count()),
            'visa_applications' => $this->safeQuery(fn() => UAEVApplication::whereBetween('UAEV_created_date', [$from, $to])->count()),
            'activity_bookings' => $this->safeQuery(fn() => ActivityBooking::whereBetween('createDate', [$from, $to])->count()),
            'flight_hotel_revenue' => $this->safeQuery(fn() => NomodTransaction::whereBetween('created_at', [$from, $to])
                ->where('status', 'completed')
                ->sum('amount')),
        ];

        $dailyRevenue = $this->safeQuery(fn() => EsimOrder::selectRaw('DATE(created_at) as date, SUM(selling_price) as revenue, COUNT(*) as orders')
            ->whereBetween('created_at', [$from, $to])
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get(), collect());

        $topCountries = $this->safeQuery(fn() => EsimOrder::selectRaw('country_name, COUNT(*) as count, SUM(selling_price) as revenue')
            ->whereBetween('created_at', [$from, $to])
            ->where('payment_status', 'paid')
            ->groupBy('country_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get(), collect());

        return view('client.analytics', compact('stats', 'dailyRevenue', 'topCountries'));
    }
}
