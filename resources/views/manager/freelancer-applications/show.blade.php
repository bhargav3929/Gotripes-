@extends('layouts.manager')

@section('title', 'Review Freelancer Application')
@section('page-title', 'Review Freelancer Application')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">{{ $application->name }}</h1>
    <a href="{{ route('manager.freelancer-applications.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Freelancer Applications
    </a>
</div>

<style>
    .gt-card {
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(255, 215, 0, 0.06), 0 0 40px rgba(255, 215, 0, 0.08), 0 16px 40px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }
    .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--wp-border-light); font-size: 13px; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row span:first-child { color: var(--wp-text-muted); }
</style>

@php $bank = $application->primaryBankAccount(); @endphp

<div class="row">
    <div class="col-lg-7">
        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-user-tie text-secondary-wp"></i> Application Details</div>
            <div class="wp-card-body">
                <div class="detail-row"><span>Status</span>
                    <span>
                        @if($application->isPendingApplication())
                            <span class="wp-badge wp-badge-amber">Pending</span>
                        @elseif($application->isApprovedApplication())
                            <span class="wp-badge wp-badge-green">Approved</span>
                        @elseif($application->isRejectedApplication())
                            <span class="wp-badge wp-badge-red">Rejected</span>
                        @else
                            <span class="wp-badge wp-badge-amber">—</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row"><span>Name</span><span>{{ $application->name }}</span></div>
                <div class="detail-row"><span>Email</span><span>{{ $application->email }}</span></div>
                <div class="detail-row"><span>Phone</span><span>{{ $application->phone }}</span></div>
                <div class="detail-row"><span>Country</span><span>{{ $application->country ?: '—' }}</span></div>
                @if($application->isRejectedApplication() && $application->rejection_reason)
                    <div class="detail-row"><span>Rejection Reason</span><span>{{ $application->rejection_reason }}</span></div>
                @endif
                @if(!$application->isPendingApplication())
                    <div class="detail-row"><span>Reviewed By</span><span>{{ optional($application->reviewer)->name ?? '—' }} on {{ optional($application->reviewed_at)->format('d M Y, H:i') ?? '—' }}</span></div>
                @endif
            </div>
        </div>

        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-building-columns text-secondary-wp"></i> Bank Details (for payouts)</div>
            <div class="wp-card-body">
                @if($bank)
                    <div class="detail-row"><span>Bank Name</span><span>{{ $bank->bank_name }}</span></div>
                    <div class="detail-row"><span>Account Holder</span><span>{{ $bank->account_holder_name }}</span></div>
                    <div class="detail-row"><span>Account Number</span><span>{{ $bank->account_number }}</span></div>
                    <div class="detail-row"><span>IBAN</span><span>{{ $bank->iban ?: '—' }}</span></div>
                    <div class="detail-row"><span>SWIFT/BIC</span><span>{{ $bank->swift_code ?: '—' }}</span></div>
                @else
                    <p class="wp-form-help mb-0">No bank account on file.</p>
                @endif
            </div>
        </div>

        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-file-signature text-secondary-wp"></i> Agreement</div>
            <div class="wp-card-body">
                @if($application->hasSignedContract())
                    <div class="detail-row"><span>Contract</span>
                        <span>{{ optional($application->contract)->title ?? 'Removed' }}
                            @if($application->contract)
                                <span class="wp-badge wp-badge-amber" style="margin-left:4px;">v{{ $application->contract->version }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-row"><span>Signed By</span><span>{{ $application->signature_full_name }}</span></div>
                    <div class="detail-row"><span>Signed At</span><span>{{ optional($application->signed_at)->format('d M Y, H:i') }} UTC</span></div>
                    <div class="detail-row"><span>IP Address</span><span>{{ $application->signature_ip ?: '—' }}</span></div>
                    <div class="detail-row"><span>Signed Copy</span>
                        <span>
                            @if($application->signed_pdf_path)
                                <a href="{{ Storage::url($application->signed_pdf_path) }}" target="_blank" style="color: var(--wp-primary);">Download PDF</a>
                            @else
                                &mdash;
                            @endif
                        </span>
                    </div>
                @else
                    <p class="wp-form-help mb-0">
                        Not signed. This applicant registered
                        @if(\App\Models\ContractDocument::current())
                            before the current contract was published, so they cannot be approved until they register again.
                        @else
                            when no contract was published, so there was nothing to sign.
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        @if($application->isPendingApplication())
            <div class="wp-card gt-card">
                <div class="wp-card-header"><i class="fas fa-gavel text-secondary-wp"></i> Decision</div>
                <div class="wp-card-body">
                    <p class="wp-form-help">Review the details above before deciding.</p>
                    <form action="{{ route('manager.freelancer-applications.approve', $application->id) }}" method="POST" class="mb-3" id="approveForm">
                        @csrf
                        <div class="wp-form-group">
                            <label>eSIM margin for this freelancer</label>
                            <div style="display:flex; gap:8px;">
                                <select name="esim_margin_type" id="marginType" class="wp-input" style="max-width:150px;">
                                    <option value="fixed" @selected(old('esim_margin_type', $application->esim_margin_type) === 'fixed')>Flat (AED)</option>
                                    <option value="percentage" @selected(old('esim_margin_type', $application->esim_margin_type) === 'percentage')>Percentage (%)</option>
                                </select>
                                <input type="number" name="esim_margin_value" id="marginValue" step="0.01" min="0"
                                       class="wp-input" required
                                       value="{{ old('esim_margin_value', (float) $application->esim_margin_value) }}">
                            </div>
                            @error('esim_margin_type')<div class="wp-form-help" style="color:#e5484d;">{{ $message }}</div>@enderror
                            @error('esim_margin_value')<div class="wp-form-help" style="color:#e5484d;">{{ $message }}</div>@enderror
                            <small class="wp-form-help" id="marginHint"></small>
                        </div>
                        <button type="submit" class="wp-btn wp-btn-primary w-100">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="wp-btn wp-btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i> Deny
                    </button>
                </div>
            </div>
        @elseif($application->isApprovedApplication())
            <div class="wp-card gt-card">
                <div class="wp-card-header"><i class="fas fa-coins text-secondary-wp"></i> eSIM Margin</div>
                <div class="wp-card-body">
                    <form action="{{ route('manager.freelancer-applications.margin.update', $application->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="wp-form-group">
                            <label>Added on top of your price, per eSIM</label>
                            <div style="display:flex; gap:8px;">
                                <select name="esim_margin_type" id="marginType" class="wp-input" style="max-width:150px;">
                                    <option value="fixed" @selected($application->esim_margin_type === 'fixed')>Flat (AED)</option>
                                    <option value="percentage" @selected($application->esim_margin_type === 'percentage')>Percentage (%)</option>
                                </select>
                                <input type="number" name="esim_margin_value" id="marginValue" step="0.01" min="0" class="wp-input" required
                                       value="{{ old('esim_margin_value', (float) $application->esim_margin_value) }}">
                            </div>
                            @error('esim_margin_value')<div class="wp-form-help" style="color:#e5484d;">{{ $message }}</div>@enderror
                            <small class="wp-form-help" id="marginHint"></small>
                        </div>
                        <button type="submit" class="wp-btn wp-btn-primary w-100"><i class="fas fa-save"></i> Save margin</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@if($application->isPendingApplication() || $application->isApprovedApplication())
<script>
(function () {
    var type = document.getElementById('marginType'), val = document.getElementById('marginValue'), hint = document.getElementById('marginHint');
    function render() {
        var v = parseFloat(val.value) || 0;
        // Worked example on a plan whose platform price is AED 120.
        var m = type.value === 'percentage' ? 120 * v / 100 : v;
        hint.textContent = 'Added on top of your own price, per eSIM. On a AED 120 plan the customer pays AED '
            + (120 + m).toFixed(2) + ' and this freelancer earns AED ' + m.toFixed(2) + '.';
    }
    type.addEventListener('change', render); val.addEventListener('input', render); render();
})();
</script>
@endif

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content gt-card" style="background: var(--wp-white); color: var(--wp-text);">
            <div class="modal-header">
                <h5 class="modal-title">Deny Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.freelancer-applications.reject', $application->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="wp-form-group mb-0">
                        <label class="wp-form-label">Reason <span class="required">*</span></label>
                        <textarea class="wp-input" name="reason" rows="4" required maxlength="1000" placeholder="Explain why this application is being denied..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="wp-btn wp-btn-danger">
                        <i class="fas fa-times"></i> Deny Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
