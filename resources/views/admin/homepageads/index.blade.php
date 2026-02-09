@extends('layouts.admin')

@section('title', 'Manage Carousel Images')

@section('page-title', 'Manage Carousel Images')

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
                            <i class="fas fa-images me-2 d-none d-sm-inline"></i>
                            <span class="fs-6 fs-sm-5 fs-md-4">
                                <span class="d-none d-sm-inline">Manage </span>Carousel Images
                            </span>
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.homepageads.create') }}" 
                               class="btn btn-primary btn-mobile animate-scale">
                                <i class="fas fa-plus me-1"></i> 
                                <span class="d-none d-sm-inline">Add New Image</span>
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

                    <!-- Statistics Info -->
                    @if($carouselImages->count() > 0)
                    <div class="stats-info">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $carouselImages->total() }}</div>
                                    <div class="stats-label">Total Images</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $carouselImages->currentPage() }}</div>
                                    <div class="stats-label">Current Page</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $carouselImages->lastPage() }}</div>
                                    <div class="stats-label">Total Pages</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Mobile Cards View (Hidden on Desktop) -->
                    <div class="d-block d-lg-none">
                        @forelse($carouselImages as $index => $image)
                        <div class="card bg-light-dark border-gold mb-3 mobile-card animate-scale">
                            <div class="card-body p-3">
                                <!-- Mobile Card Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title text-gold mb-1 fw-semibold">
                                            Carousel Image #{{ $carouselImages->firstItem() + $index }}
                                        </h6>
                                        <p class="card-text text-light-muted mb-0 fs-7">
                                            {{ $image->createdDate ? \Carbon\Carbon::parse($image->createdDate)->format('M d, Y H:i') : 'Date not available' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Mobile Image Preview -->
                                <div class="mb-3 text-center">
                                    @if($image->imgPath)
                                        <img src="{{ asset($image->imgPath) }}" 
                                             alt="Carousel Image" 
                                             class="img-fluid rounded border border-gold mobile-carousel-img"
                                             onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-image fa-2x\'></i><br>Image Error</div>'">
                                    @else
                                        <div class="no-image-placeholder">
                                            <i class="fas fa-image fa-2x"></i>
                                            <div>No Image Available</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Mobile Action Buttons -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.homepageads.edit', $image) }}" 
                                       class="btn btn-warning-custom btn-sm flex-fill">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger-custom btn-sm flex-fill delete-btn"
                                            data-image-id="{{ $image->id }}"
                                            data-image-title="Carousel Image #{{ $carouselImages->firstItem() + $index }}">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state-mobile text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-images text-gold" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                            <h5 class="text-light-muted mb-2">No Carousel Images Found</h5>
                            <p class="text-muted mb-3">Get started by uploading your first carousel image.</p>
                            <a href="{{ route('admin.homepageads.create') }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Upload First Image
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Desktop Table View (Hidden on Mobile) - IMAGE DETAILS COLUMN REMOVED -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-bordered">
                                <thead>
                                    <tr class="table-header-gold">
                                        <th class="text-center" style="width: 8%;">
                                            <i class="fas fa-sort-numeric-down me-1"></i>Slot
                                        </th>
                                        <th class="text-center" style="width: 12%;">
                                            <i class="fas fa-film me-1"></i>Type
                                        </th>
                                        <th class="text-center" style="width: 50%;">
                                            <i class="fas fa-image me-1"></i>Preview
                                        </th>
                                        <th class="text-center" style="width: 30%;">
                                            <i class="fas fa-cogs me-1"></i>Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($carouselImages as $index => $image)
                                    <tr class="table-row-hover">
                                        <td class="text-center fw-medium text-gold">
                                            {{ $image->slotOrder ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if(($image->mediaType ?? 'image') === 'video')
                                                <span class="badge bg-info-custom"><i class="fas fa-video me-1"></i>Video</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-image me-1"></i>Image</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($image->imgPath)
                                                @if(($image->mediaType ?? 'image') === 'video')
                                                    <video class="desktop-carousel-img rounded border border-gold" muted style="width:120px;height:80px;object-fit:cover;">
                                                        <source src="{{ asset($image->imgPath) }}" type="video/mp4">
                                                    </video>
                                                @else
                                                    <img src="{{ asset($image->imgPath) }}"
                                                         alt="Ad Slot"
                                                         class="desktop-carousel-img rounded border border-gold"
                                                         onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-exclamation-triangle\'></i><br>Error</div>'">
                                                @endif
                                            @else
                                                <div class="no-image-placeholder">
                                                    <i class="fas fa-image fa-2x"></i>
                                                    <div>No Media</div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.homepageads.edit', $image) }}"
                                                   class="btn btn-warning-custom btn-sm animate-scale">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                                <button type="button"
                                                        class="btn btn-danger-custom btn-sm animate-scale delete-btn"
                                                        data-image-id="{{ $image->id }}"
                                                        data-image-title="Ad Slot #{{ $image->slotOrder ?? $index+1 }}">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="empty-state-desktop">
                                                <i class="fas fa-images text-gold mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                                                <h4 class="text-light-muted mb-2">No Carousel Images Found</h4>
                                                <p class="text-muted mb-3">Get started by uploading your first carousel image to showcase on your homepage.</p>
                                                <a href="{{ route('admin.homepageads.create') }}" 
                                                   class="btn btn-primary btn-lg">
                                                    <i class="fas fa-plus me-2"></i>Upload Your First Image
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
                    @if($carouselImages->hasPages())
                    <div class="d-flex justify-content-center mt-3 mt-md-4">
                        <div class="pagination-wrapper">
                            {{ $carouselImages->links() }}
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
                <i class="fas fa-image"></i>
            </div>
            <div class="delete-modal-text">
                Are you sure you want to delete this carousel image?
            </div>
            <div class="delete-modal-image" id="imageToDelete">
                Image Title Will Appear Here
            </div>
            <div class="delete-modal-subtext">
                This action cannot be undone. The image will be permanently removed from your carousel and cannot be recovered.
            </div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete Image
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

    /* Carousel Image Styling */
    .mobile-carousel-img {
        max-width: 100%;
        height: 150px;
        object-fit: cover;
        border: 2px solid #ffd700 !important;
    }

    .desktop-carousel-img {
        width: 120px;
        height: 80px;
        object-fit: cover;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .desktop-carousel-img:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
    }

    .no-image-placeholder {
        background: rgba(255, 215, 0, 0.1);
        border: 2px dashed #ffd700;
        border-radius: 8px;
        padding: 30px 20px;
        color: #ffd700;
        font-size: 14px;
        text-align: center;
        min-height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Statistics Info */
    .stats-info {
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid #ffd700;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .stats-item {
        text-align: center;
        color: #ffd700;
    }

    .stats-number {
        font-size: 24px;
        font-weight: bold;
        color: #ffd700;
    }

    .stats-label {
        font-size: 12px;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Custom Badge Styling */
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

    .delete-modal-image {
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
        
        .mobile-carousel-img {
            height: 120px;
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
        
        .mobile-carousel-img {
            height: 140px;
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
        
        .desktop-carousel-img:hover {
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
    let currentImageId = null;

    // Add click event to all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const imageId = this.getAttribute('data-image-id');
            const imageTitle = this.getAttribute('data-image-title');
            
            currentImageId = imageId;
            showDeleteModal(imageTitle);
        });
    });

    // Confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentImageId) {
            // Show processing state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            this.classList.add('processing');
            this.disabled = true;
            
            // Set up and submit the form
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `{{ route('admin.homepageads.index') }}/${currentImageId}`;
            
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

    // Image error handling
    const images = document.querySelectorAll('.mobile-carousel-img, .desktop-carousel-img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.parentElement.innerHTML = '<div class="no-image-placeholder"><i class="fas fa-exclamation-triangle"></i><br>Image Error</div>';
        });
    });
});

function showDeleteModal(imageTitle) {
    document.getElementById('imageToDelete').textContent = imageTitle;
    document.getElementById('deleteModal').classList.add('show');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    document.body.style.overflow = ''; // Restore scrolling
    
    // Reset confirm button
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Image';
    confirmBtn.classList.remove('processing');
    confirmBtn.disabled = false;
    
    currentImageId = null;
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
