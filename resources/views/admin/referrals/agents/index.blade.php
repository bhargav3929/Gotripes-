@extends('layouts.admin')

@section('title', 'Referral Agents')

@section('content')
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 text-gold"><i class="fas fa-user-tie me-2"></i>Referral Agents</h5>
            <small class="text-muted">Manage your referral partners</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.referrals.agents.export') }}" class="btn btn-outline-info btn-sm"><i class="fas fa-download me-1"></i>Export</a>
            <a href="{{ route('admin.referrals.agents.create') }}" class="btn btn-gold btn-sm"><i class="fas fa-plus me-1"></i>Add Agent</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.referrals.agents.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, email or code..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-1">
                    <button type="submit" class="btn btn-gold btn-sm flex-grow-1"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('admin.referrals.agents.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-times"></i></a>
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
                    @forelse($agents as $agent)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
                                <div>
                                    <div class="fw-500">{{ $agent->name }}</div>
                                    <small class="text-muted">{{ $agent->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="ref-code">{{ $agent->referral_code }}</code>
                                <button type="button" class="btn btn-link btn-sm p-0 copy-btn" data-copy="{{ $agent->referral_url }}">
                                    <i class="fas fa-copy text-gold"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            @if($agent->commission_type === 'percentage')
                                <span class="badge bg-info">{{ $agent->commission_value }}%</span>
                            @else
                                <span class="badge bg-success">AED {{ number_format($agent->commission_value, 2) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $agent->status === 'active' ? 'success' : ($agent->status === 'suspended' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($agent->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span title="Clicks"><i class="fas fa-mouse-pointer text-muted me-1"></i>{{ $agent->total_clicks }}</span>
                            <span class="mx-2">|</span>
                            <span title="Sales"><i class="fas fa-shopping-cart text-muted me-1"></i>{{ $agent->total_sales }}</span>
                        </td>
                        <td class="text-end">
                            <div class="text-success">AED {{ number_format($agent->total_earnings, 2) }}</div>
                            @if($agent->pending_earnings > 0)
                                <small class="text-warning">{{ number_format($agent->pending_earnings, 2) }} pending</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.referrals.agents.show', $agent) }}" class="btn btn-xs btn-outline-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.referrals.agents.edit', $agent) }}" class="btn btn-xs btn-outline-warning"><i class="fas fa-edit"></i></a>
                                <button type="button" class="btn btn-xs btn-outline-danger delete-btn" data-id="{{ $agent->id }}" data-name="{{ $agent->name }}"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-2x mb-2 d-block"></i>
                            No agents found
                            <div class="mt-2"><a href="{{ route('admin.referrals.agents.create') }}" class="btn btn-gold btn-sm">Add First Agent</a></div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($agents->hasPages())
        <div class="card-footer py-2">{{ $agents->withQueryString()->links() }}</div>
        @endif
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
                    @csrf @method('DELETE')
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

@push('scripts')
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
@endpush
@endsection
