@extends('layouts.superadmin')

@section('title', 'Companies')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-building"></i>Companies</h1>
    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Company
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('superadmin.companies.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, domain..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
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
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
                <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
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
                        <div class="d-flex align-items-center gap-3">
                            @if($company->logo)
                            <img src="{{ $company->logo_url }}" alt="" class="company-avatar" style="object-fit: contain;">
                            @else
                            <div class="company-avatar">
                                {{ strtoupper(substr($company->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <div class="fw-600">{{ $company->name }}</div>
                                <small class="text-muted">{{ $company->slug }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($company->domain)
                        <a href="https://{{ $company->domain }}" target="_blank" class="text-info text-decoration-none">
                            <i class="fas fa-external-link-alt me-1" style="font-size: 0.7rem;"></i>{{ $company->domain }}
                        </a>
                        @else
                        <span class="text-muted">{{ $company->subdomain }}.gotrips.ai</span>
                        @endif
                    </td>
                    <td>
                        @if($company->owner)
                        <div class="fw-500">{{ $company->owner->name }}</div>
                        <small class="text-muted">{{ $company->owner->email }}</small>
                        @else
                        <span class="text-muted"><i class="fas fa-user-slash me-1"></i>No owner</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning')) }}">
                            {{ ucfirst($company->plan) }}
                        </span>
                        @if($company->isOnTrial())
                        <div class="mt-1"><small class="text-warning"><i class="fas fa-clock me-1"></i>Ends {{ $company->trial_ends_at->diffForHumans() }}</small></div>
                        @elseif($company->subscription_ends_at)
                        <div class="mt-1"><small class="text-muted">Ends {{ $company->subscription_ends_at->format('M d, Y') }}</small></div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="fw-600">{{ number_format($company->total_orders) }}</span>
                    </td>
                    <td class="text-end">
                        <span class="text-success fw-600">AED {{ number_format($company->total_revenue, 2) }}</span>
                    </td>
                    <td class="text-center">
                        @if($company->is_active)
                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                        @else
                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Inactive</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-xs btn-outline-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('superadmin.companies.impersonate', $company) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-primary" title="Login as Company Admin">
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
                    <td colspan="8" class="text-center py-5">
                        <div class="py-4">
                            <i class="fas fa-building fa-4x mb-4 text-muted" style="opacity: 0.3;"></i>
                            <h5 class="text-muted mb-3">No Companies Found</h5>
                            <p class="text-muted mb-4">Get started by creating your first company</p>
                            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create First Company
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $companies->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
