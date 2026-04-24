@extends('layouts.referral')

@section('title', 'Withdraw Earnings')

@section('content')
<div class="container" style="max-width: 960px;">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-1" style="color: #fff; font-weight: 700; letter-spacing: -0.01em;">Withdraw Earnings</h5>
            <p class="mb-0" style="color: var(--text-muted); font-size: 0.8rem;">Request a payout to your registered bank account</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert-flash alert-flash-success mb-4" id="flashMsg">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-flash alert-flash-danger mb-4" id="flashMsg">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Balance Strip --}}
    <div class="balance-strip mb-4">
        <div class="balance-item balance-item--main">
            <div class="balance-label">Available Balance</div>
            <div class="balance-amount" id="availableBalanceDisplay">AED {{ number_format($availableBalance, 2) }}</div>
        </div>
        <div class="balance-sep"></div>
        <div class="balance-item">
            <div class="balance-label">Pending Withdrawal</div>
            <div class="balance-amount balance-amount--muted">AED {{ number_format($pendingWithdrawalAmount, 2) }}</div>
        </div>
        <div class="balance-sep"></div>
        <div class="balance-item">
            <div class="balance-label">Min. Withdrawal</div>
            <div class="balance-amount balance-amount--muted">
                AED {{ number_format(isset($settings) ? $settings->min_withdrawal_amount : 50, 2) }}
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Left: Withdrawal Form --}}
        <div class="col-lg-5">

            @if($bankAccounts->isEmpty())
            {{-- No Bank Accounts Warning --}}
            <div class="no-bank-warning">
                <div class="no-bank-icon"><i class="fas fa-university"></i></div>
                <div class="no-bank-title">No bank account linked</div>
                <div class="no-bank-desc">
                    You need to add a bank account before you can request a withdrawal.
                </div>
                <a href="{{ route('referral.bank-accounts') }}" class="btn btn-gold btn-sm mt-3">
                    <i class="fas fa-plus me-1"></i>Add Bank Account
                </a>
            </div>

            @else

            {{-- Withdrawal Form --}}
            <div class="card">
                <div class="card-header">
                    <span style="font-size: 0.8rem; font-weight: 500; color: #fff;">
                        <i class="fas fa-paper-plane me-2" style="color: var(--primary-gold);"></i>Request Withdrawal
                    </span>
                </div>
                <div class="card-body">

                    @if($errors->any())
                    <div class="inline-error-box mb-3">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <div>
                            @foreach($errors->all() as $err)
                            <div>{{ $err }}</div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ url('/partner/withdraw') }}" id="withdrawForm">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label">Payout Account <span class="req">*</span></label>
                            <select name="bank_account_id" class="form-select @error('bank_account_id') is-invalid @enderror" required>
                                <option value="">Select a bank account...</option>
                                @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}"
                                    {{ old('bank_account_id') == $account->id ? 'selected' : ($account->is_primary ? 'selected' : '') }}>
                                    {{ $account->bank_name }} — •••• {{ substr($account->account_number, -4) }}
                                    @if($account->is_primary) (Primary) @endif
                                </option>
                                @endforeach
                            </select>
                            @error('bank_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="withdrawAmount">Amount (AED) <span class="req">*</span></label>
                            <div class="amount-input-wrap">
                                <span class="amount-currency">AED</span>
                                <input
                                    type="number"
                                    id="withdrawAmount"
                                    name="amount"
                                    class="form-control amount-input @error('amount') is-invalid @enderror"
                                    value="{{ old('amount') }}"
                                    placeholder="0.00"
                                    step="0.01"
                                    min="{{ isset($settings) ? $settings->min_withdrawal_amount : 50 }}"
                                    max="{{ $availableBalance }}"
                                    required
                                >
                            </div>
                            <div class="amount-helper">
                                Available: <span class="amount-available" id="availableHint">AED {{ number_format($availableBalance, 2) }}</span>
                                &nbsp;&middot;&nbsp;
                                <button type="button" class="max-btn" onclick="setMaxAmount({{ $availableBalance }})">Withdraw Max</button>
                            </div>
                            @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="notes">Notes <span class="optional-badge">Optional</span></label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="form-control"
                                rows="2"
                                placeholder="Any notes for this withdrawal..."
                                style="resize: none;"
                            >{{ old('notes') }}</textarea>
                        </div>

                        @if($availableBalance <= 0)
                        <div class="zero-balance-note mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Your available balance is AED 0.00. Earn more commissions before requesting a withdrawal.
                        </div>
                        @endif

                        <button
                            type="submit"
                            class="btn btn-gold w-100"
                            @if($availableBalance <= 0) disabled @endif
                            id="submitWithdrawBtn"
                        >
                            <i class="fas fa-paper-plane me-2"></i>Request Withdrawal
                        </button>
                    </form>
                </div>
            </div>

            <div class="processing-note mt-3">
                <i class="fas fa-clock me-2"></i>
                Withdrawals are typically processed within 1–3 business days.
            </div>

            @endif
        </div>

        {{-- Right: Withdrawal History --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span style="font-size: 0.8rem; font-weight: 500; color: #fff;">
                        <i class="fas fa-history me-2" style="color: var(--primary-gold);"></i>Withdrawal History
                    </span>
                    @if($withdrawalRequests->total() > 0)
                    <span class="history-count">{{ $withdrawalRequests->total() }} total</span>
                    @endif
                </div>

                @if($withdrawalRequests->isEmpty())
                <div class="card-body text-center py-5">
                    <div class="empty-icon-sm mx-auto mb-3">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">No withdrawal requests yet</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Your withdrawal history will appear here</div>
                </div>

                @else
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 withdrawal-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                                <th class="d-none d-md-table-cell">Bank</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawalRequests as $request)
                            <tr>
                                <td>
                                    <div style="font-size: 0.78rem;">{{ $request->created_at->format('d M Y') }}</div>
                                    <div style="font-size: 0.68rem; color: var(--text-muted);">{{ $request->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-end">
                                    <span style="font-weight: 600; font-size: 0.85rem; color: var(--primary-gold);">
                                        AED {{ number_format($request->amount, 2) }}
                                    </span>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    @if($request->bankAccount)
                                    <div style="font-size: 0.78rem;">{{ $request->bankAccount->bank_name }}</div>
                                    <div style="font-size: 0.68rem; color: var(--text-muted); font-family: monospace;">
                                        •••• {{ substr($request->bankAccount->account_number, -4) }}
                                    </div>
                                    @else
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusMap = [
                                            'pending'    => ['label' => 'Pending',    'class' => 'status-pending'],
                                            'processing' => ['label' => 'Processing', 'class' => 'status-processing'],
                                            'completed'  => ['label' => 'Completed',  'class' => 'status-completed'],
                                            'rejected'   => ['label' => 'Rejected',   'class' => 'status-rejected'],
                                        ];
                                        $s = $statusMap[$request->status] ?? ['label' => ucfirst($request->status), 'class' => 'status-pending'];
                                    @endphp
                                    <span class="status-badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                                    @if($request->notes)
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 2px; max-width: 100px;">
                                        {{ Str::limit($request->notes, 30) }}
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($withdrawalRequests->hasPages())
                <div class="card-body pt-2 pb-2">
                    <div class="pagination-wrap">
                        {{ $withdrawalRequests->links() }}
                    </div>
                </div>
                @endif

                @endif
            </div>
        </div>

    </div>
</div>

<style>
    /* Flash */
    .alert-flash {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    .alert-flash-success {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        color: var(--success);
    }
    .alert-flash-danger {
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: var(--danger);
    }
    .flash-close {
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: 0.6;
        margin-left: auto;
        padding: 0;
        font-size: 0.75rem;
    }
    .flash-close:hover { opacity: 1; }

    /* Balance Strip */
    .balance-strip {
        display: flex;
        align-items: stretch;
        background: var(--card-bg);
        border: 1px solid var(--border-gold);
        border-radius: 10px;
        overflow: hidden;
    }
    .balance-item {
        flex: 1;
        padding: 1.25rem 1.5rem;
    }
    .balance-item--main {
        background: linear-gradient(135deg, rgba(255,215,0,0.06) 0%, transparent 100%);
    }
    .balance-sep {
        width: 1px;
        background: var(--border-color);
    }
    .balance-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
        margin-bottom: 0.35rem;
    }
    .balance-amount {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary-gold);
        letter-spacing: -0.02em;
        line-height: 1;
    }
    .balance-amount--muted {
        font-size: 1.25rem;
        color: var(--text-main);
        font-weight: 600;
    }

    @media (max-width: 575.98px) {
        .balance-strip {
            flex-direction: column;
        }
        .balance-sep {
            width: 100%;
            height: 1px;
        }
        .balance-item { padding: 0.875rem 1rem; }
        .balance-amount { font-size: 1.25rem; }
        .balance-amount--muted { font-size: 1rem; }
    }

    /* No Bank Warning */
    .no-bank-warning {
        text-align: center;
        padding: 2.5rem 1.5rem;
        border: 1px dashed rgba(245, 158, 11, 0.3);
        border-radius: 10px;
        background: rgba(245, 158, 11, 0.04);
    }
    .no-bank-icon {
        width: 56px;
        height: 56px;
        background: rgba(245,158,11,0.1);
        border: 1px solid rgba(245,158,11,0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--warning);
        margin: 0 auto 1rem;
    }
    .no-bank-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #fff;
        margin-bottom: 0.375rem;
    }
    .no-bank-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Inline Error */
    .inline-error-box {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.2);
        border-radius: 8px;
        font-size: 0.8rem;
        color: var(--danger);
    }

    /* Form */
    .form-label {
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }
    .req { color: var(--danger); }
    .optional-badge {
        font-size: 0.65rem;
        background: rgba(255,255,255,0.05);
        border-radius: 4px;
        padding: 1px 5px;
        color: var(--text-muted);
        font-weight: 400;
    }
    .form-control, .form-select {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.875rem;
        padding: 0.6rem 0.875rem;
        border-radius: 8px;
    }
    .form-control:focus, .form-select:focus {
        background: var(--light-dark);
        border-color: var(--primary-gold);
        color: var(--text-main);
        box-shadow: 0 0 0 3px rgba(255,215,0,0.1);
    }
    .form-control::placeholder { color: rgba(138,143,152,0.5); }
    .form-select option { background: var(--card-bg); }

    /* Amount input */
    .amount-input-wrap {
        display: flex;
        align-items: center;
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        transition: border-color 0.15s;
    }
    .amount-input-wrap:focus-within {
        border-color: var(--primary-gold);
        box-shadow: 0 0 0 3px rgba(255,215,0,0.1);
    }
    .amount-currency {
        padding: 0 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        border-right: 1px solid var(--border-color);
        line-height: 1;
    }
    .amount-input {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        padding: 0.75rem 0.875rem !important;
        color: #fff !important;
        flex: 1;
    }
    .amount-input:focus { outline: none; }
    .amount-helper {
        margin-top: 0.35rem;
        font-size: 0.72rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .amount-available { color: var(--primary-gold); font-weight: 600; }
    .max-btn {
        background: none;
        border: none;
        color: var(--primary-gold);
        font-size: 0.72rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        text-decoration: underline;
        font-family: inherit;
    }
    .max-btn:hover { opacity: 0.8; }

    /* Zero balance note */
    .zero-balance-note {
        padding: 0.75rem 0.875rem;
        background: rgba(59,130,246,0.08);
        border: 1px solid rgba(59,130,246,0.2);
        border-radius: 8px;
        font-size: 0.78rem;
        color: var(--info);
    }

    /* Buttons */
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }
    .btn-gold:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(255,215,0,0.2);
        color: #000;
    }
    .btn-gold:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    .btn-sm { padding: 0.45rem 0.875rem !important; font-size: 0.78rem !important; }

    /* Processing note */
    .processing-note {
        font-size: 0.75rem;
        color: var(--text-muted);
        padding: 0.75rem 0;
    }

    /* History */
    .history-count {
        font-size: 0.68rem;
        background: rgba(255,255,255,0.06);
        color: var(--text-muted);
        border-radius: 4px;
        padding: 2px 8px;
    }
    .withdrawal-table th {
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 12px;
        font-weight: 500;
        border-bottom: 1px solid var(--border-color);
    }
    .withdrawal-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .withdrawal-table tbody tr:last-child td { border-bottom: none; }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .status-pending    { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }
    .status-processing { background: rgba(59,130,246,0.15); color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }
    .status-completed  { background: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
    .status-rejected   { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.25); }

    /* Empty icon sm */
    .empty-icon-sm {
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: var(--text-muted);
    }

    /* Pagination */
    .pagination-wrap .pagination {
        justify-content: center;
        margin: 0;
        gap: 4px;
    }
    .pagination-wrap .page-link {
        background: var(--light-dark);
        border-color: var(--border-color);
        color: var(--text-muted);
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .pagination-wrap .page-item.active .page-link {
        background: var(--primary-gold);
        border-color: var(--primary-gold);
        color: #000;
        font-weight: 700;
    }
    .pagination-wrap .page-link:hover {
        background: rgba(255,215,0,0.1);
        color: var(--primary-gold);
        border-color: var(--border-gold);
    }
</style>
@endsection

@push('scripts')
<script>
function setMaxAmount(max) {
    const input = document.getElementById('withdrawAmount');
    input.value = max.toFixed(2);
    input.focus();
}

// Auto-dismiss flash
setTimeout(() => {
    const flash = document.getElementById('flashMsg');
    if (flash) {
        flash.style.opacity = '0';
        flash.style.transition = 'opacity 0.4s ease';
        setTimeout(() => flash && flash.remove(), 400);
    }
}, 5000);

// Prevent double-submission
document.getElementById('withdrawForm') && document.getElementById('withdrawForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitWithdrawBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    }
});
</script>
@endpush
