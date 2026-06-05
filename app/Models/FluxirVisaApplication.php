<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * A visa application submitted through the Fluxir e-visa API.
 *
 * Records the Fluxir-side identifiers (person, trip, service application) and
 * the local lifecycle so we can resume, poll status, and reconcile payment.
 */
class FluxirVisaApplication extends Model
{
    use BelongsToCompany;

    protected $table = 'fluxir_visa_applications';

    protected $fillable = [
        'company_id',
        'order_id',                 // internal reference (ORDVISA-...)
        'fluxir_person_id',
        'fluxir_trip_id',
        'fluxir_service_application_id',
        'state',                    // Fluxir state: Draft|ReadyForPayment|InReview|...
        'status',                   // local: draft|awaiting_payment|paid|submitted|failed
        'is_paid',
        'checkout_session_id',      // Stripe cs_* id
        'checkout_url',
        // Traveller snapshot
        'title',
        'first_name',
        'last_name',
        'gender',
        'passport_number',
        'passport_expiry',
        'country_of_issuance',
        'date_of_birth',
        'nationality',
        'email',
        'phone',
        'destination_code',
        'origination_code',
        'arrival_date',
        'departure_date',
        // Money
        'currency',
        'amount',
        // Raw payloads for audit / debugging
        'attachments',
        'items',
        'last_response',
    ];

    protected $casts = [
        'is_paid'         => 'boolean',
        'passport_expiry' => 'date',
        'date_of_birth'   => 'date',
        'arrival_date'    => 'date',
        'departure_date'  => 'date',
        'amount'          => 'decimal:2',
        'attachments'     => 'array',
        'items'           => 'array',
        'last_response'   => 'array',
    ];
}
