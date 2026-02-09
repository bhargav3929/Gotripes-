@extends('layouts.manager')

@section('title', 'Hero Ad Slots')
@section('page-title', 'Hero Ad Slots')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Hero Ad Slots</h1>
    <a href="{{ route('manager.adslots.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add New Slot
    </a>
</div>

<div class="wp-notice wp-notice-info" style="border-left-color: var(--wp-primary);">
    <span>
        <strong>{{ $adSlots->total() }}</strong> of 6 slots active.
        @if($adSlots->total() >= 6)
            Remove a slot before adding new ones.
        @else
            You can add {{ 6 - $adSlots->total() }} more.
        @endif
    </span>
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Position</th>
                    <th style="width: 80px;">Type</th>
                    <th>Preview</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adSlots as $ad)
                <tr>
                    <td>
                        <span class="wp-badge wp-badge-blue" style="font-size: 12px;">Slot {{ $ad->slotOrder }}</span>
                    </td>
                    <td>
                        @if($ad->mediaType === 'video')
                            <span class="wp-badge wp-badge-amber"><i class="fas fa-video me-1"></i>Video</span>
                        @else
                            <span class="wp-badge wp-badge-green"><i class="fas fa-image me-1"></i>Image</span>
                        @endif
                    </td>
                    <td>
                        @if($ad->mediaType === 'video')
                            <video width="140" height="80" muted style="border-radius: 4px; object-fit: cover; border: 1px solid var(--wp-border-light);">
                                <source src="{{ asset($ad->imgPath) }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ asset($ad->imgPath) }}" alt="Slot {{ $ad->slotOrder }}" style="width: 140px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);">
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('manager.adslots.edit', $ad->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm" title="Edit">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="{{ route('manager.adslots.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Remove this ad slot?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" title="Remove">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="4">
                        <div style="padding: 20px 0;">
                            <i class="fas fa-photo-video" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No ad slots yet.
                            <a href="{{ route('manager.adslots.create') }}" style="color: var(--wp-primary);">Upload your first one.</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($adSlots->hasPages())
        <div class="wp-pagination">
            {{ $adSlots->links() }}
        </div>
    @endif
</div>
@endsection
