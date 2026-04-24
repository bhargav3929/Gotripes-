<?php

namespace App\Http\Controllers;

use App\Models\ReferralBankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferralBankAccountController extends Controller
{
    /**
     * Display the agent's bank accounts.
     */
    public function index()
    {
        $agent        = Auth::guard('referral_agent')->user();
        $bankAccounts = $agent->bankAccounts()->orderByDesc('is_primary')->orderBy('created_at')->get();

        return view('referral.bank-accounts', compact('bankAccounts', 'agent'));
    }

    /**
     * Store a new bank account.
     */
    public function store(Request $request)
    {
        $agent = Auth::guard('referral_agent')->user();

        $validated = $request->validate([
            'bank_name'           => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number'      => 'required|string|max:255',
            'iban'                => 'nullable|string|max:34',
            'swift_code'          => 'nullable|string|max:11',
            'country'             => 'nullable|string|max:100',
            'is_primary'          => 'boolean',
        ]);

        $isPrimary = (bool) ($validated['is_primary'] ?? false);

        if ($isPrimary) {
            $agent->bankAccounts()->update(['is_primary' => false]);
        }

        $agent->bankAccounts()->create([
            'bank_name'           => $validated['bank_name'],
            'account_holder_name' => $validated['account_holder_name'],
            'account_number'      => $validated['account_number'],
            'iban'                => $validated['iban'] ?? null,
            'swift_code'          => $validated['swift_code'] ?? null,
            'country'             => $validated['country'] ?? 'UAE',
            'is_primary'          => $isPrimary,
        ]);

        return back()->with('success', 'Bank account added successfully.');
    }

    /**
     * Delete a bank account.
     */
    public function destroy(ReferralBankAccount $account)
    {
        $agent = Auth::guard('referral_agent')->user();

        if ($account->referral_agent_id !== $agent->id) {
            abort(403, 'This bank account does not belong to you.');
        }

        $wasPrimary = $account->is_primary;
        $account->delete();

        if ($wasPrimary) {
            $next = $agent->bankAccounts()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Bank account removed successfully.');
    }

    /**
     * Set a bank account as primary.
     */
    public function setPrimary(ReferralBankAccount $account)
    {
        $agent = Auth::guard('referral_agent')->user();

        if ($account->referral_agent_id !== $agent->id) {
            abort(403, 'This bank account does not belong to you.');
        }

        $agent->bankAccounts()->update(['is_primary' => false]);
        $account->update(['is_primary' => true]);

        return back()->with('success', 'Primary bank account updated.');
    }
}
