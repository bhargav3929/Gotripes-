<?php $__env->startSection('title', 'My Orders'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-0">My Orders</h5>
            <small class="text-muted">Track all orders made through your referral link</small>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?php echo e(route('referral.orders')); ?>" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo e(request('end_date')); ?>">
                </div>
                <div class="col-6 col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-gold btn-sm flex-grow-1"><i class="fas fa-search"></i> Filter</button>
                    <a href="<?php echo e(route('referral.orders')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Type</th>
                        <th>Customer</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Commission</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><span class="order-id">#<?php echo e($order->order_id); ?></span></td>
                        <td><span class="badge bg-secondary"><?php echo e(ucfirst($order->order_type)); ?></span></td>
                        <td>
                            <div><?php echo e($order->customer_name ?? 'N/A'); ?></div>
                            <small class="text-muted"><?php echo e(Str::mask($order->customer_email ?? '', '*', 3, -10)); ?></small>
                        </td>
                        <td class="text-end"><?php echo e($order->currency); ?> <?php echo e(number_format($order->order_amount, 2)); ?></td>
                        <td class="text-end">
                            <span class="commission-amt">AED <?php echo e(number_format($order->commission_amount, 2)); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?php echo e($order->status_badge); ?>"><?php echo e(ucfirst($order->status)); ?></span>
                        </td>
                        <td>
                            <div><?php echo e($order->created_at->format('M d, Y')); ?></div>
                            <small class="text-muted"><?php echo e($order->created_at->format('H:i')); ?></small>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-receipt fa-2x mb-2 d-block"></i>
                            No orders found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($orders->hasPages()): ?>
        <div class="card-footer py-2">
            <?php echo e($orders->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>

    <!-- Status Legend -->
    <div class="card mt-3">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <span><span class="badge bg-warning">Pending</span> <small class="text-muted">Awaiting review</small></span>
                <span><span class="badge bg-info">Approved</span> <small class="text-muted">Awaiting payout</small></span>
                <span><span class="badge bg-success">Paid</span> <small class="text-muted">Paid out</small></span>
            </div>
        </div>
    </div>
</div>

<style>
    .container { max-width: 1100px; }

    /* Card */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }
    .card-body { padding: 12px; }

    /* Form */
    .form-label {
        font-size: 0.65rem;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .form-control, .form-select {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.75rem;
    }
    .form-control:focus, .form-select:focus {
        background: var(--light-dark);
        border-color: var(--primary-gold);
        color: var(--text-main);
        box-shadow: none;
    }
    .form-control-sm, .form-select-sm { padding: 4px 8px; }

    /* Table */
    .table {
        font-size: 0.75rem;
        background: transparent !important;
    }
    .table thead th {
        background: rgba(0,0,0,0.2) !important;
        color: var(--text-muted);
        font-size: 0.65rem;
        font-weight: 500;
        text-transform: uppercase;
        padding: 8px 10px;
        border-bottom: 1px solid var(--border-color);
    }
    .table tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid var(--border-color);
        background: transparent !important;
        color: #e2e8f0;
        vertical-align: middle;
    }
    .table tbody tr:hover {
        background: rgba(255,215,0,0.03) !important;
    }

    .order-id { color: var(--primary-gold); font-weight: 500; }
    .commission-amt { color: #22c55e; font-weight: 600; }

    /* Buttons */
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
    }
    .btn-sm { padding: 4px 10px; font-size: 0.75rem; }

    /* Badge */
    .badge { font-size: 0.6rem; padding: 3px 6px; }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.referral', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/referral/orders.blade.php ENDPATH**/ ?>