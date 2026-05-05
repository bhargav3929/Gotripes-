<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ActivityBooking;
use App\Models\EsimOrder;
use App\Models\NomodTransaction;
use App\Models\UAEActivity;
use App\Models\UAEVApplication;
use Illuminate\Http\Request;

/**
 * Tenant-facing order/booking views.
 *
 * Every query goes through models with the `BelongsToCompany` trait, which
 * applies CompanyScope automatically. We do NOT manually `where('company_id')`
 * — and route-model binding (`{booking}`, `{order}`, `{application}`) returns
 * 404 if the record belongs to a different tenant.
 */
class OrdersController extends Controller
{
    // ─── Activity Bookings ──────────────────────────────────────────
    public function activities(Request $request)
    {
        $bookings = ActivityBooking::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            }))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->date_to,   fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('manager.orders.activities', compact('bookings'));
    }

    public function activityDetail(ActivityBooking $booking)
    {
        $activity = $booking->activityId
            ? UAEActivity::where('activityID', $booking->activityId)->first()
            : null;

        return view('manager.orders.activity-detail', compact('booking', 'activity'));
    }

    // ─── eSIM Orders ────────────────────────────────────────────────
    public function esim(Request $request)
    {
        $orders = EsimOrder::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_email', 'like', "%{$s}%")
                  ->orWhere('order_reference', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('payment_status', $s))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('manager.orders.esim', compact('orders'));
    }

    public function esimDetail(EsimOrder $order)
    {
        return view('manager.orders.esim-detail', compact('order'));
    }

    // ─── Visa Applications ──────────────────────────────────────────
    public function visa(Request $request)
    {
        $applications = UAEVApplication::query()
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('UAEV_first_name', 'like', "%{$s}%")
                  ->orWhere('UAEV_last_name', 'like', "%{$s}%")
                  ->orWhere('UAEV_email', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('UAEV_status', $s))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('manager.orders.visa', compact('applications'));
    }

    public function visaDetail(UAEVApplication $application)
    {
        return view('manager.orders.visa-detail', compact('application'));
    }

    // ─── Flights & Hotels ───────────────────────────────────────────
    public function flightsHotels(Request $request)
    {
        $transactions = NomodTransaction::query()
            ->whereIn('booking_type', ['flight', 'hotel'])
            ->when($request->q, fn ($q, $s) => $q->where(function ($w) use ($s) {
                $w->where('order_id', 'like', "%{$s}%")
                  ->orWhere('checkout_id', 'like', "%{$s}%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type,   fn ($q, $t) => $q->where('booking_type', $t))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('manager.orders.flights-hotels', compact('transactions'));
    }
}
