@extends('layouts.superadmin')

@section('title', 'All Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 style="font-size: 1.3rem; font-weight: 800; margin: 0; color: var(--text-white);">
        <i class="fas fa-users me-2" style="color: var(--gold);"></i>Users
        <span class="badge bg-primary ms-2" style="font-size: 0.7rem;">{{ $users->total() }}</span>
    </h1>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="company_id" class="form-select form-select-sm">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="company_owner" {{ request('role') === 'company_owner' ? 'selected' : '' }}>Owner</option>
                    <option value="company_admin" {{ request('role') === 'company_admin' ? 'selected' : '' }}>Admin</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
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
                        <div class="d-flex align-items-center gap-2">
                            <div class="company-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-600 text-white">{{ $user->name }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</small>
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
