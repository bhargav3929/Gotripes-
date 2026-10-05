@extends('layouts.manager')

@section('title', 'Freelancer Applications')
@section('page-title', 'Freelancer Applications')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Freelancer Applications</h1>
</div>

<div class="wp-card">
    <div class="wp-card-header" style="display:flex; gap:6px; flex-wrap:wrap;">
        @foreach(['pending_review' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
            <a href="{{ route('manager.freelancer-applications.index', ['status' => $key]) }}"
               class="wp-btn wp-btn-sm {{ $status === $key ? 'wp-btn-primary' : 'wp-btn-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th style="width: 140px;">Country</th>
                    <th style="width: 130px;">Status</th>
                    <th style="width: 140px;">Submitted</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td>
                            <strong>{{ $application->name }}</strong>
                            <br><span class="text-secondary-wp" style="font-size:12px;">{{ $application->email }} &middot; {{ $application->phone }}</span>
                        </td>
                        <td style="font-size:12px; color: var(--wp-text-secondary);">{{ $application->country ?: '—' }}</td>
                        <td>
                            @if($application->isPendingApplication())
                                <span class="wp-badge wp-badge-amber">Pending</span>
                            @elseif($application->isApprovedApplication())
                                <span class="wp-badge wp-badge-green">Approved</span>
                            @elseif($application->isRejectedApplication())
                                <span class="wp-badge wp-badge-red">Rejected</span>
                            @else
                                <span class="wp-badge wp-badge-amber">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px; color: var(--wp-text-secondary);">{{ $application->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('manager.freelancer-applications.show', $application->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-eye"></i> Review
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="5">
                            <div style="padding: 24px 0; text-align: center;">
                                No {{ $status !== 'all' ? str_replace('_', ' ', $status) : '' }} applications.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
        <div class="wp-pagination">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
