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

    /* Read-only badge for UAE tab */
    .wp-badge-readonly {
        background: rgba(114, 174, 230, 0.12);
        color: var(--wp-info);
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 2px 8px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Activities</h1>
    <a href="{{ route('manager.activities.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add New Activity
    </a>
</div>

{{-- ── Tab Navigation ──────────────────────── --}}
<div class="wp-tabs">
    <button class="wp-tab-btn active" data-tab="partner-activities">
        <i class="fas fa-map-marker-alt"></i>
        Activities in {{ $partnerCountry }}
        <span class="tab-count">{{ $activities->total() }}</span>
    </button>
    <button class="wp-tab-btn" data-tab="uae-activities">
        <i class="fas fa-landmark"></i>
        Activities in UAE
        <span class="tab-count">{{ $uaeActivities->total() }}</span>
        <span class="wp-badge-readonly">read-only</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════
     TAB 1 — Partner's Own Activities (full CRUD)
     ══════════════════════════════════════════════ --}}
<div class="wp-tab-pane active" id="tab-partner-activities">
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
                    @forelse($activities as $index => $activity)
                    <tr>
                        <td style="color: var(--wp-text-muted);">{{ $activities->firstItem() + $index }}</td>
                        <td>
                            @if($activity->activityImage)
                                <img src="{{ str_starts_with($activity->activityImage, 'http') ? $activity->activityImage : asset($activity->activityImage) }}" alt="{{ $activity->activityName }}"
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
                                <form action="{{ route('manager.activities.destroy', $activity->activityID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?');">
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
                                <i class="fas fa-hiking" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                                No activities yet.
                                <a href="{{ route('manager.activities.create') }}" style="color: var(--wp-primary);">Create your first one.</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($activities->hasPages())
            <div class="wp-pagination">
                {{ $activities->appends(request()->only('uae_page'))->links() }}
            </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════
     TAB 2 — Platform UAE Activities (read-only)
     ══════════════════════════════════════════════ --}}
<div class="wp-tab-pane" id="tab-uae-activities">
    <div class="wp-notice wp-notice-info" style="margin-bottom: 16px;">
        <span><i class="fas fa-info-circle me-2"></i>These are GoTrips platform activities in the UAE. They are available on your storefront automatically. This list is read-only.</span>
    </div>

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
                        <th style="width: 100px;">Category</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($uaeActivities as $index => $uaeActivity)
                    <tr>
                        <td style="color: var(--wp-text-muted);">{{ $uaeActivities->firstItem() + $index }}</td>
                        <td>
                            @if($uaeActivity->activityImage)
                                <img src="{{ str_starts_with($uaeActivity->activityImage, 'http') ? $uaeActivity->activityImage : asset($uaeActivity->activityImage) }}" alt="{{ $uaeActivity->activityName }}"
                                     style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);"
                                     onerror="this.style.display='none';">
                            @else
                                <div style="width: 60px; height: 45px; background: #333; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="color: var(--wp-text-muted); font-size: 16px;"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="color: var(--wp-text);">{{ Str::limit($uaeActivity->activityName, 40) }}</strong>
                        </td>
                        <td>
                            @if($uaeActivity->emirate)
                                <span class="wp-badge wp-badge-amber">{{ $uaeActivity->emirate->emiratesName }}</span>
                            @else
                                <span class="text-muted-wp" style="font-size: 12px;">Unassigned</span>
                            @endif
                        </td>
                        <td style="font-size: 12px; color: var(--wp-text-secondary);">
                            <i class="fas fa-map-marker-alt" style="color: var(--wp-primary); margin-right: 4px;"></i>
                            {{ Str::limit($uaeActivity->activityLocation, 20) }}
                        </td>
                        <td>
                            <strong style="color: var(--wp-primary);">${{ number_format($uaeActivity->activityPrice, 2) }}</strong>
                            @if($uaeActivity->activityChildPrice && $uaeActivity->activityChildPrice > 0)
                                <br><span style="font-size: 11px; color: var(--wp-text-muted);">Child: ${{ number_format($uaeActivity->activityChildPrice, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($uaeActivity->activityCategory)
                                <span class="wp-badge wp-badge-blue">{{ $uaeActivity->activityCategory }}</span>
                            @else
                                <span class="text-muted-wp" style="font-size: 12px;">--</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="7">
                            <div style="padding: 20px 0;">
                                <i class="fas fa-landmark" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                                No platform UAE activities available yet.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($uaeActivities->hasPages())
            <div class="wp-pagination">
                {{ $uaeActivities->appends(request()->only('page'))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Tab switching
    $('.wp-tab-btn').on('click', function () {
        var target = $(this).data('tab');

        // Update tab buttons
        $('.wp-tab-btn').removeClass('active');
        $(this).addClass('active');

        // Update tab panes
        $('.wp-tab-pane').removeClass('active');
        $('#tab-' + target).addClass('active');
    });

    // If URL has uae_page param, auto-switch to UAE tab
    var params = new URLSearchParams(window.location.search);
    if (params.has('uae_page')) {
        $('.wp-tab-btn[data-tab="uae-activities"]').trigger('click');
    }
});
</script>
@endpush
