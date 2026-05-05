@extends('layouts.manager')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome -->
<div class="wp-page-header">
    <h1 class="wp-page-title">Welcome, {{ auth()->user()?->name ?? 'Manager' }}</h1>
</div>

<!-- ═════════════ Analytics ═════════════ -->
@php
    $currency = current_company()?->currency ?? 'AED';
    $periodLabels = ['today' => 'Today', '7d' => 'Last 7 days', '30d' => 'Last 30 days'];
@endphp

<div class="analytics-toolbar">
    <div class="analytics-period">
        @foreach($periodLabels as $key => $label)
            <a href="{{ route('manager.dashboard', ['period' => $key]) }}"
               class="analytics-pill {{ $period === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="analytics-grid">
    <div class="analytics-card">
        <div class="analytics-icon"><i class="fas fa-receipt"></i></div>
        <div class="analytics-meta">
            <div class="analytics-label">Total bookings</div>
            <div class="analytics-value">{{ number_format($totalBookings) }}</div>
            <div class="analytics-sub">{{ $periodLabels[$period] }}</div>
        </div>
    </div>

    <div class="analytics-card">
        <div class="analytics-icon"><i class="fas fa-dollar-sign"></i></div>
        <div class="analytics-meta">
            <div class="analytics-label">Revenue</div>
            <div class="analytics-value">{{ $currency }} {{ number_format($revenue, 2) }}</div>
            <div class="analytics-sub">{{ $periodLabels[$period] }}</div>
        </div>
    </div>

    <div class="analytics-card">
        <div class="analytics-icon"><i class="fas fa-coins"></i></div>
        <div class="analytics-meta">
            <div class="analytics-label">Commission earned</div>
            <div class="analytics-value">{{ $currency }} {{ number_format($commission, 2) }}</div>
            <div class="analytics-sub">{{ $periodLabels[$period] }}</div>
        </div>
    </div>

    <div class="analytics-card">
        <div class="analytics-icon"><i class="fas fa-calendar-week"></i></div>
        <div class="analytics-meta">
            <div class="analytics-label">Bookings · last 7 days</div>
            <div class="analytics-value">{{ number_format($last7DaysBookings) }}</div>
            <div class="analytics-sub">rolling window</div>
        </div>
    </div>
</div>

<div class="analytics-chart-card">
    <div class="analytics-chart-header">
        <i class="fas fa-chart-line"></i>
        <span>Commission · last 30 days</span>
    </div>
    @if($series->isEmpty())
        <div class="analytics-chart-empty">No commission earned in the last 30 days yet.</div>
    @else
        <canvas id="commissionChart" height="80"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const ctx = document.getElementById('commissionChart');
                if (!ctx) return;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($series->pluck('d')),
                        datasets: [{
                            label: '{{ $currency }} per day',
                            data: @json($series->pluck('v')->map(fn($v) => (float) $v)),
                            borderColor: '#FFD700',
                            backgroundColor: 'rgba(255,215,0,0.08)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointBackgroundColor: '#FFD700',
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { labels: { color: '#bbb' } },
                        },
                        scales: {
                            x: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                            y: { ticks: { color: '#888' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                        },
                    },
                });
            })();
        </script>
    @endif
</div>

<style>
    .analytics-toolbar { display:flex; justify-content:flex-end; margin-bottom:14px; }
    .analytics-period { display:inline-flex; gap:4px; background:#1a1a1a;
                         border:1px solid rgba(255,215,0,.18); border-radius:99px; padding:4px; }
    .analytics-pill { padding:6px 14px; border-radius:99px; font-size:12px; font-weight:600;
                       color:#aaa; text-decoration:none; transition:all .15s; }
    .analytics-pill:hover { color:#fff; background:rgba(255,215,0,.05); }
    .analytics-pill.active { background:linear-gradient(135deg,#FFD700,#FFA500); color:#1a1a1a; }

    .analytics-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
                       gap:14px; margin-bottom:18px; }
    .analytics-card { background:#1a1a1a; border:1px solid rgba(255,215,0,.18);
                       border-radius:12px; padding:18px 20px;
                       display:flex; gap:14px; align-items:center; }
    .analytics-icon { width:44px; height:44px; border-radius:10px;
                       background:rgba(255,215,0,.08); border:1px solid rgba(255,215,0,.2);
                       display:flex; align-items:center; justify-content:center;
                       color:#FFD700; font-size:18px; flex-shrink:0; }
    .analytics-meta { min-width:0; flex:1; }
    .analytics-label { font-size:11px; font-weight:600; text-transform:uppercase;
                        letter-spacing:.5px; color:rgba(255,215,0,.7); margin-bottom:4px; }
    .analytics-value { font-size:24px; font-weight:700; color:#fff; line-height:1.1;
                        word-break:break-all; }
    .analytics-sub { font-size:12px; color:#888; margin-top:2px; }

    .analytics-chart-card { background:#1a1a1a; border:1px solid rgba(255,215,0,.18);
                              border-radius:12px; margin-bottom:18px; overflow:hidden; }
    .analytics-chart-header { padding:14px 18px; font-size:12px; font-weight:600;
                                text-transform:uppercase; letter-spacing:.5px;
                                color:rgba(255,215,0,.85); display:flex; gap:8px; align-items:center;
                                border-bottom:1px solid rgba(255,215,0,.08); }
    .analytics-chart-card canvas { padding:18px; }
    .analytics-chart-empty { padding:40px 20px; text-align:center; color:#666; font-size:13px; }
</style>

<!-- At a Glance -->

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="wp-card">
            <div class="wp-card-header">
                <i class="fas fa-photo-video text-secondary-wp"></i>
                Hero Ad Slots
            </div>
            <div class="wp-card-body">
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--wp-primary);">{{ $adCount }}</span>
                    <span class="text-muted-wp">of 6 slots used</span>
                </div>
                <div style="height: 4px; background: var(--wp-border-light); border-radius: 2px; margin-bottom: 16px;">
                    <div style="height: 100%; width: {{ ($adCount/6)*100 }}%; background: var(--wp-primary); border-radius: 2px; transition: width 0.3s;"></div>
                </div>
                <a href="{{ route('manager.adslots.index') }}" class="wp-btn wp-btn-primary">
                    <i class="fas fa-cog"></i> Manage Slots
                </a>
                @if($adCount < 6)
                    <a href="{{ route('manager.adslots.create') }}" class="wp-btn wp-btn-secondary">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wp-card">
            <div class="wp-card-header">
                <i class="fas fa-rss text-secondary-wp"></i>
                News Ticker
            </div>
            <div class="wp-card-body">
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--wp-primary);">{{ $announcementCount }}</span>
                    <span class="text-muted-wp">active announcements</span>
                </div>
                <div style="display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap;">
                    <span class="wp-badge wp-badge-red">Breaking</span>
                    <span class="wp-badge wp-badge-amber">Trending</span>
                    <span class="wp-badge wp-badge-green">Exclusive</span>
                    <span class="wp-badge wp-badge-blue">New</span>
                </div>
                <a href="{{ route('manager.announcements.index') }}" class="wp-btn wp-btn-primary">
                    <i class="fas fa-cog"></i> Manage Ticker
                </a>
                <a href="{{ route('manager.announcements.create') }}" class="wp-btn wp-btn-secondary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wp-card">
            <div class="wp-card-header">
                <i class="fas fa-hiking text-secondary-wp"></i>
                UAE Activities
            </div>
            <div class="wp-card-body">
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--wp-primary);">{{ $activityCount }}</span>
                    <span class="text-muted-wp">active activities</span>
                </div>
                <div style="display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap;">
                    <span class="wp-badge wp-badge-amber">Dubai</span>
                    <span class="wp-badge wp-badge-green">Abu Dhabi</span>
                    <span class="wp-badge wp-badge-blue">+more</span>
                </div>
                <a href="{{ route('manager.activities.index') }}" class="wp-btn wp-btn-primary">
                    <i class="fas fa-cog"></i> Manage
                </a>
                <a href="{{ route('manager.activities.create') }}" class="wp-btn wp-btn-secondary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Info -->
<div class="wp-card">
    <div class="wp-card-header">
        <i class="fas fa-info-circle text-secondary-wp"></i>
        Quick Info
    </div>
    <div class="wp-card-body" style="font-size: 13px; color: var(--wp-text-secondary);">
        <p style="margin-bottom: 8px;"><strong>Hero Ad Slots</strong> appear in the homepage carousel. You can upload images or videos (MP4). Maximum 6 slots, 4 visible at a time.</p>
        <p style="margin-bottom: 0;"><strong>News Ticker</strong> items scroll across the header. Each item can be tagged with a color-coded label: Breaking (red), Trending (gold), Exclusive (green), or New (blue).</p>
    </div>
</div>
@endsection
