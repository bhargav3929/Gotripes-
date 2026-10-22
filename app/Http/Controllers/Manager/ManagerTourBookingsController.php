<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\TourPackageBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Tour package bookings made through the wizard (tenant-scoped). */
class ManagerTourBookingsController extends Controller
{
    public function index(Request $request): View
    {
        $query = TourPackageBooking::with('package')->latest('id');
        if (in_array($request->query('payment'), ['pending', 'paid', 'failed', 'cancelled'], true)) {
            $query->where('payment_status', $request->query('payment'));
        }
        return view('manager.tour-bookings.index', [
            'bookings' => $query->paginate(20)->withQueryString(),
            'counts'   => TourPackageBooking::selectRaw('payment_status, count(*) n')->groupBy('payment_status')->pluck('n', 'payment_status'),
        ]);
    }

    public function show(int $id): View
    {
        return view('manager.tour-bookings.show', ['booking' => TourPackageBooking::with('package')->findOrFail($id)]);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $booking = TourPackageBooking::findOrFail($id);
        $data = $request->validate(['status' => 'required|in:new,confirmed,cancelled']);
        $booking->update($data);
        return back()->with('success', 'Booking ' . $booking->order_id . ' marked ' . $data['status'] . '.');
    }
}
