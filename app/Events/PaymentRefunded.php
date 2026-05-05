<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a previously confirmed payment is refunded or charged back.
 * The ReverseCommission listener flips the matching commission rows to
 * `reversed`, removing them from the tenant's withdrawable balance.
 */
class PaymentRefunded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Model $payable,
        public readonly string $sourceType,
        public readonly ?string $reason = null
    ) {}
}
