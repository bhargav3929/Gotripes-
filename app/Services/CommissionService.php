<?php

namespace App\Services;

use App\Models\Company;
use App\Models\TenantCommission;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calculate the commission a tenant earns on a gross order amount.
     */
    public function calculate(Company $company, float $grossAmount): float
    {
        if ($company->commission_type === 'flat') {
            return round((float) $company->commission_value, 2);
        }
        // default: percentage
        $rate = (float) $company->commission_value;
        return round($grossAmount * ($rate / 100), 2);
    }

    /**
     * Record a commission ledger entry for a tenant booking/order.
     * Idempotent: if a row already exists for this source it returns existing.
     *
     * Statuses:
     *   pending   = awaiting payment confirmation / clearance window
     *   available = withdrawable
     */
    public function record(
        Company $company,
        string $sourceType,
        int|string $sourceId,
        float $grossAmount,
        string $currency = 'AED',
        string $status = 'pending'
    ): TenantCommission {
        return DB::transaction(function () use ($company, $sourceType, $sourceId, $grossAmount, $currency, $status) {
            $existing = TenantCommission::where('company_id', $company->id)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->first();
            if ($existing) {
                return $existing;
            }

            $commission = $this->calculate($company, $grossAmount);

            $entry = TenantCommission::create([
                'company_id'        => $company->id,
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
                'gross_amount'      => $grossAmount,
                'commission_type'   => $company->commission_type ?? 'percentage',
                'commission_rate'   => (float) ($company->commission_value ?? 0),
                'commission_amount' => $commission,
                'currency'          => $currency,
                'status'            => $status,
                'available_at'      => $status === 'available' ? now() : null,
            ]);

            // Update company aggregates.
            $company->increment('total_commission', $commission);
            $company->increment('pending_commission', $commission);

            return $entry;
        });
    }

    /**
     * Mark all pending commissions for a booking source as available (withdrawable).
     */
    public function markAvailable(string $sourceType, int|string $sourceId): void
    {
        TenantCommission::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->where('status', 'pending')
            ->each(function (TenantCommission $c) {
                $c->update(['status' => 'available', 'available_at' => now()]);
            });
    }
}
