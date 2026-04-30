<?php $__env->startSection('title', 'Activity Bookings'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><i class="fas fa-ticket-alt me-2" style="color: var(--gold);"></i>Activity Bookings</h1>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="<?php echo e(route('client.activities')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Activity</th>
                    <th>Date</th>
                    <th>Guests</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>#<?php echo e($booking->id); ?></td>
                    <td>
                        <strong><?php echo e($booking->name); ?></strong>
                        <br><small class="text-muted"><?php echo e($booking->email); ?></small>
                    </td>
                    <td><?php echo e($booking->activityId); ?></td>
                    <td><?php echo e($booking->date ? \Carbon\Carbon::parse($booking->date)->format('M d, Y') : '-'); ?></td>
                    <td><?php echo e($booking->adults ?? 0); ?> Adults, <?php echo e($booking->childrens ?? 0); ?> Children</td>
                    <td><?php echo e($booking->currency ?? 'AED'); ?> <?php echo e(number_format($booking->amount ?? 0, 2)); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e(($booking->status ?? 'pending') == 'confirmed' ? 'success' : (($booking->status ?? 'pending') == 'cancelled' ? 'danger' : 'warning')); ?>">
                            <?php echo e(ucfirst($booking->status ?? 'pending')); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No activity bookings found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($bookings->hasPages()): ?>
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing <?php echo e($bookings->firstItem()); ?>-<?php echo e($bookings->lastItem()); ?> of <?php echo e($bookings->total()); ?></small>
        <div class="d-flex gap-2">
            <?php if($bookings->onFirstPage()): ?>
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-left"></i></span>
            <?php else: ?>
                <a href="<?php echo e($bookings->previousPageUrl()); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>
            <span class="btn btn-sm btn-dark disabled"><?php echo e($bookings->currentPage()); ?> / <?php echo e($bookings->lastPage()); ?></span>
            <?php if($bookings->hasMorePages()): ?>
                <a href="<?php echo e($bookings->nextPageUrl()); ?>" class="btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></a>
            <?php else: ?>
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-right"></i></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/activities/index.blade.php ENDPATH**/ ?>