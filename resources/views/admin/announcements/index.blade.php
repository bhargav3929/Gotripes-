@extends('layouts.admin')

@section('title', 'Manage Announcements')

@section('page-title', 'Manage Announcements')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Main Card -->
            <div class="card shadow-lg border-0 animate-fade-in">
                <!-- Mobile-First Card Header -->
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-bullhorn me-2"></i>Manage Announcements
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
                                       class="btn-icon-tbl btn-icon-tbl-edit flex-fill" style="height:36px;">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button"
                                            class="btn-icon-tbl btn-icon-tbl-delete flex-fill delete-btn" style="height:36px;"
                                            data-announcement-id="{{ $announcement->id }}"
                                            data-announcement-title="{{ Str::limit($announcement->description, 30) }}">
                                        <i class="fas fa-trash-alt"></i>
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
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
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
                                    <tr>
                                        <td class="text-center opacity-50">
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
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <a href="{{ route('admin.announcements.edit', $announcement) }}"
                                                   class="btn-icon-tbl btn-icon-tbl-edit"
                                                   title="Edit Announcement">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn-icon-tbl btn-icon-tbl-delete delete-btn"
                                                        data-announcement-id="{{ $announcement->id }}"
                                                        data-announcement-title="{{ Str::limit($announcement->description, 40) }}"
                                                        title="Delete Announcement">
                                                    <i class="fas fa-trash-alt"></i>
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

<!-- Page-Specific Styles -->
<style>
    /* Compact icon-only table action buttons */
    .btn-icon-tbl {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 0.75rem;
        text-decoration: none;
        color: var(--text-muted);
    }
    .btn-icon-tbl:hover { color: var(--text-main); }
    .btn-icon-tbl-edit { color: var(--primary-gold); border-color: rgba(255, 215, 0, 0.12); background: rgba(255, 215, 0, 0.04); }
    .btn-icon-tbl-edit:hover { background: var(--primary-gold); color: #000; border-color: var(--primary-gold); }
    .btn-icon-tbl-delete { color: var(--text-muted); border-color: rgba(255, 255, 255, 0.06); background: rgba(255, 255, 255, 0.02); }
    .btn-icon-tbl-delete:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

    /* Mobile Card Styling */
    .mobile-card {
        transition: all 0.2s ease;
        border: 1px solid var(--border-color) !important;
    }

    .mobile-card:hover {
        border-color: var(--border-gold) !important;
    }

    /* Flag Images */
    .mobile-flag-img { width: 30px; height: 22px; object-fit: cover; }
    .desktop-flag-img { width: 35px; height: 25px; object-fit: cover; }

    /* Empty State */
    .empty-state-mobile,
    .empty-state-desktop { padding: 2rem 1rem; }

    @media (max-width: 575.98px) {
        .mobile-flag-img { width: 25px; height: 18px; }
        .delete-modal-footer { flex-direction: column; }
        .modal-btn { width: 100%; }
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
