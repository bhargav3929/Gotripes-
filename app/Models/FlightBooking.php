<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * A flight booking made through Farenexus nexusAPI.
 *
 * Tracks the full lifecycle: searched -> selected -> booked (PNR) ->
 * ticketed -> (cancelled / refunded). Raw provider payloads are retained in
 * JSON columns for audit and post-booking operations.
 */
class FlightBooking extends Model
{
    use BelongsToCompany;

    protected $table = 'flight_bookings';

    protected $fillable = [
        'company_id',
        'order_id',          // internal order reference (e.g. ORDFLT-...)
        'offer_id',          // nexusAPI offer/solution key
        'pnr',               // GDS record locator
        'booking_reference', // nexusAPI booking id
        'status',            // searched|booked|ticketed|cancelled|refunded|failed
        'trip_type',         // oneway|return|multicity
        'gds_provider',      // 1G / 1A / 1S / NDC-xx
        'branch',            // issuing PCC branch key (uae/canada/usa/india)
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'cabin',
        'adults',
        'children',
        'infants',
        'currency',
        'net_amount',        // supplier net fare
        'amount',            // customer-facing total (after markup)
        'ticket_numbers',
        'ticket_time_limit',
        'passengers',        // [] traveller details
        'contact',           // {email, phone}
        'search_request',    // raw search params
        'offer_data',        // priced offer snapshot
        'booking_response',  // raw book() response
        'ticket_response',   // raw ticket() response
        'metadata',
    ];

    protected $casts = [
        'departure_date'   => 'date',
        'return_date'      => 'date',
        'ticket_time_limit'=> 'datetime',
        'adults'           => 'integer',
        'children'         => 'integer',
        'infants'          => 'integer',
        'net_amount'       => 'decimal:2',
        'amount'           => 'decimal:2',
        'ticket_numbers'   => 'array',
        'passengers'       => 'array',
        'contact'          => 'array',
        'search_request'   => 'array',
        'offer_data'       => 'array',
        'booking_response' => 'array',
        'ticket_response'  => 'array',
        'metadata'         => 'array',
    ];
}
