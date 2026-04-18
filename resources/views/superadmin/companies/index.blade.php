@extends('layouts.superadmin')

@section('title', 'Companies')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-building me-2"></i>Companies</h1>
    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Company
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('superadmin.companies.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, domain..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Plan</label>
                <select name="plan" class="form-select">
                    <option value="">All Plans</option>
                    <option value="trial" {{ request('plan') === 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                    <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Companies Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Domain</th>
                    <th>Owner</th>
                    <th>Plan</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($company->logo)
                            <img src="{{ $company->logo_url }}" alt="" class="rounded me-2" style="width: 32px; height: 32px; object-fit: contain;">
                            @else
                            <div class="rounded me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: var(--sa-primary);">
                                {{ strtoupper(substr($company->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <div class="fw-500">{{ $company->name }}</div>
                                <small class="text-muted">{{ $company->slug }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($company->domain)
                        <a href="https://{{ $company->domain }}" target="_blank" class="text-info">
                            {{ $company->domain }}
                        </a>
                        @else
                        <span class="text-muted">{{ $company->subdomain }}.gotrips.ai</span>
                        @endif
                    </td>
                    <td>
                        @if($company->owner)
                        <div>{{ $company->owner->name }}</div>
                        <small class="text-muted">{{ $company->owner->email }}</small>
                        @else
                        <span class="text-muted">No owner</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning')) }}">
                            {{ ucfirst($company->plan) }}
                        </span>
                        @if($company->isOnTrial())
                        <small class="d-block text-warning">Ends {{ $company->trial_ends_at->diffForHumans() }}</small>
                        @elseif($company->subscription_ends_at)
                        <small class="d-block text-muted">Ends {{ $company->subscription_ends_at->format('M d') }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($company->total_orders) }}</td>
                    <td class="text-end">
                        <span class="text-success">AED {{ number_format($company->total_revenue, 2) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-xs btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('superadmin.companies.impersonate', $company) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-primary" title="Impersonate">
                                    <i class="fas fa-user-secret"></i>
                                </button>
                            </form>
                            <form action="{{ route('superadmin.companies.toggle-status', $company) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-{{ $company->is_active ? 'danger' : 'success' }}" title="{{ $company->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $company->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-building fa-3x mb-3 d-block"></i>
                        No companies found
                        <div class="mt-3">
                            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">Create First Company</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div class="card-footer">{{ $companies->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
