<?php

namespace App\Http\Controllers;

use App\Models\ReferralBankAccount;
use App\Models\ReferralSetting;
use App\Models\ReferralWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralWithdrawalController extends Controller
{
    /**
     * Display the withdrawal page with history and available balance.
     */
    public function index()
    {
        $agent                  = Auth::guard('referral_agent')->user();
        $withdrawalRequests     = $agent->withdrawalRequests()->orderByDesc('created_at')->paginate(15);
        $bankAccounts           = $agent->bankAccounts()->orderByDesc('is_primary')->get();
        $availableBalance       = $agent->availableBalance();
        $pendingWithdrawalAmount = $agent->withdrawalRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        $settings = ReferralSetting::getSettings();

        return view('referral.withdraw', compact(
            'agent', 'withdrawalRequests', 'bankAccounts',
            'availableBalance', 'pendingWithdrawalAmount', 'settings'
        ));
    }

    /**
     * Submit a new withdrawal request.
     */
    public function store(Request $request)
    {
        $agent = Auth::guard('referral_agent')->user();

        $request->validate([
            'amount'          => 'required|numeric|min:1',
            'bank_account_id' => 'required|exists:referral_bank_accounts,id',
        ]);

        $settings = ReferralSetting::getSettings();

        /** @var ReferralBankAccount $bankAccount */
        $bankAccount = ReferralBankAccount::findOrFail($request->bank_account_id);

        if ($bankAccount->referral_agent_id !== $agent->id) {
            abort(403, 'This bank account does not belong to you.');
        }

        $amount = (float) $request->amount;

        if ($amount < (float) $settings->min_withdrawal_amount) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Minimum withdrawal amount is ' . number_format($settings->min_withdrawal_amount, 2) . ' AED.']);
        }

        $available = $agent->availableBalance();

        if ($amount > $available) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Insufficient balance. Your available balance is ' . number_format($available, 2) . ' AED.']);
        }

        ReferralWithdrawalRequest::create([
            'referral_agent_id'      => $agent->id,
            'referral_bank_account_id' => $bankAccount->id,
            'bank_snapshot'          => $bankAccount->toSnapshot(),
            'amount'                 => $amount,
            'currency'               => 'AED',
            'status'                 => 'pending',
            'agent_notes'            => $request->agent_notes,
        ]);

        return back()->with('success', 'Withdrawal request submitted successfully. We will process it within 3–5 business days.');
    }
}
