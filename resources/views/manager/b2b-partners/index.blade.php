@extends('layouts.manager')

@section('title', 'B2B Partners')
@section('page-title', 'B2B Partners')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">B2B Partners</h1>
</div>

<div class="wp-card">
    <div class="wp-card-header" style="display:flex; gap:6px; flex-wrap:wrap;">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'renewal' => 'Renewal Pending', 'disabled' => 'Disabled', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
            <a href="{{ route('manager.b2b-partners.index', ['status' => $key]) }}"
               class="wp-btn wp-btn-sm {{ $status === $key ? 'wp-btn-primary' : 'wp-btn-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th>Agency</th>
                    <th style="width: 130px;">Country</th>
                    <th style="width: 130px;">Contract</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 140px;">Submitted</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $partner)
                    <tr>
                        <td>
                            <strong>{{ $partner->company_name }}</strong>
                            <br><span class="text-secondary-wp" style="font-size:12px;">{{ $partner->contact_name }} &middot; {{ $partner->email }}</span>
                        </td>
                        <td>{{ $partner->country }}</td>
                        <td>{{ ucfirst($partner->contract_type) }}</td>
                        <td>
                            @if($partner->isPending())
                                <span class="wp-badge wp-badge-amber">Pending</span>
                            @elseif($partner->isApproved())
                                <span class="wp-badge {{ $partner->is_active ? 'wp-badge-green' : 'wp-badge-red' }}">
                                    {{ $partner->is_active ? 'Approved' : 'Disabled' }}
                                </span>
                                @if($partner->pending_license_review)
                                    <span class="wp-badge wp-badge-amber">Renewal Pending</span>
                                @endif
                            @else
                                <span class="wp-badge wp-badge-red">Rejected</span>
                            @endif
                        </td>
                        <td style="font-size:12px; color: var(--wp-text-secondary);">{{ $partner->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('manager.b2b-partners.show', $partner->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="6">
                            <div style="padding: 24px 0; text-align: center;">
                                No {{ $status !== 'all' ? $status : '' }} applications.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($partners->hasPages())
        <div class="wp-pagination">
            {{ $partners->links() }}
        </div>
    @endif
</div>
@endsection
