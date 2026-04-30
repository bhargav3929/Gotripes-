<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-pie me-2"></i>Dashboard</h1>
    <span class="text-muted">Welcome back, <?php echo e(auth()->user()->name); ?></span>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-sim-card"></i>
            </div>
            <div class="stat-value"><?php echo e(number_format($stats['esim_orders'] ?? 0)); ?></div>
            <div class="stat-label">eSIM Orders</div>
            <small class="text-success">+<?php echo e($stats['today_orders'] ?? 0); ?> today</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-value"><?php echo e(number_format($stats['esim_revenue'] ?? 0, 0)); ?></div>
            <div class="stat-label">eSIM Revenue (<?php echo e($company->currency ?? 'AED'); ?>)</div>
            <small class="text-success">+<?php echo e(number_format($stats['today_revenue'] ?? 0, 0)); ?> today</small>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-passport"></i>
            </div>
            <div class="stat-value"><?php echo e(number_format($stats['visa_applications'] ?? 0)); ?></div>
            <div class="stat-label">Visa Applications</div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-value"><?php echo e(number_format($stats['total_agents'] ?? 0)); ?></div>
            <div class="stat-label">Referral Agents</div>
            <small class="text-warning"><?php echo e($company->currency ?? 'AED'); ?> <?php echo e(number_format($stats['pending_commissions'] ?? 0, 0)); ?> pending</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clock me-2"></i>Recent Orders</span>
                <a href="<?php echo e(route('client.orders')); ?>" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Country</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="fw-500">#<?php echo e($order->order_reference ?? $order->id); ?></div>
                                <small class="text-muted"><?php echo e($order->created_at->format('M d, H:i')); ?></small>
                            </td>
                            <td>
                                <div><?php echo e($order->customer_name ?? $order->user->name ?? '-'); ?></div>
                                <small class="text-muted"><?php echo e($order->customer_email ?? $order->user->email ?? ''); ?></small>
                            </td>
                            <td><?php echo e($order->country_name ?? '-'); ?></td>
                            <td class="text-end text-success">
                                <?php echo e($company->currency); ?> <?php echo e(number_format($order->selling_price ?? $order->total_amount ?? 0, 2)); ?>

                            </td>
                            <td class="text-center">
                                <span class="badge bg-<?php echo e(($order->status ?? $order->payment_status) === 'completed' ? 'success' : (($order->status ?? $order->payment_status) === 'pending' ? 'warning' : 'danger')); ?>">
                                    <?php echo e(ucfirst($order->status ?? $order->payment_status ?? 'pending')); ?>

                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No orders yet</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Agents -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trophy me-2" style="color: var(--gold);"></i>Top Referral Agents
            </div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $topAgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center p-3 border-bottom" style="border-color: var(--border) !important;">
                    <div class="me-3">
                        <span class="badge bg-<?php echo e($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark')); ?>">
                            #<?php echo e($index + 1); ?>

                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-500"><?php echo e($agent->name); ?></div>
                        <small class="text-muted"><?php echo e($agent->total_sales); ?> sales</small>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-600"><?php echo e($company->currency); ?> <?php echo e(number_format($agent->total_earnings, 2)); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted">No agents yet</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header"><i class="fas fa-bolt me-2"></i>Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('admin.referrals.agents.create')); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Add Referral Agent
                    </a>
                    <a href="<?php echo e(route('client.branding')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-palette me-2"></i>Update Branding
                    </a>
                    <a href="<?php echo e(route('client.analytics')); ?>" class="btn btn-outline-info">
                        <i class="fas fa-chart-bar me-2"></i>View Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.client', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/client/dashboard.blade.php ENDPATH**/ ?>