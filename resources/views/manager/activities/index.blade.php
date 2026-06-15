@extends('layouts.manager')

@section('title', 'Activities Management')
@section('page-title', 'Activities Management')

@push('styles')
<style>
    /* ── Activity Tabs ─────────────────────────── */
    .wp-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 20px;
        border-bottom: 2px solid var(--wp-border-light);
        flex-wrap: wrap;
    }
    .wp-tab-btn {
        padding: 10px 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--wp-text-muted);
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .wp-tab-btn:hover {
        color: var(--wp-text-secondary);
    }
    .wp-tab-btn.active {
        color: var(--wp-primary);
        border-bottom-color: var(--wp-primary);
    }
    .wp-tab-btn .tab-count {
        background: rgba(255, 215, 0, 0.12);
        color: var(--wp-primary);
        font-size: 11px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 10px;
        min-width: 20px;
        text-align: center;
    }
    .wp-tab-btn.active .tab-count {
        background: rgba(255, 215, 0, 0.25);
    }
    .wp-tab-pane {
        display: none;
    }
    .wp-tab-pane.active {
        display: block;
    }
    .country-tab-flag {
        width: 20px;
        height: 14px;
        object-fit: cover;
        border-radius: 2px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Activities</h1>
    <a href="{{ route('manager.activities.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add UAE Activity
    </a>
</div>

{{-- ── Tab Navigation ──────────────────────── --}}
<div class="wp-tabs">
    {{-- Tab 1: UAE (always present) --}}
    <button class="wp-tab-btn active" data-tab="uae-activities">
        <img src="https://flagcdn.com/w40/ae.png" class="country-tab-flag" alt="UAE">
        Activities in the UAE
        <span class="tab-count">{{ $uaeActivities->total() }}</span>
    </button>

    {{-- Dynamic tabs: one per allowed non-UAE country --}}
    @foreach($countryTabs as $ct)
    <button class="wp-tab-btn" data-tab="{{ $ct['tabKey'] }}">
        @if($ct['flagSrc'])
            <img src="{{ $ct['flagSrc'] }}" class="country-tab-flag" alt="{{ $ct['name'] }}">
        @else
            {{ $ct['flag'] }}
        @endif
        {{ $ct['name'] }}
        <span class="tab-count">{{ $ct['activities']->total() }}</span>
    </button>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════
     TAB 1 — Activities in the UAE (full CRUD)
     ══════════════════════════════════════════════ --}}
<div class="wp-tab-pane active" id="tab-uae-activities">
    <div class="wp-card">
        <div class="table-responsive">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 80px;">Image</th>
                        <th>Activity Name</th>
                        <th style="width: 140px;">Emirate</th>
                        <th style="width: 120px;">Location</th>
                        <th style="width: 100px;">Price</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uaeActivities as $index => $activity)
                    <tr>
                        <td style="color: var(--wp-text-muted);">{{ $uaeActivities->firstItem() + $index }}</td>
                        <td>
                            @if($activity->activityImage)
                                <img src="{{ str_starts_with($activity->activityImage, 'http') ? $activity->activityImage : asset($activity->activityImage) }}"
                                     alt="{{ $activity->activityName }}"
                                     style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);"
                                     onerror="this.style.display='none';">
                            @else
                                <div style="width: 60px; height: 45px; background: #333; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: var(--wp-text-muted); font-size: 16px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--wp-text);">{{ Str::limit($activity->activityName, 40) }}</strong>
                        </td>
                        <td>
                            @if($activity->emirate)
                                <span class="wp-badge wp-badge-amber">{{ $activity->emirate->emiratesName }}</span>
                            @else
                                <span class="text-muted-wp" style="font-size: 12px;">Unassigned</span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: var(--wp-text-secondary);">
                            <i class="fas fa-map-marker-alt" style="color: var(--wp-primary); margin-right: 4px;"></i>
                            {{ Str::limit($activity->activityLocation, 20) }}
                        </td>
                        <td>
                            <strong style="color: var(--wp-primary);">${{ number_format($activity->activityPrice, 2) }}</strong>
                            @if($activity->activityChildPrice && $activity->activityChildPrice > 0)
                                <br><span style="font-size: 11px; color: var(--wp-text-muted);">Child: ${{ number_format($activity->activityChildPrice, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('manager.activities.edit', $activity->activityID) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('manager.activities.destroy', $activity->activityID) }}" method="POST"
                                      onsubmit="return confirm('Delete this activity?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7">
                            <div style="padding: 20px 0;">
                                <i class="fas fa-landmark" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                                No UAE activities yet.
                                <a href="{{ route('manager.activities.create') }}" style="color: var(--wp-primary);">Add one.</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($uaeActivities->hasPages())
            <div class="wp-pagination">
                {{ $uaeActivities->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════
     DYNAMIC COUNTRY TABS — one per allowed country
     ══════════════════════════════════════════════ --}}
@foreach($countryTabs as $ct)
<div class="wp-tab-pane" id="tab-{{ $ct['tabKey'] }}">
    {{-- Add button scoped to this country --}}
    <div style="display:flex; justify-content:flex-end; margin-bottom: 16px;">
        <a href="{{ route('manager.activities.create', ['scope' => 'outside', 'country' => $ct['name']]) }}"
           class="wp-btn wp-btn-primary wp-btn-sm">
            <i class="fas fa-plus"></i> Add {{ $ct['name'] }} Activity
        </a>
    </div>

    <div class="wp-card">
        <div class="table-responsive">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 80px;">Image</th>
                        <th>Activity Name</th>
                        <th style="width: 120px;">Location</th>
                        <th style="width: 100px;">Price</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ct['activities'] as $index => $activity)
                    <tr>
                        <td style="color: var(--wp-text-muted);">{{ $ct['activities']->firstItem() + $index }}</td>
                        <td>
                            @if($activity->activityImage)
                                <img src="{{ str_starts_with($activity->activityImage, 'http') ? $activity->activityImage : asset($activity->activityImage) }}"
                                     alt="{{ $activity->activityName }}"
                                     style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);"
                                     onerror="this.style.display='none';">
                            @else
                                <div style="width: 60px; height: 45px; background: #333; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: var(--wp-text-muted); font-size: 16px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--wp-text);">{{ Str::limit($activity->activityName, 40) }}</strong>
                        </td>
                        <td style="font-size: 12px; color: var(--wp-text-secondary);">
                            <i class="fas fa-map-marker-alt" style="color: var(--wp-primary); margin-right: 4px;"></i>
                            {{ Str::limit($activity->activityLocation, 20) }}
                        </td>
                        <td>
                            <strong style="color: var(--wp-primary);">${{ number_format($activity->activityPrice, 2) }}</strong>
                            @if($activity->activityChildPrice && $activity->activityChildPrice > 0)
                                <br><span style="font-size: 11px; color: var(--wp-text-muted);">Child: ${{ number_format($activity->activityChildPrice, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <a href="{{ route('manager.activities.edit', $activity->activityID) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('manager.activities.destroy', $activity->activityID) }}" method="POST"
                                      onsubmit="return confirm('Delete this activity?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="6">
                            <div style="padding: 20px 0;">
                                @if($ct['flagSrc'])
                                    <img src="{{ $ct['flagSrc'] }}" style="width:32px; height:22px; object-fit:cover; border-radius:3px; margin-bottom:8px; display:block; margin-left:auto; margin-right:auto; opacity:0.4;">
                                @endif
                                No {{ $ct['name'] }} activities yet.
                                <a href="{{ route('manager.activities.create', ['scope' => 'outside', 'country' => $ct['name']]) }}"
                                   style="color: var(--wp-primary);">Add one.</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ct['activities']->hasPages())
            <div class="wp-pagination">
                {{ $ct['activities']->links() }}
            </div>
        @endif
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
$(function () {
    // Build a map of page-params → tab keys so we can auto-activate the right tab.
    var paramTabMap = {};
    @foreach($countryTabs as $ct)
    paramTabMap['{{ $ct['pageParam'] }}'] = '{{ $ct['tabKey'] }}';
    @endforeach

    function activateTab(tabKey) {
        $('.wp-tab-btn').removeClass('active');
        $('.wp-tab-pane').removeClass('active');
        $('.wp-tab-btn[data-tab="' + tabKey + '"]').addClass('active');
        $('#tab-' + tabKey).addClass('active');
    }

    // Tab click
    $('.wp-tab-btn').on('click', function () {
        activateTab($(this).data('tab'));
    });

    // Auto-activate from URL params (pagination links).
    var params = new URLSearchParams(window.location.search);
    if (params.has('uae_page')) {
        activateTab('uae-activities');
    } else {
        var matched = false;
        Object.keys(paramTabMap).forEach(function (param) {
            if (!matched && params.has(param)) {
                activateTab(paramTabMap[param]);
                matched = true;
            }
        });
    }
    // Explicit ?tab= override.
    if (params.has('tab')) {
        activateTab(params.get('tab'));
    }
});
</script>
@endpush
