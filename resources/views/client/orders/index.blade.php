@extends('layouts.client')

@section('title', 'eSIM Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title"><i class="fas fa-sim-card me-2"></i>eSIM Orders</h1>
    <span class="badge bg-dark px-3 py-2">{{ $orders->total() }} Total Orders</span>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, reference..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" placeholder="To">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('client.orders') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Orders List -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 120px;">ORDER ID</th>
                    <th>CUSTOMER</th>
                    <th>DESTINATION</th>
                    <th class="text-end">AMOUNT</th>
                    <th class="text-center">STATUS</th>
                    <th>DATE</th>
                    <th class="text-center" style="width: 80px;">VIEW</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <code class="text-gold">#{{ $order->order_reference ?? 'ORD'.$order->id }}</code>
                    </td>
                    <td>
                        <div class="fw-500">{{ $order->customer_name ?? '-' }}</div>
                        <small class="text-muted">{{ $order->customer_email ?? '' }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-2">{{ $order->country_name ?? '-' }}</span>
                        </div>
                        <small class="text-muted">{{ $order->bundle_name ?? '' }}</small>
                    </td>
                    <td class="text-end">
                        <span class="fw-600 text-success">{{ app('current_company')->currency ?? 'AED' }} {{ number_format($order->selling_price ?? 0, 2) }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $status = $order->payment_status ?? 'pending';
                            $statusClass = match($status) {
                                'paid', 'completed' => 'success',
                                'unpaid', 'pending' => 'warning',
                                default => 'danger'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($status) }}</span>
                    </td>
                    <td>
                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('client.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-sim-card fa-3x mb-3 text-muted"></i>
                        <p class="text-muted mb-0">No orders found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}</small>
        <div class="d-flex gap-2">
            @if($orders->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $orders->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-chevron-left"></i></a>
            @endif

            <span class="btn btn-sm btn-dark disabled">{{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>

            @if($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" class="btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
