@extends('layouts.agent')

@section('title', 'My Activities')
@section('page-title', 'My Activities')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">My Activities</h1>
    <a href="{{ route('agent.activities.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add Activity
    </a>
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 90px;">Image</th>
                    <th>Activity</th>
                    <th style="width: 160px;">Emirate</th>
                    <th style="width: 160px;">Location</th>
                    <th style="width: 110px;">Price</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $index => $activity)
                <tr>
                    <td style="color: var(--wp-text-muted);">{{ $activities->firstItem() + $index }}</td>
                    <td>
                        @if($activity->activityImage)
                            <img src="{{ str_starts_with($activity->activityImage, 'http') ? $activity->activityImage : asset($activity->activityImage) }}"
                                 alt="{{ $activity->activityName }}"
                                 style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);"
                                 onerror="this.style.display='none';">
                        @else
                            <div style="width: 70px; height: 50px; background: #333; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: var(--wp-text-muted); font-size: 16px;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="color: var(--wp-text);">{{ Str::limit($activity->activityName, 50) }}</strong>
                        @if($activity->activityCategory)
                            <br><span class="wp-badge wp-badge-blue" style="margin-top: 4px;">{{ $activity->activityCategory }}</span>
                        @endif
                    </td>
                    <td style="font-size: 13px; color: var(--wp-text-secondary);">
                        {{ $activity->emirate?->emiratesName ?? '—' }}
                    </td>
                    <td style="font-size: 13px; color: var(--wp-text-secondary);">
                        <i class="fas fa-map-marker-alt" style="color: var(--wp-primary); margin-right: 4px;"></i>
                        {{ Str::limit($activity->activityLocation, 30) }}
                    </td>
                    <td>
                        <strong style="color: var(--wp-primary);">{{ number_format((float) $activity->activityPrice, 2) }}</strong>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('agent.activities.edit', $activity->activityID) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="{{ route('agent.activities.destroy', $activity->activityID) }}" method="POST" onsubmit="return confirm('Delete this activity?');">
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
                        <div style="padding: 24px 0; text-align: center;">
                            <i class="fas fa-hiking" style="font-size: 32px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No activities yet.
                            <a href="{{ route('agent.activities.create') }}" style="color: var(--wp-primary);">Add your first one.</a>
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
