<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralWithdrawalRequest;
use Illuminate\Http\Request;

class ReferralWithdrawalAdminController extends Controller
{
    /**
     * List all withdrawal requests with optional filters.
     */
    public function index(Request $request)
    {
        $query = ReferralWithdrawalRequest::with('referralAgent');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->whereHas('referralAgent', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $withdrawals = $query->orderByDesc('created_at')->paginate(20);

        $pendingTotal    = ReferralWithdrawalRequest::where('status', 'pending')->sum('amount');
        $processingTotal = ReferralWithdrawalRequest::where('status', 'processing')->sum('amount');
        $completedTotal  = ReferralWithdrawalRequest::where('status', 'completed')->sum('amount');

        $stats = [
            'pending_count'    => ReferralWithdrawalRequest::where('status', 'pending')->count(),
            'processing_count' => ReferralWithdrawalRequest::where('status', 'processing')->count(),
            'completed_count'  => ReferralWithdrawalRequest::where('status', 'completed')->count(),
            'rejected_count'   => ReferralWithdrawalRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.referrals.withdrawals.index', compact(
            'withdrawals',
            'pendingTotal',
            'processingTotal',
            'completedTotal',
            'stats'
        ));
    }

    /**
     * Move a pending withdrawal to processing (admin has started the bank transfer).
     */
    public function approve(ReferralWithdrawalRequest $withdrawal)
    {
        $withdrawal->update(['status' => 'processing']);

        return back()->with([
            'message'    => 'Withdrawal marked as processing.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Mark a withdrawal as completed and update the agent's paid_earnings.
     */
    public function complete(ReferralWithdrawalRequest $withdrawal)
    {
        $withdrawal->update([
            'status'       => 'completed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Update the agent's paid_earnings counter
        $agent = $withdrawal->referralAgent;
        if ($agent) {
            $agent->increment('paid_earnings', $withdrawal->amount);
        }

        return back()->with([
            'message'    => 'Withdrawal completed and agent earnings updated.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Reject a withdrawal request with an admin note.
     */
    public function reject(Request $request, ReferralWithdrawalRequest $withdrawal)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $withdrawal->update([
            'status'       => 'rejected',
            'admin_notes'  => $request->admin_notes,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with([
            'message'    => 'Withdrawal request rejected.',
            'alert-type' => 'warning',
        ]);
    }
}
