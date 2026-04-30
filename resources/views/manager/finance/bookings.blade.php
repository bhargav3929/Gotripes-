@extends('layouts.manager')
@section('title', 'Bookings')
@section('page-title', 'Bookings')

@section('content')
<style>
    .b-table { background:#1a1a1a; border:1px solid rgba(255,215,0,0.12); border-radius:12px; overflow:hidden; }
    .b-table table { width:100%; border-collapse: collapse; }
    .b-table th { background:#0d0d0d; padding:12px 14px; font-size:11px; letter-spacing:1.2px; text-transform:uppercase; color:#888; text-align:left; }
    .b-table td { padding:12px 14px; border-top:1px solid rgba(255,215,0,0.06); font-size:13px; color:#eee; vertical-align:middle; }
    .b-table tr:hover td { background:#222; }
    .pill { display:inline-block; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:600; letter-spacing:0.5px; text-transform:uppercase; }
    .pill.pending  { background:rgba(255,215,0,0.12); color:#FFD23F; }
    .pill.available { background:rgba(34,197,94,0.12); color:#22c55e; }
    .pill.paid     { background:rgba(96,165,250,0.12); color:#60a5fa; }
    .pill.none     { background:rgba(255,255,255,0.06); color:#888; }
</style>

<div class="b-table">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Pax</th>
                <th>Amount</th>
                <th>Your Commission</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
                @php $com = $commissionByBooking->get($b->id); @endphp
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>
                        <strong style="color:#fff;">{{ $b->name }}</strong><br>
                        <small style="color:#888;">{{ $b->email }}</small>
                    </td>
                    <td>{{ \Illuminate\Support\Carbon::parse($b->date)->format('Y-m-d') }}</td>
                    <td>{{ $b->adults }}A · {{ $b->childrens }}C</td>
                    <td>{{ $b->currency }} {{ number_format($b->amount, 2) }}</td>
                    <td>
                        @if($com)
                            <strong style="color:#FFD700;">{{ $com->currency }} {{ number_format($com->commission_amount, 2) }}</strong>
                        @else
                            <span style="color:#666;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($com)
                            <span class="pill {{ $com->status }}">{{ $com->status }}</span>
                        @else
                            <span class="pill none">no commission</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center; padding:36px; color:#666;">No bookings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($bookings->hasPages())
        <div style="padding:14px 18px; background:#0d0d0d;">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
