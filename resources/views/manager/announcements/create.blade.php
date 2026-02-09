@extends('layouts.manager')

@section('title', 'Add Announcement')
@section('page-title', 'News Ticker')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Add New Announcement</h1>
    <a href="{{ route('manager.announcements.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('manager.announcements.store') }}" method="POST">
            @csrf

            <div class="wp-card">
                <div class="wp-card-header">Announcement Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label for="description" class="wp-form-label">Text <span class="required">*</span></label>
                        <textarea class="wp-textarea" id="description" name="description" maxlength="500" required placeholder="Enter the text that will scroll in the news ticker...">{{ old('description') }}</textarea>
                        <p class="wp-form-help"><span id="charCount">0</span> / 500 characters</p>
                    </div>

                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <label for="tagType" class="wp-form-label">Tag Label <span class="required">*</span></label>
                        <select class="wp-select" id="tagType" name="tagType" required style="max-width: 260px;">
                            <option value="none">No tag</option>
                            <option value="breaking">Breaking &mdash; Red</option>
                            <option value="trending">Trending &mdash; Gold</option>
                            <option value="exclusive">Exclusive &mdash; Green</option>
                            <option value="alert">New &mdash; Blue</option>
                            <option value="hot">Hot &mdash; Yellow</option>
                        </select>
                        <div id="tagPreview" style="margin-top: 8px;"></div>
                    </div>
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-check"></i> Publish Announcement
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="wp-card">
            <div class="wp-card-header">Tag Colors</div>
            <div class="wp-card-body" style="font-size: 12px; line-height: 2.2;">
                <div><span class="wp-badge wp-badge-red" style="width: 70px; justify-content: center;">Breaking</span> &mdash; Urgent or important news</div>
                <div><span class="wp-badge wp-badge-amber" style="width: 70px; justify-content: center;">Trending</span> &mdash; Popular or hot topics</div>
                <div><span class="wp-badge wp-badge-green" style="width: 70px; justify-content: center;">Exclusive</span> &mdash; Special or limited offers</div>
                <div><span class="wp-badge wp-badge-blue" style="width: 70px; justify-content: center;">New</span> &mdash; Latest updates</div>
                <div><span class="wp-badge" style="width: 70px; justify-content: center; background: linear-gradient(135deg, #FFE600, #FFC107); color: #000;">Hot</span> &mdash; Hot deals & offers</div>
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

    $('#tagType').change(function() {
        $('#tagPreview').html(badges[$(this).val()] || '');
    });

    $('#description').on('input', function() {
        $('#charCount').text($(this).val().length);
    });
});
</script>
@endpush
