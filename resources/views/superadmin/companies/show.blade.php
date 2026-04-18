@extends('layouts.superadmin')

@section('title', $company->name)

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title mb-0">{{ $company->name }}</h1>
            <small class="text-muted">{{ $company->subdomain }}.gotrips.ai</small>
        </div>
        <span class="badge bg-{{ $company->is_active ? 'success' : 'danger' }} ms-2">
            {{ $company->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('superadmin.companies.impersonate', $company) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="fas fa-user-secret me-2"></i>Impersonate
            </button>
        </form>
        <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Users</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-label">Orders</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success">AED {{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stat-label">Revenue</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['referral_agents']) }}</div>
            <div class="stat-label">Referral Agents</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Company Info -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-info-circle me-2"></i>Company Information</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="150">Name</td><td>{{ $company->name }}</td></tr>
                    <tr><td class="text-muted">Slug</td><td><code>{{ $company->slug }}</code></td></tr>
                    <tr><td class="text-muted">Subdomain</td><td>{{ $company->subdomain }}.gotrips.ai</td></tr>
                    <tr><td class="text-muted">Custom Domain</td><td>{{ $company->domain ?: 'Not set' }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $company->email }}</td></tr>
                    <tr><td class="text-muted">Phone</td><td>{{ $company->phone ?: 'Not set' }}</td></tr>
                    <tr><td class="text-muted">Currency</td><td>{{ $company->currency }}</td></tr>
                    <tr><td class="text-muted">Markup</td><td>{{ $company->markup_percentage }}%</td></tr>
                    <tr><td class="text-muted">Created</td><td>{{ $company->created_at->format('M d, Y H:i') }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Branding -->
        <div class="card">
            <div class="card-header"><i class="fas fa-palette me-2"></i>Branding</div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-4 mb-3">
                    @if($company->logo)
                    <img src="{{ $company->logo_url }}" alt="Logo" style="max-height: 60px;">
                    @else
                    <div class="text-muted">No logo uploaded</div>
                    @endif
                </div>
                <div class="d-flex gap-3">
                    <div class="text-center">
                        <div style="width: 40px; height: 40px; background: {{ $company->primary_color }}; border-radius: 8px;"></div>
                        <small class="text-muted">Primary</small>
                    </div>
                    <div class="text-center">
                        <div style="width: 40px; height: 40px; background: {{ $company->secondary_color }}; border-radius: 8px;"></div>
                        <small class="text-muted">Secondary</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription & Users -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-credit-card me-2"></i>Subscription</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Current Plan</span>
                    <span class="badge bg-{{ $company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning')) }} fs-6">
                        {{ ucfirst($company->plan) }}
                    </span>
                </div>

                @if($company->isOnTrial())
                <div class="alert alert-warning py-2 mb-3">
                    <i class="fas fa-clock me-2"></i>Trial ends {{ $company->trial_ends_at->diffForHumans() }}
                </div>
                @elseif($company->subscription_ends_at)
                <div class="alert alert-{{ $company->subscription_ends_at->isFuture() ? 'info' : 'danger' }} py-2 mb-3">
                    <i class="fas fa-calendar me-2"></i>Subscription {{ $company->subscription_ends_at->isFuture() ? 'ends' : 'ended' }} {{ $company->subscription_ends_at->format('M d, Y') }}
                </div>
                @endif

                <hr>

                <!-- Change Plan -->
                <form action="{{ route('superadmin.companies.change-plan', $company) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <select name="plan" class="form-select">
                            <option value="trial" {{ $company->plan === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="basic" {{ $company->plan === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ $company->plan === 'pro' ? 'selected' : '' }}>Pro</option>
                            <option value="enterprise" {{ $company->plan === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        <button type="submit" class="btn btn-outline-primary">Change Plan</button>
                    </div>
                </form>

                <!-- Extend Subscription -->
                <form action="{{ route('superadmin.companies.extend-subscription', $company) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="number" name="days" class="form-control" placeholder="Days" min="1" max="365" value="30">
                        <button type="submit" class="btn btn-outline-success">Extend</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admin Users -->
        <div class="card">
            <div class="card-header"><i class="fas fa-users me-2"></i>Admin Users</div>
            <div class="card-body p-0">
                @forelse($company->admins as $admin)
                <div class="d-flex align-items-center p-3 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <div class="fw-500">{{ $admin->name }}</div>
                        <small class="text-muted">{{ $admin->email }}</small>
                    </div>
                    <span class="badge bg-{{ $admin->role === 'company_owner' ? 'primary' : 'info' }}">
                        {{ str_replace('_', ' ', ucfirst($admin->role)) }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No admin users</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Danger Zone -->
<div class="card mt-4 border-danger">
    <div class="card-header text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $company->is_active ? 'Deactivate' : 'Activate' }} Company</strong>
                <p class="text-muted mb-0 small">{{ $company->is_active ? 'Suspend this company and prevent access' : 'Reactivate this company' }}</p>
            </div>
            <form action="{{ route('superadmin.companies.toggle-status', $company) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-{{ $company->is_active ? 'danger' : 'success' }}">
                    {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>

        <hr>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-danger">Delete Company</strong>
                <p class="text-muted mb-0 small">Permanently delete this company and all its data</p>
            </div>
            <form action="{{ route('superadmin.companies.destroy', $company) }}" method="POST"
                  onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
