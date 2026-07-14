<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ActivityBooking;
use App\Models\Announcement;
use App\Models\EsimOrder;
use App\Models\HomepageAd;
use App\Models\TenantCommission;
use App\Models\UAEActivity;
use App\Models\UAEVApplication;
use App\Models\UmrahPackage;
use App\Models\UmrahDeparture;
use App\Models\UmrahBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = in_array($request->query('period'), ['today', '7d', '30d'], true)
            ? $request->query('period')
            : '30d';

        [$from, $to] = match ($period) {
            'today' => [now()->startOfDay(),    now()->endOfDay()],
            '7d'    => [now()->subDays(7),      now()],
            default => [now()->subDays(30),     now()],
        };

        // ─── Period-filtered analytics (tenant scope auto-applied) ─────
        // Tables have different timestamp column names — these are legacy
        // tables that pre-date Laravel conventions:
        //   activitybookings  → createDate     (no created_at)
        //   esim_orders       → created_at     (Laravel default)
        //   UAEV_application  → UAEV_created_date
        //   tenant_commissions→ created_at     (Laravel default)
        $activityBookings = ActivityBooking::whereBetween('createDate', [$from, $to])->count();
        $esimOrders       = EsimOrder::whereBetween('created_at', [$from, $to])->count();
        $visaApps         = UAEVApplication::whereBetween('UAEV_created_date', [$from, $to])->count();
        $totalBookings    = $activityBookings + $esimOrders + $visaApps;

        $revenue = (float) ActivityBooking::whereBetween('createDate', [$from, $to])->sum('amount')
                 + (float) EsimOrder::whereBetween('created_at', [$from, $to])->sum('selling_price');

        // TenantCommission does not use BelongsToCompany — filter explicitly.
        $commission = (float) TenantCommission::where('company_id', current_company_id())
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('status', ['pending', 'available', 'reserved', 'paid'])
            ->sum('commission_amount');

        // ─── Fixed-window: last 7 days bookings ─────────────────────────
        $last7DaysBookings = ActivityBooking::where('createDate', '>=', now()->subDays(7))->count()
                           + EsimOrder::where('created_at', '>=', now()->subDays(7))->count()
                           + UAEVApplication::where('UAEV_created_date', '>=', now()->subDays(7))->count();

        // ─── Daily commission series for the chart (last 30 days) ──────
        $series = TenantCommission::where('company_id', current_company_id())
            ->where('created_at', '>=', now()->subDays(30))
            ->whereIn('status', ['pending', 'available', 'reserved', 'paid'])
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(commission_amount) as v'))
            ->groupBy('d')->orderBy('d')->get();

        // ─── Existing content cards ─────────────────────────────────────
        $adCount           = HomepageAd::where('isActive', 1)->count();
        $announcementCount = Announcement::where('isActive', 1)->count();
        $activityCount     = UAEActivity::where('isActive', 1)->count();

        // ─── Umrah & Saudi module KPIs ──────────────────────────────────
        $umrahTotalPackages  = UmrahPackage::count();
        $umrahActivePackages = UmrahPackage::where('isActive', 1)->count();
        $umrahUpcomingDeps   = UmrahDeparture::where('departure_date', '>=', now()->toDateString())
                                ->where('status', 'available')->count();
        $umrahTotalBookings  = UmrahBooking::count();
        $umrahAvailSeats     = UmrahDeparture::where('departure_date', '>=', now()->toDateString())
                                ->where('status', 'available')
                                ->selectRaw('SUM(seats_available - seats_booked) as total')
                                ->value('total') ?? 0;

        return view('manager.dashboard', compact(
            'period',
            'totalBookings', 'revenue', 'commission', 'last7DaysBookings',
            'series',
            'adCount', 'announcementCount', 'activityCount',
            'umrahTotalPackages', 'umrahActivePackages', 'umrahUpcomingDeps',
            'umrahTotalBookings', 'umrahAvailSeats'
        ));
    }
}
