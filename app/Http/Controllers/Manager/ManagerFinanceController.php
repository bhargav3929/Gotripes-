<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\TenantBankAccount;
use App\Models\TenantCommission;
use App\Models\TenantWithdrawal;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManagerFinanceController extends Controller
{
    private function tenant(): ?Company
    {
        $c = app()->bound('current_company') ? app('current_company') : null;
        return $c instanceof Company ? $c : null;
    }

    /**
     * Earnings dashboard — overview of pending / available / paid commissions.
     */
    public function index()
    {
        $company = $this->tenant();
        if (!$company) {
            return view('manager.finance.no-tenant');
        }

        // All four are now computed live from the ledger via Company accessors —
        // no more drift between this dashboard and the SA dashboard.
        $totals = [
            'pending'   => $company->pending_commission,
            'available' => $company->available_commission,
            'reserved'  => $company->reserved_commission,
            'paid'      => $company->paid_commission,
            'total'     => $company->total_commission,
            'currency'  => $company->currency ?? 'AED',
        ];

        $recent = TenantCommission::where('company_id', $company->id)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $bookingCount = \App\Models\ActivityBooking::forCompany($company->id)->count();
        $withdrawableMin = 50.00; // AED min withdrawal threshold

        return view('manager.finance.index', compact('company', 'totals', 'recent', 'bookingCount', 'withdrawableMin'));
    }

    /**
     * List of orders / commissions for this tenant.
     */
    public function bookings(Request $request)
    {
        $company = $this->tenant();
        if (!$company) {
            return view('manager.finance.no-tenant');
        }

        $bookings = \App\Models\ActivityBooking::forCompany($company->id)
            ->orderByDesc('id')
            ->paginate(25);

        // Map commissions by source_id for fast lookup in the view
        $commissionByBooking = TenantCommission::where('company_id', $company->id)
            ->where('source_type', 'activity_booking')
            ->whereIn('source_id', $bookings->pluck('id'))
            ->get()
            ->keyBy('source_id');

        return view('manager.finance.bookings', compact('company', 'bookings', 'commissionByBooking'));
    }

    /**
     * Bank accounts CRUD.
     */
    public function bankAccounts()
    {
        $company = $this->tenant();
        if (!$company) {
            return view('manager.finance.no-tenant');
        }
        $accounts = TenantBankAccount::where('company_id', $company->id)->orderByDesc('is_primary')->get();
        return view('manager.finance.bank-accounts', compact('company', 'accounts'));
    }

    public function storeBankAccount(Request $request)
    {
        $company = $this->tenant();
        if (!$company) return back()->with('error', 'No tenant context.');

        $validated = $request->validate([
            'bank_name'           => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number'      => 'required|string|max:64',
            'iban'                => 'nullable|string|max:64',
            'swift_code'          => 'nullable|string|max:32',
            'country'             => 'nullable|string|max:100',
            'is_primary'          => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($company, $validated) {
            if (!empty($validated['is_primary'])) {
                TenantBankAccount::where('company_id', $company->id)->update(['is_primary' => false]);
            }
            // First account becomes primary by default
            $isFirst = !TenantBankAccount::where('company_id', $company->id)->exists();
            TenantBankAccount::create(array_merge($validated, [
                'company_id' => $company->id,
                'country'    => $validated['country'] ?? 'UAE',
                'is_primary' => !empty($validated['is_primary']) || $isFirst,
            ]));
        });

        return redirect()->route('manager.finance.bank-accounts')
            ->with('success', 'Bank account added.');
    }

    public function deleteBankAccount(TenantBankAccount $bank)
    {
        $company = $this->tenant();
        if (!$company || $bank->company_id !== $company->id) {
            abort(404);
        }
        $bank->delete();
        return back()->with('success', 'Bank account removed.');
    }

    /**
     * Withdrawals — list + create.
     */
    public function withdrawals()
    {
        $company = $this->tenant();
        if (!$company) {
            return view('manager.finance.no-tenant');
        }

        $available = $company->available_commission;

        $accounts = TenantBankAccount::where('company_id', $company->id)->orderByDesc('is_primary')->get();
        $withdrawals = TenantWithdrawal::where('company_id', $company->id)->orderByDesc('id')->paginate(20);

        return view('manager.finance.withdrawals', compact('company', 'available', 'accounts', 'withdrawals'));
    }

    /**
     * Request a withdrawal.
     *
     * The whole flow runs inside ONE serializable transaction with a row lock
     * on the company. Two concurrent requests for the same tenant cannot both
     * pass the balance check and both reserve the same commissions — the
     * second one will see the post-reservation balance and either fail or
     * reserve only what's still left.
     */
    public function requestWithdrawal(Request $request, CommissionService $commissions)
    {
        $company = $this->tenant();
        if (!$company) return back()->with('error', 'No tenant context.');

        $validated = $request->validate([
            'amount'          => 'required|numeric|min:50',
            'bank_account_id' => 'required|exists:tenant_bank_accounts,id',
            'notes'           => 'nullable|string|max:500',
        ]);

        // Bank ownership check (still outside the transaction — cheap)
        $bank = TenantBankAccount::where('id', $validated['bank_account_id'])
            ->where('company_id', $company->id)->first();
        if (!$bank) abort(403);

        try {
            DB::transaction(function () use ($company, $bank, $validated, $commissions) {
                // Lock the company row for the duration of the transaction.
                // Any other request hitting this code path for THIS tenant
                // will block here until we're done.
                $locked = Company::lockForUpdate()->findOrFail($company->id);

                // Recompute the available balance from rows AFTER taking the lock.
                // (The `$company->available_commission` accessor already reads live data.)
                $available = $locked->available_commission;
                $amount = (float) $validated['amount'];

                if ($amount > $available + 0.001) {
                    throw ValidationException::withMessages([
                        'amount' => "Insufficient available balance. You have " . number_format($available, 2) . " AED.",
                    ]);
                }

                // Create withdrawal in 'pending' state.
                $withdrawal = TenantWithdrawal::create([
                    'company_id'      => $locked->id,
                    'bank_account_id' => $bank->id,
                    'amount'          => $amount,
                    'currency'        => $locked->currency ?? 'AED',
                    'status'          => 'pending',
                    'notes'           => $validated['notes'] ?? null,
                ]);

                // Atomically reserve the commissions backing this request (FIFO).
                $commissions->reserveFor($locked, $withdrawal, $amount);
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('manager.finance.withdrawals')
            ->with('success', 'Withdrawal requested. Our team will review and process it shortly.');
    }
}
