@extends('layouts.superadmin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-pie me-2"></i>Super Admin Dashboard</h1>
    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Company
    </a>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: var(--sa-primary);">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_companies']) }}</div>
            <div class="stat-label">Total Companies</div>
            <small class="text-success">{{ $stats['active_companies'] }} active</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.15); color: var(--sa-success);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_revenue'], 0) }}</div>
            <div class="stat-label">Total Revenue (AED)</div>
            <small class="text-muted">All companies</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: var(--sa-info);">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-label">Total Orders</div>
            <small class="text-muted">All companies</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: var(--sa-warning);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Total Users</div>
            <small class="text-muted">All companies</small>
        </div>
    </div>
</div>

<!-- Subscription Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value text-warning">{{ $stats['trial_companies'] }}</div>
            <div class="stat-label">Trial Companies</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value text-success">{{ $stats['paid_companies'] }}</div>
            <div class="stat-label">Paid Companies</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value text-danger">{{ $stats['expiring_soon'] }}</div>
            <div class="stat-label">Expiring in 7 Days</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Companies -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clock me-2"></i>Recent Companies</span>
                <a href="{{ route('superadmin.companies.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompanies as $company)
                        <tr>
                            <td>
                                <div class="fw-500">{{ $company->name }}</div>
                                <small class="text-muted">{{ $company->subdomain }}.gotrips.ai</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning')) }}">
                                    {{ ucfirst($company->plan) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }}">
                                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $company->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-xs btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No companies yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Companies -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trophy me-2 text-warning"></i>Top Companies by Revenue
            </div>
            <div class="card-body p-0">
                @forelse($topCompanies as $index => $company)
                <div class="d-flex align-items-center p-3 border-bottom border-dark">
                    <div class="me-3">
                        <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark') }}">
                            #{{ $index + 1 }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-500">{{ $company->name }}</div>
                        <small class="text-muted">{{ $company->total_orders }} orders</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-600">AED {{ number_format($company->total_revenue, 2) }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No revenue data yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
