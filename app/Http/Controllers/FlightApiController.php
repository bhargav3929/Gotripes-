<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use App\Models\NomodTransaction;
use App\Services\NexusApiService;
use App\Services\NomodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Backend API for Farenexus nexusAPI flight bookings.
 *
 * Drives the full lifecycle and receives all storefront data:
 *   search -> price -> (rules/seatmap/ancillaries) -> book -> pay -> ticket
 *   plus retrieve / cancel / void / refund.
 *
 * All responses are JSON in a uniform { success, ... } shape. The service
 * layer ({@see NexusApiService}) owns auth and HTTP; this controller owns
 * validation, persistence ({@see FlightBooking}) and payment orchestration.
 */
class FlightApiController extends Controller
{
    public function __construct(protected NexusApiService $nexus)
    {
    }

    /* ---------------------------------------------------------------- Shopping */

    /** POST /api/flights/search */
    public function search(Request $request)
    {
        $data = $request->validate([
            'trip_type'              => 'required|in:oneway,return,multicity',
            'segments'               => 'required|array|min:1',
            'segments.*.origin'      => 'required|string|size:3',
            'segments.*.destination' => 'required|string|size:3',
            'segments.*.date'        => 'required|date',
            'adults'                 => 'required|integer|min:1|max:9',
            'children'               => 'nullable|integer|min:0|max:9',
            'infants'                => 'nullable|integer|min:0|max:9',
            'cabin'                  => 'nullable|in:economy,premium,business,first',
            'currency'               => 'nullable|string|size:3',
            'direct_only'            => 'nullable|boolean',
            'airlines'               => 'nullable|array',
        ]);

        $data['currency'] ??= config('nexusapi.default_currency', 'AED');

        $result = $this->nexus->search($data);

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /** POST /api/flights/price — revalidate a selected offer */
    public function price(Request $request)
    {
        $data = $request->validate([
            'offer_id' => 'required|string',
        ]);

        $result = $this->nexus->price($data['offer_id'], $request->except('offer_id'));

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /** POST /api/flights/fare-rules */
    public function fareRules(Request $request)
    {
        $data = $request->validate(['offer_id' => 'required|string']);
        $result = $this->nexus->fareRules($data['offer_id']);

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /** POST /api/flights/seatmap */
    public function seatMap(Request $request)
    {
        $data = $request->validate(['offer_id' => 'required|string']);
        $result = $this->nexus->seatMap($data['offer_id']);

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /** POST /api/flights/ancillaries */
    public function ancillaries(Request $request)
    {
        $data = $request->validate(['offer_id' => 'required|string']);
        $result = $this->nexus->ancillaries($data['offer_id']);

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /* ---------------------------------------------------------------- Booking */

    /**
     * POST /api/flights/book — create the PNR (un-ticketed) and persist it.
     */
    public function book(Request $request)
    {
        $data = $request->validate([
            'offer_id'                    => 'required|string',
            'trip_type'                   => 'nullable|in:oneway,return,multicity',
            'branch'                      => 'nullable|string',
            'passengers'                  => 'required|array|min:1',
            'passengers.*.type'           => 'required|in:ADT,CHD,INF',
            'passengers.*.title'          => 'nullable|string|max:10',
            'passengers.*.first_name'     => 'required|string|max:100',
            'passengers.*.last_name'      => 'required|string|max:100',
            'passengers.*.dob'            => 'nullable|date',
            'passengers.*.gender'         => 'nullable|in:M,F',
            'passengers.*.nationality'    => 'nullable|string|size:2',
            'passengers.*.passport'       => 'nullable|array',
            'contact.email'               => 'required|email',
            'contact.phone'               => 'required|string|max:20',
            'currency'                    => 'nullable|string|size:3',
            'net_amount'                  => 'nullable|numeric',
            'amount'                      => 'required|numeric',
            'ancillaries'                 => 'nullable|array',
            'origin'                      => 'nullable|string|size:3',
            'destination'                 => 'nullable|string|size:3',
            'departure_date'              => 'nullable|date',
            'return_date'                 => 'nullable|date',
            'cabin'                       => 'nullable|string',
        ]);

        $currency = $data['currency'] ?? config('nexusapi.default_currency', 'AED');
        $orderId  = 'ORDFLT-' . strtoupper(uniqid());

        // Call nexusAPI to create the PNR.
        $result = $this->nexus->book([
            'offer_id'    => $data['offer_id'],
            'passengers'  => $data['passengers'],
            'contact'     => $data['contact'],
            'ancillaries' => $data['ancillaries'] ?? [],
            'gds_context' => $this->nexus->gdsContext($data['branch'] ?? null),
        ]);

        // Persist regardless of outcome (audit trail).
        $booking = FlightBooking::create([
            'order_id'          => $orderId,
            'offer_id'          => $data['offer_id'],
            'pnr'               => $result['pnr'] ?? null,
            'booking_reference' => $result['booking_ref'] ?? null,
            'status'            => $result['success'] ? ($result['status'] ?? 'booked') : 'failed',
            'trip_type'         => $data['trip_type'] ?? null,
            'gds_provider'      => config('nexusapi.gds.provider', '1G'),
            'branch'            => $data['branch'] ?? config('nexusapi.default_branch'),
            'origin'            => $data['origin'] ?? null,
            'destination'       => $data['destination'] ?? null,
            'departure_date'    => $data['departure_date'] ?? null,
            'return_date'       => $data['return_date'] ?? null,
            'cabin'             => $data['cabin'] ?? null,
            'adults'            => collect($data['passengers'])->where('type', 'ADT')->count(),
            'children'          => collect($data['passengers'])->where('type', 'CHD')->count(),
            'infants'           => collect($data['passengers'])->where('type', 'INF')->count(),
            'currency'          => $currency,
            'net_amount'        => $data['net_amount'] ?? null,
            'amount'            => $data['amount'],
            'ticket_time_limit' => $result['ticket_time_limit'] ?? null,
            'passengers'        => $data['passengers'],
            'contact'           => $data['contact'],
            'booking_response'  => $result['data'] ?? null,
        ]);

        if (!$result['success']) {
            return response()->json([
                'success'  => false,
                'message'  => $result['error'] ?? 'Booking failed.',
                'order_id' => $orderId,
            ], 502);
        }

        return response()->json([
            'success'           => true,
            'order_id'          => $orderId,
            'pnr'               => $booking->pnr,
            'booking_reference' => $booking->booking_reference,
            'ticket_time_limit' => $booking->ticket_time_limit,
            'amount'            => $booking->amount,
            'currency'          => $booking->currency,
        ]);
    }

    /**
     * POST /api/flights/checkout — create a Nomod hosted payment for a booking.
     * Ticketing happens after payment confirmation (see ticket()).
     */
    public function checkout(Request $request)
    {
        $data = $request->validate(['order_id' => 'required|string']);

        $booking = FlightBooking::where('order_id', $data['order_id'])->firstOrFail();

        $nomod = new NomodService();
        $checkout = $nomod->createCheckout([
            'amount'      => (float) $booking->amount,
            'currency'    => $booking->currency,
            'order_id'    => $booking->order_id,
            'description' => 'Flight booking ' . ($booking->pnr ?? $booking->order_id),
            'customer'    => [
                'email' => $booking->contact['email'] ?? '',
                'phone' => $booking->contact['phone'] ?? '',
            ],
            'metadata'    => ['booking_type' => 'flight', 'pnr' => $booking->pnr],
        ]);

        if (!$checkout['success']) {
            return response()->json([
                'success' => false,
                'message' => $checkout['error'] ?? 'Payment initiation failed.',
            ], 502);
        }

        NomodTransaction::create([
            'checkout_id'   => $checkout['checkout_id'],
            'order_id'      => $booking->order_id,
            'status'        => 'created',
            'amount'        => $booking->amount,
            'currency'      => $booking->currency,
            'booking_type'  => 'flight',
            'checkout_url'  => $checkout['checkout_url'],
            'customer'      => $booking->contact,
            'response_data' => $checkout['data'] ?? null,
        ]);

        return response()->json([
            'success'      => true,
            'checkout_url' => $checkout['checkout_url'],
            'order_id'     => $booking->order_id,
        ]);
    }

    /**
     * POST /api/flights/ticket — issue the ticket after payment is captured.
     */
    public function ticket(Request $request)
    {
        $data = $request->validate(['order_id' => 'required|string']);

        $booking = FlightBooking::where('order_id', $data['order_id'])->firstOrFail();

        if (!$booking->booking_reference) {
            return response()->json(['success' => false, 'message' => 'Booking has no reference to ticket.'], 422);
        }

        $result = $this->nexus->ticket($booking->booking_reference);

        if ($result['success']) {
            $booking->update([
                'status'          => 'ticketed',
                'ticket_numbers'  => $result['ticket_numbers'] ?? [],
                'ticket_response' => $result['data'] ?? null,
            ]);
        } else {
            Log::error('nexusAPI ticketing failed', ['order_id' => $booking->order_id, 'error' => $result['error'] ?? null]);
        }

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /* ---------------------------------------------------------------- Post-booking */

    /** GET /api/flights/booking/{orderId} */
    public function show(string $orderId)
    {
        $booking = FlightBooking::where('order_id', $orderId)->firstOrFail();

        // Refresh from provider when we have a reference.
        $live = $booking->booking_reference
            ? $this->nexus->retrieve($booking->booking_reference)
            : ['success' => true, 'data' => null];

        return response()->json([
            'success' => true,
            'booking' => $booking,
            'live'    => $live['data'] ?? null,
        ]);
    }

    /** POST /api/flights/cancel */
    public function cancel(Request $request)
    {
        $data = $request->validate(['order_id' => 'required|string']);
        $booking = FlightBooking::where('order_id', $data['order_id'])->firstOrFail();

        $result = $this->nexus->cancel($booking->booking_reference ?? '');

        if ($result['success']) {
            $booking->update(['status' => 'cancelled']);
        }

        return response()->json($result, $result['success'] ? 200 : 502);
    }

    /** POST /api/flights/refund */
    public function refund(Request $request)
    {
        $data = $request->validate(['order_id' => 'required|string']);
        $booking = FlightBooking::where('order_id', $data['order_id'])->firstOrFail();

        $result = $this->nexus->refund($booking->booking_reference ?? '');

        if ($result['success']) {
            $booking->update(['status' => 'refunded']);
        }

        return response()->json($result, $result['success'] ? 200 : 502);
    }
}
