<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .mini-stat {
        background: var(--dark-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .mini-stat .icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .mini-stat .value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-white);
        line-height: 1;
    }
    .mini-stat .label {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .quick-stat {
        background: var(--dark-3);
        border-radius: 10px;
        padding: 14px 18px;
        text-align: center;
    }
    .quick-stat .num {
        font-size: 1.25rem;
        font-weight: 800;
    }
    .quick-stat .txt {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .company-row {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .company-row:last-child { border-bottom: none; }
    .company-row:hover { background: var(--dark-3); }
    .rank-badge {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 800;
    }
    .table-compact th { padding: 12px 16px; font-size: 0.7rem; }
    .table-compact td { padding: 10px 16px; font-size: 0.85rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--text-white);">
        <i class="fas fa-th-large me-2" style="color: var(--gold);"></i>Dashboard
    </h1>
    <a href="<?php echo e(route('superadmin.companies.create')); ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-2"></i>Add Company
    </a>
</div>

<!-- Main Stats -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="mini-stat">
            <div class="icon" style="background: rgba(246, 195, 67, 0.15); color: var(--gold);">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <div class="value"><?php echo e($stats['total_companies']); ?></div>
                <div class="label">Companies <span class="text-success">(<?php echo e($stats['active_companies']); ?> active)</span></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="mini-stat">
            <div class="icon" style="background: rgba(34, 197, 94, 0.15); color: var(--success);">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="value"><?php echo e(number_format($stats['total_revenue'], 0)); ?></div>
                <div class="label">Revenue (AED)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="mini-stat">
            <div class="icon" style="background: rgba(59, 130, 246, 0.15); color: var(--info);">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div>
                <div class="value"><?php echo e($stats['total_orders']); ?></div>
                <div class="label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="mini-stat">
            <div class="icon" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div class="value"><?php echo e($stats['total_users']); ?></div>
                <div class="label">Total Users</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="quick-stat">
            <div class="num text-warning"><?php echo e($stats['trial_companies']); ?></div>
            <div class="txt">On Trial</div>
        </div>
    </div>
    <div class="col-4">
        <div class="quick-stat">
            <div class="num text-success"><?php echo e($stats['paid_companies']); ?></div>
            <div class="txt">Paid</div>
        </div>
    </div>
    <div class="col-4">
        <div class="quick-stat">
            <div class="num text-danger"><?php echo e($stats['expiring_soon']); ?></div>
            <div class="txt">Expiring Soon</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Companies -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span style="font-size: 0.9rem;"><i class="fas fa-clock"></i> Recent Companies</span>
                <a href="<?php echo e(route('superadmin.companies.index')); ?>" class="btn btn-xs btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-compact mb-0">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('superadmin.companies.show', $company)); ?>" class="fw-600 text-white text-decoration-none"><?php echo e($company->name); ?></a>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($company->plan === 'enterprise' ? 'primary' : ($company->plan === 'pro' ? 'info' : ($company->plan === 'basic' ? 'success' : 'warning'))); ?>" style="font-size: 0.65rem; padding: 4px 8px;">
                                    <?php echo e(ucfirst($company->plan)); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($company->is_active): ?>
                                <span class="text-success"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Active</span>
                                <?php else: ?>
                                <span class="text-danger"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?php echo e($company->created_at->format('M d')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No companies yet</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Companies -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-3" style="font-size: 0.9rem;">
                <i class="fas fa-trophy" style="color: var(--gold);"></i> Top by Revenue
            </div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $topCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="company-row">
                    <div class="rank-badge" style="background: <?php echo e($index === 0 ? 'var(--gold)' : ($index === 1 ? '#94a3b8' : 'var(--dark-4)')); ?>; color: <?php echo e($index === 0 ? 'var(--dark-1)' : ($index === 1 ? 'var(--dark-1)' : 'var(--text-muted)')); ?>;">
                        <?php echo e($index + 1); ?>

                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-600 text-white" style="font-size: 0.9rem;"><?php echo e($company->name); ?></div>
                        <small class="text-muted"><?php echo e($company->total_orders); ?> orders</small>
                    </div>
                    <div class="text-gold fw-700">AED <?php echo e(number_format($company->total_revenue, 0)); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-4 text-muted">No data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/dashboard.blade.php ENDPATH**/ ?>