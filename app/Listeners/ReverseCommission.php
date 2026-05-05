<?php

namespace App\Listeners;

use App\Events\PaymentRefunded;
use App\Services\CommissionService;
use Illuminate\Support\Facades\Log;

class ReverseCommission
{
    public function __construct(
        protected CommissionService $commissions
    ) {}

    public function handle(PaymentRefunded $event): void
    {
        $reversed = $this->commissions->reverse(
            sourceType: $event->sourceType,
            sourceId:   $event->payable->getKey()
        );

        Log::info('Commission reversed for refunded payment', [
            'source_type'    => $event->sourceType,
            'source_id'      => $event->payable->getKey(),
            'rows_reversed'  => $reversed,
            'reason'         => $event->reason,
        ]);
    }
}
