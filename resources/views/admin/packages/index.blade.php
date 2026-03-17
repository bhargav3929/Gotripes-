@extends('layouts.admin')

@section('title', 'Manage Travel Packages')

@section('page-title', 'Manage Travel Packages')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-suitcase-rolling me-2"></i>Manage Travel Packages
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.packages.create') }}"
                               class="btn btn-primary btn-mobile animate-scale">
                                <i class="fas fa-plus me-1"></i>
                                <span class="d-none d-sm-inline">Add New Package</span>
                                <span class="d-sm-none">Add New</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-2 p-sm-3 p-md-4">
                    @if(session('success'))
                        <div class="alert alert-success-custom alert-dismissible fade show animate-fade-in mb-3 mb-md-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Mobile Cards View -->
                    <div class="d-block d-lg-none">
                        @forelse($packages as $index => $package)
                        <div class="card bg-light-dark border-gold mb-3 mobile-card animate-scale">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title text-gold mb-1 fw-semibold">
                                            {{ $package->title }}
                                        </h6>
                                        <div class="mb-1">
                                            <span class="text-gold fw-bold" style="font-size: 1.1rem;">
                                                AED {{ number_format($package->price, 2) }}
                                            </span>
                                        </div>
                                        <span class="badge bg-info-custom">
                                            <i class="fas fa-clock me-1"></i>{{ $package->duration }}
                                        </span>
                                    </div>
                                    @if($package->image)
                                    <div class="ms-2">
                                        <img src="{{ asset($package->image) }}"
                                             alt="{{ $package->title }}"
                                             class="rounded border border-gold"
                                             style="width: 70px; height: 50px; object-fit: cover;">
                                    </div>
                                    @endif
                                </div>

                                <p class="text-muted mb-2" style="font-size: 0.8rem;">
                                    {{ Str::limit($package->description, 80) }}
                                </p>

                                <div class="mb-3">
                                    <small class="text-light-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $package->createdDate ? \Carbon\Carbon::parse($package->createdDate)->format('M d, Y') : 'N/A' }}
                                    </small>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.packages.edit', $package->id) }}"
                                       class="btn-icon-tbl btn-icon-tbl-edit flex-fill" style="height:36px;">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button"
                                            class="btn-icon-tbl btn-icon-tbl-delete flex-fill delete-btn" style="height:36px;"
                                            data-package-id="{{ $package->id }}"
                                            data-package-title="{{ Str::limit($package->title, 30) }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <!-- Mobile Empty State -->
                        <div class="pkg-empty-state text-center">
                            <div class="pkg-empty-glow"></div>
                            <div class="pkg-empty-icon-wrap">
                                <div class="pkg-empty-icon">
                                    <i class="fas fa-suitcase-rolling"></i>
                                </div>
                                <div class="pkg-empty-ring"></div>
                            </div>
                            <h4 class="pkg-empty-title">No Packages Yet</h4>
                            <p class="pkg-empty-desc">Start building your travel collection by adding your first package.</p>
                            <a href="{{ route('admin.packages.create') }}" class="pkg-empty-btn">
                                <i class="fas fa-plus me-2"></i>Create First Package
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Desktop Table View -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 60px;">
                                            <i class="fas fa-hashtag me-1"></i>S.No
                                        </th>
                                        <th style="width: 80px;">
                                            <i class="fas fa-image me-1"></i>Image
                                        </th>
                                        <th>
                                            <i class="fas fa-heading me-1"></i>Title
                                        </th>
                                        <th style="width: 130px;">
                                            <i class="fas fa-tag me-1"></i>Price
                                        </th>
                                        <th style="width: 160px;">
                                            <i class="fas fa-clock me-1"></i>Duration
                                        </th>
                                        <th style="width: 140px;">
                                            <i class="fas fa-calendar me-1"></i>Created
                                        </th>
                                        <th class="text-center" style="width: 120px;">
                                            <i class="fas fa-cogs me-1"></i>Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packages as $index => $package)
                                    <tr>
                                        <td class="text-center opacity-50">
                                            {{ $packages->firstItem() + $index }}
                                        </td>
                                        <td>
                                            @if($package->image)
                                            <img src="{{ asset($package->image) }}"
                                                 alt="{{ $package->title }}"
                                                 class="rounded border border-gold"
                                                 style="width: 64px; height: 44px; object-fit: cover;">
                                            @else
                                            <div class="d-flex align-items-center justify-content-center rounded"
                                                 style="width: 64px; height: 44px; background: rgba(255, 215, 0, 0.06);">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-white fw-semibold">{{ $package->title }}</span>
                                            <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                {{ Str::limit($package->description, 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gold fw-bold">AED {{ number_format($package->price, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-custom">
                                                <i class="fas fa-clock me-1"></i>{{ $package->duration }}
                                            </span>
                                        </td>
                                        <td class="text-light-muted">
                                            {{ $package->createdDate ? \Carbon\Carbon::parse($package->createdDate)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <a href="{{ route('admin.packages.edit', $package->id) }}"
                                                   class="btn-icon-tbl btn-icon-tbl-edit"
                                                   title="Edit Package">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn-icon-tbl btn-icon-tbl-delete delete-btn"
                                                        data-package-id="{{ $package->id }}"
                                                        data-package-title="{{ Str::limit($package->title, 40) }}"
                                                        title="Delete Package">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-0 border-0">
                                            <!-- Desktop Empty State -->
                                            <div class="pkg-empty-state">
                                                <div class="pkg-empty-glow"></div>
                                                <div class="pkg-empty-icon-wrap">
                                                    <div class="pkg-empty-icon">
                                                        <i class="fas fa-suitcase-rolling"></i>
                                                    </div>
                                                    <div class="pkg-empty-ring"></div>
                                                </div>
                                                <h4 class="pkg-empty-title">No Packages Yet</h4>
                                                <p class="pkg-empty-desc">Start building your travel collection by adding your first package.</p>
                                                <a href="{{ route('admin.packages.create') }}" class="pkg-empty-btn">
                                                    <i class="fas fa-plus me-2"></i>Create First Package
                                                </a>
                                                <div class="pkg-empty-features">
                                                    <div class="pkg-empty-feature">
                                                        <i class="fas fa-image"></i>
                                                        <span>Upload Images</span>
                                                    </div>
                                                    <div class="pkg-empty-feature">
                                                        <i class="fas fa-tag"></i>
                                                        <span>Set Pricing</span>
                                                    </div>
                                                    <div class="pkg-empty-feature">
                                                        <i class="fas fa-clock"></i>
                                                        <span>Add Duration</span>
                                                    </div>
                                                    <div class="pkg-empty-feature">
                                                        <i class="fas fa-pen"></i>
                                                        <span>Edit Anytime</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($packages->hasPages())
                    <div class="d-flex justify-content-center mt-3 mt-md-4">
                        <div class="pagination-wrapper">
                            {{ $packages->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h4 class="delete-modal-title">
                <i class="fas fa-exclamation-triangle"></i>
                Confirm Deletion
            </h4>
            <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-modal-icon">
                <i class="fas fa-suitcase-rolling"></i>
            </div>
            <div class="delete-modal-text">
                Are you sure you want to delete this travel package?
            </div>
            <div class="delete-modal-announcement" id="packageToDelete">
                Package Title Will Appear Here
            </div>
            <div class="delete-modal-subtext">
                This action cannot be undone.
            </div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete Package
            </button>
        </div>
    </div>
</div>

<style>
    .btn-icon-tbl {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid transparent; background: transparent;
        cursor: pointer; transition: all 0.15s ease;
        font-size: 0.75rem; text-decoration: none; color: var(--text-muted);
    }
    .btn-icon-tbl:hover { color: var(--text-main); }
    .btn-icon-tbl-edit { color: var(--primary-gold); border-color: rgba(255, 215, 0, 0.12); background: rgba(255, 215, 0, 0.04); }
    .btn-icon-tbl-edit:hover { background: var(--primary-gold); color: #000; border-color: var(--primary-gold); }
    .btn-icon-tbl-delete { color: var(--text-muted); border-color: rgba(255, 255, 255, 0.06); background: rgba(255, 255, 255, 0.02); }
    .btn-icon-tbl-delete:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

    .mobile-card { transition: all 0.2s ease; border: 1px solid var(--border-color) !important; }
    .mobile-card:hover { border-color: var(--border-gold) !important; }
    .border-gold { border-color: rgba(255, 215, 0, 0.2) !important; }

    /* Attractive Empty State */
    .pkg-empty-state {
        padding: 60px 20px 50px;
        position: relative;
        overflow: hidden;
    }

    .pkg-empty-glow {
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .pkg-empty-icon-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 28px;
    }

    .pkg-empty-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.12), rgba(255, 215, 0, 0.04));
        border: 2px solid rgba(255, 215, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #FFD700;
        position: relative;
        z-index: 1;
        animation: iconFloat 3s ease-in-out infinite;
    }

    .pkg-empty-ring {
        position: absolute;
        top: -8px;
        left: -8px;
        right: -8px;
        bottom: -8px;
        border-radius: 50%;
        border: 1px dashed rgba(255, 215, 0, 0.15);
        animation: spinSlow 20s linear infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    @keyframes spinSlow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .pkg-empty-title {
        color: #fff;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.02em;
    }

    .pkg-empty-desc {
        color: #888;
        font-size: 0.95rem;
        max-width: 380px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }

    .pkg-empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #FFD700, #b8860b);
        color: #000;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 14px 32px;
        border-radius: 12px;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
    }

    .pkg-empty-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.35);
        color: #000;
        text-decoration: none;
    }

    .pkg-empty-features {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .pkg-empty-feature {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: #666;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pkg-empty-feature i {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(255, 215, 0, 0.06);
        border: 1px solid rgba(255, 215, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        color: rgba(255, 215, 0, 0.6);
        transition: all 0.3s ease;
    }

    .pkg-empty-feature:hover i {
        background: rgba(255, 215, 0, 0.12);
        color: #FFD700;
        transform: translateY(-2px);
    }

    @media (max-width: 575.98px) {
        .delete-modal-footer { flex-direction: column; }
        .modal-btn { width: 100%; }
        .pkg-empty-state { padding: 40px 16px 36px; }
        .pkg-empty-icon { width: 80px; height: 80px; font-size: 2rem; }
        .pkg-empty-title { font-size: 1.3rem; }
        .pkg-empty-features { gap: 20px; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const successAlert = document.querySelector('.alert-success-custom');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => successAlert.remove(), 300);
        }, 5000);
    }

    let currentPackageId = null;

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentPackageId = this.getAttribute('data-package-id');
            const packageTitle = this.getAttribute('data-package-title');
            showDeleteModal(packageTitle);
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentPackageId) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            this.disabled = true;

            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `{{ route('admin.packages.index') }}/${currentPackageId}`;

            setTimeout(() => deleteForm.submit(), 500);
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
});

function showDeleteModal(packageTitle) {
    document.getElementById('packageToDelete').textContent = packageTitle;
    document.getElementById('deleteModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    document.body.style.overflow = '';
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Package';
    confirmBtn.disabled = false;
    currentPackageId = null;
}
</script>
@endsection
