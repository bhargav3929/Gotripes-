<?php

namespace App\Services;

use App\Models\Company;
use App\Models\TenantCommission;
use App\Models\TenantWithdrawal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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
        $rate = (float) $company->commission_value;
        return round($grossAmount * ($rate / 100), 2);
    }

    /**
     * Record a commission ledger entry.
     *
     * Idempotent on (source_type, source_id): re-firing returns the existing row.
     *
     * Lifecycle:
     *   pending   → newly recorded, held for `commission.hold_days`
     *   available → released by `commissions:release`; counts toward withdrawable balance
     *   reserved  → locked to a specific TenantWithdrawal
     *   paid      → settled by markPaid; not withdrawable again
     *   reversed  → refunded / chargeback; doesn't count anywhere
     *
     * NOTE: this method NO LONGER writes to companies.pending_commission /
     * total_commission. Those columns are now computed live by the Company
     * model accessors. The columns survive only as legacy / cached data and
     * are no longer load-bearing.
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
            $holdDays = (int) config('commission.hold_days', 0);
            $availableAt = $status === 'available' ? now() : now()->addDays($holdDays);

            return TenantCommission::create([
                'company_id'        => $company->id,
                'source_type'       => $sourceType,
                'source_id'         => $sourceId,
                'gross_amount'      => $grossAmount,
                'commission_type'   => $company->commission_type ?? 'percentage',
                'commission_rate'   => (float) ($company->commission_value ?? 0),
                'commission_amount' => $commission,
                'currency'          => $currency,
                'status'            => $status,
                'available_at'      => $availableAt,
            ]);
        });
    }

    /**
     * Release pending commissions whose hold window has elapsed.
     */
    public function releaseDue(): int
    {
        return TenantCommission::where('status', 'pending')
            ->where('available_at', '<=', now())
            ->update(['status' => 'available']);
    }

    /**
     * Reverse commissions for a refunded payment.
     * Affects pending and available rows that aren't yet linked to a paid withdrawal.
     */
    public function reverse(string $sourceType, int|string $sourceId): int
    {
        return TenantCommission::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->whereIn('status', ['pending', 'available'])
            ->update(['status' => 'reversed']);
    }

    /**
     * Atomically reserve enough available commissions to cover a withdrawal.
     *
     * Caller MUST already hold a row lock on the company (typically via
     * `Company::lockForUpdate()->find($id)` inside the same transaction) so
     * that two concurrent requests cannot both reserve the same balance.
     *
     * Picks rows in FIFO order (oldest available_at first).
     *
     * @throws RuntimeException if available balance is insufficient
     */
    public function reserveFor(Company $company, TenantWithdrawal $withdrawal, float $amount): Collection
    {
        return DB::transaction(function () use ($company, $withdrawal, $amount) {
            $available = TenantCommission::where('company_id', $company->id)
                ->where('status', 'available')
                ->orderBy('available_at')->orderBy('id')   // FIFO
                ->lockForUpdate()
                ->get();

            $sum = (float) $available->sum('commission_amount');
            if ($sum < $amount - 0.01) {
                throw new RuntimeException("Insufficient available balance: have {$sum}, need {$amount}.");
            }

            $reserved = collect();
            $remaining = $amount;
            foreach ($available as $row) {
                if ($remaining <= 0.01) break;
                $row->update([
                    'status'        => 'reserved',
                    'withdrawal_id' => $withdrawal->id,
                ]);
                $reserved->push($row);
                $remaining = round($remaining - (float) $row->commission_amount, 2);
            }

            return $reserved;
        });
    }

    /**
     * Release reserved commissions back to available — used when a withdrawal
     * is rejected. Idempotent.
     */
    public function releaseReservation(TenantWithdrawal $withdrawal): int
    {
        return TenantCommission::where('withdrawal_id', $withdrawal->id)
            ->where('status', 'reserved')
            ->update([
                'status'        => 'available',
                'withdrawal_id' => null,
            ]);
    }

    /**
     * Settle a paid withdrawal: flip its reserved commissions to paid.
     */
    public function markReservedAsPaid(TenantWithdrawal $withdrawal): int
    {
        return TenantCommission::where('withdrawal_id', $withdrawal->id)
            ->where('status', 'reserved')
            ->update(['status' => 'paid']);
    }
}
