@extends('layouts.admin')

@section('title', 'Create Referral Agent')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.referrals.agents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h5 class="mb-0 text-gold"><i class="fas fa-user-plus me-2"></i>Create Referral Agent</h5>
            <small class="text-muted">Add a new referral partner</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.referrals.agents.store') }}">
                @csrf

                <!-- Basic Info -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-user me-2 text-gold"></i>Basic Information</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required placeholder="John Doe">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required placeholder="agent@example.com">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}" placeholder="+971 50 123 4567">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-lock me-2 text-gold"></i>Login Credentials</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                                       required placeholder="Min 8 characters">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control form-control-sm"
                                       required placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral Settings -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-link me-2 text-gold"></i>Referral Settings</div>
                    <div class="card-body">
                        <label class="form-label">Referral Code</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">{{ url('/') }}/?ref=</span>
                            <input type="text" name="referral_code" id="referralCode"
                                   class="form-control @error('referral_code') is-invalid @enderror"
                                   value="{{ old('referral_code') }}" placeholder="Auto-generate if blank">
                            <button type="button" class="btn btn-outline-gold" id="generateCode">
                                <i class="fas fa-magic"></i>
                            </button>
                        </div>
                        <small class="text-muted">Letters, numbers, dashes and underscores only</small>
                        @error('referral_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <!-- Commission -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-percentage me-2 text-gold"></i>Commission Settings</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="alert alert-info py-2 px-3 mb-2" style="font-size: 0.75rem; background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.3); color: #93c5fd;">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Global default: <strong>{{ $settings->commission_type === 'percentage' ? $settings->commission_value . '%' : 'AED ' . number_format($settings->commission_value, 2) }}</strong> per sale.
                                    Leaving these as-is will use the global rate.
                                    <a href="{{ route('admin.referrals.settings') }}" class="text-warning">Change global rate →</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="commission_type" id="commissionType" class="form-select form-select-sm @error('commission_type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('commission_type', $settings->commission_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('commission_type', $settings->commission_type) == 'fixed' ? 'selected' : '' }}>Fixed (AED)</option>
                                </select>
                                @error('commission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Value <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="commission_value" step="0.01" min="0"
                                           class="form-control @error('commission_value') is-invalid @enderror"
                                           value="{{ old('commission_value', $settings->commission_value) }}" required>
                                    <span class="input-group-text" id="commissionSuffix">%</span>
                                </div>
                                @error('commission_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info py-2 mb-0">
                                    <small><i class="fas fa-info-circle me-1"></i><span id="commissionInfo">Agent will earn 10% of each order</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-sticky-note me-2 text-gold"></i>Notes</div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                  rows="2" placeholder="Internal notes (optional)">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.referrals.agents.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-save me-1"></i>Create Agent</button>
                </div>
            </form>
        </div>

        <!-- Tips -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header"><i class="fas fa-lightbulb me-2 text-warning"></i>Quick Tips</div>
                <div class="card-body">
                    <ul class="tips-list mb-0">
                        <li><i class="fas fa-check-circle text-success me-1"></i><strong>Code:</strong> Short & memorable. Auto-generates if blank.</li>
                        <li><i class="fas fa-check-circle text-success me-1"></i><strong>Commission:</strong> % for varying orders, fixed for consistent payouts.</li>
                        <li><i class="fas fa-check-circle text-success me-1"></i><strong>Status:</strong> Only active agents can generate referrals.</li>
                        <li><i class="fas fa-check-circle text-success me-1"></i><strong>Login:</strong> Agent uses email + password for dashboard.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Card */
    .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; }
    .card-header { background: transparent; border-bottom: 1px solid var(--border-color); padding: 10px 14px; font-size: 0.8rem; font-weight: 500; color: #fff; }
    .card-body { padding: 14px; }

    /* Form */
    .form-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px; font-weight: 500; }
    .form-control, .form-select { background: var(--light-dark); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.75rem; }
    .form-control:focus, .form-select:focus { background: var(--light-dark); border-color: var(--primary-gold); color: var(--text-main); box-shadow: none; }
    .form-control::placeholder { color: var(--text-muted); }
    .form-control-sm, .form-select-sm { padding: 6px 10px; }
    .input-group-text { background: var(--dark-bg); border: 1px solid var(--border-color); color: var(--text-muted); font-size: 0.7rem; }

    /* Buttons */
    .btn-gold { background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)); border: none; color: #000; font-weight: 600; }
    .btn-outline-gold { border: 1px solid var(--primary-gold); color: var(--primary-gold); }
    .btn-outline-gold:hover { background: var(--primary-gold); color: #000; }
    .btn-sm { padding: 6px 12px; font-size: 0.75rem; }

    /* Alert */
    .alert-info { background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: var(--info); }

    /* Tips */
    .tips-list { list-style: none; padding: 0; margin: 0; }
    .tips-list li { font-size: 0.7rem; color: #a0aec0; margin-bottom: 8px; line-height: 1.4; }
    .tips-list li strong { color: #fff; }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
    .is-invalid { border-color: var(--danger) !important; }
    .invalid-feedback { color: var(--danger); font-size: 0.65rem; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commissionType = document.getElementById('commissionType');
    const commissionSuffix = document.getElementById('commissionSuffix');
    const commissionInfo = document.getElementById('commissionInfo');
    const commissionValue = document.querySelector('input[name="commission_value"]');
    const generateCodeBtn = document.getElementById('generateCode');
    const referralCodeInput = document.getElementById('referralCode');
    const nameInput = document.querySelector('input[name="name"]');

    function updateCommissionInfo() {
        const type = commissionType.value;
        const value = commissionValue.value || 0;
        if (type === 'percentage') {
            commissionSuffix.textContent = '%';
            commissionInfo.textContent = `Agent will earn ${value}% of each order`;
        } else {
            commissionSuffix.textContent = 'AED';
            commissionInfo.textContent = `Agent will earn AED ${value} per order`;
        }
    }

    commissionType.addEventListener('change', updateCommissionInfo);
    commissionValue.addEventListener('input', updateCommissionInfo);

    generateCodeBtn.addEventListener('click', function() {
        const name = nameInput.value || 'agent';
        const baseCode = name.toLowerCase().replace(/[^a-z0-9]/g, '').substring(0, 10);
        const randomSuffix = Math.random().toString(36).substring(2, 5);
        referralCodeInput.value = baseCode + randomSuffix;
    });

    updateCommissionInfo();
});
</script>
@endpush
@endsection
