<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Models\Company;
use App\Services\CommissionService;
use Illuminate\Support\Facades\Log;

class RecordCommission
{
    public function __construct(
        protected CommissionService $commissions
    ) {}

    public function handle(PaymentConfirmed $event): void
    {
        $company = Company::find($event->companyId);

        if (!$company) {
            Log::warning('RecordCommission: company not found', [
                'company_id'  => $event->companyId,
                'source_type' => $event->sourceType,
                'source_id'   => $event->payable->getKey(),
            ]);
            return;
        }

        // The platform main tenant (gotrips) sells direct — don't record
        // commission against itself.
        if ($company->slug === 'gotrips') {
            return;
        }

        // Skip services not configured for commission (e.g. umrah).
        $eligible = config('commission.eligible_services', []);
        if (!in_array($event->sourceType, $eligible, true)) {
            return;
        }

        // CommissionService::record() is idempotent on (source_type, source_id):
        // re-firing the event for the same payment does not create a duplicate.
        $this->commissions->record(
            company:     $company,
            sourceType:  $event->sourceType,
            sourceId:    $event->payable->getKey(),
            grossAmount: $event->grossAmount,
            currency:    $event->currency,
            status:      'pending'
        );
    }
}
