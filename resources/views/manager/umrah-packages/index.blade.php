@extends('layouts.manager')

@section('title', 'Umrah Packages')
@section('page-title', 'Umrah & Saudi Packages')

@section('content')

{{-- Page Header --}}
<div class="wp-page-header">
    <h1 class="wp-page-title">
        <i class="fas fa-kaaba" style="color: var(--wp-primary); margin-right: 8px;"></i>
        Hajj & Umrah Packages
    </h1>
    <a href="{{ route('manager.umrah-packages.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add Package
    </a>
</div>

{{-- Flash message --}}
@if(session('success'))
<div class="wp-alert wp-alert-success" style="margin-bottom:18px; padding:12px 16px; background: rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#22c55e; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- Quick KPI bar --}}
@php
    $total    = $packages->total();
    $active   = \App\Models\UmrahPackage::where('isActive',1)->count();
    $inactive = \App\Models\UmrahPackage::where('isActive',0)->count();
@endphp
<div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:22px;">
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:26px; font-weight:800; color:var(--wp-text);">{{ $total }}</div>
        <div style="font-size:12px; color:var(--wp-text-muted); margin-top:4px;">Total Packages</div>
    </div>
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:26px; font-weight:800; color:#22c55e;">{{ $active }}</div>
        <div style="font-size:12px; color:var(--wp-text-muted); margin-top:4px;">Active</div>
    </div>
    <div class="wp-card" style="padding:16px; text-align:center;">
        <div style="font-size:26px; font-weight:800; color:#ef4444;">{{ $inactive }}</div>
        <div style="font-size:12px; color:var(--wp-text-muted); margin-top:4px;">Inactive</div>
    </div>
    <div class="wp-card" style="padding:16px; text-align:center; cursor:pointer;" onclick="window.location='{{ route('manager.umrah-bookings.index') }}'">
        <div style="font-size:26px; font-weight:800; color:var(--wp-primary);">
            <i class="fas fa-arrow-right" style="font-size:18px;"></i>
        </div>
        <div style="font-size:12px; color:var(--wp-text-muted); margin-top:4px;">View Bookings</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="wp-card" style="padding:16px; margin-bottom:16px;">
    <form method="GET" action="{{ route('manager.umrah-packages.index') }}" style="display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;">
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Type</label>
            <select name="category" class="wp-select" style="min-width:110px;">
                <option value="">All Types</option>
                <option value="bus" {{ request('category')=='bus' ? 'selected' : '' }}>🚌 Bus</option>
                <option value="air" {{ request('category')=='air' ? 'selected' : '' }}>✈️ Air</option>
            </select>
        </div>
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Tier</label>
            <select name="sub_category" class="wp-select" style="min-width:130px;">
                <option value="">All Tiers</option>
                <option value="economy"  {{ request('sub_category')=='economy'  ? 'selected' : '' }}>Economy</option>
                <option value="standard" {{ request('sub_category')=='standard' ? 'selected' : '' }}>Standard</option>
                <option value="premium"  {{ request('sub_category')=='premium'  ? 'selected' : '' }}>Premium</option>
                <option value="vip"      {{ request('sub_category')=='vip'      ? 'selected' : '' }}>VIP</option>
            </select>
        </div>
        <div>
            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Status</label>
            <select name="status" class="wp-select" style="min-width:110px;">
                <option value="">All</option>
                <option value="active"   {{ request('status')=='active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="wp-btn wp-btn-primary wp-btn-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
            @if(request()->hasAny(['category','sub_category','status']))
            <a href="{{ route('manager.umrah-packages.index') }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Packages Table --}}
<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th style="width:80px;">Image</th>
                    <th>Package</th>
                    <th style="width:110px;">Type / Tier</th>
                    <th style="width:140px;">Pricing</th>
                    <th style="width:110px;">Duration</th>
                    <th style="width:80px;">Featured</th>
                    <th style="width:90px;">Status</th>
                    <th style="width:190px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $index => $package)
                <tr id="pkg-row-{{ $package->id }}">
                    <td style="color: var(--wp-text-muted);">{{ $packages->firstItem() + $index }}</td>
                    <td>
                        @if($package->image)
                            <img src="{{ str_starts_with($package->image,'http') ? $package->image : asset($package->image) }}"
                                 alt="{{ $package->title }}"
                                 style="width:70px; height:48px; object-fit:cover; border-radius:6px; border:1px solid var(--wp-border-light);"
                                 onerror="this.style.display='none'">
                        @else
                            <div style="width:70px; height:48px; background:var(--wp-bg-secondary); border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-image" style="color:var(--wp-text-muted);"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="color:var(--wp-text); display:block;">{{ Str::limit($package->title, 55) }}</strong>
                        @if($package->description)
                            <span style="font-size:11px; color:var(--wp-text-muted);">{{ Str::limit(strip_tags($package->description), 65) }}</span>
                        @endif
                        @if($package->tag)
                            <span class="wp-badge wp-badge-amber" style="margin-top:4px;">{{ $package->tag }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="wp-badge" style="background:rgba(59,130,246,0.15); color:#60a5fa; font-size:10px; margin-bottom:4px; display:inline-block;">
                            {{ $package->category == 'bus' ? '🚌 Bus' : '✈️ Air' }}
                        </span>
                        <br>
                        <span class="wp-badge wp-badge-amber" style="font-size:10px;">
                            {{ ucfirst($package->sub_category ?? '—') }}
                        </span>
                    </td>
                    <td>
                        @if($package->discount_price && $package->discount_price < $package->price)
                            <span style="text-decoration:line-through; color:var(--wp-text-muted); font-size:12px;">{{ $package->currency }} {{ number_format($package->price, 0) }}</span>
                            <br>
                            <strong style="color:var(--wp-primary);">{{ $package->currency }} {{ number_format($package->discount_price, 0) }}</strong>
                        @else
                            <strong style="color:var(--wp-primary);">{{ $package->currency }} {{ number_format($package->price, 0) }}</strong>
                        @endif
                        @if($package->adult_price)
                            <br><span style="font-size:10px; color:var(--wp-text-muted);">Adult: {{ number_format($package->adult_price,0) }}</span>
                        @endif
                    </td>
                    <td style="font-size:13px; color:var(--wp-text-secondary);">
                        <i class="fas fa-clock" style="color:var(--wp-primary); margin-right:4px;"></i>
                        {{ $package->duration }}
                    </td>
                    <td style="text-align:center;">
                        @if($package->isFeatured)
                            <i class="fas fa-star" style="color:var(--wp-primary);"></i>
                        @else
                            <span style="color:var(--wp-text-muted); font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        {{-- Toggle switch --}}
                        <div style="display:flex; flex-direction:column; align-items:center; gap:4px;">
                            <label class="pkg-toggle" style="cursor:pointer; position:relative; display:inline-block; width:42px; height:22px;">
                                <input type="checkbox" {{ $package->isActive ? 'checked' : '' }}
                                       onchange="togglePackage({{ $package->id }}, this)"
                                       style="opacity:0; width:0; height:0;">
                                <span class="pkg-slider" data-id="{{ $package->id }}"
                                      style="position:absolute; inset:0; border-radius:22px; transition:0.3s; background:{{ $package->isActive ? 'var(--wp-primary)' : '#555' }};">
                                    <span style="position:absolute; left:{{ $package->isActive ? '22px' : '2px' }}; top:2px; width:18px; height:18px; border-radius:50%; background:#fff; transition:0.3s; display:block;"></span>
                                </span>
                            </label>
                            <span id="pkg-status-{{ $package->id }}" style="font-size:10px; font-weight:600; color:{{ $package->isActive ? '#22c55e' : '#ef4444' }};">
                                {{ $package->isActive ? 'Active' : 'Off' }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; gap:5px; flex-wrap:wrap;">
                            <a href="{{ route('manager.umrah-packages.departures.index', $package->id) }}"
                               class="wp-btn wp-btn-sm"
                               style="background:#16a34a; border-color:#16a34a; color:#fff; font-size:11px;"
                               title="Manage Departures">
                                <i class="fas fa-calendar-alt"></i> Dates
                            </a>
                            <a href="{{ route('manager.umrah-packages.edit', $package->id) }}"
                               class="wp-btn wp-btn-secondary wp-btn-sm"
                               title="Edit Package">
                                <i class="fas fa-pen"></i>
                            </a>
                            <form action="{{ route('manager.umrah-packages.destroy', $package->id) }}" method="POST"
                                  onsubmit="return confirm('Archive this package?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" title="Archive">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div style="padding:32px; text-align:center;">
                            <i class="fas fa-kaaba" style="font-size:36px; color:var(--wp-border); display:block; margin-bottom:10px;"></i>
                            <p style="color:var(--wp-text-muted); margin-bottom:12px;">No packages found.</p>
                            <a href="{{ route('manager.umrah-packages.create') }}" class="wp-btn wp-btn-primary wp-btn-sm">
                                <i class="fas fa-plus"></i> Create First Package
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($packages->hasPages())
    <div class="wp-pagination" style="padding:16px 20px;">
        {{ $packages->links() }}
    </div>
    @endif
</div>

<style>
.pkg-toggle input:checked + .pkg-slider { background: var(--wp-primary) !important; }
.pkg-toggle input:checked + .pkg-slider span { left: 22px !important; }
.pkg-toggle .pkg-slider span { left: 2px; }
</style>

<script>
function togglePackage(id, checkbox) {
    const slider    = document.querySelector(`.pkg-slider[data-id="${id}"]`);
    const dot       = slider.querySelector('span');
    const statusEl  = document.getElementById('pkg-status-' + id);
    const isChecked = checkbox.checked;

    // Optimistic UI
    slider.style.background    = isChecked ? 'var(--wp-primary)' : '#555';
    dot.style.left             = isChecked ? '22px' : '2px';
    statusEl.style.color       = isChecked ? '#22c55e' : '#ef4444';
    statusEl.textContent       = isChecked ? 'Active' : 'Off';

    fetch(`/manager/umrah-packages/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            // Revert on failure
            checkbox.checked = !isChecked;
            slider.style.background = !isChecked ? 'var(--wp-primary)' : '#555';
            dot.style.left = !isChecked ? '22px' : '2px';
        }
    })
    .catch(() => {
        checkbox.checked = !isChecked;
        slider.style.background = !isChecked ? 'var(--wp-primary)' : '#555';
    });
}
</script>

@endsection
