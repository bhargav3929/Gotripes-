<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title mb-0" style="font-size: 1.25rem;"><i class="fas fa-chart-bar me-2"></i>Reports</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="fas fa-print"></i></button>
        <a href="<?php echo e(route('superadmin.reports.export')); ?>" class="btn btn-primary btn-sm"><i class="fas fa-download me-1"></i>Export</a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e(request('from', now()->subMonth()->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e(request('to', now()->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-4">
                <select name="company_id" class="form-select form-select-sm">
                    <option value="">All Companies</option>
                    <?php $__currentLoopData = $companies ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($company->id); ?>" <?php echo e(request('company_id') == $company->id ? 'selected' : ''); ?>><?php echo e($company->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-filter"></i></button>
                <a href="<?php echo e(route('superadmin.reports.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="text-success fw-bold" style="font-size: 1.1rem;">AED <?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?></div>
                <small class="text-muted">Revenue</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="fw-bold" style="font-size: 1.1rem;"><?php echo e(number_format($stats['total_orders'] ?? 0)); ?></div>
                <small class="text-muted">Orders</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="fw-bold" style="font-size: 1.1rem;"><?php echo e(number_format($stats['new_users'] ?? 0)); ?></div>
                <small class="text-muted">Users</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card">
            <div class="card-body py-2 px-3 text-center">
                <div class="fw-bold" style="font-size: 1.1rem;"><?php echo e(number_format($stats['new_companies'] ?? 0)); ?></div>
                <small class="text-muted">Companies</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Revenue by Company -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-building me-2"></i>Revenue by Company</div>
            <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                <?php $__empty_1 = true; $__currentLoopData = $revenueByCompany ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center px-3 py-2 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <div style="font-size: 0.85rem;"><?php echo e($company->name); ?></div>
                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo e($company->orders_count); ?> orders</small>
                    </div>
                    <div class="text-success" style="font-size: 0.85rem;">AED <?php echo e(number_format($company->revenue, 2)); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-3 text-muted" style="font-size: 0.8rem;">No data</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-shopping-cart me-2"></i>Orders by Status</div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $ordersByStatus ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center px-3 py-2 border-bottom border-dark">
                    <div class="flex-grow-1">
                        <span class="badge bg-<?php echo e($status === 'completed' || $status === 'paid' ? 'success' : ($status === 'pending' || $status === 'unpaid' ? 'warning' : 'danger')); ?>" style="font-size: 0.7rem;">
                            <?php echo e(ucfirst($status)); ?>

                        </span>
                    </div>
                    <div class="fw-bold" style="font-size: 0.9rem;"><?php echo e(number_format($count)); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-3 text-muted" style="font-size: 0.8rem;">No data</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Trend -->
<div class="card mt-3">
    <div class="card-header py-2" style="font-size: 0.85rem;"><i class="fas fa-chart-line me-2"></i>Monthly Trend</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0" style="font-size: 0.8rem;">
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-end">Orders</th>
                    <th class="text-end">Users</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $monthlyTrend ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($month->month); ?></td>
                    <td class="text-end text-success">AED <?php echo e(number_format($month->revenue, 2)); ?></td>
                    <td class="text-end"><?php echo e(number_format($month->orders)); ?></td>
                    <td class="text-end"><?php echo e(number_format($month->users)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">No data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/reports/index.blade.php ENDPATH**/ ?>