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

<style>
    .gold-theme {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #ffd700;
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .btn-gold {
        background: linear-gradient(45deg, #ffd700, #ffed4e);
        color: #1a1a1a;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-gold:hover {
        background: linear-gradient(45deg, #ffed4e, #ffd700);
        color: #1a1a1a;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    }
    
    .form-control-dark {
        background-color: #2d2d2d;
        border: 2px solid #555;
        color: #ffffff;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .form-control-dark:focus {
        background-color: #3d3d3d;
        border-color: #ffd700;
        color: #ffffff;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
    }
    
    .form-control-dark::placeholder {
        color: #aaa;
    }
    
    /* Select dropdown styling */
    .form-control-dark option {
        background-color: #2d2d2d;
        color: #ffffff;
    }
    
    .form-control-dark option:hover,
    .form-control-dark option:focus,
    .form-control-dark option:selected {
        background-color: #ffd700;
        color: #1a1a1a;
    }
    
    .form-label-gold {
        color: #ffd700;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .card-dark-gold {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border: 2px solid #ffd700;
        border-radius: 15px;
    }
    
    .card-dark-gold .card-header {
        background: linear-gradient(45deg, #ffd700, #ffed4e);
        color: #1a1a1a;
        font-weight: 600;
        border-radius: 13px 13px 0 0;
        border-bottom: 2px solid #ffd700;
    }
    
    .section-divider {
        border-top: 2px solid #ffd700;
        margin: 30px 0;
        position: relative;
    }
    
    .section-title {
        background: linear-gradient(45deg, #ffd700, #ffed4e);
        color: #1a1a1a;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .dynamic-input-group {
        border: 1px solid #ffd700;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        background: rgba(255, 215, 0, 0.1);
    }
    
    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .btn-remove:hover {
        background: #c82333;
        transform: translateY(-1px);
    }
    
    .btn-add {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        margin-top: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-add:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    /* Partner Filter Info Box */
    .partner-filter-info {
        background: rgba(23, 162, 184, 0.1) !important;
        border: 2px solid #17a2b8 !important;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        color: #17a2b8 !important;
    }

    .partner-filter-icon {
        color: #17a2b8 !important;
        font-size: 1.2rem;
        margin-right: 10px;
    }

    /* Image Upload Styles */
    .image-upload-area {
        border: 3px dashed #ffd700;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: rgba(255, 215, 0, 0.05);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .image-upload-area:hover {
        background: rgba(255, 215, 0, 0.1);
        border-color: #ffed4e;
    }

    .image-upload-area.dragover {
        background: rgba(255, 215, 0, 0.15);
        border-color: #ffed4e;
        transform: scale(1.02);
    }

    .image-preview-container {
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid #ffd700;
        border-radius: 10px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }

    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
    }

    .image-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #ffd700;
    }

    .image-preview-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        display: block;
    }

    .main-image-badge {
        position: absolute;
        top: 5px;
        left: 5px;
        background: linear-gradient(45deg, #ffd700, #ffed4e);
        color: #1a1a1a;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
    }

    .image-count-info {
        color: #ffd700;
        font-size: 14px;
        margin-top: 10px;
    }

    /* Form Validation Enhancements */
    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 5px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .section-title {
            font-size: 14px;
            padding: 8px 15px;
        }
        
        .image-preview-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
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
                        <a href="{{ route('admin.uaeactivities.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
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
                            <div class="section-title">
                                <i class="fas fa-dollar-sign"></i> Pricing Information
                            </div>
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
                            <div class="section-title">
                                <i class="fas fa-truck"></i> Supplier Information
                            </div>
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
                            <div class="section-title">
                                <i class="fas fa-images"></i> Activity Images
                            </div>
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
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i> Activity Details
                            </div>
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
                        <a href="{{ route('admin.uaeactivities.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
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
