<?php $__env->startSection('title', 'Visa Applications'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><i class="fas fa-passport me-2" style="color: var(--gold);"></i>Visa Applications</h1>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="<?php echo e(route('client.visa')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Applicant</th>
                    <th>Email</th>
                    <th>Visa Type</th>
                    <th>Travel Date</th>
                    <th>Status</th>
                    <th>Applied On</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $visas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $visa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>#<?php echo e($visa->id); ?></td>
                    <td>
                        <strong><?php echo e($visa->UAEV_first_name); ?> <?php echo e($visa->UAEV_last_name); ?></strong>
                    </td>
                    <td><?php echo e($visa->UAEV_email); ?></td>
                    <td><?php echo e($visa->UAEV_visaDuration ?? 'Standard'); ?></td>
                    <td><?php echo e($visa->UAEV_arrival_date ? \Carbon\Carbon::parse($visa->UAEV_arrival_date)->format('M d, Y') : '-'); ?></td>
                    <td>
                        <span class="badge bg-<?php echo e($visa->UAEV_status == 1 ? 'warning' : ($visa->UAEV_status == 2 ? 'success' : 'danger')); ?>">
                            <?php echo e($visa->UAEV_status == 1 ? 'Pending' : ($visa->UAEV_status == 2 ? 'Approved' : 'Rejected')); ?>

                        </span>
                    </td>
                    <td><?php echo e($visa->UAEV_created_date ? \Carbon\Carbon::parse($visa->UAEV_created_date)->format('M d, Y') : '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No visa applications found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($visas->hasPages()): ?>
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing <?php echo e($visas->firstItem()); ?>-<?php echo e($visas->lastItem()); ?> of <?php echo e($visas->total()); ?></small>
        <div class="d-flex gap-2">
            <?php if($visas->onFirstPage()): ?>
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-left"></i></span>
            <?php else: ?>
                <a href="<?php echo e($visas->previousPageUrl()); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>
            <span class="btn btn-sm btn-dark disabled"><?php echo e($visas->currentPage()); ?> / <?php echo e($visas->lastPage()); ?></span>
            <?php if($visas->hasMorePages()): ?>
                <a href="<?php echo e($visas->nextPageUrl()); ?>" class="btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></a>
            <?php else: ?>
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-right"></i></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/visa/index.blade.php ENDPATH**/ ?>