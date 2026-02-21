@extends('layouts.manager')

@section('title', 'Activities Management')
@section('page-title', 'Activities Management')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">UAE Activities</h1>
    <a href="{{ route('manager.activities.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add New Activity
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
                            <img src="{{ asset($activity->activityImage) }}" alt="{{ $activity->activityName }}"
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
            {{ $activities->links() }}
        </div>
    @endif
</div>
@endsection
