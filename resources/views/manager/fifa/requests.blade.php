@extends('layouts.manager')

@section('title', 'FIFA Ticket Requests')
@section('page-title', 'FIFA Ticket Requests')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Customer Ticket Requests</h1>
    <a href="{{ route('manager.fifa-tickets.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Tickets
    </a>
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Customer</th>
                    <th>Match</th>
                    <th style="width:90px;">Category</th>
                    <th style="width:60px;">Qty</th>
                    <th style="width:100px;">Quoted</th>
                    <th style="width:150px;">Received</th>
                    <th style="width:200px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td style="color:var(--wp-text-muted);">{{ $req->id }}</td>
                    <td>
                        <strong style="color:var(--wp-text);">{{ $req->name }}</strong><br>
                        <a href="mailto:{{ $req->email }}" style="font-size:12px; color:var(--wp-primary);">{{ $req->email }}</a>
                        @if($req->phone)<br><span style="font-size:12px; color:var(--wp-text-secondary);"><i class="fas fa-phone" style="font-size:10px;"></i> {{ $req->phone }}</span>@endif
                        @if($req->message)<br><span style="font-size:12px; color:var(--wp-text-muted); font-style:italic;">“{{ Str::limit($req->message, 80) }}”</span>@endif
                    </td>
                    <td style="font-size:13px;">{{ $req->match_label ?: ($req->match?->title ?? '—') }}</td>
                    <td>@if($req->category)<span class="wp-badge wp-badge-amber">{{ $req->category }}</span>@else — @endif</td>
                    <td><strong>{{ $req->quantity }}×</strong></td>
                    <td>@if($req->quoted_price)<span style="color:var(--wp-primary); font-weight:700;">${{ number_format($req->quoted_price, 0) }}</span>@else — @endif</td>
                    <td style="font-size:12px; color:var(--wp-text-secondary);">{{ $req->created_at?->format('d M Y, H:i') }}</td>
                    <td>
                        <form action="{{ route('manager.fifa-tickets.requests.status', $req->id) }}" method="POST" style="display:flex; gap:6px;">
                            @csrf
                            <select name="status" class="wp-input" style="padding:5px 8px; font-size:12px;">
                                @foreach(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $val => $label)
                                    <option value="{{ $val }}" @selected($req->status === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button class="wp-btn wp-btn-secondary wp-btn-sm"><i class="fas fa-check"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="8">
                        <div style="padding:24px 0; text-align:center; color:var(--wp-text-muted);">
                            <i class="fas fa-inbox" style="font-size:28px; display:block; margin-bottom:8px; color:var(--wp-border);"></i>
                            No ticket requests yet.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="wp-pagination">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
