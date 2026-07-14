@extends('layouts.manager')

@section('title', 'Saudi Visa Application #' . $application->id)
@section('page-title', 'Saudi Visa Application #' . $application->id)

@section('content')
<div class="orders-toolbar">
    <a href="{{ route('manager.orders.saudi-visa') }}" class="orders-btn orders-btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to applications
    </a>
</div>

@if(session('success'))
<div style="padding:12px 16px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#22c55e; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="orders-detail-grid">
    <div class="orders-card orders-detail-card">
        <h3>Applicant Info</h3>
        <ul class="orders-detail-list">
            <li><span class="label">First Name</span>     <span class="value">{{ $application->first_name ?: '—' }}</span></li>
            <li><span class="label">Last Name</span>      <span class="value">{{ $application->last_name ?: '—' }}</span></li>
            <li><span class="label">Passport Number</span> <span class="value">{{ $application->passport_number ?: '—' }}</span></li>
            <li><span class="label">Passport Expiry</span> <span class="value">{{ optional($application->passport_expiry)?->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Date of Birth</span>  <span class="value">{{ optional($application->dob)?->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Gender</span>         <span class="value">{{ $application->gender ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Contact Info</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Email</span>       <span class="value">{{ $application->email ?: '—' }}</span></li>
            <li><span class="label">Phone</span>       <span class="value">{{ $application->phone ?: '—' }}</span></li>
            <li><span class="label">Nationality</span> <span class="value">{{ $application->nationality ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Visa & Payment Details</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Visa Type</span>       <span class="value">{{ $application->visaType?->name ?: '—' }}</span></li>
            <li><span class="label">Price</span>           <span class="value">AED {{ number_format($application->price, 2) }}</span></li>
            <li><span class="label">Order ID</span>        <span class="value">{{ $application->order_id }}</span></li>
            <li><span class="label">Payment Status</span>  <span class="value">{{ ucfirst($application->payment_status) }}</span></li>
            <li><span class="label">Submitted At</span>    <span class="value">{{ $application->created_at->format('d M Y H:i') }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Uploaded Documents</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Passport Copy</span>
                <span class="value">
                    @if($application->passport_path)
                        <a href="{{ asset('storage/' . $application->passport_path) }}" target="_blank" style="color:#FFD700;"><i class="fas fa-file-pdf"></i> View File</a>
                    @else — @endif
                </span>
            </li>
            <li><span class="label">Passport Photo</span>
                <span class="value">
                    @if($application->photo_path)
                        <a href="{{ asset('storage/' . $application->photo_path) }}" target="_blank" style="color:#FFD700;"><i class="fas fa-image"></i> View File</a>
                    @else — @endif
                </span>
            </li>
            <li><span class="label">Additional Doc</span>
                <span class="value">
                    @if($application->additional_doc_path)
                        <a href="{{ asset('storage/' . $application->additional_doc_path) }}" target="_blank" style="color:#FFD700;"><i class="fas fa-file-alt"></i> View File</a>
                    @else — @endif
                </span>
            </li>
        </ul>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="orders-card">
            <h3>Manage Application Status & Internal Notes</h3>
            <form action="{{ route('manager.orders.saudi-visa.status', $application) }}" method="POST" style="margin-top: 15px;">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600; color:#fff;">Application Status</label>
                    <select name="status" class="form-select" style="background:#111; color:#fff; border:1px solid rgba(255,215,0,0.25);">
                        <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="submitted" {{ $application->status === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ $application->status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-weight:600; color:#fff;">Internal Notes (Only visible to managers)</label>
                    <textarea name="internal_notes" rows="4" class="form-control" placeholder="Add details or notes here..." style="background:#111; color:#fff; border:1px solid rgba(255,215,0,0.25);">{{ $application->internal_notes }}</textarea>
                </div>
                <button type="submit" class="orders-btn orders-btn-primary">
                    <i class="fas fa-save me-1"></i> Update Status & Notes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
