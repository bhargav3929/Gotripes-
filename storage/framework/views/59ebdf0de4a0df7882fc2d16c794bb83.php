<?php $__env->startSection('title', 'Referral Agents'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1" style="color: var(--primary-gold);">
                <i class="fas fa-user-tie me-2"></i>Referral Agents
            </h1>
            <p class="text-muted mb-0">Manage your referral partners</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.referrals.agents.export')); ?>" class="btn btn-outline-info">
                <i class="fas fa-download me-2"></i>Export
            </a>
            <a href="<?php echo e(route('admin.referrals.agents.create')); ?>" class="btn btn-gold">
                <i class="fas fa-plus me-2"></i>Add Agent
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.referrals.agents.index')); ?>" class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name, email or referral code..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        <option value="suspended" <?php echo e(request('status') == 'suspended' ? 'selected' : ''); ?>>Suspended</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 col-lg-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-gold flex-grow-1">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <a href="<?php echo e(route('admin.referrals.agents.index')); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Agents Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 datatable">
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
                                    <div class="avatar-circle me-3">
                                        <?php echo e(strtoupper(substr($agent->name, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?php echo e($agent->name); ?></div>
                                        <small class="text-muted"><?php echo e($agent->email); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="referral-code"><?php echo e($agent->referral_code); ?></code>
                                    <button type="button" class="btn btn-sm btn-link text-gold p-0 copy-btn" data-copy="<?php echo e($agent->referral_url); ?>" title="Copy referral link">
                                        <i class="fas fa-copy"></i>
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
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                           data-agent-id="<?php echo e($agent->id); ?>"
                                           <?php echo e($agent->status === 'active' ? 'checked' : ''); ?>>
                                </div>
                                <span class="badge bg-<?php echo e($agent->status === 'active' ? 'success' : ($agent->status === 'suspended' ? 'danger' : 'secondary')); ?> ms-1">
                                    <?php echo e(ucfirst($agent->status)); ?>

                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-3">
                                    <div title="Clicks">
                                        <i class="fas fa-mouse-pointer text-muted me-1"></i>
                                        <span><?php echo e(number_format($agent->total_clicks)); ?></span>
                                    </div>
                                    <div title="Sales">
                                        <i class="fas fa-shopping-cart text-muted me-1"></i>
                                        <span><?php echo e(number_format($agent->total_sales)); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="text-success fw-semibold">AED <?php echo e(number_format($agent->total_earnings, 2)); ?></div>
                                <?php if($agent->pending_earnings > 0): ?>
                                    <small class="text-warning"><?php echo e(number_format($agent->pending_earnings, 2)); ?> pending</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?php echo e(route('admin.referrals.agents.show', $agent)); ?>" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('admin.referrals.agents.edit', $agent)); ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-agent-id="<?php echo e($agent->id); ?>"
                                            data-agent-name="<?php echo e($agent->name); ?>" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-3">No referral agents found</p>
                                <a href="<?php echo e(route('admin.referrals.agents.create')); ?>" class="btn btn-gold">
                                    <i class="fas fa-plus me-2"></i>Add Your First Agent
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($agents->hasPages()): ?>
        <div class="card-footer border-top border-dark">
            <?php echo e($agents->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-dark">
                <h5 class="modal-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Delete Agent</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteAgentName"></strong>?</p>
                <p class="text-warning small mb-0"><i class="fas fa-info-circle me-1"></i>This action cannot be undone. All related tracking data will also be deleted.</p>
            </div>
            <div class="modal-footer border-dark">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Agent</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .referral-code {
        background: rgba(255, 215, 0, 0.1);
        color: var(--primary-gold);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #000;
        font-size: 1rem;
    }
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold));
        color: #000;
    }
    .text-gold {
        color: var(--primary-gold) !important;
    }
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
    }
    .card-body {
        padding: 1.25rem;
    }
    .form-control, .form-select {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        color: var(--text-main);
    }
    .form-control:focus, .form-select:focus {
        background: var(--light-dark);
        border-color: var(--primary-gold);
        color: var(--text-main);
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.15);
    }
    .table-dark {
        --bs-table-bg: transparent;
        --bs-table-hover-bg: rgba(255, 255, 255, 0.03);
    }
    .table-dark th {
        color: var(--text-muted);
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-color);
        padding: 1rem;
    }
    .table-dark td {
        color: var(--text-main);
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        padding: 1rem;
    }
    .form-check-input:checked {
        background-color: var(--success);
        border-color: var(--success);
    }
    .btn-group .btn {
        padding: 0.375rem 0.625rem;
    }
    .modal-content {
        border: 1px solid var(--border-color);
    }
    /* Text Visibility Fixes */
    p, span, div, li, small {
        color: #e2e8f0;
    }
    .text-muted {
        color: #a0aec0 !important;
    }
    h1, h2, h3, h4, h5, h6, strong {
        color: #ffffff !important;
    }
    .card-header h5, .card-header h6 {
        color: #ffffff !important;
    }
    .form-label {
        color: #e2e8f0 !important;
    }
    .modal-title {
        color: #ffffff !important;
    }
    .modal-body p {
        color: #e2e8f0 !important;
    }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy to clipboard
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = this.dataset.copy;
            navigator.clipboard.writeText(text).then(() => {
                const icon = this.querySelector('i');
                icon.className = 'fas fa-check';
                setTimeout(() => {
                    icon.className = 'fas fa-copy';
                }, 2000);
            });
        });
    });

    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const agentId = this.dataset.agentId;
            fetch(`/admin/referrals/agents/${agentId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });

    // Delete modal
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const agentId = this.dataset.agentId;
            const agentName = this.dataset.agentName;
            document.getElementById('deleteAgentName').textContent = agentName;
            document.getElementById('deleteForm').action = `/admin/referrals/agents/${agentId}`;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Pragathi\Desktop\GoTrips-Complete\resources\views/admin/referrals/agents/index.blade.php ENDPATH**/ ?>