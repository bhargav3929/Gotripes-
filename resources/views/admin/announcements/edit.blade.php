@extends('layouts.admin')

@section('title', 'Edit Announcement')

@section('page-title', 'Edit Announcement')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Main Card -->
            <div class="card shadow-lg border-0 animate-fade-in">
                <!-- Mobile-First Card Header -->
                <div class="card-header bg-gold border-bottom-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                        <h3 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-edit me-2 d-none d-sm-inline"></i>
                            <span class="fs-6 fs-sm-5 fs-md-4">Edit Announcement</span>
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.announcements.index') }}" 
                               class="btn btn-outline-dark btn-sm btn-mobile animate-scale">
                                <i class="fas fa-arrow-left me-1"></i> 
                                <span class="d-none d-sm-inline">Back to List</span>
                                <span class="d-sm-none">Back</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Responsive Form -->
                <form method="POST" action="{{ route('admin.announcements.update', $announcement->id) }}" enctype="multipart/form-data" id="announcementForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        <div class="row g-2 g-sm-3 g-md-4">
                            
                            <!-- Main Content - Full width on mobile, 8/12 on desktop -->
                            <div class="col-12 col-lg-8">
                                <!-- Title Field -->
                                <div class="mb-3 mb-md-4">
                                    <label for="description" class="form-label fw-semibold text-gold d-flex align-items-center">
                                        <i class="fas fa-heading me-2 d-none d-sm-inline"></i>
                                        <span class="fs-6 fs-md-5">Announcement Title</span>
                                        <span class="text-danger ms-1">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-mobile @error('description') is-invalid @enderror" 
                                           id="description" 
                                           name="description" 
                                           value="{{ old('description', $announcement->description) }}" 
                                           placeholder="Enter announcement title..."
                                           required
                                           maxlength="255">
                                    @error('description')
                                        <div class="invalid-feedback d-flex align-items-center">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text text-light-muted">
                                        <small><i class="fas fa-info-circle me-1"></i>Maximum 255 characters</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Column - Full width on mobile, 4/12 on desktop -->
                            <div class="col-12 col-lg-4">
                                <!-- Mobile Settings Section -->
                                <div class="row g-2 g-sm-3">
                                    
                                    <!-- Importance Toggle Card -->
                                    <div class="col-12 col-sm-6 col-lg-12">
                                        <div class="card bg-light-dark border-gold mb-2 mb-lg-3">
                                            <div class="card-body p-2 p-sm-3">
                                                <h6 class="card-title text-gold fw-semibold mb-2 mb-sm-3 fs-6">
                                                    <i class="fas fa-star me-2"></i>Settings
                                                </h6>
                                                
                                                <!-- Mobile-Friendly Switch -->
                                                <div class="form-check form-switch mb-2 mb-sm-3">
                                                    <input class="form-check-input mobile-switch" 
                                                           type="checkbox" 
                                                           role="switch"
                                                           id="announcementImportance" 
                                                           name="announcementImportance" 
                                                           value="1"
                                                           {{ old('announcementImportance', $announcement->AnnouncementImportance) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-medium text-white ms-2 fs-7 fs-sm-6" for="announcementImportance">
                                                        <span class="badge {{ $announcement->AnnouncementImportance ? 'bg-success' : 'bg-warning' }} text-{{ $announcement->AnnouncementImportance ? 'white' : 'dark' }} me-1 me-sm-2">
                                                            <i class="fas fa-{{ $announcement->AnnouncementImportance ? 'star' : 'badge-check' }}"></i>
                                                        </span>
                                                        <span class="d-none d-sm-inline">Mark as <strong>{{ $announcement->AnnouncementImportance ? 'New ✓' : 'New' }}</strong></span>
                                                        <span class="d-sm-none"><strong>{{ $announcement->AnnouncementImportance ? 'New ✓' : 'New' }}</strong></span>
                                                    </label>
                                                </div>
                                                
                                                <div class="alert alert-info-custom border-0 p-2 mb-0">
                                                    <small class="text-light-info fs-8 fs-sm-7">
                                                        <i class="fas fa-lightbulb me-1"></i>
                                                        <span class="d-none d-sm-inline"><strong>ON</strong> = New Badge (1) | <strong>OFF</strong> = Normal (0)</span>
                                                        <span class="d-sm-none">Toggle for New badge</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SVG File Upload Card -->
                                    <div class="col-12 col-sm-6 col-lg-12">
                                        <div class="card bg-light-dark border-gold mb-2 mb-lg-0">
                                            <div class="card-body p-2 p-sm-3">
                                                <h6 class="card-title text-gold fw-semibold mb-2 mb-sm-3 fs-6">
                                                    <i class="fas fa-flag me-2"></i>
                                                    <span class="d-none d-sm-inline">Country Flag (SVG Only)</span>
                                                    <span class="d-sm-none">Flag (SVG)</span>
                                                </h6>

                                                <!-- Current SVG Display -->
                                                @if($announcement->flagImgPath)
                                                <div class="current-flag mb-2 mb-sm-3">
                                                    <label class="form-label fw-medium text-white fs-7 fs-sm-6">
                                                        Current Flag:
                                                    </label>
                                                    <div class="border border-gold rounded p-2 bg-dark text-center">
                                                        <div class="current-svg-preview">
                                                            @if(file_exists(public_path($announcement->flagImgPath)))
                                                                {!! file_get_contents(public_path($announcement->flagImgPath)) !!}
                                                            @else
                                                                <div class="text-muted">
                                                                    <i class="fas fa-exclamation-triangle"></i>
                                                                    <br>SVG file not found
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="mt-1">
                                                            <small class="text-gold">Current SVG</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <!-- SVG-Only File Upload -->
                                                <div class="mb-2 mb-sm-3">
                                                    <label for="flagPath" class="form-label fw-medium text-white fs-7 fs-sm-6">
                                                        {{ $announcement->flagImgPath ? 'Replace SVG Flag' : 'Upload SVG Flag' }}
                                                    </label>
                                                    <input type="file" 
                                                           class="form-control form-control-mobile @error('flagPath') is-invalid @enderror" 
                                                           id="flagPath" 
                                                           name="flagPath"
                                                           accept=".svg"
                                                           onchange="previewSVG(this)">
                                                    @error('flagPath')
                                                        <div class="invalid-feedback d-flex align-items-center">
                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <!-- New SVG Preview -->
                                                <div id="svgPreview" class="text-center d-none">
                                                    <div class="border border-warning rounded p-2 bg-dark">
                                                        <div id="previewSvg" class="mobile-preview-svg"></div>
                                                        <div class="mt-1 mt-sm-2">
                                                            <small class="text-warning">New SVG Preview</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- SVG File Info -->
                                                <div class="alert alert-warning-custom border-0 p-2 mb-0">
                                                    <small class="text-light-warning fs-8 fs-sm-7">
                                                        <i class="fas fa-file-code me-1"></i>
                                                        <strong>Format:</strong> SVG only<br class="d-sm-none">
                                                        <span class="d-none d-sm-inline"><strong>Max Size:</strong> 2MB | Leave empty to keep current</span>
                                                        <span class="d-sm-none">Leave empty to keep current</span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile-Responsive Footer -->
                    <div class="card-footer bg-transparent border-top-gold p-2 p-sm-3 p-md-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2">
                            <div class="text-light-muted order-2 order-sm-1 text-center text-sm-start">
                                <small class="fs-8 fs-sm-7">
                                    <i class="fas fa-info-circle me-1"></i>
                                    <span class="d-none d-sm-inline">All required fields must be filled</span>
                                    <span class="d-sm-none">Required fields must be filled</span>
                                </small>
                            </div>
                            <div class="d-flex gap-2 order-1 order-sm-2">
                                <a href="{{ route('admin.announcements.index') }}" 
                                   class="btn btn-outline-secondary flex-fill flex-sm-grow-0 animate-scale">
                                    <i class="fas fa-times me-1"></i> 
                                    <span class="d-none d-sm-inline">Cancel</span>
                                    <span class="d-sm-none">Cancel</span>
                                </a>
                                <button type="submit" class="btn btn-primary flex-fill flex-sm-grow-0 animate-scale" id="submitBtn">
                                    <i class="fas fa-save me-1 me-sm-2"></i> 
                                    <span class="d-none d-sm-inline">Update Announcement</span>
                                    <span class="d-sm-none">Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Mobile-First Responsive Styles -->
<style>
    /* Custom Font Size Classes */
    .fs-8 { font-size: 0.7rem !important; }
    .fs-7 { font-size: 0.8rem !important; }
    .fs-6 { font-size: 0.9rem !important; }

    /* Mobile-First Text Visibility */
    .text-light-muted {
        color: #e9ecef !important;
    }
    
    .text-light-info {
        color: #b8daff !important;
    }
    
    .text-light-warning {
        color: #fff3cd !important;
    }

    /* Mobile-Optimized Form Controls */
    .form-control-mobile {
        background-color: rgba(45, 45, 45, 0.9) !important;
        border: 2px solid rgba(255, 215, 0, 0.3) !important;
        color: #ffffff !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
        transition: all 0.3s ease;
        min-height: 44px; /* Touch-friendly height */
    }
    
    .form-control-mobile:focus {
        background-color: rgba(45, 45, 45, 1) !important;
        border-color: var(--primary-gold) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25) !important;
        color: #ffffff !important;
    }

    /* Mobile Switch Styling */
    .mobile-switch {
        min-width: 3rem !important;
        min-height: 1.5rem !important;
        background-color: rgba(45, 45, 45, 0.8) !important;
        border: 2px solid rgba(255, 215, 0, 0.5) !important;
    }
    
    .mobile-switch:checked {
        background-color: var(--primary-gold) !important;
        border-color: var(--primary-gold) !important;
    }

    /* Mobile Button Styling */
    .btn-mobile {
        min-height: 44px !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.875rem !important;
    }

    /* Current SVG Preview */
    .current-svg-preview {
        max-height: 80px;
        max-width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .current-svg-preview svg {
        max-height: 80px;
        max-width: 100%;
        width: auto;
        height: auto;
    }

    /* New SVG Preview */
    .mobile-preview-svg {
        max-height: 80px !important;
        max-width: 100% !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-preview-svg svg {
        max-height: 80px;
        max-width: 100%;
        width: auto;
        height: auto;
    }

    /* Placeholder Visibility */
    .form-control-mobile::placeholder {
        color: #adb5bd !important;
        opacity: 1;
    }

    /* Card Responsive Styling */
    .bg-light-dark {
        background-color: rgba(45, 45, 45, 0.95) !important;
        border: 1px solid rgba(255, 215, 0, 0.3) !important;
    }
    
    .border-top-gold {
        border-top: 2px solid var(--primary-gold) !important;
    }
    
    .border-gold {
        border-color: rgba(255, 215, 0, 0.6) !important;
    }

    /* Alert Responsive Styling */
    .alert-info-custom {
        background-color: rgba(13, 110, 253, 0.2) !important;
        border: 1px solid rgba(13, 110, 253, 0.4) !important;
        border-radius: 0.375rem;
    }
    
    .alert-warning-custom {
        background-color: rgba(255, 193, 7, 0.2) !important;
        border: 1px solid rgba(255, 193, 7, 0.4) !important;
        border-radius: 0.375rem;
    }

    /* Form Labels Mobile */
    .form-label {
        color: var(--primary-gold) !important;
        font-weight: 600 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .form-check-label {
        color: #ffffff !important;
        cursor: pointer !important;
    }

    /* Button Responsive Styling */
    .btn-outline-dark {
        border-color: #495057 !important;
        color: #495057 !important;
        background-color: transparent !important;
        min-height: 44px !important;
    }
    
    .btn-outline-dark:hover {
        background-color: #495057 !important;
        border-color: #495057 !important;
        color: var(--primary-gold) !important;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d !important;
        color: #ffffff !important;
        background-color: transparent !important;
        min-height: 44px !important;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #ffffff !important;
    }

    /* File Input Mobile Styling */
    input[type="file"] {
        padding: 0.6rem 0.75rem !important;
        font-size: 16px !important; /* Prevents zoom on iOS */
        min-height: 44px !important;
    }
    
    input[type="file"]::-webkit-file-upload-button {
        background: var(--gradient-primary);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        color: var(--darker-bg);
        font-weight: 500;
        cursor: pointer;
        margin-right: 0.75rem;
        min-height: 32px;
    }

    /* Invalid Feedback Mobile */
    .invalid-feedback {
        color: #f5c6cb !important;
        background-color: rgba(220, 53, 69, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border-left: 3px solid #dc3545;
        font-size: 0.875rem;
    }

    /* Current Flag Styling */
    .current-flag {
        background: rgba(255, 215, 0, 0.05);
        border-radius: 8px;
        padding: 8px;
    }

    /* BREAKPOINT: Extra Small (Phone) */
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .card {
            margin: 0.5rem 0;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem 1rem !important;
        }
        
        .card-footer {
            padding: 0.75rem 1rem !important;
        }
        
        .btn {
            font-size: 0.875rem !important;
            padding: 0.5rem 0.75rem !important;
        }
        
        h3 {
            font-size: 1.1rem !important;
        }
        
        .form-control-mobile {
            font-size: 16px !important;
            padding: 0.6rem 0.75rem !important;
        }
        
        .mobile-preview-svg, .current-svg-preview {
            max-height: 60px !important;
        }
        
        .mobile-preview-svg svg, .current-svg-preview svg {
            max-height: 60px !important;
        }
    }

    /* BREAKPOINT: Small (Small Phone Landscape / Large Phone) */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .fs-sm-7 { font-size: 0.8rem !important; }
        .fs-sm-6 { font-size: 0.9rem !important; }
        .fs-sm-5 { font-size: 1rem !important; }
        
        .mobile-preview-svg, .current-svg-preview {
            max-height: 90px !important;
        }
        
        .mobile-preview-svg svg, .current-svg-preview svg {
            max-height: 90px !important;
        }
    }

    /* BREAKPOINT: Medium (Tablet) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .card-body {
            padding: 2rem !important;
        }
        
        .mobile-preview-svg, .current-svg-preview {
            max-height: 100px !important;
        }
        
        .mobile-preview-svg svg, .current-svg-preview svg {
            max-height: 100px !important;
        }
    }

    /* BREAKPOINT: Large (Desktop) */
    @media (min-width: 992px) {
        .fs-md-5 { font-size: 1rem !important; }
        .fs-md-4 { font-size: 1.25rem !important; }
        
        .mobile-preview-svg, .current-svg-preview {
            max-height: 100px !important;
        }
        
        .mobile-preview-svg svg, .current-svg-preview svg {
            max-height: 100px !important;
        }
        
        .form-control-mobile {
            font-size: 1rem !important;
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
        
        .form-control-mobile {
            min-height: 48px !important;
            font-size: 16px !important;
        }
        
        .mobile-switch {
            min-width: 3.5rem !important;
            min-height: 2rem !important;
        }
        
        .animate-scale:hover {
            transform: none !important;
        }
    }

    /* High DPI Displays */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .mobile-preview-svg, .current-svg-preview {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
    }

    /* Dark Mode Safe Colors */
    @media (prefers-color-scheme: dark) {
        .form-control-mobile::placeholder {
            color: #ced4da !important;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .animate-fade-in,
        .animate-scale,
        .card,
        .mobile-preview-svg,
        .current-svg-preview {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Landscape Mobile Adjustments */
    @media (max-height: 500px) and (orientation: landscape) {
        .card-body {
            padding: 1rem !important;
        }
        
        .mb-3, .mb-md-4 {
            margin-bottom: 0.75rem !important;
        }
    }
</style>

<!-- Enhanced Mobile-Responsive JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('announcementForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Mobile-optimized character count
    const titleInput = document.getElementById('description');
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            const remaining = 255 - this.value.length;
            const helpText = this.parentNode.querySelector('.form-text small');
            if (helpText) {
                const isMobile = window.innerWidth < 576;
                if (remaining < 20) {
                    helpText.innerHTML = `<i class="fas fa-exclamation-triangle me-1 text-warning"></i>Remaining: <strong class="text-warning">${remaining}</strong>`;
                } else if (remaining < 50) {
                    helpText.innerHTML = `<i class="fas fa-info-circle me-1 text-info"></i>${isMobile ? 'Left' : 'Characters remaining'}: <strong class="text-info">${remaining}</strong>`;
                } else {
                    helpText.innerHTML = `<i class="fas fa-info-circle me-1"></i>${isMobile ? 'Left' : 'Characters remaining'}: <strong>${remaining}</strong>`;
                }
            }
        });
    }
    
    // Mobile-friendly form submission
    form.addEventListener('submit', function(e) {
        const isMobile = window.innerWidth < 576;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span>${isMobile ? 'Updating...' : 'Updating...'}`;
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.innerHTML = `<i class="fas fa-save me-1 me-sm-2"></i> ${isMobile ? 'Update' : 'Update Announcement'}`;
            submitBtn.disabled = false;
        }, 10000);
    });
    
    // Mobile-optimized toggle feedback
    const importanceToggle = document.getElementById('announcementImportance');
    const toggleLabel = document.querySelector('label[for="announcementImportance"]');
    
    importanceToggle.addEventListener('change', function() {
        const isMobile = window.innerWidth < 576;
        if (this.checked) {
            toggleLabel.innerHTML = `
                <span class="badge bg-success text-white me-1 me-sm-2">
                    <i class="fas fa-star"></i>
                </span>
                ${isMobile ? '<strong class="text-success">New ✓</strong>' : 'Mark as <strong class="text-success">New ✓</strong>'}
            `;
        } else {
            toggleLabel.innerHTML = `
                <span class="badge bg-warning text-dark me-1 me-sm-2">
                    <i class="fas fa-badge-check"></i>
                </span>
                ${isMobile ? '<strong>New</strong>' : 'Mark as <strong>New</strong>'}
            `;
        }
    });
    
    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            // Trigger resize-dependent updates
            if (titleInput) {
                titleInput.dispatchEvent(new Event('input'));
            }
        }, 100);
    });
});

// Enhanced SVG preview function for edit form
function previewSVG(input) {
    const preview = document.getElementById('svgPreview');
    const previewSvg = document.getElementById('previewSvg');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const isMobile = window.innerWidth < 768;
        
        // SVG file type validation
        if (file.type !== 'image/svg+xml') {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-2';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Invalid file type!</strong> 
                ${isMobile ? 'Only SVG files allowed.' : 'Please select SVG files only. Selected file is not an SVG.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            input.parentNode.appendChild(alertDiv);
            input.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // File size validation (2MB)
        const fileSize = file.size / 1024 / 1024;
        if (fileSize > 2) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-2';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>File too large!</strong> 
                ${isMobile ? `Max 2MB (${fileSize.toFixed(1)}MB selected)` : `Maximum size is 2MB. Your file is ${fileSize.toFixed(2)}MB.`}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            input.parentNode.appendChild(alertDiv);
            input.value = '';
            preview.classList.add('d-none');
            return;
        }
        
        // Remove existing alerts
        const existingAlerts = input.parentNode.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewSvg.innerHTML = e.target.result;
            preview.classList.remove('d-none');
            
            // Mobile-optimized animation
            preview.style.opacity = '0';
            setTimeout(() => {
                preview.style.transition = 'opacity 0.3s ease';
                preview.style.opacity = '1';
            }, 100);
        }
        
        reader.readAsText(file);
    } else {
        preview.classList.add('d-none');
    }
}
</script>
@endsection
