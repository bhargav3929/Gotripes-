<?php $__env->startSection('title', 'Manage Commissions'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 text-gold"><i class="fas fa-hand-holding-usd me-2"></i>Commissions</h5>
            <small class="text-muted">Manage and approve referral commissions</small>
        </div>
        <a href="<?php echo e(route('admin.referrals.commissions.export', request()->all())); ?>" class="btn btn-gold btn-sm">
            <i class="fas fa-download me-1"></i>Export CSV
        </a>
    </div>

    <!-- Stats Summary -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <small class="text-muted">Pending</small>
                <div class="stat-value text-warning">AED <?php echo e(number_format($stats['pending'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <small class="text-muted">Approved</small>
                <div class="stat-value text-info">AED <?php echo e(number_format($stats['approved'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <small class="text-muted">Paid</small>
                <div class="stat-value text-success">AED <?php echo e(number_format($stats['paid'], 2)); ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-box">
                <small class="text-muted">Total</small>
                <div class="stat-value text-gold">AED <?php echo e(number_format($stats['pending'] + $stats['approved'] + $stats['paid'], 2)); ?></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?php echo e(route('admin.referrals.commissions.index')); ?>" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Approved</option>
                        <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                        <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Agent</label>
                    <select name="agent_id" class="form-select form-select-sm">
                        <option value="">All Agents</option>
                        <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent_id') == $agent->id ? 'selected' : ''); ?>><?php echo e($agent->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">Order Type</label>
                    <select name="order_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="esim" <?php echo e(request('order_type') == 'esim' ? 'selected' : ''); ?>>eSIM</option>
                        <option value="activity" <?php echo e(request('order_type') == 'activity' ? 'selected' : ''); ?>>Activity</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo e(request('start_date')); ?>">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo e(request('end_date')); ?>">
                </div>
                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-gold btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                    <a href="<?php echo e(route('admin.referrals.commissions.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Commissions Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Agent</th>
                        <th>Customer</th>
                        <th class="text-end">Order Amount</th>
                        <th class="text-end">Commission</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <span class="order-id">#<?php echo e($commission->order_id); ?></span>
                            <small class="d-block text-muted"><?php echo e(ucfirst($commission->order_type)); ?></small>
                        </td>
                        <td>
                            <a href="<?php echo e(route('admin.referrals.agents.show', $commission->referralAgent)); ?>" class="agent-link">
                                <?php echo e($commission->referralAgent->name ?? 'N/A'); ?>

                            </a>
                        </td>
                        <td>
                            <div><?php echo e($commission->customer_name ?? 'N/A'); ?></div>
                            <small class="text-muted"><?php echo e($commission->customer_email); ?></small>
                        </td>
                        <td class="text-end">AED <?php echo e(number_format($commission->order_amount, 2)); ?></td>
                        <td class="text-end">
                            <span class="commission-amt">AED <?php echo e(number_format($commission->commission_amount, 2)); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-<?php echo e($commission->status_badge); ?>"><?php echo e(ucfirst($commission->status)); ?></span>
                        </td>
                        <td>
                            <div><?php echo e($commission->created_at->format('M d, Y')); ?></div>
                            <small class="text-muted"><?php echo e($commission->created_at->format('H:i')); ?></small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <?php if($commission->canBeApproved()): ?>
                                <form method="POST" action="<?php echo e(route('admin.referrals.commissions.approve', $commission)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-xs btn-success" title="Approve"><i class="fas fa-check"></i></button>
                                </form>
                                <button type="button" class="btn btn-xs btn-danger reject-btn" data-commission-id="<?php echo e($commission->id); ?>" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                                <?php elseif($commission->canBeMarkedAsPaid()): ?>
                                <form method="POST" action="<?php echo e(route('admin.referrals.commissions.mark-paid', $commission)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-xs btn-info" title="Mark Paid"><i class="fas fa-dollar-sign"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No commissions found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($commissions->hasPages()): ?>
        <div class="card-footer border-top py-2">
            <?php echo e($commissions->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-dark">
            <div class="modal-header border-dark py-2">
                <h6 class="modal-title text-danger"><i class="fas fa-times-circle me-2"></i>Reject</h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body py-2">
                    <label class="form-label">Reason</label>
                    <textarea name="rejection_reason" class="form-control form-control-sm" rows="2" required></textarea>
                </div>
                <div class="modal-footer border-dark py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .text-gold { color: var(--primary-gold) !important; }

    /* Stat Box */
    .stat-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 12px;
    }
    .stat-value {
        font-size: 0.95rem;
        font-weight: 600;
    }

    /* Card */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }
    .card-body {
        padding: 12px;
    }

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
    .form-control-sm, .form-select-sm {
        padding: 4px 8px;
    }

    /* Table */
    .table {
        font-size: 0.75rem;
        margin: 0;
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

    .order-id {
        color: var(--primary-gold);
        font-weight: 500;
    }
    .agent-link {
        color: #e2e8f0;
        text-decoration: none;
    }
    .agent-link:hover {
        color: var(--primary-gold);
    }
    .commission-amt {
        color: #22c55e;
        font-weight: 600;
    }

    /* Buttons */
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
    }
    .btn-sm {
        padding: 4px 10px;
        font-size: 0.75rem;
    }
    .btn-xs {
        padding: 2px 6px;
        font-size: 0.65rem;
    }

    /* Badge */
    .badge {
        font-size: 0.6rem;
        padding: 3px 6px;
    }

    /* Modal */
    .modal-content {
        border: 1px solid var(--border-color);
    }
    .modal-title {
        font-size: 0.85rem;
    }

    /* Text */
    h1, h2, h3, h4, h5, h6 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.reject-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('rejectForm').action = `/admin/referrals/commissions/${this.dataset.commissionId}/reject`;
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Pragathi\Desktop\GoTrips-Complete\resources\views/admin/referrals/commissions/index.blade.php ENDPATH**/ ?>