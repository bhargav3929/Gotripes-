<?php

namespace App\Http\Controllers;

use App\Models\DestinationVisaRule;
use App\Models\NomodTransaction;
use App\Models\TourPackageBooking;
use App\Models\TravelPackage;
use App\Services\NomodService;
use App\Services\TourPackages\TourPackageQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * The customer side of the tour package wizard: the 6-step page, live
 * quotes, the visa check and checkout. Every amount comes from
 * TourPackageQuote on the server; the browser only sends choices.
 */
class TourPackageBookingController extends Controller
{
    private function package(int $id): TravelPackage
    {
        $package = TravelPackage::where('isActive', 1)->where('package_type', 'wizard')->findOrFail($id);
        $package->load(['rates', 'hotels', 'addons', 'itineraryDays', 'seasons', 'images']);
        abort_if($package->rates->isEmpty(), 404);
        return $package;
    }

    public function show(int $id): View
    {
        $package = $this->package($id);

        return view('tour-package-book', [
            'package'     => $package,
            'durations'   => $package->rates->pluck('days')->unique()->sort()->values(),
            'tiers'       => $package->rates->groupBy('days')->map(fn ($r) => $r->pluck('hotel_stars')->unique()->sort()->values()),
            'countries'   => array_keys(config('countries')),
            'fromPrice'   => $package->wizardFromPrice(),
        ]);
    }

    public function quote(Request $request, int $id): JsonResponse
    {
        $result = TourPackageQuote::make($this->package($id), $request->all());
        if (isset($result['quote'])) {
            unset($result['quote']['net_lines'], $result['quote']['net_total']);
        }
        return response()->json($result, $result['ok'] ? 200 : 422);
    }

    public function visa(Request $request, int $id): JsonResponse
    {
        $package = $this->package($id);
        $nationality = (string) $request->query('nationality', '');
        $rule = $nationality !== '' ? DestinationVisaRule::lookup((string) $package->country, $nationality) : null;
        return response()->json(TourPackageQuote::visaPayload($rule));
    }

    public function book(Request $request, int $id): JsonResponse
    {
        $package = $this->package($id);

        $customer = $request->validate([
            'customer_name'    => 'required|string|max:120',
            'customer_email'   => 'required|email|max:190',
            'customer_phone'   => ['required', 'string', 'max:30', 'regex:/^\+?[0-9 ()\-]{7,30}$/'],
            'nationality'      => 'required|string|in:' . implode(',', array_keys(config('countries'))),
            'special_requests' => 'nullable|string|max:1000',
            'accept_terms'     => 'accepted',
        ], [
            'accept_terms.accepted' => 'Please accept the booking terms.',
            'customer_phone.regex'  => 'Enter a phone number with country code, like +971 50 123 4567.',
        ]);

        $result = TourPackageQuote::make($package, $request->all());
        if (!$result['ok']) {
            return response()->json(['ok' => false, 'errors' => $result['errors'],
                'message' => 'Some choices need attention: ' . implode(' ', $result['errors'])], 422);
        }
        $q = $result['quote'];

        $booking = DB::transaction(function () use ($package, $customer, $q) {
            $booking = TourPackageBooking::create([
                'company_id'       => $package->company_id,
                'package_id'       => $package->id,
                'customer_name'    => $customer['customer_name'],
                'customer_email'   => $customer['customer_email'],
                'customer_phone'   => $customer['customer_phone'],
                'nationality'      => $customer['nationality'],
                'special_requests' => $customer['special_requests'] ?? null,
                'start_date'       => $q['start_date'],
                'end_date'         => $q['end_date'],
                'days'             => $q['days'],
                'hotel_stars'      => $q['hotel_stars'],
                'traveler_type'    => $q['traveler_type'],
                'adults'           => $q['adults'],
                'children'         => $q['children'],
                'infants'          => $q['infants'],
                'child_ages'       => $q['child_ages'],
                'selections'       => $q['selections'],
                'price_breakdown'  => ['lines' => $q['net_lines'], 'hotel' => $q['hotel'], 'season' => $q['season'],
                                       'margin_percent' => (float) $package->margin_percent],
                'net_total'        => $q['net_total'],
                'total'            => $q['total'],
                'currency'         => $q['currency'],
                'visa_requirement' => $q['visa']['requirement'] ?? null,
            ]);
            $booking->update(['order_id' => TourPackageBooking::ORDER_PREFIX . $booking->id]);
            return $booking;
        });

        try {
            $checkout = (new NomodService())->createCheckout([
                'amount'      => $booking->total,
                'currency'    => $booking->currency,
                'order_id'    => $booking->order_id,
                'description' => $package->title . ' (' . $booking->days . ' days, ' . $booking->travellerSummary() . ')',
                'customer'    => ['name' => $booking->customer_name, 'email' => $booking->customer_email, 'phone' => $booking->customer_phone],
            ]);
        } catch (\Throwable $e) {
            Log::error('Tour package checkout failed', ['booking' => $booking->id, 'error' => $e->getMessage()]);
            $checkout = ['success' => false];
        }

        if (!($checkout['success'] ?? false)) {
            $booking->update(['payment_status' => 'failed']);
            return response()->json(['ok' => false, 'message' => 'The payment page could not be opened. Please try again in a moment.'], 502);
        }

        NomodTransaction::create([
            'company_id'    => $booking->company_id,
            'checkout_id'   => $checkout['checkout_id'],
            'order_id'      => $booking->order_id,
            'status'        => 'created',
            'amount'        => $booking->total,
            'currency'      => $booking->currency,
            'booking_type'  => 'tour_package',
            'checkout_url'  => $checkout['checkout_url'],
            'metadata'      => ['package_name' => $package->title, 'booking_id' => $booking->id],
            'response_data' => $checkout['data'] ?? null,
        ]);

        return response()->json(['ok' => true, 'checkout_url' => $checkout['checkout_url'], 'order_id' => $booking->order_id]);
    }
}
