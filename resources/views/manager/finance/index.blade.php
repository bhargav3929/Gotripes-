@extends('layouts.manager')
@section('title', 'Earnings')
@section('page-title', 'Earnings')

@section('content')
<style>
    .fin-grid { display:grid; grid-template-columns: repeat(4, 1fr); gap:18px; margin-bottom:28px; }
    .fin-card { background:#1a1a1a; border:1px solid rgba(255,215,0,0.18); border-radius:12px; padding:20px 22px; }
    .fin-card .label { font-size:11px; font-weight:600; letter-spacing:1.2px; text-transform:uppercase; color:#888; margin-bottom:10px; }
    .fin-card .value { font-size:26px; font-weight:800; color:#fff; letter-spacing:-0.02em; }
    .fin-card .value small { font-size:13px; font-weight:500; color:#888; margin-left:4px; }
    .fin-card.pending .value { color:#FFD23F; }
    .fin-card.available .value { color:#22c55e; }
    .fin-card.paid .value { color:#60a5fa; }
    .fin-card.total .value { color:#fff; }
    .fin-tip { background: rgba(255, 215, 0, 0.06); border-left: 3px solid #FFD700; padding: 14px 18px; border-radius: 8px; margin-bottom:24px; color:#ddd; font-size:13.5px; line-height:1.6; }
    .fin-recent { background:#1a1a1a; border:1px solid rgba(255,215,0,0.12); border-radius:12px; overflow:hidden; }
    .fin-recent table { width:100%; }
    .fin-recent th { background:#0d0d0d; padding:12px 16px; font-size:11px; letter-spacing:1.2px; text-transform:uppercase; color:#888; text-align:left; }
    .fin-recent td { padding:12px 16px; border-top:1px solid rgba(255,215,0,0.06); font-size:13.5px; color:#eee; }
    .fin-recent tr:hover td { background:#222; }
    .pill { display:inline-block; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; }
    .pill.pending  { background:rgba(255,215,0,0.12); color:#FFD23F; }
    .pill.available { background:rgba(34,197,94,0.12); color:#22c55e; }
    .pill.paid     { background:rgba(96,165,250,0.12); color:#60a5fa; }
    .pill.reversed { background:rgba(239,68,68,0.12); color:#f87171; }
    @media (max-width:900px) { .fin-grid { grid-template-columns: repeat(2, 1fr); } }
</style>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="fin-tip">
    <strong>Commission rate:</strong>
    @if($company->commission_type === 'flat')
        Flat <strong>{{ $totals['currency'] }} {{ number_format($company->commission_value, 2) }}</strong> per order
    @else
        <strong>{{ rtrim(rtrim(number_format($company->commission_value, 2), '0'), '.') }}%</strong> of every order's gross amount
    @endif
    · Set by your platform admin. Withdraw available funds anytime from the
    <a href="{{ route('manager.finance.withdrawals') }}" style="color:#FFD700;">Withdrawals</a> page.
</div>

<div class="fin-grid">
    <div class="fin-card pending">
        <div class="label">Pending</div>
        <div class="value">{{ number_format($totals['pending'], 2) }}<small>{{ $totals['currency'] }}</small></div>
    </div>
    <div class="fin-card available">
        <div class="label">Available to Withdraw</div>
        <div class="value">{{ number_format($totals['available'], 2) }}<small>{{ $totals['currency'] }}</small></div>
    </div>
    <div class="fin-card paid">
        <div class="label">Paid Out</div>
        <div class="value">{{ number_format($totals['paid'], 2) }}<small>{{ $totals['currency'] }}</small></div>
    </div>
    <div class="fin-card total">
        <div class="label">Total Bookings</div>
        <div class="value">{{ $bookingCount }}</div>
    </div>
</div>

<div class="fin-recent">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Gross</th>
                <th>Rate</th>
                <th>Commission</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent as $c)
                <tr>
                    <td>{{ $c->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ str_replace('_', ' ', $c->source_type) }} #{{ $c->source_id }}</td>
                    <td>{{ $c->currency }} {{ number_format($c->gross_amount, 2) }}</td>
                    <td>{{ $c->commission_type === 'flat' ? $c->currency.' '.number_format($c->commission_rate, 2).' flat' : rtrim(rtrim(number_format($c->commission_rate, 2), '0'), '.').'%' }}</td>
                    <td><strong style="color:#FFD700;">{{ $c->currency }} {{ number_format($c->commission_amount, 2) }}</strong></td>
                    <td><span class="pill {{ $c->status }}">{{ $c->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; padding:36px; color:#666;">No commissions yet. Once customers book, your earnings will appear here.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
