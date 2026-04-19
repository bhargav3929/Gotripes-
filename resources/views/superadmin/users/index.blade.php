@extends('layouts.superadmin')

@section('title', 'All Users')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users"></i>All Users</h1>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">{{ $users->total() }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-value">{{ $users->where('role', 'super_admin')->count() }}</div>
            <div class="stat-label">Super Admins</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: var(--info);">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-value">{{ $users->where('role', 'company_owner')->count() }}</div>
            <div class="stat-label">Company Owners</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">
                <i class="fas fa-user"></i>
            </div>
            <div class="stat-value">{{ $users->where('role', 'customer')->count() }}</div>
            <div class="stat-label">Customers</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Company</label>
                <select name="company_id" class="form-select">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="company_owner" {{ request('role') === 'company_owner' ? 'selected' : '' }}>Company Owner</option>
                    <option value="company_admin" {{ request('role') === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                    <option value="company_staff" {{ request('role') === 'company_staff' ? 'selected' : '' }}>Staff</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-search me-2"></i>Search
                </button>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i>
        User Directory
        <span class="badge bg-primary ms-2">{{ $users->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Company</th>
                    <th>Role</th>
                    <th class="text-center">Orders</th>
                    <th>Joined</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="company-avatar" style="width: 42px; height: 42px; font-size: 1rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-700 text-white">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->company)
                        <a href="{{ route('superadmin.companies.show', $user->company) }}">
                            {{ $user->company->name }}
                        </a>
                        @else
                        <span class="text-muted">No company</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $roleColors = [
                                'super_admin' => 'danger',
                                'company_owner' => 'primary',
                                'company_admin' => 'info',
                                'company_staff' => 'warning',
                                'customer' => 'success'
                            ];
                            $roleLabels = [
                                'super_admin' => 'Super Admin',
                                'company_owner' => 'Owner',
                                'company_admin' => 'Admin',
                                'company_staff' => 'Staff',
                                'customer' => 'Customer'
                            ];
                        @endphp
                        <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                            {{ $roleLabels[$user->role] ?? ucfirst($user->role ?? 'User') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="fw-600">{{ $user->esim_orders_count ?? 0 }}</span>
                    </td>
                    <td>
                        <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-xs btn-outline-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-xs btn-outline-warning" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->role !== 'super_admin')
                            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h5>No Users Found</h5>
                            <p>Try adjusting your search filters</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
