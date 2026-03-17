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
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-edit me-2"></i>Edit Announcement
                        </h3>
                        <a href="{{ route('admin.announcements.index') }}"
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
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

                            <!-- Tag Type Field -->
                            <div class="col-12 col-lg-8">
                                <div class="mb-3 mb-md-4">
                                    <label for="tagType" class="form-label fw-semibold text-gold d-flex align-items-center">
                                        <i class="fas fa-tag me-2 d-none d-sm-inline"></i>
                                        <span class="fs-6 fs-md-5">News Ticker Tag</span>
                                    </label>
                                    <select class="form-control form-control-mobile" id="tagType" name="tagType">
                                        <option value="none" {{ old('tagType', $announcement->tagType) == 'none' ? 'selected' : '' }}>No Tag</option>
                                        <option value="breaking" {{ old('tagType', $announcement->tagType) == 'breaking' ? 'selected' : '' }}>BREAKING (Red)</option>
                                        <option value="trending" {{ old('tagType', $announcement->tagType) == 'trending' ? 'selected' : '' }}>TRENDING (Gold)</option>
                                        <option value="exclusive" {{ old('tagType', $announcement->tagType) == 'exclusive' ? 'selected' : '' }}>EXCLUSIVE (Green)</option>
                                        <option value="alert" {{ old('tagType', $announcement->tagType) == 'alert' ? 'selected' : '' }}>NEW / ALERT (Blue)</option>
                                        <option value="hot" {{ old('tagType', $announcement->tagType) == 'hot' ? 'selected' : '' }}>HOT (Yellow)</option>
                                    </select>
                                    <div class="form-text text-light-muted">
                                        <small><i class="fas fa-info-circle me-1"></i>Select a colored tag to display in the homepage news ticker</small>
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
                                   class="btn btn-outline-primary flex-fill flex-sm-grow-0 animate-scale">
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

<!-- Page-Specific Styles -->
<style>
    /* Announcement edit form helpers */
    .text-light-info { color: #93c5fd !important; }
    .text-light-warning { color: #fde68a !important; }
    .border-top-gold { border-top: 1px solid var(--border-gold) !important; }

    /* Mobile Switch */
    .mobile-switch {
        min-width: 3rem !important;
        min-height: 1.5rem !important;
        background-color: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid var(--border-color) !important;
    }
    .mobile-switch:checked {
        background-color: var(--accent-gold) !important;
        border-color: var(--accent-gold) !important;
    }

    /* Alert variants for form hints */
    .alert-info-custom {
        background: rgba(59, 130, 246, 0.08) !important;
        border: 1px solid rgba(59, 130, 246, 0.15) !important;
        border-radius: var(--radius-sm);
    }
    .alert-warning-custom {
        background: rgba(245, 158, 11, 0.08) !important;
        border: 1px solid rgba(245, 158, 11, 0.15) !important;
        border-radius: var(--radius-sm);
    }

    /* Current SVG Preview */
    .current-svg-preview {
        max-height: 80px;
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
        max-height: 80px;
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

    /* Current Flag Styling */
    .current-flag {
        background: rgba(255, 215, 0, 0.04);
        border-radius: var(--radius-sm);
        padding: 8px;
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
