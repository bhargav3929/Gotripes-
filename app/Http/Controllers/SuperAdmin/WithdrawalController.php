<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SuperAdminAuditLog;
use App\Models\TenantCommission;
use App\Models\TenantWithdrawal;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        // All four totals are now true sums of the live ledger, not denormalized columns.
        $stats = [
            'pending'  => (float) TenantWithdrawal::where('status', 'pending')->sum('amount'),
            'approved' => (float) TenantWithdrawal::where('status', 'approved')->sum('amount'),
            'paid'     => (float) TenantWithdrawal::where('status', 'paid')->sum('amount'),
            'all_time_commission' => (float) TenantCommission::whereIn('status', ['pending', 'available', 'reserved', 'paid'])
                ->sum('commission_amount'),
        ];

        $withdrawals = TenantWithdrawal::with(['company', 'bankAccount'])
            ->orderByDesc('id')->paginate(25);

        return view('superadmin.withdrawals.index', compact('stats', 'withdrawals'));
    }

    /**
     * Approve a pending withdrawal.
     *
     * State machine: pending → approved.
     * No financial movement happens here — the commissions were already reserved
     * at request time. This is just the SA saying "OK to proceed with payout."
     */
    public function approve(TenantWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        DB::transaction(function () use ($withdrawal) {
            $locked = TenantWithdrawal::lockForUpdate()->findOrFail($withdrawal->id);
            if ($locked->status !== 'pending') {
                throw new \RuntimeException('Withdrawal is no longer pending.');
            }
            $locked->update(['status' => 'approved']);

            SuperAdminAuditLog::log('withdrawal.approve', $locked, [
                'amount'     => (float) $locked->amount,
                'company_id' => $locked->company_id,
            ]);
        });

        return back()->with('success', 'Approved. Now mark as paid once you transfer the funds.');
    }

    /**
     * Reject a withdrawal.
     *
     * State machine: pending|approved → rejected (terminal).
     * Releases the reserved commissions back to available so the tenant
     * can request another withdrawal.
     */
    public function reject(Request $request, TenantWithdrawal $withdrawal, CommissionService $commissions)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($withdrawal, $validated, $commissions) {
            $locked = TenantWithdrawal::lockForUpdate()->findOrFail($withdrawal->id);

            if (!in_array($locked->status, ['pending', 'approved'], true)) {
                throw new \RuntimeException('Only pending or approved withdrawals can be rejected.');
            }

            // Release the reserved commissions back to 'available'.
            $released = $commissions->releaseReservation($locked);

            $locked->update([
                'status'       => 'rejected',
                'admin_notes'  => $validated['admin_notes'],
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);

            SuperAdminAuditLog::log('withdrawal.reject', $locked, [
                'amount'             => (float) $locked->amount,
                'company_id'         => $locked->company_id,
                'commissions_released' => $released,
                'reason'             => $validated['admin_notes'],
            ]);
        });

        return back()->with('success', 'Withdrawal rejected. Commissions returned to available balance.');
    }

    /**
     * Mark a withdrawal as paid.
     *
     * State machine: pending|approved → paid (terminal).
     * Settles the reservation: reserved commissions flip to 'paid' and stay
     * linked to this withdrawal for audit / reconciliation.
     */
    public function markPaid(Request $request, TenantWithdrawal $withdrawal, CommissionService $commissions)
    {
        $validated = $request->validate([
            'payment_reference' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($withdrawal, $validated, $commissions) {
            $locked = TenantWithdrawal::lockForUpdate()->findOrFail($withdrawal->id);

            if (!in_array($locked->status, ['pending', 'approved'], true)) {
                throw new \RuntimeException('This withdrawal has already been processed.');
            }

            // Flip the reserved commissions to paid.
            $settled = $commissions->markReservedAsPaid($locked);

            $locked->update([
                'status'            => 'paid',
                'payment_reference' => $validated['payment_reference'],
                'processed_at'      => now(),
                'processed_by'      => auth()->id(),
            ]);

            SuperAdminAuditLog::log('withdrawal.mark_paid', $locked, [
                'amount'              => (float) $locked->amount,
                'company_id'          => $locked->company_id,
                'commissions_settled' => $settled,
                'payment_reference'   => $validated['payment_reference'],
            ]);
        });

        return back()->with('success', 'Marked as paid. Commission ledger entries have been settled.');
    }

    /**
     * Bulk-promote pending commissions older than X hours to 'available'.
     * Manual override of the scheduled `commissions:release` command.
     */
    public function releaseCommissions(Request $request)
    {
        $hours = (int) $request->input('hours', 24);
        $threshold = now()->subHours($hours);

        $count = TenantCommission::where('status', 'pending')
            ->where('created_at', '<=', $threshold)
            ->update(['status' => 'available', 'available_at' => now()]);

        SuperAdminAuditLog::log('commissions.bulk_release', null, [
            'hours_threshold' => $hours,
            'rows_released'   => $count,
        ]);

        return back()->with('success', "Released {$count} commission(s) older than {$hours}h to 'available'.");
    }
}
