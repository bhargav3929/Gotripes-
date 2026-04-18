@extends('layouts.client')

@section('title', 'Orders')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-shopping-cart me-2"></i>Orders</h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('client.orders') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, reference..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Filter</button>
                <a href="{{ route('client.orders') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Agent</th>
                    <th class="text-end">Amount</th>
                    <th class="text-center">Status</th>
                    <th>Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <span class="fw-500">#{{ $order->order_reference ?? $order->id }}</span>
                    </td>
                    <td>
                        <div>{{ $order->customer_name ?? $order->user->name ?? '-' }}</div>
                        <small class="text-muted">{{ $order->customer_email ?? $order->user->email ?? '' }}</small>
                    </td>
                    <td>
                        <div>{{ $order->bundle_name ?? '-' }}</div>
                        <small class="text-muted">{{ $order->country_name ?? '' }}</small>
                    </td>
                    <td>
                        @if($order->referralAgent)
                        <span class="badge bg-info">{{ $order->referralAgent->name }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <span class="text-success fw-500">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($order->selling_price ?? $order->total_amount ?? 0, 2) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ ($order->status ?? $order->payment_status) === 'completed' ? 'success' : (($order->status ?? $order->payment_status) === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($order->status ?? $order->payment_status ?? 'pending') }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('client.orders.show', $order) }}" class="btn btn-xs btn-outline-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-shopping-cart fa-3x mb-3 d-block"></i>
                        No orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer">{{ $orders->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
