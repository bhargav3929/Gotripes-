@extends('layouts.manager')

@section('title', 'Departure Dates — ' . $package->title)

@section('page-title')
    <i class="fas fa-calendar-alt" style="color:var(--wp-primary);"></i>
    Departures: {{ Str::limit($package->title, 45) }}
@endsection

@section('content')

{{-- Back link + header --}}
<div class="wp-page-header">
    <div>
        <a href="{{ route('manager.umrah-packages.index') }}" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-bottom:10px;">
            <i class="fas fa-arrow-left"></i> All Packages
        </a>
        <h2 style="margin:0; font-size:18px; color:var(--wp-text);">{{ $package->title }}</h2>
        <span class="wp-badge wp-badge-amber" style="font-size:11px;">
            {{ strtoupper($package->category ?? 'bus') }} · {{ ucfirst($package->sub_category ?? '') }}
        </span>
    </div>
    <div style="text-align:right;">
        <span style="font-size:12px; color:var(--wp-text-muted);">Base Price</span>
        <div style="font-size:20px; font-weight:800; color:var(--wp-primary);">
            {{ $package->currency }} {{ number_format($package->price, 0) }}
        </div>
    </div>
</div>

@if(session('success'))
<div class="wp-alert wp-alert-success" style="margin-bottom:16px; padding:12px 16px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#22c55e; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="wp-alert wp-alert-danger" style="margin-bottom:16px; padding:12px 16px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:8px; color:#ef4444;">
    <i class="fas fa-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<div class="row g-4">

    {{-- Departure List --}}
    <div class="col-lg-8">
        <div class="wp-card">
            <div style="padding:18px 20px; border-bottom:1px solid var(--wp-border-light); display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--wp-text);">
                    <i class="fas fa-list" style="color:var(--wp-primary); margin-right:8px;"></i>
                    Scheduled Departures ({{ count($departures) }})
                </h3>
                @php
                    $upcoming = $departures->filter(fn($d) => $d->departure_date->isFuture())->count();
                    $totalAvail = $departures->sum(fn($d) => max(0, $d->seats_available - $d->seats_booked));
                @endphp
                <div style="font-size:12px; color:var(--wp-text-muted);">
                    <span style="color:#22c55e; font-weight:700;">{{ $upcoming }}</span> upcoming ·
                    <span style="color:var(--wp-primary); font-weight:700;">{{ $totalAvail }}</span> seats free
                </div>
            </div>

            <div class="table-responsive">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>Departure Date</th>
                            <th style="width:80px; text-align:center;">Day</th>
                            <th style="width:110px; text-align:center;">Capacity</th>
                            <th style="width:90px; text-align:center;">Booked</th>
                            <th style="width:90px; text-align:center;">Available</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:80px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departures as $dep)
                        @php
                            $avail    = $dep->seats_available - $dep->seats_booked;
                            $isPast   = $dep->departure_date->isPast();
                            $fillPct  = $dep->seats_available > 0
                                        ? round(($dep->seats_booked / $dep->seats_available) * 100)
                                        : 0;
                        @endphp
                        <tr style="{{ $isPast ? 'opacity:0.55;' : '' }}">
                            <td>
                                <strong style="color:var(--wp-text);">{{ $dep->departure_date->format('d M Y') }}</strong>
                                @if($isPast)
                                    <span class="wp-badge" style="background:rgba(107,114,128,0.15); color:#9ca3af; font-size:9px; margin-left:6px;">Past</span>
                                @elseif($dep->departure_date->isToday())
                                    <span class="wp-badge" style="background:rgba(234,179,8,0.15); color:#eab308; font-size:9px; margin-left:6px;">Today</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="wp-badge" style="background:rgba(59,130,246,0.12); color:#60a5fa; font-size:10px;">Wed</span>
                            </td>
                            <td style="text-align:center;">
                                <form action="{{ route('manager.umrah-packages.departures.update', [$package->id, $dep->id]) }}"
                                      method="POST" id="form-dep-{{ $dep->id }}">
                                    @csrf @method('PUT')
                                    <input type="number" name="seats_available" value="{{ $dep->seats_available }}"
                                           class="wp-input" min="0" style="width:70px; text-align:center; padding:5px 8px; font-size:13px;"
                                           onchange="document.getElementById('form-dep-{{ $dep->id }}').submit()">
                            </td>
                            <td style="text-align:center;">
                                <span style="font-weight:700; color:{{ $dep->seats_booked > 0 ? 'var(--wp-primary)' : 'var(--wp-text-muted)' }}">
                                    {{ $dep->seats_booked }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span style="font-weight:700; color:{{ $avail > 10 ? '#22c55e' : ($avail > 0 ? '#eab308' : '#ef4444') }}">
                                    {{ $avail }}
                                </span>
                                <div style="width:100%; height:4px; background:var(--wp-bg-secondary); border-radius:4px; margin-top:4px; overflow:hidden;">
                                    <div style="height:100%; width:{{ $fillPct }}%; background:{{ $fillPct >= 90 ? '#ef4444' : ($fillPct >= 60 ? '#eab308' : '#22c55e') }}; border-radius:4px;"></div>
                                </div>
                            </td>
                            <td>
                                    <select name="status" class="wp-select"
                                            style="font-size:12px; padding:5px 8px;"
                                            onchange="document.getElementById('form-dep-{{ $dep->id }}').submit()">
                                        <option value="available" {{ $dep->status == 'available' ? 'selected' : '' }}>✅ Available</option>
                                        <option value="sold_out"  {{ $dep->status == 'sold_out'  ? 'selected' : '' }}>🔴 Sold Out</option>
                                        <option value="inactive"  {{ $dep->status == 'inactive'  ? 'selected' : '' }}>⚪ Inactive</option>
                                    </select>
                                </form>
                            </td>
                            <td style="text-align:center;">
                                <form action="{{ route('manager.umrah-packages.departures.destroy', [$package->id, $dep->id]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Remove this departure date?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="padding:32px; text-align:center; color:var(--wp-text-muted);">
                                <i class="fas fa-calendar-times" style="font-size:28px; display:block; margin-bottom:10px; color:var(--wp-border);"></i>
                                No departure dates configured. Add your first Wednesday departure →
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Departure Form --}}
    <div class="col-lg-4">
        <div class="wp-card" style="position:sticky; top:85px;">
            <div style="padding:18px 20px; border-bottom:1px solid var(--wp-border-light);">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--wp-text);">
                    <i class="fas fa-plus" style="color:var(--wp-primary); margin-right:8px;"></i>
                    Add Departure Date
                </h3>
            </div>
            <div style="padding:20px;">
                <div style="background:rgba(255,215,0,0.06); border:1px solid rgba(255,215,0,0.15); border-radius:8px; padding:10px 13px; font-size:12px; color:var(--wp-text-muted); margin-bottom:18px; line-height:1.5;">
                    <i class="fas fa-info-circle" style="color:var(--wp-primary); margin-right:6px;"></i>
                    <strong style="color:var(--wp-primary);">Wednesdays only.</strong> Invalid dates are rejected automatically.
                </div>

                <form action="{{ route('manager.umrah-packages.departures.store', $package->id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom:14px;">
                        <label class="wp-label" style="display:block; font-size:12px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; margin-bottom:6px;">
                            Departure Date <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="date" name="departure_date" class="wp-input" required value="{{ old('departure_date') }}"
                               min="{{ now()->addDays(5)->toDateString() }}">
                        <div id="day-hint" style="font-size:11px; color:var(--wp-text-muted); margin-top:5px;"></div>
                    </div>

                    <div style="margin-bottom:14px;">
                        <label class="wp-label" style="display:block; font-size:12px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; margin-bottom:6px;">
                            Seats Available <span style="color:#ef4444;">*</span>
                        </label>
                        <input type="number" name="seats_available" class="wp-input" required min="1"
                               value="{{ old('seats_available', 50) }}">
                    </div>

                    <div style="margin-bottom:18px;">
                        <label class="wp-label" style="display:block; font-size:12px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; margin-bottom:6px;">
                            Status
                        </label>
                        <select name="status" class="wp-select">
                            <option value="available">✅ Available</option>
                            <option value="sold_out">🔴 Sold Out</option>
                            <option value="inactive">⚪ Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="wp-btn wp-btn-primary w-100">
                        <i class="fas fa-plus me-2"></i> Add Departure
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show weekday name hint
document.querySelector('input[name="departure_date"]')?.addEventListener('change', function() {
    const hint = document.getElementById('day-hint');
    if (!this.value) { hint.textContent = ''; return; }
    const d = new Date(this.value + 'T00:00:00');
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const name = days[d.getDay()];
    if (d.getDay() !== 3) {
        hint.style.color = '#ef4444';
        hint.innerHTML = '<i class="fas fa-times-circle me-1"></i>' + name + ' — must be a Wednesday.';
    } else {
        hint.style.color = '#22c55e';
        hint.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + name + ' — valid!';
    }
});
</script>
@endsection
