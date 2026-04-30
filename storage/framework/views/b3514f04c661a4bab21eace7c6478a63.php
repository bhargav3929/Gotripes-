<?php $__env->startSection('title', 'Companies'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 style="font-size: 1.3rem; font-weight: 800; margin: 0; color: var(--text-white);">
        <i class="fas fa-building me-2" style="color: var(--gold);"></i>Companies
        <span class="badge bg-primary ms-2" style="font-size: 0.7rem;"><?php echo e($companies->total()); ?></span>
    </h1>
    <a href="<?php echo e(route('superadmin.companies.create')); ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Add
    </a>
</div>


<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?php echo e(route('superadmin.companies.index')); ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search companies..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="plan" class="form-select form-select-sm">
                    <option value="">All Plans</option>
                    <option value="trial" <?php echo e(request('plan') === 'trial' ? 'selected' : ''); ?>>Trial</option>
                    <option value="basic" <?php echo e(request('plan') === 'basic' ? 'selected' : ''); ?>>Basic</option>
                    <option value="pro" <?php echo e(request('plan') === 'pro' ? 'selected' : ''); ?>>Pro</option>
                    <option value="enterprise" <?php echo e(request('plan') === 'enterprise' ? 'selected' : ''); ?>>Enterprise</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="fas fa-search"></i>
                </button>
                <a href="<?php echo e(route('superadmin.companies.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Companies Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Domain</th>
                    <th>Owner</th>
                    <th>Plan</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Revenue</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if($company->logo): ?>
                            <img src="<?php echo e($company->logo_url); ?>" alt="" class="company-avatar" style="object-fit: contain;">
                            <?php else: ?>
                            <div class="company-avatar">
                                <?php echo e(strtoupper(substr($company->name, 0, 1))); ?>

                            </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-700 text-white"><?php echo e($company->name); ?></div>
                                <small class="text-muted"><?php echo e($company->slug); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($company->domain): ?>
                        <a href="https://<?php echo e($company->domain); ?>" target="_blank">
                            <i class="fas fa-external-link-alt me-1" style="font-size: 0.7rem;"></i><?php echo e($company->domain); ?>

                        </a>
                        <?php else: ?>
                        <span class="text-muted"><?php echo e($company->subdomain); ?>.gotrips.ai</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($company->owner): ?>
                        <div class="fw-600"><?php echo e($company->owner->name); ?></div>
                        <small class="text-muted"><?php echo e($company->owner->email); ?></small>
                        <?php else: ?>
                        <span class="text-muted"><i class="fas fa-user-slash me-1"></i>No owner</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            $planColors = [
                                'enterprise' => 'primary',
                                'pro' => 'info',
                                'basic' => 'success',
                                'trial' => 'warning'
                            ];
                        ?>
                        <span class="badge bg-<?php echo e($planColors[$company->plan] ?? 'secondary'); ?>">
                            <?php echo e(ucfirst($company->plan)); ?>

                        </span>
                        <?php if($company->isOnTrial()): ?>
                        <div class="mt-2"><small class="text-warning"><i class="fas fa-clock me-1"></i><?php echo e($company->trial_ends_at->diffForHumans()); ?></small></div>
                        <?php elseif($company->subscription_ends_at): ?>
                        <div class="mt-2"><small class="text-muted">Ends <?php echo e($company->subscription_ends_at->format('M d, Y')); ?></small></div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="fw-700 text-white"><?php echo e(number_format($company->total_orders)); ?></span>
                    </td>
                    <td class="text-end">
                        <span class="text-gold fw-700">AED <?php echo e(number_format($company->total_revenue, 2)); ?></span>
                    </td>
                    <td class="text-center">
                        <?php if($company->is_active): ?>
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>
                        <?php else: ?>
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="<?php echo e(route('superadmin.companies.show', $company)); ?>" class="btn btn-xs btn-outline-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('superadmin.companies.edit', $company)); ?>" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('superadmin.companies.impersonate', $company)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-xs btn-outline-primary" title="Login as Admin">
                                    <i class="fas fa-user-secret"></i>
                                </button>
                            </form>
                            <form action="<?php echo e(route('superadmin.companies.toggle-status', $company)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-xs btn-outline-<?php echo e($company->is_active ? 'danger' : 'success'); ?>" title="<?php echo e($company->is_active ? 'Deactivate' : 'Activate'); ?>">
                                    <i class="fas fa-<?php echo e($company->is_active ? 'ban' : 'check'); ?>"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-building"></i>
                            <h5>No Companies Found</h5>
                            <p>Get started by creating your first company</p>
                            <a href="<?php echo e(route('superadmin.companies.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Company
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($companies->hasPages()): ?>
    <div class="card-footer d-flex justify-content-center">
        <?php echo e($companies->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/companies/index.blade.php ENDPATH**/ ?>