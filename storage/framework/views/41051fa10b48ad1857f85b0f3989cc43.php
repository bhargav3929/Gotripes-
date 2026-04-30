<?php $__env->startSection('title', 'News Ticker'); ?>
<?php $__env->startSection('page-title', 'News Ticker'); ?>

<?php $__env->startSection('content'); ?>
<div class="wp-page-header">
    <h1 class="wp-page-title">News Ticker</h1>
    <a href="<?php echo e(route('manager.announcements.create')); ?>" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add New Item
    </a>
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 100px;">Tag</th>
                    <th>Announcement Text</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="color: var(--wp-text-muted);"><?php echo e($announcements->firstItem() + $index); ?></td>
                    <td>
                        <?php if($item->tagType && $item->tagType !== 'none'): ?>
                            <?php switch($item->tagType):
                                case ('breaking'): ?>
                                    <span class="wp-badge wp-badge-red">Breaking</span>
                                    <?php break; ?>
                                <?php case ('trending'): ?>
                                    <span class="wp-badge wp-badge-amber">Trending</span>
                                    <?php break; ?>
                                <?php case ('exclusive'): ?>
                                    <span class="wp-badge wp-badge-green">Exclusive</span>
                                    <?php break; ?>
                                <?php case ('alert'): ?>
                                    <span class="wp-badge wp-badge-blue">New</span>
                                    <?php break; ?>
                            <?php endswitch; ?>
                        <?php else: ?>
                            <span class="text-muted-wp" style="font-size: 12px;">No tag</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(Str::limit($item->description, 100)); ?></td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="<?php echo e(route('manager.announcements.edit', $item->id)); ?>" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="<?php echo e(route('manager.announcements.destroy', $item->id)); ?>" method="POST" onsubmit="return confirm('Remove this announcement?');">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="empty-row">
                    <td colspan="4">
                        <div style="padding: 20px 0;">
                            <i class="fas fa-rss" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No announcements yet.
                            <a href="<?php echo e(route('manager.announcements.create')); ?>" style="color: var(--wp-primary);">Create your first one.</a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($announcements->hasPages()): ?>
        <div class="wp-pagination">
            <?php echo e($announcements->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/manager/announcements/index.blade.php ENDPATH**/ ?>