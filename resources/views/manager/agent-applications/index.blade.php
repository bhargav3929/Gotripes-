@extends('layouts.manager')

@section('title', 'Agent Applications')
@section('page-title', 'Agent Applications')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Agent Applications</h1>
</div>

<div class="wp-card">
    <div class="wp-card-header" style="display:flex; gap:6px; flex-wrap:wrap;">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
            <a href="{{ route('manager.agent-applications.index', ['status' => $key]) }}"
               class="wp-btn wp-btn-sm {{ $status === $key ? 'wp-btn-primary' : 'wp-btn-secondary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <div class="wp-card-body" style="border-bottom:1px solid var(--wp-border-light);">
        <form method="GET" action="{{ route('manager.agent-applications.index') }}" class="application-filters">
            <input type="hidden" name="status" value="{{ $status }}">
            @foreach($filters as $key => $filter)
                <label>
                    <span>{{ $filter['label'] }}</span>
                    @if(($filter['input'] ?? 'select') === 'text')
                        <input class="wp-input" type="search" name="{{ $key }}" value="{{ $filter['value'] }}" placeholder="{{ $filter['placeholder'] ?? '' }}">
                    @else
                        <select class="wp-select" name="{{ $key }}">
                            <option value="">All</option>
                            @foreach($filter['options'] ?? [] as $value => $label)
                                <option value="{{ $value }}" @selected((string)$filter['value'] === (string)$value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    @endif
                </label>
            @endforeach
            <div class="application-filter-actions">
                <button class="wp-btn wp-btn-primary" type="submit"><i class="fas fa-filter"></i> Apply filters</button>
                @if(collect($filters)->contains(fn($filter) => $filter['value'] !== ''))
                    <a class="wp-btn wp-btn-secondary" href="{{ route('manager.agent-applications.index', ['status' => $status]) }}">Clear</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th style="width: 140px;">Location</th>
                    <th style="width: 200px;">Services Requested</th>
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
                            @if($application->company_name)
                                <br><span class="text-secondary-wp" style="font-size:12px;">{{ $application->company_name }}</span>
                            @endif
                            <br><span class="text-secondary-wp" style="font-size:12px;">{{ $application->email }} &middot; {{ $application->phone }}</span>
                        </td>
                        <td style="font-size:12px; color: var(--wp-text-secondary);">
                            {{ $application->emirate ?: ($application->country ?: '—') }}
                        </td>
                        <td>
                            @foreach($application->services ?? [] as $service)
                                <span class="wp-badge wp-badge-amber" style="margin: 2px 2px 0 0;">{{ \App\Models\User::AGENT_SERVICES[$service] ?? $service }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($application->isPending())
                                <span class="wp-badge wp-badge-amber">Pending</span>
                            @elseif($application->isApproved())
                                <span class="wp-badge wp-badge-green">Approved</span>
                            @else
                                <span class="wp-badge wp-badge-red">Rejected</span>
                            @endif
                        </td>
                        <td style="font-size:12px; color: var(--wp-text-secondary);">{{ $application->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('manager.agent-applications.show', $application->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
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
    @if($applications->hasPages())
        <div class="wp-pagination">
            {{ $applications->links() }}
        </div>
    @endif
</div>
<style>
.application-filters{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;align-items:end}.application-filters label>span{display:block;color:#aaa;font-size:11px;text-transform:uppercase;margin-bottom:4px}.application-filter-actions{display:flex;gap:7px;align-items:center}@media(max-width:600px){.application-filters{grid-template-columns:1fr}}
</style>
@endsection
