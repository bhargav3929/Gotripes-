@extends('layouts.manager')

@section('title', 'Review Agent Application')
@section('page-title', 'Review Agent Application')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">{{ $application->name }}</h1>
    <a href="{{ route('manager.agent-applications.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Agent Applications
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
            <div class="wp-card-header"><i class="fas fa-user-tie text-secondary-wp"></i> Application Details</div>
            <div class="wp-card-body">
                <div class="detail-row"><span>Status</span>
                    <span>
                        @if($application->isPending())
                            <span class="wp-badge wp-badge-amber">Pending</span>
                        @elseif($application->isApproved())
                            <span class="wp-badge wp-badge-green">Approved</span>
                        @else
                            <span class="wp-badge wp-badge-red">Rejected</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row"><span>Name</span><span>{{ $application->name }}</span></div>
                <div class="detail-row"><span>Email</span><span>{{ $application->email }}</span></div>
                <div class="detail-row"><span>Phone</span><span>{{ $application->phone }}</span></div>
                <div class="detail-row"><span>Country</span><span>{{ $application->country ?: '—' }}</span></div>
                <div class="detail-row"><span>Services Requested</span>
                    <span>
                        @foreach($application->services ?? [] as $service)
                            <span class="wp-badge wp-badge-amber" style="margin: 2px 0 2px 4px;">{{ \App\Models\User::AGENT_SERVICES[$service] ?? $service }}</span>
                        @endforeach
                    </span>
                </div>
                @if($application->isRejected() && $application->rejection_reason)
                    <div class="detail-row"><span>Rejection Reason</span><span>{{ $application->rejection_reason }}</span></div>
                @endif
                @if($application->isApproved() && $application->user)
                    <div class="detail-row"><span>Agent Account</span><span>Active — <a href="{{ route('manager.agents.edit', $application->user_id) }}" style="color: var(--wp-primary);">manage in Agents</a></span></div>
                @endif
                @if(!$application->isPending())
                    <div class="detail-row"><span>Reviewed By</span><span>{{ optional($application->reviewer)->name ?? '—' }} on {{ optional($application->reviewed_at)->format('d M Y, H:i') ?? '—' }}</span></div>
                @endif
            </div>
        </div>

        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-id-card text-secondary-wp"></i> Trade License</div>
            <div class="wp-card-body">
                <div class="detail-row"><span>License Number</span><span>{{ $application->trade_license_number }}</span></div>
                <div class="detail-row"><span>License Expiry</span><span>{{ optional($application->trade_license_expiry_date)->format('d M Y') }}</span></div>
                <div class="detail-row"><span>License Document</span>
                    <span>
                        @if($application->trade_license_document_path)
                            <a href="{{ Storage::url($application->trade_license_document_path) }}" target="_blank" style="color: var(--wp-primary);">View document</a>
                        @else
                            &mdash;
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        @if($application->isPending())
            <div class="wp-card gt-card">
                <div class="wp-card-header"><i class="fas fa-gavel text-secondary-wp"></i> Decision</div>
                <div class="wp-card-body">
                    <p class="wp-form-help">Review the trade license above before deciding.</p>
                    <form action="{{ route('manager.agent-applications.approve', $application->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="wp-btn wp-btn-primary w-100">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                    <button type="button" class="wp-btn wp-btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i> Deny
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog">
        <div class="modal-content gt-card" style="background: var(--wp-white); color: var(--wp-text);">
            <div class="modal-header">
                <h5 class="modal-title">Deny Application</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('manager.agent-applications.reject', $application->id) }}" method="POST">
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
