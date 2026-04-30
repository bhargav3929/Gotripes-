<?php $__env->startSection('title', 'eSIM Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title"><i class="fas fa-sim-card me-2"></i>eSIM Orders</h1>
    <span class="badge bg-dark px-3 py-2"><?php echo e($orders->total()); ?> Total Orders</span>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, reference..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="paid" <?php echo e(request('status') === 'paid' ? 'selected' : ''); ?>>Paid</option>
                    <option value="unpaid" <?php echo e(request('status') === 'unpaid' ? 'selected' : ''); ?>>Unpaid</option>
                    <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e(request('from')); ?>" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e(request('to')); ?>" placeholder="To">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i> Filter</button>
                <a href="<?php echo e(route('client.orders')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Orders List -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 120px;">ORDER ID</th>
                    <th>CUSTOMER</th>
                    <th>DESTINATION</th>
                    <th class="text-end">AMOUNT</th>
                    <th class="text-center">STATUS</th>
                    <th>DATE</th>
                    <th class="text-center" style="width: 80px;">VIEW</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <code class="text-gold">#<?php echo e($order->order_reference ?? 'ORD'.$order->id); ?></code>
                    </td>
                    <td>
                        <div class="fw-500"><?php echo e($order->customer_name ?? '-'); ?></div>
                        <small class="text-muted"><?php echo e($order->customer_email ?? ''); ?></small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="me-2"><?php echo e($order->country_name ?? '-'); ?></span>
                        </div>
                        <small class="text-muted"><?php echo e($order->bundle_name ?? ''); ?></small>
                    </td>
                    <td class="text-end">
                        <span class="fw-600 text-success"><?php echo e(app('current_company')->currency ?? 'AED'); ?> <?php echo e(number_format($order->selling_price ?? 0, 2)); ?></span>
                    </td>
                    <td class="text-center">
                        <?php
                            $status = $order->payment_status ?? 'pending';
                            $statusClass = match($status) {
                                'paid', 'completed' => 'success',
                                'unpaid', 'pending' => 'warning',
                                default => 'danger'
                            };
                        ?>
                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e(ucfirst($status)); ?></span>
                    </td>
                    <td>
                        <div><?php echo e($order->created_at->format('M d, Y')); ?></div>
                        <small class="text-muted"><?php echo e($order->created_at->format('h:i A')); ?></small>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo e(route('client.orders.show', $order)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-sim-card fa-3x mb-3 text-muted"></i>
                        <p class="text-muted mb-0">No orders found</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($orders->hasPages()): ?>
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing <?php echo e($orders->firstItem()); ?>-<?php echo e($orders->lastItem()); ?> of <?php echo e($orders->total()); ?></small>
        <div class="d-flex gap-2">
            <?php if($orders->onFirstPage()): ?>
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-left"></i></span>
            <?php else: ?>
                <a href="<?php echo e($orders->previousPageUrl()); ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-chevron-left"></i></a>
            <?php endif; ?>

            <span class="btn btn-sm btn-dark disabled"><?php echo e($orders->currentPage()); ?> / <?php echo e($orders->lastPage()); ?></span>

            <?php if($orders->hasMorePages()): ?>
                <a href="<?php echo e($orders->nextPageUrl()); ?>" class="btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></a>
            <?php else: ?>
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-right"></i></span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/orders/index.blade.php ENDPATH**/ ?>