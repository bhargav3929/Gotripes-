<?php $__env->startSection('title', 'Edit Announcement'); ?>
<?php $__env->startSection('page-title', 'News Ticker'); ?>

<?php $__env->startSection('content'); ?>
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Announcement</h1>
    <a href="<?php echo e(route('manager.announcements.index')); ?>" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to All
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="<?php echo e(route('manager.announcements.update', $announcement->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="wp-card">
                <div class="wp-card-header">Announcement Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label for="description" class="wp-form-label">Text <span class="required">*</span></label>
                        <textarea class="wp-textarea" id="description" name="description" maxlength="500" required><?php echo e(old('description', $announcement->description)); ?></textarea>
                        <p class="wp-form-help"><span id="charCount"><?php echo e(strlen($announcement->description)); ?></span> / 500 characters</p>
                    </div>

                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <label for="tagType" class="wp-form-label">Tag Label <span class="required">*</span></label>
                        <select class="wp-select" id="tagType" name="tagType" required style="max-width: 260px;">
                            <option value="none" <?php echo e(old('tagType', $announcement->tagType) === 'none' ? 'selected' : ''); ?>>No tag</option>
                            <option value="breaking" <?php echo e(old('tagType', $announcement->tagType) === 'breaking' ? 'selected' : ''); ?>>Breaking &mdash; Red</option>
                            <option value="trending" <?php echo e(old('tagType', $announcement->tagType) === 'trending' ? 'selected' : ''); ?>>Trending &mdash; Gold</option>
                            <option value="exclusive" <?php echo e(old('tagType', $announcement->tagType) === 'exclusive' ? 'selected' : ''); ?>>Exclusive &mdash; Green</option>
                            <option value="alert" <?php echo e(old('tagType', $announcement->tagType) === 'alert' ? 'selected' : ''); ?>>New &mdash; Blue</option>
                            <option value="hot" <?php echo e(old('tagType', $announcement->tagType) === 'hot' ? 'selected' : ''); ?>>Hot &mdash; Yellow</option>
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
                <p style="margin-bottom: 4px;"><strong>Created by:</strong> <?php echo e($announcement->createdBy ?? 'Unknown'); ?></p>
                <?php if($announcement->createdDate): ?>
                    <p style="margin-bottom: 4px;"><strong>Created:</strong> <?php echo e($announcement->createdDate->format('M j, Y')); ?></p>
                <?php endif; ?>
                <?php if($announcement->modifiedBy): ?>
                    <p style="margin-bottom: 0;"><strong>Last modified by:</strong> <?php echo e($announcement->modifiedBy); ?></p>
                <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/manager/announcements/edit.blade.php ENDPATH**/ ?>