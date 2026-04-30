<?php $__env->startSection('title', 'Activities Management'); ?>
<?php $__env->startSection('page-title', 'Activities Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="wp-page-header">
    <h1 class="wp-page-title">UAE Activities</h1>
    <a href="<?php echo e(route('manager.activities.create')); ?>" class="wp-btn wp-btn-primary">
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
                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="color: var(--wp-text-muted);"><?php echo e($activities->firstItem() + $index); ?></td>
                    <td>
                        <?php if($activity->activityImage): ?>
                            <img src="<?php echo e(str_starts_with($activity->activityImage, 'http') ? $activity->activityImage : asset($activity->activityImage)); ?>" alt="<?php echo e($activity->activityName); ?>"
                                 style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);"
                                 onerror="this.style.display='none';">
                        <?php else: ?>
                            <div style="width: 60px; height: 45px; background: #333; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: var(--wp-text-muted); font-size: 16px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong style="color: var(--wp-text);"><?php echo e(Str::limit($activity->activityName, 40)); ?></strong>
                    </td>
                    <td>
                        <?php if($activity->emirate): ?>
                            <span class="wp-badge wp-badge-amber"><?php echo e($activity->emirate->emiratesName); ?></span>
                        <?php else: ?>
                            <span class="text-muted-wp" style="font-size: 12px;">Unassigned</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size: 12px; color: var(--wp-text-secondary);">
                        <i class="fas fa-map-marker-alt" style="color: var(--wp-primary); margin-right: 4px;"></i>
                        <?php echo e(Str::limit($activity->activityLocation, 20)); ?>

                    </td>
                    <td>
                        <strong style="color: var(--wp-primary);">$<?php echo e(number_format($activity->activityPrice, 2)); ?></strong>
                        <?php if($activity->activityChildPrice && $activity->activityChildPrice > 0): ?>
                            <br><span style="font-size: 11px; color: var(--wp-text-muted);">Child: $<?php echo e(number_format($activity->activityChildPrice, 2)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="<?php echo e(route('manager.activities.edit', $activity->activityID)); ?>" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="<?php echo e(route('manager.activities.destroy', $activity->activityID)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this activity?');">
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
                    <td colspan="7">
                        <div style="padding: 20px 0;">
                            <i class="fas fa-hiking" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No activities yet.
                            <a href="<?php echo e(route('manager.activities.create')); ?>" style="color: var(--wp-primary);">Create your first one.</a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($activities->hasPages()): ?>
        <div class="wp-pagination">
            <?php echo e($activities->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/manager/activities/index.blade.php ENDPATH**/ ?>