@extends('layouts.superadmin')

@section('title', $user->name)

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title mb-0">{{ $user->name }}</h1>
            <small class="text-muted">{{ $user->email }}</small>
        </div>
        <span class="badge bg-{{
            $user->role === 'super_admin' ? 'danger' :
            ($user->role === 'company_owner' ? 'primary' :
            ($user->role === 'company_admin' ? 'info' :
            ($user->role === 'company_staff' ? 'warning' : 'secondary')))
        }} ms-2">
            {{ str_replace('_', ' ', ucfirst($user->role ?? 'customer')) }}
        </span>
    </div>
    <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i>Edit
    </a>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success">AED {{ number_format($stats['total_spent'], 2) }}</div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- User Info -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-user me-2"></i>User Information</div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" width="150">Name</td><td>{{ $user->name }}</td></tr>
                    <tr><td class="text-muted">Email</td><td>{{ $user->email }}</td></tr>
                    <tr><td class="text-muted">Role</td><td>{{ str_replace('_', ' ', ucfirst($user->role ?? 'customer')) }}</td></tr>
                    <tr>
                        <td class="text-muted">Company</td>
                        <td>
                            @if($user->company)
                            <a href="{{ route('superadmin.companies.show', $user->company) }}" class="text-info">
                                {{ $user->company->name }}
                            </a>
                            @else
                            <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td class="text-muted">Created</td><td>{{ $user->created_at->format('M d, Y H:i') }}</td></tr>
                    <tr><td class="text-muted">Last Updated</td><td>{{ $user->updated_at->format('M d, Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-shopping-cart me-2"></i>Recent Orders</div>
            <div class="card-body p-0">
                @forelse($user->esimOrders->take(10) as $order)
                <div class="d-flex align-items-center p-3 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <div class="fw-500">Order #{{ $order->id }}</div>
                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success">AED {{ number_format($order->total_amount, 2) }}</div>
                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">No orders yet</div>
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
                <strong class="text-danger">Delete User</strong>
                <p class="text-muted mb-0 small">Permanently delete this user and their data</p>
            </div>
            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST"
                  onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" {{ $user->role === 'super_admin' ? 'disabled' : '' }}>
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
