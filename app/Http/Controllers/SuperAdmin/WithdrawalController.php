<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\TenantCommission;
use App\Models\TenantWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index()
    {
        $stats = [
            'pending'  => (float) TenantWithdrawal::where('status', 'pending')->sum('amount'),
            'approved' => (float) TenantWithdrawal::where('status', 'approved')->sum('amount'),
            'paid'     => (float) TenantWithdrawal::where('status', 'paid')->sum('amount'),
            'all_time_commission' => (float) Company::sum('total_commission'),
        ];

        $withdrawals = TenantWithdrawal::with(['company', 'bankAccount'])
            ->orderByDesc('id')->paginate(25);

        return view('superadmin.withdrawals.index', compact('stats', 'withdrawals'));
    }

    public function approve(TenantWithdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }
        $withdrawal->update(['status' => 'approved']);
        return back()->with('success', 'Approved. Now mark as paid once you transfer the funds.');
    }

    public function reject(Request $request, TenantWithdrawal $withdrawal)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);
        $withdrawal->update([
            'status'      => 'rejected',
            'admin_notes' => $validated['admin_notes'],
        ]);
        return back()->with('success', 'Withdrawal rejected.');
    }

    public function markPaid(Request $request, TenantWithdrawal $withdrawal)
    {
        if (!in_array($withdrawal->status, ['pending', 'approved'])) {
            return back()->with('error', 'This withdrawal has already been processed.');
        }
        $validated = $request->validate([
            'payment_reference' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($withdrawal, $validated) {
            // Mark withdrawal paid
            $withdrawal->update([
                'status'            => 'paid',
                'payment_reference' => $validated['payment_reference'],
                'processed_at'      => now(),
                'processed_by'      => auth()->id(),
            ]);

            // Apply remaining amount: consume available commissions FIFO and link them.
            $remaining = (float) $withdrawal->amount;
            $available = TenantCommission::where('company_id', $withdrawal->company_id)
                ->where('status', 'available')
                ->orderBy('id')->get();

            foreach ($available as $com) {
                if ($remaining <= 0) break;
                $com->update([
                    'status'        => 'paid',
                    'withdrawal_id' => $withdrawal->id,
                ]);
                $remaining = round($remaining - (float) $com->commission_amount, 2);
            }

            // Update company aggregates.
            $company = Company::find($withdrawal->company_id);
            if ($company) {
                $company->decrement('pending_commission', (float) $withdrawal->amount);
                if ($company->pending_commission < 0) {
                    $company->update(['pending_commission' => 0]);
                }
                $company->increment('paid_commission', (float) $withdrawal->amount);
            }
        });

        return back()->with('success', 'Marked as paid. Commission ledger entries have been settled.');
    }

    /**
     * Bulk-promote pending commissions older than X hours to "available" status
     * so tenants can withdraw them. (Manual trigger from finance dashboard.)
     */
    public function releaseCommissions(Request $request)
    {
        $hours = (int) $request->input('hours', 24);
        $threshold = now()->subHours($hours);

        $count = TenantCommission::where('status', 'pending')
            ->where('created_at', '<=', $threshold)
            ->update(['status' => 'available', 'available_at' => now()]);

        return back()->with('success', "Released {$count} commission(s) older than {$hours}h to 'available'.");
    }
}
