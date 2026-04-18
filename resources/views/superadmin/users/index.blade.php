@extends('layouts.superadmin')

@section('title', 'All Users')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users me-2"></i>All Users</h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
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
                    <option value="company_staff" {{ request('role') === 'company_staff' ? 'selected' : '' }}>Company Staff</option>
                    <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
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
                    <th>Orders</th>
                    <th>Created</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="fw-500">{{ $user->name }}</div>
                        <small class="text-muted">{{ $user->email }}</small>
                    </td>
                    <td>
                        @if($user->company)
                        <a href="{{ route('superadmin.companies.show', $user->company) }}" class="text-info">
                            {{ $user->company->name }}
                        </a>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{
                            $user->role === 'super_admin' ? 'danger' :
                            ($user->role === 'company_owner' ? 'primary' :
                            ($user->role === 'company_admin' ? 'info' :
                            ($user->role === 'company_staff' ? 'warning' : 'secondary')))
                        }}">
                            {{ str_replace('_', ' ', ucfirst($user->role ?? 'customer')) }}
                        </span>
                    </td>
                    <td>{{ $user->esim_orders_count ?? 0 }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-xs btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
