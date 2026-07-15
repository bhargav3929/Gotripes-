<?php

namespace App\Http\Controllers;

use App\Models\UmrahPackage;
use App\Models\UmrahDeparture;
use App\Models\UmrahBooking;
use App\Models\SaudiVisaType;
use App\Models\NomodTransaction;
use App\Services\NomodService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UmrahController extends Controller
{
    public function index()
    {
        $packages = UmrahPackage::with('umrahCategory')->where('isActive', 1)
            ->orderBy('sortOrder', 'asc')
            ->get();
            
        $packagesBus = $packages->where('category', 'bus');
        $packagesAir = $packages->where('category', 'air');

        $saudiVisas = SaudiVisaType::where('isActive', 1)->get();
        $categories = \App\Models\UmrahCategory::where('isActive', 1)->get();

        return view('umrah-visas', compact('packagesBus', 'packagesAir', 'saudiVisas', 'categories'));
    }

    public function show($id)
    {
        $package = UmrahPackage::where('isActive', 1)->findOrFail($id);

        // Fetch active departures for this package that are Wednesdays, at least 5 days in the future, and have seats left
        $minDate = now()->addDays(5)->toDateString();
        $departures = $package->departures()
            ->where('status', 'available')
            ->where('departure_date', '>=', $minDate)
            ->get()
            ->filter(function ($dep) {
                // Ensure date is indeed a Wednesday (safety check)
                return date('w', strtotime($dep->departure_date)) == 3;
            })
            ->values();

        $relatedPackages = UmrahPackage::where('isActive', 1)
            ->where('id', '!=', $id)
            ->orderBy('sortOrder', 'asc')
            ->take(3)
            ->get();

        return view('umrah-details', compact('package', 'departures', 'relatedPackages'));
    }

    public function checkout(Request $request, $id)
    {
        $package = UmrahPackage::where('isActive', 1)->findOrFail($id);

        $validated = $request->validate([
            'departure_date'     => 'required|date',
            'adults'             => 'required|integer|min:1|max:20',
            'children'           => 'required|integer|min:0|max:20',
            'infants'            => 'required|integer|min:0|max:20',
            'payment_gateway'    => 'required|string|in:Card,Tabby,Tamara',
            'customer_name'      => 'required|string|max:255',
            'customer_email'     => 'required|email|max:255',
            'customer_phone'     => 'required|string|max:30',
            'passenger_details'  => 'required|array',
            'passenger_details.*.name'          => 'required|string|max:255',
            'passenger_details.*.passport_no'   => 'required|string|max:50',
            'passenger_details.*.dob'           => 'required|date',
        ]);

        $departureDate = $validated['departure_date'];
        $totalPassengers = $validated['adults'] + $validated['children'] + $validated['infants'];

        // Enforce Wednesday rule
        if (date('w', strtotime($departureDate)) != 3) {
            return response()->json(['success' => false, 'error' => 'Bus departures are only available on Wednesdays.'], 422);
        }

        // Enforce 5 days in advance rule
        if (strtotime($departureDate) < strtotime('+5 days')) {
            return response()->json(['success' => false, 'error' => 'Bookings must be made at least 5 days prior to departure.'], 422);
        }

        // Check departures availability in DB
        $departure = $package->departures()
            ->whereDate('departure_date', $departureDate)
            ->where('status', 'available')
            ->first();

        if (!$departure) {
            return response()->json(['success' => false, 'error' => 'The selected departure date is unavailable.'], 422);
        }

        if (($departure->seats_available - $departure->seats_booked) < $totalPassengers) {
            return response()->json(['success' => false, 'error' => 'Not enough seats available on this departure date.'], 422);
        }

        // Calculate total price based on individual passenger category pricing
        $totalPrice = ($validated['adults'] * $package->priceFor('adult'))
                    + ($validated['children'] * $package->priceFor('child'))
                    + ($validated['infants'] * $package->priceFor('infant'));

        // Generate Order ID (starts with ORDUMB to track Umrah Bus Package)
        $orderId = 'ORDUMB-' . time() . '-' . Str::random(4);

        try {
            // Create a pending booking
            $booking = UmrahBooking::create([
                'company_id'         => current_company()?->id,
                'umrah_package_id'   => $package->id,
                'departure_date'     => $departureDate,
                'adults'             => $validated['adults'],
                'children'           => $validated['children'],
                'infants'            => $validated['infants'],
                'payment_gateway'    => $validated['payment_gateway'],
                'installment_months' => $validated['payment_gateway'] !== 'Card' ? 4 : null,
                'total_price'        => $totalPrice,
                'customer_name'      => $validated['customer_name'],
                'customer_email'     => $validated['customer_email'],
                'customer_phone'     => $validated['customer_phone'],
                'passenger_details'  => $validated['passenger_details'],
                'payment_status'     => 'pending',
                'order_id'           => $orderId,
            ]);

            // Call Nomod hosted checkout
            $nomodService = new NomodService();
            $checkout = $nomodService->createCheckout([
                'amount' => $totalPrice,
                'currency' => 'AED',
                'order_id' => $orderId,
                'description' => "Umrah Bus Package: {$package->title} ({$totalPassengers} Pax)",
                'customer' => [
                    'email' => $validated['customer_email'],
                    'phone' => $validated['customer_phone'],
                ],
            ]);

            if (!($checkout['success'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'error' => $checkout['error'] ?? 'Payment gateway checkout initialization failed.',
                ], 500);
            }

            // Save to transaction table
            NomodTransaction::create([
                'company_id'   => current_company()?->id,
                'checkout_id'  => $checkout['checkout_id'],
                'order_id'     => $orderId,
                'status'       => 'created',
                'amount'       => $totalPrice,
                'currency'     => 'AED',
                'booking_type' => 'umrah',
                'checkout_url' => $checkout['checkout_url'],
                'metadata'     => [
                    'booking_id'   => $booking->id,
                    'package_name' => $package->title,
                    'gateway'      => $validated['payment_gateway'],
                ],
                'response_data'=> $checkout['data'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'checkout_url' => $checkout['checkout_url'],
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Umrah checkout failed', [
                'error' => $e->getMessage(),
                'package_id' => $package->id,
                'total_price' => $totalPrice,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Checkout failed. Please try again later.',
            ], 500);
        }
    }
}
