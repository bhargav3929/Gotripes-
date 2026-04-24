@extends('layouts.referral')

@section('title', 'Bank Accounts')

@section('content')
<div class="container" style="max-width: 900px;">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <div>
                    <h5 class="mb-1" style="color: #fff; font-weight: 700; letter-spacing: -0.01em;">Bank Accounts</h5>
                    <p class="mb-0" style="color: var(--text-muted); font-size: 0.8rem;">Add your bank details to receive commission payouts</p>
                </div>
                <button class="btn btn-outline-gold btn-sm" id="toggleFormBtn" onclick="toggleAddForm()">
                    <i class="fas fa-plus me-1"></i>Add Account
                </button>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert-flash alert-flash-success mb-3" id="flashMsg">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-flash alert-flash-danger mb-3" id="flashMsg">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Add Bank Account Form --}}
    <div class="add-form-wrapper mb-4" id="addFormWrapper" style="{{ $errors->any() ? '' : 'display: none;' }}">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span style="font-size: 0.8rem; font-weight: 500; color: #fff;">
                    <i class="fas fa-university me-2" style="color: var(--primary-gold);"></i>Add New Bank Account
                </span>
                <button class="close-form-btn" onclick="toggleAddForm()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="card-body">

                @if($errors->any())
                <div class="inline-error-box mb-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ url('/partner/bank-accounts') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Bank Name <span class="req">*</span></label>
                            <input
                                type="text"
                                name="bank_name"
                                class="form-control @error('bank_name') is-invalid @enderror"
                                value="{{ old('bank_name') }}"
                                placeholder="e.g. Emirates NBD"
                                required
                            >
                            @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Account Holder Name <span class="req">*</span></label>
                            <input
                                type="text"
                                name="account_holder_name"
                                class="form-control @error('account_holder_name') is-invalid @enderror"
                                value="{{ old('account_holder_name') }}"
                                placeholder="As shown on bank statement"
                                required
                            >
                            @error('account_holder_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Account Number <span class="req">*</span></label>
                            <input
                                type="text"
                                name="account_number"
                                class="form-control @error('account_number') is-invalid @enderror"
                                value="{{ old('account_number') }}"
                                placeholder="Your account number"
                                required
                            >
                            @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select name="country" class="form-select @error('country') is-invalid @enderror">
                                <option value="UAE" {{ old('country', 'UAE') === 'UAE' ? 'selected' : '' }}>United Arab Emirates</option>
                                <option value="SA"  {{ old('country') === 'SA'  ? 'selected' : '' }}>Saudi Arabia</option>
                                <option value="KW"  {{ old('country') === 'KW'  ? 'selected' : '' }}>Kuwait</option>
                                <option value="QA"  {{ old('country') === 'QA'  ? 'selected' : '' }}>Qatar</option>
                                <option value="BH"  {{ old('country') === 'BH'  ? 'selected' : '' }}>Bahrain</option>
                                <option value="OM"  {{ old('country') === 'OM'  ? 'selected' : '' }}>Oman</option>
                                <option value="IN"  {{ old('country') === 'IN'  ? 'selected' : '' }}>India</option>
                                <option value="PK"  {{ old('country') === 'PK'  ? 'selected' : '' }}>Pakistan</option>
                                <option value="GB"  {{ old('country') === 'GB'  ? 'selected' : '' }}>United Kingdom</option>
                                <option value="US"  {{ old('country') === 'US'  ? 'selected' : '' }}>United States</option>
                                <option value="OTHER" {{ old('country') === 'OTHER' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">IBAN <span class="optional-badge">Optional</span></label>
                            <input
                                type="text"
                                name="iban"
                                class="form-control @error('iban') is-invalid @enderror"
                                value="{{ old('iban') }}"
                                placeholder="AE07 0331 2345 6789 0123 456"
                            >
                            @error('iban')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SWIFT / BIC Code <span class="optional-badge">Optional</span></label>
                            <input
                                type="text"
                                name="swift_code"
                                class="form-control @error('swift_code') is-invalid @enderror"
                                value="{{ old('swift_code') }}"
                                placeholder="e.g. EBILAEAD"
                            >
                            @error('swift_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    name="is_primary"
                                    id="isPrimary"
                                    class="form-check-input"
                                    value="1"
                                    {{ old('is_primary') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="isPrimary" style="font-size: 0.82rem;">
                                    Set as primary payout account
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-gold btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Bank Account
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAddForm()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bank Accounts List --}}
    <div class="accounts-section">
        <div class="section-label mb-3" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">
            Saved Accounts &mdash; {{ count($bankAccounts) }} {{ count($bankAccounts) === 1 ? 'account' : 'accounts' }}
        </div>

        @forelse($bankAccounts as $account)
        <div class="account-card mb-3 {{ $account->is_primary ? 'account-card--primary' : '' }}">
            <div class="account-card-inner">
                <div class="account-left">
                    <div class="bank-icon-wrap">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="account-info">
                        <div class="account-bank-name">
                            {{ $account->bank_name }}
                            @if($account->is_primary)
                            <span class="primary-badge"><i class="fas fa-star me-1"></i>Primary</span>
                            @endif
                        </div>
                        <div class="account-holder">{{ $account->account_holder_name }}</div>
                        <div class="account-meta">
                            <span class="account-number">•••• {{ substr($account->account_number, -4) }}</span>
                            @if($account->iban)
                            <span class="meta-divider">·</span>
                            <span class="account-iban">IBAN: {{ substr($account->iban, 0, 8) }}…</span>
                            @endif
                            @if($account->country)
                            <span class="meta-divider">·</span>
                            <span class="account-country">{{ $account->country }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="account-actions">
                    @if(!$account->is_primary)
                    <form method="POST" action="{{ url('/partner/bank-accounts/' . $account->id . '/primary') }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-action btn-action--outline" title="Set as Primary">
                            <i class="fas fa-star me-1"></i><span class="d-none d-sm-inline">Set Primary</span>
                        </button>
                    </form>
                    @endif

                    <form method="POST" action="{{ url('/partner/bank-accounts/' . $account->id) }}"
                          class="d-inline"
                          onsubmit="return confirm('Remove {{ addslashes($account->bank_name) }} ending in {{ substr($account->account_number, -4) }}? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-action--danger" title="Remove Account">
                            <i class="fas fa-trash-alt me-1"></i><span class="d-none d-sm-inline">Remove</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-university"></i>
            </div>
            <div class="empty-title">No bank accounts yet</div>
            <div class="empty-desc">Add a bank account to start receiving your commission payouts.</div>
            <button class="btn btn-gold btn-sm mt-3" onclick="toggleAddForm()">
                <i class="fas fa-plus me-1"></i>Add Your First Account
            </button>
        </div>
        @endforelse
    </div>

</div>

<style>
    /* Flash Messages */
    .alert-flash {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        position: relative;
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
        margin-left: auto;
        cursor: pointer;
        color: inherit;
        opacity: 0.6;
        padding: 0 0 0 0.5rem;
        font-size: 0.75rem;
    }
    .flash-close:hover { opacity: 1; }

    /* Inline Error Box */
    .inline-error-box {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: rgba(239, 68, 68, 0.08);
        border: 1px solid rgba(239, 68, 68, 0.2);
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
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
    }
    .form-control::placeholder { color: rgba(138,143,152,0.5); }
    .form-select option { background: var(--card-bg); }
    .form-check-input {
        background-color: var(--light-dark);
        border-color: var(--border-color);
    }
    .form-check-input:checked {
        background-color: var(--primary-gold);
        border-color: var(--primary-gold);
    }

    /* Close form button */
    .close-form-btn {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 0.8rem;
        padding: 2px 6px;
        border-radius: 4px;
        transition: color 0.15s;
    }
    .close-form-btn:hover { color: var(--text-main); background: rgba(255,255,255,0.05); }

    /* Buttons */
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 7px;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    .btn-gold:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(255,215,0,0.2);
        color: #000;
    }
    .btn-outline-gold {
        border: 1px solid rgba(255,215,0,0.4);
        color: var(--primary-gold);
        background: transparent;
        font-size: 0.78rem;
        font-weight: 500;
        padding: 0.4rem 0.875rem;
        border-radius: 7px;
        transition: all 0.15s;
    }
    .btn-outline-gold:hover {
        background: rgba(255,215,0,0.08);
        color: var(--primary-gold);
        border-color: var(--primary-gold);
    }
    .btn-secondary {
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        font-size: 0.8rem;
        border-radius: 7px;
    }
    .btn-secondary:hover {
        background: rgba(255,255,255,0.08);
        color: var(--text-main);
    }

    /* Account Card */
    .account-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        transition: border-color 0.2s ease;
    }
    .account-card:hover {
        border-color: rgba(255,255,255,0.1);
    }
    .account-card--primary {
        border-color: var(--border-gold);
        background: linear-gradient(135deg, rgba(255,215,0,0.03) 0%, var(--card-bg) 100%);
    }
    .account-card-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        gap: 1rem;
    }
    .account-left {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        min-width: 0;
        flex: 1;
    }
    .bank-icon-wrap {
        width: 44px;
        height: 44px;
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid var(--border-gold);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-gold);
        font-size: 1rem;
        flex-shrink: 0;
    }
    .account-bank-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 0.15rem;
    }
    .primary-badge {
        font-size: 0.65rem;
        background: rgba(255, 215, 0, 0.15);
        color: var(--primary-gold);
        border: 1px solid rgba(255, 215, 0, 0.25);
        border-radius: 4px;
        padding: 1px 6px;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .account-holder {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }
    .account-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.3rem;
        font-size: 0.72rem;
        color: var(--text-muted);
    }
    .account-number { font-family: monospace; letter-spacing: 0.05em; }
    .meta-divider { opacity: 0.4; }

    /* Action buttons */
    .account-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
        align-items: center;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 500;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
        border: 1px solid;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .btn-action--outline {
        background: transparent;
        border-color: rgba(255, 215, 0, 0.25);
        color: var(--primary-gold);
    }
    .btn-action--outline:hover {
        background: rgba(255, 215, 0, 0.08);
        border-color: var(--primary-gold);
    }
    .btn-action--danger {
        background: transparent;
        border-color: rgba(239, 68, 68, 0.25);
        color: var(--danger);
    }
    .btn-action--danger:hover {
        background: rgba(239, 68, 68, 0.08);
        border-color: var(--danger);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3.5rem 1.5rem;
        border: 1px dashed var(--border-color);
        border-radius: 10px;
        background: var(--card-bg);
    }
    .empty-icon {
        width: 64px;
        height: 64px;
        background: rgba(255,215,0,0.06);
        border: 1px solid var(--border-gold);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--primary-gold);
        margin: 0 auto 1rem;
    }
    .empty-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #fff;
        margin-bottom: 0.375rem;
    }
    .empty-desc {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* Mobile responsive */
    @media (max-width: 575.98px) {
        .account-card-inner {
            flex-direction: column;
            align-items: flex-start;
        }
        .account-actions {
            width: 100%;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border-color);
        }
    }
</style>
@endsection

@push('scripts')
<script>
function toggleAddForm() {
    const wrapper = document.getElementById('addFormWrapper');
    const btn = document.getElementById('toggleFormBtn');
    if (wrapper.style.display === 'none') {
        wrapper.style.display = 'block';
        wrapper.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        btn.innerHTML = '<i class="fas fa-times me-1"></i>Cancel';
    } else {
        wrapper.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-plus me-1"></i>Add Account';
    }
}

// Auto-dismiss flash messages after 5s
setTimeout(() => {
    const flash = document.getElementById('flashMsg');
    if (flash) {
        flash.style.opacity = '0';
        flash.style.transition = 'opacity 0.4s ease';
        setTimeout(() => flash.remove(), 400);
    }
}, 5000);
</script>
@endpush
