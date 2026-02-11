@extends('layouts.manager')

@section('title', 'Hero Ad TVs')
@section('page-title', 'Hero Ad TVs')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Hero Ad TVs</h1>
    <a href="{{ route('manager.adslots.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add Media to TV
    </a>
</div>

<div class="wp-notice wp-notice-info" style="border-left-color: var(--wp-primary);">
    <span>
        <i class="fas fa-tv me-1"></i>
        <strong>{{ $usedSlots ?? 0 }}</strong> of 5 TVs active with <strong>{{ $totalMedia ?? 0 }}</strong> total media items.
        Each TV cycles through its media like an airport display.
    </span>
</div>

@if(session('success'))
    <div class="wp-notice wp-notice-success" style="border-left-color: #28a745; background: rgba(40,167,69,0.1);">
        <span><i class="fas fa-check-circle me-1" style="color: #5cbf70;"></i> {{ session('success') }}</span>
    </div>
@endif

{{-- TV Slots --}}
@for($tv = 1; $tv <= 5; $tv++)
    @php $mediaItems = isset($adSlots[$tv]) ? $adSlots[$tv] : collect(); @endphp
    <div class="wp-card" style="margin-bottom: 20px;">
        <div class="wp-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="wp-badge wp-badge-blue" style="font-size: 14px; padding: 6px 12px; border-radius: 8px;">TV {{ $tv }}</span>
                <span style="font-size: 12px; color: var(--wp-text-muted);">
                    {{ $mediaItems->count() }} media item{{ $mediaItems->count() !== 1 ? 's' : '' }}
                </span>
            </div>
            <a href="{{ route('manager.adslots.create') }}?tv={{ $tv }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                <i class="fas fa-plus"></i> Add Media
            </a>
        </div>
        <div class="wp-card-body">
            @if($mediaItems->count() > 0)
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    @foreach($mediaItems as $media)
                        <div style="width: 160px; border: 1px solid var(--wp-border-light); border-radius: 6px; overflow: hidden; background: #1a1a1a;">
                            <div style="position: relative; height: 90px; overflow: hidden; background: #111;">
                                @if($media->mediaType === 'video')
                                    <video muted style="width: 100%; height: 100%; object-fit: cover;">
                                        <source src="{{ asset($media->imgPath) }}" type="video/mp4">
                                    </video>
                                    <span style="position: absolute; top: 4px; left: 4px; background: rgba(23,162,184,0.9); color: #fff; padding: 1px 6px; border-radius: 3px; font-size: 10px;">
                                        <i class="fas fa-video"></i>
                                    </span>
                                @else
                                    <img src="{{ asset($media->imgPath) }}" alt="Slot {{ $tv }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    <span style="position: absolute; top: 4px; left: 4px; background: rgba(255,193,7,0.9); color: #000; padding: 1px 6px; border-radius: 3px; font-size: 10px;">
                                        <i class="fas fa-image"></i>
                                    </span>
                                @endif
                                <span style="position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.6); color: #fff; padding: 1px 6px; border-radius: 3px; font-size: 10px; font-weight: 700;">
                                    #{{ $media->displayOrder }}
                                </span>
                            </div>
                            <div style="padding: 8px; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 11px; color: var(--wp-text-muted);">
                                    @if($media->mediaType === 'video')
                                        <i class="fas fa-film"></i> Video
                                    @else
                                        <i class="fas fa-clock"></i> {{ $media->duration ?? 5 }}s
                                    @endif
                                </span>
                                <div style="display: flex; gap: 4px;">
                                    <a href="{{ route('manager.adslots.edit', $media->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm" style="padding: 2px 8px; font-size: 11px;" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('manager.adslots.destroy', $media->id) }}" method="POST" onsubmit="return confirm('Remove this media from TV {{ $tv }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm" style="padding: 2px 8px; font-size: 11px;" title="Remove">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 20px 0; color: var(--wp-text-muted);">
                    <i class="fas fa-photo-video" style="font-size: 24px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                    No media in this TV yet.
                    <a href="{{ route('manager.adslots.create') }}?tv={{ $tv }}" style="color: var(--wp-primary);">Add media.</a>
                </div>
            @endif
        </div>
    </div>
@endfor
@endsection
