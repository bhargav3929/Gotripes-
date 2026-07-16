@extends('layouts.admin')

@section('title', 'Manage Hajj & Umrah Packages')

@section('page-title', 'Manage Hajj & Umrah Packages')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-kaaba me-2"></i>Manage Hajj & Umrah Packages
                        </h3>
                        <div class="card-tools d-flex align-items-center gap-2">
                            @if(isset($packages) && $packages->count() > 0)
                            <span class="umrah-count-badge">
                                <i class="fas fa-box me-1"></i>{{ $packages->total() }} {{ Str::plural('Package', $packages->total()) }}
                            </span>
                            @endif
                            <a href="{{ route('admin.umrah-packages.create') }}"
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

                    @forelse($packages as $index => $package)
                    <!-- Package Row Card -->
                    <div class="umrah-pkg-row animate-fade-in" style="animation-delay: {{ $index * 0.05 }}s;">
                        <div class="umrah-pkg-rank">
                            <span class="umrah-rank-num">{{ $packages->firstItem() + $index }}</span>
                        </div>

                        <div class="umrah-pkg-img-wrap">
                            @if($package->image)
                            <img src="{{ asset($package->image) }}" alt="{{ $package->title }}" class="umrah-pkg-img">
                            @else
                            <div class="umrah-pkg-img-placeholder">
                                <i class="fas fa-kaaba"></i>
                            </div>
                            @endif
                            @if($package->isFeatured)
                            <div class="umrah-featured-star" title="Featured">
                                <i class="fas fa-star"></i>
                            </div>
                            @endif
                        </div>

                        <div class="umrah-pkg-info">
                            <div class="umrah-pkg-title-row">
                                <h5 class="umrah-pkg-title">{{ $package->title }}</h5>
                                <div class="umrah-pkg-badges">
                                    @if($package->tag)
                                    <span class="umrah-tag-badge umrah-tag-{{ strtolower(str_replace(' ', '-', $package->tag)) }}">{{ $package->tag }}</span>
                                    @endif
                                    <span class="umrah-duration-badge">
                                        <i class="fas fa-clock me-1"></i>{{ $package->duration }}
                                    </span>
                                </div>
                            </div>
                            <p class="umrah-pkg-desc">{{ Str::limit($package->description, 100) }}</p>
                            @if($package->features && count($package->features) > 0)
                            <div class="umrah-pkg-features">
                                @foreach(array_slice($package->features, 0, 4) as $feature)
                                <span class="umrah-feature-chip">
                                    <i class="fas fa-check"></i>{{ $feature }}
                                </span>
                                @endforeach
                                @if(count($package->features) > 4)
                                <span class="umrah-feature-more">+{{ count($package->features) - 4 }} more</span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="umrah-pkg-price-col">
                            <div class="umrah-pkg-currency">{{ $package->currency }}</div>
                            <div class="umrah-pkg-price">{{ number_format($package->price, 0) }}</div>
                            <div class="umrah-pkg-order">Order: {{ $package->sortOrder }}</div>
                        </div>

                        <div class="umrah-pkg-actions">
                            <a href="{{ route('admin.umrah-packages.departures.index', $package->id) }}"
                               class="umrah-action-btn umrah-action-edit bg-success" title="Manage Departures" style="margin-right: 5px;">
                                <i class="fas fa-calendar-alt"></i>
                            </a>
                            <a href="{{ route('admin.umrah-packages.edit', $package->id) }}"
                               class="umrah-action-btn umrah-action-edit" title="Edit Package">
                                <i class="fas fa-pen"></i>
                            </a>
                            <button type="button"
                                    class="umrah-action-btn umrah-action-delete delete-btn"
                                    data-package-id="{{ $package->id }}"
                                    data-package-title="{{ Str::limit($package->title, 40) }}"
                                    title="Delete Package">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <!-- Empty State -->
                    <div class="umrah-empty-state">
                        <div class="umrah-empty-glow"></div>
                        <div class="umrah-empty-icon-wrap">
                            <div class="umrah-empty-icon">
                                <i class="fas fa-kaaba"></i>
                            </div>
                            <div class="umrah-empty-ring"></div>
                        </div>
                        <h4 class="umrah-empty-title">No Packages Yet</h4>
                        <p class="umrah-empty-desc">Start managing your Umrah offerings by creating your first package.</p>
                        <a href="{{ route('admin.umrah-packages.create') }}" class="umrah-empty-btn">
                            <i class="fas fa-plus me-2"></i>Create First Package
                        </a>
                        <div class="umrah-empty-features">
                            <div class="umrah-empty-feature">
                                <i class="fas fa-image"></i>
                                <span>Upload Images</span>
                            </div>
                            <div class="umrah-empty-feature">
                                <i class="fas fa-tags"></i>
                                <span>Add Tags</span>
                            </div>
                            <div class="umrah-empty-feature">
                                <i class="fas fa-list-check"></i>
                                <span>Set Features</span>
                            </div>
                            <div class="umrah-empty-feature">
                                <i class="fas fa-star"></i>
                                <span>Mark Featured</span>
                            </div>
                        </div>
                    </div>
                    @endforelse

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
                <i class="fas fa-kaaba"></i>
            </div>
            <div class="delete-modal-text">
                Are you sure you want to delete this Hajj & Umrah package?
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
    /* Count Badge */
    .umrah-count-badge {
        background: rgba(255, 215, 0, 0.08);
        border: 1px solid rgba(255, 215, 0, 0.15);
        color: #FFD700;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Package Row Card */
    .umrah-pkg-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 10px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .umrah-pkg-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #FFD700, #b8860b);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .umrah-pkg-row:hover {
        border-color: rgba(255, 215, 0, 0.2);
        background: rgba(255, 215, 0, 0.03);
        transform: translateX(4px);
    }

    .umrah-pkg-row:hover::before {
        opacity: 1;
    }

    /* Rank */
    .umrah-pkg-rank {
        flex-shrink: 0;
    }

    .umrah-rank-num {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: rgba(255, 215, 0, 0.06);
        border: 1px solid rgba(255, 215, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: #FFD700;
    }

    /* Image */
    .umrah-pkg-img-wrap {
        flex-shrink: 0;
        position: relative;
    }

    .umrah-pkg-img {
        width: 56px;
        height: 42px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(255, 215, 0, 0.15);
        transition: transform 0.3s ease;
    }

    .umrah-pkg-row:hover .umrah-pkg-img {
        transform: scale(1.05);
    }

    .umrah-pkg-img-placeholder {
        width: 56px;
        height: 42px;
        border-radius: 8px;
        background: rgba(255, 215, 0, 0.04);
        border: 1px dashed rgba(255, 215, 0, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 215, 0, 0.3);
        font-size: 1.2rem;
    }

    .umrah-featured-star {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FFD700, #b8860b);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.5rem;
        color: #000;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
    }

    /* Info */
    .umrah-pkg-info {
        flex: 1;
        min-width: 0;
    }

    .umrah-pkg-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }

    .umrah-pkg-title {
        color: #fff;
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .umrah-pkg-badges {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .umrah-tag-badge {
        padding: 1px 8px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .umrah-tag-popular {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #000;
    }

    .umrah-tag-best-value {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
    }

    .umrah-tag-luxury {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
    }

    .umrah-tag-new {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
    }

    .umrah-tag-economy {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: #fff;
    }

    .umrah-duration-badge {
        padding: 1px 8px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.06);
        color: #999;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .umrah-pkg-desc {
        color: #666;
        font-size: 0.72rem;
        margin: 2px 0 6px;
        line-height: 1.3;
    }

    .umrah-pkg-features {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .umrah-feature-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 1px 7px;
        border-radius: 4px;
        background: rgba(255, 215, 0, 0.04);
        border: 1px solid rgba(255, 215, 0, 0.08);
        font-size: 0.62rem;
        color: #aaa;
    }

    .umrah-feature-chip i {
        color: #FFD700;
        font-size: 0.5rem;
    }

    .umrah-feature-more {
        font-size: 0.62rem;
        color: #666;
        padding: 3px 8px;
    }

    /* Price Column */
    .umrah-pkg-price-col {
        flex-shrink: 0;
        text-align: center;
        min-width: 80px;
    }

    .umrah-pkg-currency {
        font-size: 0.6rem;
        font-weight: 600;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .umrah-pkg-price {
        font-size: 1.1rem;
        font-weight: 800;
        background: linear-gradient(135deg, #FFD700, #debb55);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .umrah-pkg-order {
        font-size: 0.58rem;
        color: #555;
        margin-top: 2px;
    }

    /* Actions */
    .umrah-pkg-actions {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .umrah-action-btn {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .umrah-action-edit {
        color: #FFD700;
        border-color: rgba(255, 215, 0, 0.12);
        background: rgba(255, 215, 0, 0.04);
    }

    .umrah-action-edit:hover {
        background: #FFD700;
        color: #000;
        border-color: #FFD700;
        transform: scale(1.1);
    }

    .umrah-action-delete {
        color: #666;
        border-color: rgba(255, 255, 255, 0.06);
        background: rgba(255, 255, 255, 0.02);
    }

    .umrah-action-delete:hover {
        background: #ef4444;
        color: #fff;
        border-color: #ef4444;
        transform: scale(1.1);
    }

    /* Empty State */
    .umrah-empty-state {
        padding: 60px 20px 50px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .umrah-empty-glow {
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 215, 0, 0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .umrah-empty-icon-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 28px;
    }

    .umrah-empty-icon {
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

    .umrah-empty-ring {
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

    .umrah-empty-title {
        color: #fff;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .umrah-empty-desc {
        color: #888;
        font-size: 0.95rem;
        max-width: 380px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }

    .umrah-empty-btn {
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

    .umrah-empty-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.35);
        color: #000;
        text-decoration: none;
    }

    .umrah-empty-features {
        display: flex;
        justify-content: center;
        gap: 32px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .umrah-empty-feature {
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

    .umrah-empty-feature i {
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

    .umrah-empty-feature:hover i {
        background: rgba(255, 215, 0, 0.12);
        color: #FFD700;
        transform: translateY(-2px);
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .umrah-pkg-row {
            flex-wrap: wrap;
            gap: 14px;
            padding: 16px;
        }

        .umrah-pkg-rank { display: none; }

        .umrah-pkg-info {
            order: 3;
            flex-basis: 100%;
        }

        .umrah-pkg-price-col {
            text-align: left;
            min-width: auto;
            order: 2;
            flex: 1;
        }

        .umrah-pkg-actions {
            flex-direction: row;
            order: 4;
            flex-basis: 100%;
        }

        .umrah-action-btn {
            flex: 1;
            height: 38px;
            width: auto;
        }

        .umrah-pkg-features { display: none; }
    }

    @media (max-width: 575.98px) {
        .delete-modal-footer { flex-direction: column; }
        .modal-btn { width: 100%; }
        .umrah-empty-state { padding: 40px 16px 36px; }
        .umrah-empty-icon { width: 80px; height: 80px; font-size: 2rem; }
        .umrah-empty-features { gap: 20px; }
        .umrah-count-badge { display: none; }
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
            showDeleteModal(this.getAttribute('data-package-title'));
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentPackageId) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            this.disabled = true;
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `{{ route('admin.umrah-packages.index') }}/${currentPackageId}`;
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

function showDeleteModal(title) {
    document.getElementById('packageToDelete').textContent = title;
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
