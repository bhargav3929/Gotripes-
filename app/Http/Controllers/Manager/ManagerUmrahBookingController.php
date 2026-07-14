<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\UmrahBooking;
use App\Models\UmrahPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ManagerUmrahBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = UmrahBooking::with('package')
            ->orderBy('created_at', 'desc');

        // Search by customer name or email
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_email', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('order_id', 'like', "%{$s}%");
            });
        }

        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('umrah_package_id', $request->package_id);
        }

        // Filter by departure date
        if ($request->filled('departure_date')) {
            $query->where('departure_date', $request->departure_date);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $bookings = $query->paginate(25)->withQueryString();

        // Stats
        $totalBookings   = UmrahBooking::count();
        $pendingBookings = UmrahBooking::where('payment_status', 'pending')->count();
        $confirmedBookings = UmrahBooking::where('payment_status', 'paid')->count();
        $totalRevenue    = UmrahBooking::where('payment_status', 'paid')->sum('total_price');

        $packages = UmrahPackage::where('isActive', 1)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('manager.umrah-bookings.index', compact(
            'bookings', 'packages',
            'totalBookings', 'pendingBookings', 'confirmedBookings', 'totalRevenue'
        ));
    }

    public function show($id)
    {
        $booking = UmrahBooking::with('package')->findOrFail($id);
        return view('manager.umrah-bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, $id)
    {
        $booking = UmrahBooking::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|string|in:pending,paid,cancelled,refunded',
        ]);

        $booking->update(['payment_status' => $validated['payment_status']]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $validated['payment_status']]);
        }

        return back()->with('success', 'Booking status updated to ' . ucfirst($validated['payment_status']) . '.');
    }

    public function export(Request $request)
    {
        $query = UmrahBooking::with('package')->orderBy('created_at', 'desc');

        if ($request->filled('package_id')) {
            $query->where('umrah_package_id', $request->package_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="umrah-bookings-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, [
                'Order ID', 'Package', 'Departure Date',
                'Customer Name', 'Email', 'Phone',
                'Adults', 'Children', 'Infants',
                'Total Price', 'Payment Gateway', 'Payment Status',
                'Booked On',
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->order_id,
                    $b->package?->title ?? '—',
                    $b->departure_date?->format('d M Y') ?? '—',
                    $b->customer_name,
                    $b->customer_email,
                    $b->customer_phone,
                    $b->adults,
                    $b->children,
                    $b->infants,
                    $b->total_price,
                    $b->payment_gateway,
                    $b->payment_status,
                    $b->created_at?->format('d M Y H:i') ?? '—',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
