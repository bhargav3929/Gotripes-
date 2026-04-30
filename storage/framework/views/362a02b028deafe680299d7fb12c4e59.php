<?php $__env->startSection('title', 'Referral Agents'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 text-gold"><i class="fas fa-user-tie me-2"></i>Referral Agents</h5>
            <small class="text-muted">Manage your referral partners</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.referrals.agents.export')); ?>" class="btn btn-outline-info btn-sm"><i class="fas fa-download me-1"></i>Export</a>
            <a href="<?php echo e(route('admin.referrals.agents.create')); ?>" class="btn btn-gold btn-sm"><i class="fas fa-plus me-1"></i>Add Agent</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?php echo e(route('admin.referrals.agents.index')); ?>" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, email or code..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        <option value="suspended" <?php echo e(request('status') == 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-1">
                    <button type="submit" class="btn btn-gold btn-sm flex-grow-1"><i class="fas fa-search"></i> Filter</button>
                    <a href="<?php echo e(route('admin.referrals.agents.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Agents Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Agent</th>
                        <th>Referral Code</th>
                        <th>Commission</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Stats</th>
                        <th class="text-end">Earnings</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2"><?php echo e(strtoupper(substr($agent->name, 0, 1))); ?></div>
                                <div>
                                    <div class="fw-500"><?php echo e($agent->name); ?></div>
                                    <small class="text-muted"><?php echo e($agent->email); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="ref-code"><?php echo e($agent->referral_code); ?></code>
                                <button type="button" class="btn btn-link btn-sm p-0 copy-btn" data-copy="<?php echo e($agent->referral_url); ?>">
                                    <i class="fas fa-copy text-gold"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <?php if($agent->commission_type === 'percentage'): ?>
                                <span class="badge bg-info"><?php echo e($agent->commission_value); ?>%</span>
                            <?php else: ?>
                                <span class="badge bg-success">AED <?php echo e(number_format($agent->commission_value, 2)); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?php echo e($agent->status === 'active' ? 'success' : ($agent->status === 'suspended' ? 'danger' : 'secondary')); ?>">
                                <?php echo e(ucfirst($agent->status)); ?>

                            </span>
                        </td>
                        <td class="text-center">
                            <span title="Clicks"><i class="fas fa-mouse-pointer text-muted me-1"></i><?php echo e($agent->total_clicks); ?></span>
                            <span class="mx-2">|</span>
                            <span title="Sales"><i class="fas fa-shopping-cart text-muted me-1"></i><?php echo e($agent->total_sales); ?></span>
                        </td>
                        <td class="text-end">
                            <div class="text-success">AED <?php echo e(number_format($agent->total_earnings, 2)); ?></div>
                            <?php if($agent->pending_earnings > 0): ?>
                                <small class="text-warning"><?php echo e(number_format($agent->pending_earnings, 2)); ?> pending</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?php echo e(route('admin.referrals.agents.show', $agent)); ?>" class="btn btn-xs btn-outline-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('admin.referrals.agents.edit', $agent)); ?>" class="btn btn-xs btn-outline-warning"><i class="fas fa-edit"></i></a>
                                <button type="button" class="btn btn-xs btn-outline-danger delete-btn" data-id="<?php echo e($agent->id); ?>" data-name="<?php echo e($agent->name); ?>"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            No agents found
                            <div class="mt-2"><a href="<?php echo e(route('admin.referrals.agents.create')); ?>" class="btn btn-gold btn-sm">Add First Agent</a></div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($agents->hasPages()): ?>
        <div class="card-footer py-2"><?php echo e($agents->withQueryString()->links()); ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-dark">
            <div class="modal-header border-dark py-2">
                <h6 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Delete Agent</h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <p class="small mb-0">Delete <strong id="deleteAgentName"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer border-dark py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Card */
    .card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; }
    .card-body { padding: 12px; }

    /* Form */
    .form-label { font-size: 0.65rem; color: var(--text-muted); margin-bottom: 2px; }
    .form-control, .form-select { background: var(--light-dark); border: 1px solid var(--border-color); color: var(--text-main); font-size: 0.75rem; }
    .form-control:focus, .form-select:focus { background: var(--light-dark); border-color: var(--primary-gold); box-shadow: none; }
    .form-control-sm, .form-select-sm { padding: 4px 8px; }

    /* Table */
    .table { font-size: 0.75rem; background: transparent !important; }
    .table thead th { background: rgba(0,0,0,0.2) !important; color: var(--text-muted); font-size: 0.65rem; font-weight: 500; text-transform: uppercase; padding: 8px 10px; border-bottom: 1px solid var(--border-color); }
    .table tbody td { padding: 8px 10px; border-bottom: 1px solid var(--border-color); background: transparent !important; color: #e2e8f0; vertical-align: middle; }
    .table tbody tr:hover { background: rgba(255,215,0,0.03) !important; }

    /* Avatar */
    .avatar-sm { width: 28px; height: 28px; background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 600; color: #000; }
    .fw-500 { font-weight: 500; }
    .ref-code { background: rgba(255, 215, 0, 0.1); color: var(--primary-gold); padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; }

    /* Buttons */
    .btn-gold { background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold)); border: none; color: #000; font-weight: 600; }
    .btn-sm { padding: 4px 10px; font-size: 0.75rem; }
    .btn-xs { padding: 2px 6px; font-size: 0.65rem; }

    /* Badge */
    .badge { font-size: 0.6rem; padding: 3px 6px; }

    /* Modal */
    .modal-content { border: 1px solid var(--border-color); }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            const icon = this.querySelector('i');
            icon.className = 'fas fa-check text-success';
            setTimeout(() => icon.className = 'fas fa-copy text-gold', 2000);
        });
    });
});
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('deleteAgentName').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = `/admin/referrals/agents/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/admin/referrals/agents/index.blade.php ENDPATH**/ ?>