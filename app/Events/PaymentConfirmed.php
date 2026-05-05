<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired after a payment provider confirms a successful charge for any tenant
 * service (activity, eSIM, visa, agent booking, etc.).
 *
 * The RecordCommission listener uses this to write a `tenant_commissions` row.
 * Other listeners (analytics, notifications, etc.) can subscribe later without
 * touching the controllers that confirm payment.
 */
class PaymentConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Model $payable,        // ActivityBooking | EsimOrder | UAEVApplication | AgentBooking
        public readonly int $companyId,        // tenant that owns the booking
        public readonly float $grossAmount,
        public readonly string $currency,      // 'AED', 'USD', etc.
        public readonly string $sourceType,    // mapped via config('commission.eligible_services')
        public readonly ?string $reference = null  // external payment reference, for traceability
    ) {}
}
