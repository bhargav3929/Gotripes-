<?php $__env->startSection('title', 'All Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 style="font-size: 1.3rem; font-weight: 800; margin: 0; color: var(--text-white);">
        <i class="fas fa-users me-2" style="color: var(--gold);"></i>Users
        <span class="badge bg-primary ms-2" style="font-size: 0.7rem;"><?php echo e($users->total()); ?></span>
    </h1>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="<?php echo e(route('superadmin.users.index')); ?>" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search users..." value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-3">
                <select name="company_id" class="form-select form-select-sm">
                    <option value="">All Companies</option>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($company->id); ?>" <?php echo e(request('company_id') == $company->id ? 'selected' : ''); ?>>
                        <?php echo e($company->name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="super_admin" <?php echo e(request('role') === 'super_admin' ? 'selected' : ''); ?>>Super Admin</option>
                    <option value="company_owner" <?php echo e(request('role') === 'company_owner' ? 'selected' : ''); ?>>Owner</option>
                    <option value="company_admin" <?php echo e(request('role') === 'company_admin' ? 'selected' : ''); ?>>Admin</option>
                    <option value="customer" <?php echo e(request('role') === 'customer' ? 'selected' : ''); ?>>Customer</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="<?php echo e(route('superadmin.users.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Company</th>
                    <th>Role</th>
                    <th class="text-center">Orders</th>
                    <th>Joined</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="company-avatar">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                            <div>
                                <div class="fw-600 text-white"><?php echo e($user->name); ?></div>
                                <small class="text-muted" style="font-size: 0.75rem;"><?php echo e($user->email); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($user->company): ?>
                        <a href="<?php echo e(route('superadmin.companies.show', $user->company)); ?>">
                            <?php echo e($user->company->name); ?>

                        </a>
                        <?php else: ?>
                        <span class="text-muted">No company</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            $roleColors = [
                                'super_admin' => 'danger',
                                'company_owner' => 'primary',
                                'company_admin' => 'info',
                                'company_staff' => 'warning',
                                'customer' => 'success'
                            ];
                            $roleLabels = [
                                'super_admin' => 'Super Admin',
                                'company_owner' => 'Owner',
                                'company_admin' => 'Admin',
                                'company_staff' => 'Staff',
                                'customer' => 'Customer'
                            ];
                        ?>
                        <span class="badge bg-<?php echo e($roleColors[$user->role] ?? 'secondary'); ?>">
                            <?php echo e($roleLabels[$user->role] ?? ucfirst($user->role ?? 'User')); ?>

                        </span>
                    </td>
                    <td class="text-center">
                        <span class="fw-600"><?php echo e($user->esim_orders_count ?? 0); ?></span>
                    </td>
                    <td>
                        <span class="text-muted"><?php echo e($user->created_at->format('M d, Y')); ?></span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="<?php echo e(route('superadmin.users.show', $user)); ?>" class="btn btn-xs btn-outline-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('superadmin.users.edit', $user)); ?>" class="btn btn-xs btn-outline-warning" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($user->role !== 'super_admin'): ?>
                            <form action="<?php echo e(route('superadmin.users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h5>No Users Found</h5>
                            <p>Try adjusting your search filters</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($users->hasPages()): ?>
    <div class="card-footer d-flex justify-content-center">
        <?php echo e($users->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.superadmin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/superadmin/users/index.blade.php ENDPATH**/ ?>