@extends('layouts.manager')

@section('title', 'Edit Announcement')
@section('page-title', 'News Ticker')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Announcement</h1>
    <a href="{{ route('manager.announcements.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('manager.announcements.update', $announcement->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="wp-card">
                <div class="wp-card-header">Announcement Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label for="description" class="wp-form-label">Text <span class="required">*</span></label>
                        <textarea class="wp-textarea" id="description" name="description" maxlength="500" required>{{ old('description', $announcement->description) }}</textarea>
                        <p class="wp-form-help"><span id="charCount">{{ strlen($announcement->description) }}</span> / 500 characters</p>
                    </div>

                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <label for="tagType" class="wp-form-label">Tag Label <span class="required">*</span></label>
                        <select class="wp-select" id="tagType" name="tagType" required style="max-width: 260px;">
                            <option value="none" {{ old('tagType', $announcement->tagType) === 'none' ? 'selected' : '' }}>No tag</option>
                            <option value="breaking" {{ old('tagType', $announcement->tagType) === 'breaking' ? 'selected' : '' }}>Breaking &mdash; Red</option>
                            <option value="trending" {{ old('tagType', $announcement->tagType) === 'trending' ? 'selected' : '' }}>Trending &mdash; Gold</option>
                            <option value="exclusive" {{ old('tagType', $announcement->tagType) === 'exclusive' ? 'selected' : '' }}>Exclusive &mdash; Green</option>
                            <option value="alert" {{ old('tagType', $announcement->tagType) === 'alert' ? 'selected' : '' }}>New &mdash; Blue</option>
                            <option value="hot" {{ old('tagType', $announcement->tagType) === 'hot' ? 'selected' : '' }}>Hot &mdash; Yellow</option>
                        </select>
                        <div id="tagPreview" style="margin-top: 8px;"></div>
                    </div>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-save"></i> Update Announcement
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="wp-card">
            <div class="wp-card-header">Details</div>
            <div class="wp-card-body" style="font-size: 12px; color: var(--wp-text-secondary); line-height: 1.8;">
                <p style="margin-bottom: 4px;"><strong>Created by:</strong> {{ $announcement->createdBy ?? 'Unknown' }}</p>
                @if($announcement->createdDate)
                    <p style="margin-bottom: 4px;"><strong>Created:</strong> {{ $announcement->createdDate->format('M j, Y') }}</p>
                @endif
                @if($announcement->modifiedBy)
                    <p style="margin-bottom: 0;"><strong>Last modified by:</strong> {{ $announcement->modifiedBy }}</p>
                @endif
            </div>
        </div>

        <div class="wp-card">
            <div class="wp-card-header">Tag Colors</div>
            <div class="wp-card-body" style="font-size: 12px; line-height: 2.2;">
                <div><span class="wp-badge wp-badge-red" style="width: 70px; justify-content: center;">Breaking</span> &mdash; Red</div>
                <div><span class="wp-badge wp-badge-amber" style="width: 70px; justify-content: center;">Trending</span> &mdash; Gold</div>
                <div><span class="wp-badge wp-badge-green" style="width: 70px; justify-content: center;">Exclusive</span> &mdash; Green</div>
                <div><span class="wp-badge wp-badge-blue" style="width: 70px; justify-content: center;">New</span> &mdash; Blue</div>
                <div><span class="wp-badge" style="width: 70px; justify-content: center; background: linear-gradient(135deg, #FFE600, #FFC107); color: #000;">Hot</span> &mdash; Yellow</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    const badges = {
        'breaking': '<span class="wp-badge wp-badge-red">Breaking</span>',
        'trending': '<span class="wp-badge wp-badge-amber">Trending</span>',
        'exclusive': '<span class="wp-badge wp-badge-green">Exclusive</span>',
        'alert': '<span class="wp-badge wp-badge-blue">New</span>',
        'hot': '<span class="wp-badge" style="background: linear-gradient(135deg, #FFE600, #FFC107); color: #000;">Hot</span>'
    };

    function updatePreview() {
        $('#tagPreview').html(badges[$('#tagType').val()] || '');
    }
    $('#tagType').change(updatePreview);
    updatePreview();

    $('#description').on('input', function() {
        $('#charCount').text($(this).val().length);
    });
});
</script>
@endpush
