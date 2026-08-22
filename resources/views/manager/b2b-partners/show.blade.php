@extends('layouts.manager')

@section('title', 'Review Partner Application')
@section('page-title', 'Review Partner Application')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">{{ $partner->company_name }}</h1>
    <a href="{{ route('manager.b2b-partners.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to B2B Partners
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

<div class="row">
    <div class="col-lg-7">
        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-building text-secondary-wp"></i> Application Details</div>
            <div class="wp-card-body">
                <div class="detail-row"><span>Status</span>
                    <span>
                        @if($partner->isPending())
                            <span class="wp-badge wp-badge-amber">Pending</span>
                        @elseif($partner->isApproved())
                            <span class="wp-badge {{ $partner->is_active ? 'wp-badge-green' : 'wp-badge-red' }}">{{ $partner->is_active ? 'Approved' : 'Disabled (license expired)' }}</span>
                        @else
                            <span class="wp-badge wp-badge-red">Rejected</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row"><span>Company</span><span>{{ $partner->company_name }}</span></div>
                <div class="detail-row"><span>Contact</span><span>{{ $partner->contact_name }}</span></div>
                <div class="detail-row"><span>Email</span><span>{{ $partner->email }}</span></div>
                <div class="detail-row"><span>Phone</span><span>{{ $partner->phone }}</span></div>
                <div class="detail-row"><span>Country</span><span>{{ $partner->country }}</span></div>
                <div class="detail-row"><span>Contract Type</span><span>{{ ucfirst($partner->contract_type) }}</span></div>
                @if($partner->isUae())
                    <div class="detail-row"><span>Trade License #</span><span>{{ $partner->trade_license_number }}</span></div>
                    <div class="detail-row"><span>License Expiry</span><span>{{ optional($partner->trade_license_expiry_date)->format('d M Y') }}</span></div>
                    <div class="detail-row"><span>License Document</span>
                        <span>
                            @if($partner->trade_license_document_path)
                                <a href="{{ Storage::url($partner->trade_license_document_path) }}" target="_blank" style="color: var(--wp-primary);">View document</a>
                            @else
                                &mdash;
                            @endif
                        </span>
                    </div>
                @endif
                @if($partner->isRejected() && $partner->rejection_reason)
                    <div class="detail-row"><span>Rejection Reason</span><span>{{ $partner->rejection_reason }}</span></div>
                @endif
                @if(!$partner->isPending())
                    <div class="detail-row"><span>Reviewed By</span><span>{{ optional($partner->reviewer)->name ?? '—' }} on {{ optional($partner->reviewed_at)->format('d M Y, H:i') ?? '—' }}</span></div>
                @endif
                @if($partner->isApproved() && !$partner->is_active && $partner->disabled_at)
                    <div class="detail-row"><span>Disabled At</span><span>{{ $partner->disabled_at->format('d M Y, H:i') }}</span></div>
                @endif
            </div>
        </div>

        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-signature text-secondary-wp"></i> Signature &amp; Contract</div>
            <div class="wp-card-body">
                <div class="detail-row"><span>Signed By</span><span>{{ $partner->signature_full_name }}</span></div>
                <div class="detail-row"><span>IP Address</span><span>{{ $partner->signature_ip }}</span></div>
                <div class="detail-row"><span>Signed At</span><span>{{ optional($partner->signed_at)->format('d M Y, H:i') }}</span></div>
                <div class="detail-row"><span>Contract PDF</span>
                    <span>
                        @if($partner->contract_pdf_path)
                            <a href="{{ Storage::url($partner->contract_pdf_path) }}" target="_blank" style="color: var(--wp-primary);">View contract (sample content)</a>
                        @else
                            &mdash;
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        @if($partner->isPending())
            <div class="wp-card gt-card">
                <div class="wp-card-header"><i class="fas fa-gavel text-secondary-wp"></i> Decision</div>
                <div class="wp-card-body">
                    <form action="{{ route('manager.b2b-partners.approve', $partner->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="wp-btn wp-btn-primary w-100">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="wp-btn wp-btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </div>
        @endif

        @if($partner->pending_license_review)
            <div class="wp-card gt-card" style="border-color: var(--wp-warning);">
                <div class="wp-card-header"><i class="fas fa-rotate text-secondary-wp"></i> License Renewal Awaiting Confirmation</div>
                <div class="wp-card-body">
                    <p class="wp-form-help">
                        This partner submitted a renewed trade license (shown above — number and expiry date already updated). Their account stays disabled until you confirm it.
                    </p>
                    <form action="{{ route('manager.b2b-partners.confirm-renewal', $partner->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="wp-btn wp-btn-primary w-100">
                            <i class="fas fa-check"></i> Confirm Renewal &amp; Re-activate
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($partner->isApproved())
            <div class="wp-card gt-card">
                <div class="wp-card-header"><i class="fas fa-percent text-secondary-wp"></i> Commission</div>
                <div class="wp-card-body">
                    <form action="{{ route('manager.b2b-partners.commission.update', $partner->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="wp-form-group mb-2">
                            <label class="wp-form-label">Commission (%)</label>
                            <input type="number" class="wp-input" name="commission_percent" value="{{ $partner->commission_percent }}" step="0.01" min="0" max="100" placeholder="e.g. 10">
                        </div>
                        <button type="submit" class="wp-btn wp-btn-secondary w-100">
                            <i class="fas fa-save"></i> Save Commission
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content gt-card" style="background: var(--wp-white); color: var(--wp-text);">
            <div class="modal-header">
                <h5 class="modal-title">Reject Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.b2b-partners.reject', $partner->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="wp-form-group mb-0">
                        <label class="wp-form-label">Reason <span class="required">*</span></label>
                        <textarea class="wp-input" name="reason" rows="4" required maxlength="1000" placeholder="Explain why this application is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="wp-btn wp-btn-danger">
                        <i class="fas fa-times"></i> Reject Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
