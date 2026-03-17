@extends('layouts.admin')

@section('title', 'Create UAE Activity')

@section('content')

@php
    // Get user session info and emirates filtering logic
    $user = Auth::user();
    $userType = session('user_type', 'unknown');
    $isPartnerRestricted = session('is_partner_restricted', false);
    $isApprovedPartner = ($userType === 'approved_partner' && $isPartnerRestricted === true);
    
    // Get emirates based on user type
    if ($isApprovedPartner && $user->email_verified_at) {
        // For approved partners, parse their email_verified_at to get allowed emirates
        $parts = explode('rseparator', $user->email_verified_at, 3);
        $allowedEmiratesIds = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
        
        // Get emirates that match partner's allowed emirates
        $emirates = \App\Models\Emirates::where('isActive', 1)
                    ->whereIn('emiratesID', $allowedEmiratesIds)
                    ->orderBy('emiratesName')
                    ->get();
    } else {
        // For admin users, show all active emirates
        $emirates = \App\Models\Emirates::where('isActive', 1)
                    ->orderBy('emiratesName')
                    ->get();
    }
@endphp

<!-- Page-Specific Styles (Create Activity) -->
<style>
    /* Card wrapper using layout tokens */
    .card-dark-gold {
        background: var(--card-bg);
        border: 1px solid var(--border-gold);
        border-radius: var(--radius-lg);
    }
    .card-dark-gold .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
    }
    .card-dark-gold .card-header .card-title {
        color: var(--text-main);
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: -0.02em;
    }
    .card-dark-gold .card-header .card-title i {
        color: var(--primary-gold);
        opacity: 0.8;
    }

    /* Form controls */
    .form-control-dark {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        border-radius: var(--radius-sm);
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-control-dark:focus {
        background: rgba(255, 255, 255, 0.06);
        border-color: var(--primary-gold);
        color: var(--text-main);
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
    }
    .form-control-dark::placeholder { color: var(--text-muted); opacity: 0.6; }
    .form-control-dark option { background: var(--card-bg); color: var(--text-main); }

    .form-label-gold { color: var(--primary-gold); font-weight: 600; margin-bottom: 0.375rem; font-size: 0.8125rem; }

    /* Buttons */
    .btn-gold { background: var(--primary-gold); color: #000; border: none; font-weight: 700; transition: all 0.15s; }
    .btn-gold:hover { background: #ffe44d; color: #000; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(255, 215, 0, 0.25); }

    /* Section Dividers */
    .section-divider { margin: 2rem 0 1.25rem; }
    .section-title { color: var(--primary-gold); font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color); }

    /* Dynamic Input Groups */
    .dynamic-input-group { border: 1px solid var(--border-gold); border-radius: var(--radius-sm); padding: 0.625rem; margin-bottom: 0.625rem; background: rgba(255, 215, 0, 0.04); }
    .btn-remove { background: rgba(255, 255, 255, 0.04); color: var(--text-muted); border: 1px solid rgba(255, 255, 255, 0.08); padding: 5px 10px; border-radius: var(--radius-sm); transition: all 0.15s; font-size: 0.8125rem; }
    .btn-remove:hover { background: var(--danger); color: #fff; border-color: var(--danger); }
    .btn-add { background: transparent; color: var(--primary-gold); border: 1px dashed rgba(255, 215, 0, 0.25); padding: 0.375rem 0.875rem; border-radius: var(--radius-sm); margin-top: 0.625rem; transition: all 0.15s; font-size: 0.75rem; font-weight: 600; }
    .btn-add:hover { background: rgba(255, 215, 0, 0.06); border-style: solid; border-color: rgba(255, 215, 0, 0.4); }

    /* Partner Info */
    .partner-filter-info { background: rgba(59, 130, 246, 0.06) !important; border: 1px solid rgba(59, 130, 246, 0.15) !important; border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.25rem; color: var(--info) !important; }
    .partner-filter-icon { color: var(--info) !important; font-size: 1.125rem; margin-right: 0.625rem; }

    /* Image Upload */
    .image-upload-area { border: 2px dashed var(--border-gold); border-radius: var(--radius-sm); padding: 1.25rem; text-align: center; background: rgba(255, 215, 0, 0.03); transition: all 0.15s; cursor: pointer; }
    .image-upload-area:hover { background: rgba(255, 215, 0, 0.06); border-color: var(--primary-gold); }
    .image-upload-area.dragover { background: rgba(255, 215, 0, 0.08); border-color: var(--primary-gold); }
    .image-preview-container { background: rgba(255, 215, 0, 0.03); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); padding: 1rem; margin-top: 1rem; display: none; }
    .image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
    .image-preview-item { position: relative; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-gold); }
    .image-preview-item img { width: 100%; height: 100px; object-fit: cover; display: block; }
    .main-image-badge { position: absolute; top: 5px; left: 5px; background: var(--primary-gold); color: #000; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
    .image-count-info { color: var(--primary-gold); font-size: 0.875rem; margin-top: 0.625rem; }

    @media (max-width: 768px) {
        .image-preview-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-dark-gold">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-plus-circle"></i> Create New UAE Activity
                        @if($isApprovedPartner)
                            <small class="text-dark ms-2">(Partner: {{ $user->name }})</small>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.uaeactivities.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.uaeactivities.store') }}" enctype="multipart/form-data" id="activityForm">
                    @csrf
                    <div class="card-body">
                        
                        <!-- Partner Filter Information -->
                        {{-- @if($isApprovedPartner)
                        <div class="partner-filter-info">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle partner-filter-icon"></i>
                                <div>
                                    <strong>Partner Creation Mode:</strong> You are creating an activity as an approved partner.
                                    <br><small>Emirates selection is limited to your approved regions: 
                                    @if($emirates->count() > 0)
                                        {{ $emirates->pluck('emiratesName')->join(', ') }}
                                    @else
                                        <span class="text-warning">No emirates assigned to your account</span>
                                    @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif --}}

                        @if($isApprovedPartner && $emirates->count() == 0)
                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> No emirates are assigned to your partner account. Please contact the administrator to assign emirates before creating activities.
                        </div>
                        @endif

                        <!-- Basic Activity Information -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="activityName" class="form-label form-label-gold">
                                        <i class="fas fa-tag"></i> Activity Name *
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-dark @error('activityName') is-invalid @enderror" 
                                           id="activityName" 
                                           name="activityName" 
                                           value="{{ old('activityName') }}" 
                                           placeholder="Enter activity name"
                                           required>
                                    @error('activityName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="activityLocation" class="form-label form-label-gold">
                                        <i class="fas fa-map-marker-alt"></i> Activity Location *
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-dark @error('activityLocation') is-invalid @enderror" 
                                           id="activityLocation" 
                                           name="activityLocation" 
                                           value="{{ old('activityLocation') }}" 
                                           placeholder="Enter location (e.g., Dubai, Abu Dhabi)"
                                           required>
                                    @error('activityLocation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- UPDATED: Emirates Dropdown with Partner Filtering -->
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="emiratesID" class="form-label form-label-gold">
                                        <i class="fas fa-flag"></i> Emirates * 
                                        @if($isApprovedPartner)
                                            <small class="text-info">(Your Approved Emirates)</small>
                                        @endif
                                    </label>
                                    <select class="form-control form-control-dark @error('emiratesID') is-invalid @enderror" 
                                            id="emiratesID" 
                                            name="emiratesID" 
                                            required 
                                            @if($emirates->count() == 0) disabled @endif>
                                        <option value="" disabled selected>
                                            @if($emirates->count() > 0)
                                                Select Emirates
                                            @else
                                                No Emirates Available
                                            @endif
                                        </option>
                                        @forelse($emirates as $emirate)
                                            <option value="{{ $emirate->emiratesID }}" 
                                                    {{ old('emiratesID') == $emirate->emiratesID ? 'selected' : '' }}>
                                                {{ $emirate->emiratesName }}
                                                @if($isApprovedPartner)
                                                    ✓
                                                @endif
                                            </option>
                                        @empty
                                            <option value="" disabled>No emirates assigned to your account</option>
                                        @endforelse
                                    </select>
                                    @if($isApprovedPartner && $emirates->count() == 0)
                                        <small class="text-warning">Contact administrator to assign emirates to your account.</small>
                                    @endif
                                    @error('emiratesID')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="activityCategory" class="form-label form-label-gold">
                                        <i class="fas fa-layer-group"></i> Activity Category / Type (Optional)
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-dark @error('activityCategory') is-invalid @enderror" 
                                           id="activityCategory" 
                                           name="activityCategory" 
                                           value="{{ old('activityCategory') }}" 
                                           placeholder="e.g., Adventure, Water Sports, Desert Safari, Luxury">
                                    <small class="text-white-50">Used for future filtering and grouping activities.</small>
                                    @error('activityCategory')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="fas fa-dollar-sign me-1"></i> Pricing Information
                            </h6>
                        </div>

                        <!-- Pricing Information -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="activityPrice" class="form-label form-label-gold">
                                        <i class="fas fa-dollar-sign"></i> Activity Price (AED) *
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('activityPrice') is-invalid @enderror" 
                                           id="activityPrice" 
                                           name="activityPrice" 
                                           value="{{ old('activityPrice') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0" 
                                           required>
                                    @error('activityPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="activityChildPrice" class="form-label form-label-gold">
                                        <i class="fas fa-child"></i> Children Price (AED)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('activityChildPrice') is-invalid @enderror" 
                                           id="activityChildPrice" 
                                           name="activityChildPrice" 
                                           value="{{ old('activityChildPrice') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0">
                                    @error('activityChildPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="activityTransactionCharges" class="form-label form-label-gold">
                                        <i class="fas fa-credit-card"></i> Transaction Charges (AED)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('activityTransactionCharges') is-invalid @enderror" 
                                           id="activityTransactionCharges" 
                                           name="activityTransactionCharges" 
                                           value="{{ old('activityTransactionCharges') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0">
                                    @error('activityTransactionCharges')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Transport Pricing Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="dubaiPrice" class="form-label form-label-gold">
                                        <i class="fas fa-bus"></i> Transport Price from Dubai (AED)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('dubaiPrice') is-invalid @enderror" 
                                           id="dubaiPrice" 
                                           name="dubaiPrice" 
                                           value="{{ old('dubaiPrice') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0">
                                    @error('dubaiPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="abuDhabiPrice" class="form-label form-label-gold">
                                        <i class="fas fa-bus"></i> Transport Price from Abu Dhabi (AED)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('abuDhabiPrice') is-invalid @enderror" 
                                           id="abuDhabiPrice" 
                                           name="abuDhabiPrice" 
                                           value="{{ old('abuDhabiPrice') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0">
                                    @error('abuDhabiPrice')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Additional Transport Options -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fromAbuDhabiToDubai" class="form-label form-label-gold">
                                        <i class="fas fa-exchange-alt"></i> Abu Dhabi ⇄ Dubai Transport Price (AED)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('fromAbuDhabiToDubai') is-invalid @enderror" 
                                           id="fromAbuDhabiToDubai" 
                                           name="fromAbuDhabiToDubai" 
                                           value="{{ old('fromAbuDhabiToDubai') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0">
                                    <small class="text-muted">Transport between Abu Dhabi and Dubai (both directions)</small>
                                    @error('fromAbuDhabiToDubai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="emirates" class="form-label form-label-gold">
                                        <i class="fas fa-map"></i> Any Emirates Transport Price (AED)
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-dark @error('emirates') is-invalid @enderror" 
                                           id="emirates" 
                                           name="emirates" 
                                           value="{{ old('emirates') }}" 
                                           placeholder="0.00"
                                           step="0.01" 
                                           min="0">
                                    <small class="text-muted">Transport from any Emirates location</small>
                                    @error('emirates')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="fas fa-truck me-1"></i> Supplier Information
                            </h6>
                        </div>

                        <!-- Supplier Information (Optional) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="supplierName" class="form-label form-label-gold">
                                        <i class="fas fa-user-tie"></i> Supplier / Tour Operator Name
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-dark @error('supplierName') is-invalid @enderror" 
                                           id="supplierName" 
                                           name="supplierName" 
                                           value="{{ old('supplierName') }}" 
                                           placeholder="e.g., Desert Safari Tours LLC">
                                    <small class="text-muted">Optional — the supplier will receive booking notifications</small>
                                    @error('supplierName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="supplierEmail" class="form-label form-label-gold">
                                        <i class="fas fa-envelope"></i> Supplier Email
                                    </label>
                                    <input type="email" 
                                           class="form-control form-control-dark @error('supplierEmail') is-invalid @enderror" 
                                           id="supplierEmail" 
                                           name="supplierEmail" 
                                           value="{{ old('supplierEmail') }}" 
                                           placeholder="supplier@example.com">
                                    <small class="text-muted">Optional — booking confirmations will be sent to this email</small>
                                    @error('supplierEmail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="fas fa-images me-1"></i> Activity Images
                            </h6>
                        </div>

                        <!-- Enhanced Image Upload -->
                        <div class="form-group mb-4">
                            <label for="activityImageFiles" class="form-label form-label-gold">
                                <i class="fas fa-images"></i> Activity Images *
                            </label>
                            
                            <div class="image-upload-area" onclick="document.getElementById('activityImageFiles').click()">
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #ffd700;"></i>
                                    <h5 class="text-light">Click to select images or drag & drop</h5>
                                    <p class="text-muted mb-0">
                                        Select multiple images (JPEG, PNG, JPG, GIF, WebP)<br>
                                        Max size: 5MB each | First image will be the main image
                                    </p>
                                </div>
                            </div>
                            
                            <input type="file" 
                                   class="d-none @error('activityImageFiles') is-invalid @enderror" 
                                   id="activityImageFiles" 
                                   name="activityImageFiles[]" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                                   multiple 
                                   required>
                            
                            @error('activityImageFiles')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <!-- Image Preview Container -->
                            <div class="image-preview-container" id="imagePreview">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="form-label-gold mb-0">
                                        <i class="fas fa-eye"></i> Image Preview
                                    </h6>
                                    <div class="image-count-info" id="imageCount"></div>
                                </div>
                                <div class="image-preview-grid" id="previewContainer"></div>
                            </div>
                        </div>

                        <div class="section-divider">
                            <h6 class="section-title">
                                <i class="fas fa-info-circle me-1"></i> Activity Details
                            </h6>
                        </div>

                        <!-- Activity Details -->
                       <div class="form-group mb-3">
    <label for="detailsOverview" class="form-label form-label-gold">
        <i class="fas fa-eye"></i> Activity Overview *
    </label>
    
    <!-- Quill Editor with Dark Theme Styling -->
    <div id="quill-editor" style="height: 200px;"></div>
    
    <!-- Hidden textarea for form submission -->
    <textarea 
        class="form-control d-none @error('detailsOverview') is-invalid @enderror" 
        id="detailsOverview" 
        name="detailsOverview"
    >{{ old('detailsOverview') }}</textarea>
    
    @error('detailsOverview')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<!-- Quill CSS and JS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<!-- Dark Theme Custom Styles -->
<style>
    #quill-editor {
        background-color: #2d2d2d;
        color: #e0e0e0;
        border: 1px solid #444;
        border-radius: 4px;
    }
    .ql-toolbar.ql-snow {
        background-color: #1a1a1a;
        border: 1px solid #444;
        border-bottom: none;
        border-radius: 4px 4px 0 0;
    }
    .ql-container.ql-snow {
        background-color: #2d2d2d;
        border: 1px solid #444;
        border-top: none;
        border-radius: 0 0 4px 4px;
    }
    .ql-editor {
        color: #e0e0e0 !important;
    }
    .ql-editor.ql-blank::before {
        color: #888 !important;
    }
    .ql-toolbar.ql-snow .ql-stroke {
        stroke: #e0e0e0;
    }
    .ql-toolbar.ql-snow .ql-fill {
        fill: #e0e0e0;
    }
    .ql-toolbar.ql-snow .ql-picker-label {
        color: #e0e0e0;
    }
    .ql-toolbar.ql-snow button:hover .ql-stroke,
    .ql-toolbar.ql-snow button.ql-active .ql-stroke {
        stroke: #ffd700;
    }
    .ql-toolbar.ql-snow button:hover .ql-fill,
    .ql-toolbar.ql-snow button.ql-active .ql-fill {
        fill: #ffd700;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill editor
        var quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Provide a detailed overview of the activity...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'header': [1, 2, 3, false] }],
                    ['clean']
                ]
            }
        });

        // Load existing content on page load
        var existingContent = document.getElementById("detailsOverview").value;
        if(existingContent && existingContent.trim() !== ''){
            quill.root.innerHTML = existingContent;
        }

        // Function to sync Quill to textarea
        function syncQuillToTextarea() {
            var html = quill.root.innerHTML;
            // If editor is empty, Quill returns '<p><br></p>'
            if (html === '<p><br></p>' || html.trim() === '') {
                html = '';
            }
            document.getElementById("detailsOverview").value = html;
            console.log('Synced detailsOverview:', html);
            return html;
        }

        // Sync on every text change (real-time sync)
        quill.on('text-change', function() {
            syncQuillToTextarea();
        });

        // Also sync when form is submitted (double safety)
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var content = syncQuillToTextarea();
                console.log('Form submitting with detailsOverview:', content);
            });
        });

        // Sync before page unload (extra safety)
        window.addEventListener('beforeunload', function() {
            syncQuillToTextarea();
        });
    });
</script>


                        <!-- Dynamic Important Information -->
                        <div class="form-group mb-3">
                            <label class="form-label form-label-gold">
                                <i class="fas fa-exclamation-triangle"></i> Important Information *
                            </label>
                            <div id="importantInfoContainer">
                                <div class="dynamic-input-group">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control form-control-dark" 
                                               name="detailsIminfo[]" 
                                               placeholder="Enter important information (e.g., age restrictions, dress code, etc.)" 
                                               required>
                                        <button type="button" class="btn btn-remove" onclick="removeInput(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-add" onclick="addImportantInfo()">
                                <i class="fas fa-plus"></i> Add More Information
                            </button>
                            @error('detailsIminfo')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamic Highlights -->
                        <div class="form-group mb-4">
                            <label class="form-label form-label-gold">
                                <i class="fas fa-star"></i> Activity Highlights *
                            </label>
                            <div id="highlightsContainer">
                                <div class="dynamic-input-group">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control form-control-dark" 
                                               name="detailsHighlights[]" 
                                               placeholder="Enter activity highlight (e.g., breathtaking views, expert guides, etc.)" 
                                               required>
                                        <button type="button" class="btn btn-remove" onclick="removeInput(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-add" onclick="addHighlight()">
                                <i class="fas fa-plus"></i> Add More Highlights
                            </button>
                            @error('detailsHighlights')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-gold btn-lg me-3" @if($emirates->count() == 0) disabled @endif>
                            <i class="fas fa-save"></i> 
                            @if($emirates->count() == 0)
                                Cannot Create Activity
                            @else
                                Create UAE Activity
                            @endif
                        </button>
                        <a href="{{ route('admin.uaeactivities.index') }}" class="btn btn-outline-primary btn-lg">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Dynamic input functions
function addImportantInfo() {
    const container = document.getElementById('importantInfoContainer');
    const newInput = document.createElement('div');
    newInput.className = 'dynamic-input-group';
    newInput.innerHTML = `
        <div class="input-group">
            <input type="text" 
                   class="form-control form-control-dark" 
                   name="detailsIminfo[]" 
                   placeholder="Enter important information" 
                   required>
            <button type="button" class="btn btn-remove" onclick="removeInput(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newInput);
}

function addHighlight() {
    const container = document.getElementById('highlightsContainer');
    const newInput = document.createElement('div');
    newInput.className = 'dynamic-input-group';
    newInput.innerHTML = `
        <div class="input-group">
            <input type="text" 
                   class="form-control form-control-dark" 
                   name="detailsHighlights[]" 
                   placeholder="Enter activity highlight" 
                   required>
            <button type="button" class="btn btn-remove" onclick="removeInput(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newInput);
}

function removeInput(button) {
    const container = button.closest('#importantInfoContainer, #highlightsContainer');
    if (container.children.length > 1) {
        button.closest('.dynamic-input-group').remove();
    } else {
        // Show alert if trying to remove the last input
        alert('At least one field is required.');
    }
}

// Enhanced image handling
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('activityImageFiles');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const imageCount = document.getElementById('imageCount');
    const uploadArea = document.querySelector('.image-upload-area');

    // File input change event
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    // Drag and drop functionality
    if (uploadArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        uploadArea.addEventListener('drop', handleDrop, false);
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        uploadArea.classList.add('dragover');
    }

    function unhighlight(e) {
        uploadArea.classList.remove('dragover');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        imageInput.files = files;
        handleFiles(files);
    }

    function handleFiles(files) {
        if (previewContainer && imagePreview && imageCount) {
            previewContainer.innerHTML = '';

            if (files.length > 0) {
                imagePreview.style.display = 'block';
                imageCount.textContent = `${files.length} image(s) selected`;
                
                Array.from(files).forEach((file, index) => {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert(`File "${file.name}" is not an image.`);
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'image-preview-item';
                        imgDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            ${index === 0 ? '<span class="main-image-badge">Main Image</span>' : ''}
                        `;
                        previewContainer.appendChild(imgDiv);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                imagePreview.style.display = 'none';
                imageCount.textContent = '';
            }
        }
    }

    // Form validation before submit
    const form = document.getElementById('activityForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check if emirates dropdown has valid selection
            const emiratesSelect = document.getElementById('emiratesID');
            if (!emiratesSelect.value || emiratesSelect.disabled) {
                isValid = false;
                alert('Please select a valid Emirates. Contact administrator if no emirates are available.');
                emiratesSelect.focus();
            }
            
            // Check if at least one image is selected
            if (!imageInput.files || imageInput.files.length === 0) {
                isValid = false;
                alert('Please select at least one image for the activity.');
                imageInput.focus();
            }

            // Check required fields
            const requiredFields = form.querySelectorAll('[required]:not([disabled])');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.focus();
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection
