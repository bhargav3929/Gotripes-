<?php $__env->startSection('title', 'Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-line me-2"></i>Analytics</h1>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="<?php echo e(route('client.analytics')); ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size: 0.75rem;">From Date</label>
                <input type="date" name="from" class="form-control form-control-sm" value="<?php echo e(request('from', now()->subMonth()->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" style="font-size: 0.75rem;">To Date</label>
                <input type="date" name="to" class="form-control form-control-sm" value="<?php echo e(request('to', now()->format('Y-m-d'))); ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Apply</button>
                <a href="<?php echo e(route('client.analytics')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success"><?php echo e(app('current_company')->currency ?? 'AED'); ?> <?php echo e(number_format($stats['esim_revenue'] ?? 0, 0)); ?></div>
            <div class="stat-label">eSIM Revenue</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value"><?php echo e(number_format($stats['esim_orders'] ?? 0)); ?></div>
            <div class="stat-label">eSIM Orders</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value"><?php echo e(number_format($stats['visa_applications'] ?? 0)); ?></div>
            <div class="stat-label">Visa Applications</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-center">
            <div class="stat-value text-success"><?php echo e(app('current_company')->currency ?? 'AED'); ?> <?php echo e(number_format($stats['flight_hotel_revenue'] ?? 0, 0)); ?></div>
            <div class="stat-label">Flights & Hotels</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Daily Revenue Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fas fa-chart-area me-2"></i>Daily Revenue</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Orders</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $dailyRevenue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e(\Carbon\Carbon::parse($day->date)->format('M d, Y')); ?></td>
                                <td class="text-end text-success"><?php echo e(app('current_company')->currency ?? 'AED'); ?> <?php echo e(number_format($day->revenue, 2)); ?></td>
                                <td class="text-end"><?php echo e($day->orders); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No data for selected period</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Countries -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="fas fa-globe me-2"></i>Top Countries</div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $topCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center p-3 border-bottom" style="border-color: var(--border) !important;">
                    <div class="flex-grow-1">
                        <div class="fw-500"><?php echo e($country->country_name); ?></div>
                        <small class="text-muted"><?php echo e($country->count); ?> orders</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success"><?php echo e(app('current_company')->currency ?? 'AED'); ?> <?php echo e(number_format($country->revenue, 0)); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted">No data available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/analytics.blade.php ENDPATH**/ ?>