@extends('layouts.admin')

@section('title', 'Manage Announcements')

@section('page-title', 'Manage Announcements')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Main Card -->
            <div class="card shadow-lg border-0 animate-fade-in">
                <!-- Mobile-First Card Header -->
                <div class="card-header bg-gold border-bottom-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 gap-sm-3">
                        <h3 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-bullhorn me-2 d-none d-sm-inline"></i>
                            <span class="fs-6 fs-sm-5 fs-md-4">
                                <span class="d-none d-sm-inline">Manage </span>Announcements
                            </span>
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.announcements.create') }}" 
                               class="btn btn-primary btn-mobile animate-scale">
                                <i class="fas fa-plus me-1"></i> 
                                <span class="d-none d-sm-inline">Add New Announcement</span>
                                <span class="d-sm-none">Add New</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-2 p-sm-3 p-md-4">
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert alert-success-custom alert-dismissible fade show animate-fade-in mb-3 mb-md-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Mobile Cards View (Hidden on Desktop) -->
                    <div class="d-block d-lg-none">
                        @forelse($announcements as $index => $announcement)
                        <div class="card bg-light-dark border-gold mb-3 mobile-card animate-scale">
                            <div class="card-body p-3">
                                <!-- Mobile Card Header -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title text-gold mb-1 fw-semibold">
                                            #{{ $announcements->firstItem() + $index }}
                                        </h6>
                                        <p class="card-text text-white mb-2 fs-7">
                                            {{ Str::limit($announcement->description, 45) }}
                                        </p>
                                    </div>
                                    @if($announcement->flagPath)
                                    <div class="ms-2">
                                        <img src="{{ asset($announcement->flagPath) }}" 
                                             alt="Flag" 
                                             class="rounded border border-gold mobile-flag-img">
                                    </div>
                                    @endif
                                </div>

                                <!-- Mobile Badges -->
                                <div class="mb-2">
                                    @if($announcement->announcementImportance == 1)
                                    <span class="badge bg-success-custom me-1">
                                        <i class="fas fa-star me-1"></i>New
                                    </span>
                                    @endif
                                    <span class="badge bg-info-custom">
                                        {{ $announcement->createdBy ?? 'System' }}
                                    </span>
                                </div>

                                <!-- Mobile Date -->
                                <div class="mb-3">
                                    <small class="text-light-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $announcement->createdDate ? \Carbon\Carbon::parse($announcement->createdDate)->format('M d, Y H:i') : 'N/A' }}
                                    </small>
                                </div>

                                <!-- Mobile Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                                       class="btn btn-warning-custom btn-sm flex-fill">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger-custom btn-sm flex-fill delete-btn"
                                            data-announcement-id="{{ $announcement->id }}"
                                            data-announcement-title="{{ Str::limit($announcement->description, 30) }}">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state-mobile text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-bullhorn text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                            <h5 class="text-light-muted mb-2">No Announcements Found</h5>
                            <p class="text-muted mb-3">Get started by creating your first announcement.</p>
                            <a href="{{ route('admin.announcements.create') }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Announcement
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Desktop Table View (Hidden on Mobile) -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-bordered">
                                <thead>
                                    <tr class="table-header-gold">
                                        <th class="text-center" style="width: 80px;">
                                            <i class="fas fa-hashtag me-1"></i>S.No
                                        </th>
                                        <th>
                                            <i class="fas fa-heading me-1"></i>Title
                                        </th>
                                        <th style="width: 150px;">
                                            <i class="fas fa-user me-1"></i>Created By
                                        </th>
                                        <th style="width: 180px;">
                                            <i class="fas fa-calendar me-1"></i>Created Date
                                        </th>
                                        <th class="text-center" style="width: 200px;">
                                            <i class="fas fa-cogs me-1"></i>Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($announcements as $index => $announcement)
                                    <tr class="table-row-hover">
                                        <td class="text-center fw-medium text-gold">
                                            {{ $announcements->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <span class="text-white">{{ Str::limit($announcement->description, 60) }}</span>
                                                    <div class="mt-1">
                                                        @if($announcement->announcementImportance == 1)
                                                        <span class="badge bg-success-custom me-1">
                                                            <i class="fas fa-star me-1"></i>New
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($announcement->flagPath)
                                                <div class="ms-2">
                                                    <img src="{{ asset($announcement->flagPath) }}" 
                                                         alt="Flag" 
                                                         class="rounded border border-gold desktop-flag-img"
                                                         title="Country Flag">
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-custom">
                                                {{ $announcement->createdBy ?? 'System' }}
                                            </span>
                                        </td>
                                        <td class="text-light-muted">
                                            {{ $announcement->createdDate ? \Carbon\Carbon::parse($announcement->createdDate)->format('M d, Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                                                   class="btn btn-warning-custom btn-sm animate-scale"
                                                   title="Edit Announcement">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger-custom btn-sm animate-scale delete-btn"
                                                        data-announcement-id="{{ $announcement->id }}"
                                                        data-announcement-title="{{ Str::limit($announcement->description, 40) }}"
                                                        title="Delete Announcement">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state-desktop">
                                                <i class="fas fa-bullhorn text-gold mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                                                <h4 class="text-light-muted mb-2">No Announcements Found</h4>
                                                <p class="text-muted mb-3">Get started by creating your first announcement to engage your audience.</p>
                                                <a href="{{ route('admin.announcements.create') }}" 
                                                   class="btn btn-primary btn-lg">
                                                    <i class="fas fa-plus me-2"></i>Create Your First Announcement
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile-Responsive Pagination -->
                    @if($announcements->hasPages())
                    <div class="d-flex justify-content-center mt-3 mt-md-4">
                        <div class="pagination-wrapper">
                            {{ $announcements->links() }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Custom Delete Confirmation Popup -->
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
                <i class="fas fa-bullhorn"></i>
            </div>
            <div class="delete-modal-text">
                Are you sure you want to delete this announcement?
            </div>
            <div class="delete-modal-announcement" id="announcementToDelete">
                Announcement Title Will Appear Here
            </div>
            <div class="delete-modal-subtext">
                This action cannot be undone. The announcement will be permanently removed from the system.
            </div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete Announcement
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Mobile-First Responsive Styles -->
<style>
    /* Custom Font Size Classes */
    .fs-8 { font-size: 0.7rem !important; }
    .fs-7 { font-size: 0.8rem !important; }
    .fs-6 { font-size: 0.9rem !important; }

    /* Text Visibility */
    .text-light-muted {
        color: #e9ecef !important;
    }

    /* Mobile Button Styling */
    .btn-mobile {
        min-height: 44px !important;
        padding: 0.6rem 1rem !important;
        font-size: 0.875rem !important;
    }

    /* Custom Alert Styling */
    .alert-success-custom {
        background: rgba(40, 167, 69, 0.2) !important;
        border: 1px solid rgba(40, 167, 69, 0.5) !important;
        color: #28a745 !important;
        border-radius: 8px;
    }

    /* Mobile Card Styling */
    .mobile-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
    }

    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2) !important;
    }

    .bg-light-dark {
        background-color: rgba(45, 45, 45, 0.95) !important;
    }

    .border-gold {
        border-color: rgba(255, 215, 0, 0.6) !important;
    }

    /* Mobile Flag Image */
    .mobile-flag-img {
        width: 30px;
        height: 22px;
        object-fit: cover;
    }

    .desktop-flag-img {
        width: 35px;
        height: 25px;
        object-fit: cover;
    }

    /* Custom Badge Styling */
    .bg-success-custom {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: #ffffff !important;
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .bg-info-custom {
        background: linear-gradient(135deg, #17a2b8, #6f42c1) !important;
        color: #ffffff !important;
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* Custom Button Styling */
    .btn-warning-custom {
        background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
        border: 1px solid #ffc107 !important;
        color: #212529 !important;
        font-weight: 500;
        min-height: 40px;
    }

    .btn-warning-custom:hover {
        background: linear-gradient(135deg, #e0a800, #dc6502) !important;
        border-color: #d39e00 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4) !important;
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border: 1px solid #dc3545 !important;
        color: #ffffff !important;
        font-weight: 500;
        min-height: 40px;
    }

    .btn-danger-custom:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a) !important;
        border-color: #bd2130 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    }

    /* Desktop Table Styling */
    .table-header-gold th {
        background: var(--gradient-primary) !important;
        color: var(--darker-bg) !important;
        font-weight: 600 !important;
        border: 1px solid var(--primary-gold) !important;
        vertical-align: middle !important;
        font-size: 0.9rem;
    }

    .table-dark {
        background: var(--light-dark) !important;
        color: var(--text-light) !important;
    }

    .table-dark td {
        border-color: rgba(255, 215, 0, 0.2) !important;
        vertical-align: middle !important;
        padding: 1rem 0.75rem !important;
    }

    .table-row-hover:hover {
        background: rgba(255, 215, 0, 0.1) !important;
    }

    /* Responsive Table */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    /* Empty State */
    .empty-state-mobile,
    .empty-state-desktop {
        padding: 2rem 1rem;
    }

    /* Custom Delete Modal */
    .delete-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        animation: fadeIn 0.3s ease;
    }

    .delete-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .delete-modal-content {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border: 3px solid #ffd700;
        border-radius: 15px;
        padding: 0;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }

    .delete-modal-header {
        background: linear-gradient(45deg, #dc3545, #ff4757);
        color: white;
        padding: 20px 25px;
        border-bottom: 2px solid #ffd700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .delete-modal-title {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .delete-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .delete-modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    .delete-modal-body {
        padding: 30px 25px;
        text-align: center;
    }

    .delete-modal-icon {
        color: #ffd700;
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .delete-modal-text {
        color: #ffffff;
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .delete-modal-subtext {
        color: #aaa;
        font-size: 14px;
        margin-bottom: 30px;
        line-height: 1.5;
    }

    .delete-modal-announcement {
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid #ffd700;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
        color: #ffd700;
        font-weight: 600;
    }

    .delete-modal-footer {
        display: flex;
        gap: 15px;
        justify-content: center;
        padding: 0 25px 25px 25px;
    }

    .modal-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    .modal-btn-cancel {
        background: linear-gradient(45deg, #6c757d, #868e96);
        color: white;
    }

    .modal-btn-cancel:hover {
        background: linear-gradient(45deg, #868e96, #6c757d);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }

    .modal-btn-delete {
        background: linear-gradient(45deg, #dc3545, #ff4757);
        color: white;
    }

    .modal-btn-delete:hover {
        background: linear-gradient(45deg, #ff4757, #dc3545);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .modal-btn-delete.processing {
        background: #666;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .modal-btn-delete.processing:hover {
        transform: none;
        box-shadow: none;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { 
            transform: scale(0.8) translateY(-50px);
            opacity: 0;
        }
        to { 
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    /* Pagination Styling */
    .pagination-wrapper .page-link {
        background: var(--light-dark) !important;
        border-color: rgba(255, 215, 0, 0.3) !important;
        color: var(--primary-gold) !important;
        min-width: 40px;
        min-height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pagination-wrapper .page-link:hover {
        background: var(--gradient-primary) !important;
        border-color: var(--primary-gold) !important;
        color: var(--darker-bg) !important;
    }

    .pagination-wrapper .page-item.active .page-link {
        background: var(--gradient-primary) !important;
        border-color: var(--primary-gold) !important;
        color: var(--darker-bg) !important;
    }

    /* BREAKPOINT: Extra Small (Phone) */
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem 1rem !important;
        }
        
        .mobile-card .card-body {
            padding: 0.75rem !important;
        }
        
        .btn {
            font-size: 0.8rem !important;
            padding: 0.5rem 0.75rem !important;
        }
        
        h3 {
            font-size: 1.1rem !important;
        }
        
        .mobile-flag-img {
            width: 25px;
            height: 18px;
        }
        
        .empty-state-mobile {
            padding: 1.5rem 0.5rem;
        }
        
        .delete-modal-content {
            width: 95%;
            margin: 20px;
        }
        
        .delete-modal-footer {
            flex-direction: column;
        }
        
        .modal-btn {
            width: 100%;
        }
    }

    /* BREAKPOINT: Small (Large Phone) */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .fs-sm-7 { font-size: 0.8rem !important; }
        .fs-sm-6 { font-size: 0.9rem !important; }
        .fs-sm-5 { font-size: 1rem !important; }
        
        .mobile-flag-img {
            width: 28px;
            height: 20px;
        }
    }

    /* BREAKPOINT: Medium (Tablet) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .card-body {
            padding: 2rem !important;
        }
    }

    /* BREAKPOINT: Large (Desktop) */
    @media (min-width: 992px) {
        .fs-md-5 { font-size: 1rem !important; }
        .fs-md-4 { font-size: 1.25rem !important; }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* BREAKPOINT: Extra Large (Large Desktop) */
    @media (min-width: 1200px) {
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }
    }

    /* Touch Device Optimizations */
    @media (hover: none) and (pointer: coarse) {
        .btn {
            min-height: 48px !important;
        }
        
        .animate-scale:hover {
            transform: none !important;
        }
        
        .mobile-card:hover {
            transform: none !important;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .animate-fade-in,
        .animate-scale,
        .mobile-card {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Landscape Mobile */
    @media (max-height: 500px) and (orientation: landscape) and (max-width: 991px) {
        .mobile-card .card-body {
            padding: 0.75rem !important;
        }
        
        .empty-state-mobile {
            padding: 1rem 0.5rem;
        }
        
        .delete-modal-header {
            padding: 15px 20px;
        }
        
        .delete-modal-body {
            padding: 20px 15px;
        }
        
        .delete-modal-title {
            font-size: 18px;
        }
        
        .delete-modal-text {
            font-size: 16px;
        }
    }
</style>

<!-- Enhanced Mobile-Responsive JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success alerts
    const successAlert = document.querySelector('.alert-success-custom');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.remove();
            }, 300);
        }, 5000);
    }

    // Delete modal functionality
    let currentDeleteForm = null;
    let currentAnnouncementId = null;

    // Add click event to all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const announcementId = this.getAttribute('data-announcement-id');
            const announcementTitle = this.getAttribute('data-announcement-title');
            
            currentAnnouncementId = announcementId;
            showDeleteModal(announcementTitle);
        });
    });

    // Confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentAnnouncementId) {
            // Show processing state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            this.classList.add('processing');
            this.disabled = true;
            
            // Set up and submit the form
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `{{ route('admin.announcements.index') }}/${currentAnnouncementId}`;
            
            setTimeout(() => {
                deleteForm.submit();
            }, 500);
        }
    });

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
});

function showDeleteModal(announcementTitle) {
    document.getElementById('announcementToDelete').textContent = announcementTitle;
    document.getElementById('deleteModal').classList.add('show');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    document.body.style.overflow = ''; // Restore scrolling
    
    // Reset confirm button
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Announcement';
    confirmBtn.classList.remove('processing');
    confirmBtn.disabled = false;
    
    currentAnnouncementId = null;
}

// Handle window resize for responsive adjustments
window.addEventListener('resize', function() {
    // Add any resize-specific logic here if needed
});

// Touch gesture support for mobile cards (optional enhancement)
if ('ontouchstart' in window) {
    document.querySelectorAll('.mobile-card').forEach(card => {
        let touchStartY = 0;
        let touchEndY = 0;
        
        card.addEventListener('touchstart', e => {
            touchStartY = e.changedTouches[0].screenY;
        });
        
        card.addEventListener('touchend', e => {
            touchEndY = e.changedTouches[0].screenY;
            // Add any swipe gesture logic here if needed
        });
    });
}
</script>
@endsection
