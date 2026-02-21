@extends('layouts.admin')

@section('title', 'Edit UAE Activity')

@section('page-title', 'Edit UAE Activity')

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

    // 🔥 FIX: Check if current activity's creator matches logged-in user (case-insensitive)
    $canEditThisActivity = true;
    if ($isApprovedPartner) {
        // Check if partner created this activity using case-insensitive name comparison
        // AND if activity's emirate is in their allowed list
        $userCreatedActivity = strtolower($activity->createdBy ?? '') === strtolower($user->name);
        $emirateAllowed = in_array($activity->emiratesID, $allowedEmiratesIds);
        
        $canEditThisActivity = $userCreatedActivity && $emirateAllowed;
        
        // Debug logging for troubleshooting
        \Log::info('🔍 Edit permission check', [
            'user_name' => strtolower($user->name),
            'activity_created_by' => strtolower($activity->createdBy ?? ''),
            'user_created_activity' => $userCreatedActivity,
            'activity_emirate_id' => $activity->emiratesID,
            'allowed_emirates' => $allowedEmiratesIds,
            'emirate_allowed' => $emirateAllowed,
            'can_edit' => $canEditThisActivity
        ]);
    }
@endphp

<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">
            <!-- Main Card -->
            <div class="card shadow-lg border-0 animate-fade-in gold-theme-card">
                <!-- Mobile-First Card Header -->
                <div class="card-header bg-gold border-bottom-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                        <h3 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-edit me-2 d-none d-sm-inline"></i>
                            <span class="fs-6 fs-sm-5 fs-md-4">Edit UAE Activity</span>
                            @if($isApprovedPartner)
                                <small class="text-dark ms-2">(Partner: {{ $user->name }})</small>
                            @endif
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.uaeactivities.index') }}" 
                               class="btn btn-outline-dark btn-sm btn-mobile animate-scale">
                                <i class="fas fa-arrow-left me-1"></i> 
                                <span class="d-none d-sm-inline">Back to List</span>
                                <span class="d-sm-none">Back</span>
                            </a>
                        </div>
                    </div>
                </div>

                @if(!$canEditThisActivity)
                <!-- Access Restriction Alert -->
                <div class="alert alert-danger m-3" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Access Denied:</strong> You don't have permission to edit this activity. 
                    @if($isApprovedPartner)
                        <br><small>
                            <strong>Debug Info:</strong><br>
                            • Your name: {{ strtolower($user->name) }}<br>
                            • Activity created by: {{ strtolower($activity->createdBy ?? 'Unknown') }}<br>
                            • Name match: {{ strtolower($activity->createdBy ?? '') === strtolower($user->name) ? 'YES' : 'NO' }}<br>
                            • Activity emirate ID: {{ $activity->emiratesID }}<br>
                            • Your allowed emirates: {{ implode(', ', $allowedEmiratesIds) }}<br>
                            • Emirate allowed: {{ in_array($activity->emiratesID, $allowedEmiratesIds) ? 'YES' : 'NO' }}
                        </small>
                    @endif
                    <br>You can only edit activities you created within your approved emirates.
                </div>
                @else

                <!-- Responsive Form -->
                <form method="POST" action="{{ route('admin.uaeactivities.update', $activity->activityID) }}" 
                      enctype="multipart/form-data" id="activityEditForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        
                        <!-- Partner Filter Information -->
                        @if($isApprovedPartner)
                        <div class="partner-filter-info">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle partner-filter-icon"></i>
                                <div>
                                    <strong>Partner Edit Mode:</strong> You are editing an activity as an approved partner.
                                    <br><small>Emirates selection is limited to your approved regions: 
                                    @if($emirates->count() > 0)
                                        {{ $emirates->pluck('emiratesName')->join(', ') }}
                                    @else
                                        <span class="text-warning">No emirates assigned to your account</span>
                                    @endif
                                    </small>
                                    <br><small class="text-success">
                                        ✅ Access granted - You created this activity and it's in your approved emirates.
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Basic Activity Information Section -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title text-gold fw-bold">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h5>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="activityName" class="form-label form-label-gold">
                                            <i class="fas fa-tag me-1"></i>Activity Name
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-dark @error('activityName') is-invalid @enderror" 
                                               id="activityName" 
                                               name="activityName" 
                                               value="{{ old('activityName', $activity->activityName) }}" 
                                               placeholder="Enter activity name"
                                               required>
                                        @error('activityName')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="activityLocation" class="form-label form-label-gold">
                                            <i class="fas fa-map-marker-alt me-1"></i>Activity Location
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-dark @error('activityLocation') is-invalid @enderror" 
                                               id="activityLocation" 
                                               name="activityLocation" 
                                               value="{{ old('activityLocation', $activity->activityLocation) }}" 
                                               placeholder="Enter location (e.g., Dubai, Abu Dhabi)"
                                               required>
                                        @error('activityLocation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- UPDATED: Emirates Dropdown with Partner Filtering -->
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="emiratesID" class="form-label form-label-gold">
                                            <i class="fas fa-flag me-1"></i>Emirates
                                            <span class="text-danger">*</span>
                                            @if($isApprovedPartner)
                                                <small class="text-info">(Your Approved Emirates)</small>
                                            @endif
                                        </label>
                                        <select class="form-control form-control-dark @error('emiratesID') is-invalid @enderror" 
                                                id="emiratesID" 
                                                name="emiratesID" 
                                                required
                                                @if($emirates->count() == 0) disabled @endif>
                                            <option value="" disabled>
                                                @if($emirates->count() > 0)
                                                    Select Emirates
                                                @else
                                                    No Emirates Available
                                                @endif
                                            </option>
                                            @forelse($emirates as $emirate)
                                                <option value="{{ $emirate->emiratesID }}" 
                                                        {{ old('emiratesID', $activity->emiratesID) == $emirate->emiratesID ? 'selected' : '' }}>
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
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="activityCategory" class="form-label form-label-gold">
                                            <i class="fas fa-layer-group me-1"></i>Activity Category / Type (Optional)
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-dark @error('activityCategory') is-invalid @enderror" 
                                               id="activityCategory" 
                                               name="activityCategory" 
                                               value="{{ old('activityCategory', $activity->activityCategory) }}" 
                                               placeholder="e.g., Adventure, Water Sports, Desert Safari, Luxury">
                                        <small class="text-white-50">Used for future filtering and grouping activities.</small>
                                        @error('activityCategory')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Information Section -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title text-gold fw-bold">
                                    <i class="fas fa-dollar-sign me-2"></i>Pricing Information
                                </h5>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="activityPrice" class="form-label form-label-gold">
                                            <i class="fas fa-dollar-sign me-1"></i>Activity Price (AED)
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('activityPrice') is-invalid @enderror" 
                                               id="activityPrice" 
                                               name="activityPrice" 
                                               value="{{ old('activityPrice', $activity->activityPrice) }}" 
                                               placeholder="0.00"
                                               step="0.01" 
                                               min="0" 
                                               required>
                                        @error('activityPrice')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="activityChildPrice" class="form-label form-label-gold">
                                            <i class="fas fa-child me-1"></i>Children Price (AED)
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('activityChildPrice') is-invalid @enderror" 
                                               id="activityChildPrice" 
                                               name="activityChildPrice" 
                                               value="{{ old('activityChildPrice', $activity->activityChildPrice) }}" 
                                               placeholder="0.00"
                                               step="0.01" 
                                               min="0">
                                        @error('activityChildPrice')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label for="activityTransactionCharges" class="form-label form-label-gold">
                                            <i class="fas fa-credit-card me-1"></i>Transaction Charges (AED)
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('activityTransactionCharges') is-invalid @enderror" 
                                               id="activityTransactionCharges" 
                                               name="activityTransactionCharges" 
                                               value="{{ old('activityTransactionCharges', $activity->activityTransactionCharges) }}" 
                                               placeholder="0.00"
                                               step="0.01" 
                                               min="0">
                                        @error('activityTransactionCharges')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Transport Pricing - Original Fields -->
                            <div class="row g-3 mt-2">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="dubaiPrice" class="form-label form-label-gold">
                                            <i class="fas fa-bus me-1"></i>Transport Price from Dubai (AED)
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('dubaiPrice') is-invalid @enderror" 
                                               id="dubaiPrice" 
                                               name="dubaiPrice" 
                                               value="{{ old('dubaiPrice', $activity->dubaiPrice) }}" 
                                               placeholder="0.00"
                                               step="0.01" 
                                               min="0">
                                        @error('dubaiPrice')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="abuDhabiPrice" class="form-label form-label-gold">
                                            <i class="fas fa-bus me-1"></i>Transport Price from Abu Dhabi (AED)
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('abuDhabiPrice') is-invalid @enderror" 
                                               id="abuDhabiPrice" 
                                               name="abuDhabiPrice" 
                                               value="{{ old('abuDhabiPrice', $activity->abuDhabiPrice) }}" 
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
                            <div class="row g-3 mt-2">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="fromAbuDhabiToDubai" class="form-label form-label-gold">
                                            <i class="fas fa-exchange-alt me-1"></i>Abu Dhabi ⇄ Dubai Transport Price (AED)
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('fromAbuDhabiToDubai') is-invalid @enderror" 
                                               id="fromAbuDhabiToDubai" 
                                               name="fromAbuDhabiToDubai" 
                                               value="{{ old('fromAbuDhabiToDubai', $activity->fromAbuDhabiToDubai ?? '') }}" 
                                               placeholder="0.00"
                                               step="0.01" 
                                               min="0">
                                        <small class="text-muted">Transport between Abu Dhabi and Dubai (both directions)</small>
                                        @error('fromAbuDhabiToDubai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="emirates" class="form-label form-label-gold">
                                            <i class="fas fa-map me-1"></i>Any Emirates Transport Price (AED)
                                        </label>
                                        <input type="number" 
                                               class="form-control form-control-dark @error('emirates') is-invalid @enderror" 
                                               id="emirates" 
                                               name="emirates" 
                                               value="{{ old('emirates', $activity->emirates ?? '') }}" 
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
                        </div>

                        <!-- Supplier Information Section -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title text-gold fw-bold">
                                    <i class="fas fa-truck me-2"></i>Supplier Information
                                </h5>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="supplierName" class="form-label form-label-gold">
                                            <i class="fas fa-user-tie me-1"></i>Supplier / Tour Operator Name
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-dark @error('supplierName') is-invalid @enderror" 
                                               id="supplierName" 
                                               name="supplierName" 
                                               value="{{ old('supplierName', $activity->supplierName) }}" 
                                               placeholder="e.g., Desert Safari Tours LLC">
                                        <small class="text-muted">Optional — the supplier will receive booking notifications</small>
                                        @error('supplierName')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="supplierEmail" class="form-label form-label-gold">
                                            <i class="fas fa-envelope me-1"></i>Supplier Email
                                        </label>
                                        <input type="email" 
                                               class="form-control form-control-dark @error('supplierEmail') is-invalid @enderror" 
                                               id="supplierEmail" 
                                               name="supplierEmail" 
                                               value="{{ old('supplierEmail', $activity->supplierEmail) }}" 
                                               placeholder="supplier@example.com">
                                        <small class="text-muted">Optional — booking confirmations will be sent to this email</small>
                                        @error('supplierEmail')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Images Section -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title text-gold fw-bold">
                                    <i class="fas fa-images me-2"></i>Activity Images
                                </h5>
                            </div>
                            
                            <!-- Current Images Display -->
                            @if($details && $details->activityImage)
                            <div class="current-images-section mb-4">
                                <h6 class="text-light fw-semibold mb-3">
                                    <i class="fas fa-eye me-2"></i>Current Images:
                                </h6>
                                <div class="row g-3">
                                    @php
                                        $currentImages = explode('#cseparator', $details->activityImage);
                                    @endphp
                                    @foreach($currentImages as $index => $imagePath)
                                        @if(trim($imagePath))
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="current-image-item">
                                                <img src="{{ asset($imagePath) }}" 
                                                     alt="Activity Image {{ $index + 1 }}" 
                                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                                @if($index === 0)
                                                    <div class="main-image-badge">
                                                        <i class="fas fa-star me-1"></i>Main
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="image-count-info mt-3">
                                    <span class="badge bg-info-custom">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ count(array_filter($currentImages, 'trim')) }} image(s) currently uploaded
                                    </span>
                                </div>
                            </div>
                            @endif

                            <!-- Image Update Options -->
                            <div class="image-options-section mb-4">
                                <h6 class="text-light fw-semibold mb-3">
                                    <i class="fas fa-cog me-2"></i>Image Update Options:
                                </h6>
                                <div class="row g-2">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-check-custom">
                                            <input class="form-check-input" type="radio" name="image_action" id="keep_images" value="keep" checked>
                                            <label class="form-check-label text-light" for="keep_images">
                                                <i class="fas fa-check text-success me-2"></i>Keep current images
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-check-custom">
                                            <input class="form-check-input" type="radio" name="image_action" id="add_images" value="add">
                                            <label class="form-check-label text-light" for="add_images">
                                                <i class="fas fa-plus text-primary me-2"></i>Add new images
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-check-custom">
                                            <input class="form-check-input" type="radio" name="image_action" id="replace_images" value="replace">
                                            <label class="form-check-label text-light" for="replace_images">
                                                <i class="fas fa-sync-alt text-warning me-2"></i>Replace all images
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Image Upload Section -->
                            <div class="image-upload-section" id="imageUploadSection" style="display: none;">
                                <div class="form-group">
                                    <label for="activityImageFiles" class="form-label form-label-gold fw-semibold">
                                        <i class="fas fa-cloud-upload-alt me-2"></i>Select New Images
                                    </label>
                                    
                                    <div class="image-upload-area" onclick="document.getElementById('activityImageFiles').click()">
                                        <div class="upload-content text-center">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-gold mb-3"></i>
                                            <h6 class="text-light fw-semibold mb-2">Click to select images or drag & drop</h6>
                                            <p class="text-light-muted mb-2">
                                                Select multiple images (JPEG, PNG, JPG, GIF, WebP)
                                            </p>
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Maximum size: 5MB each
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <input type="file" 
                                           class="d-none @error('activityImageFiles') is-invalid @enderror" 
                                           id="activityImageFiles" 
                                           name="activityImageFiles[]" 
                                           accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" 
                                           multiple>
                                    
                                    @error('activityImageFiles')
                                        <div class="invalid-feedback d-block mt-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror

                                    <!-- New Image Preview Container -->
                                    <div class="image-preview-container mt-3" id="imagePreview" style="display: none;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-light fw-semibold mb-0">
                                                <i class="fas fa-eye me-2"></i>New Images Preview
                                            </h6>
                                            <div class="image-count-info" id="imageCount"></div>
                                        </div>
                                        <div class="image-preview-grid" id="previewContainer"></div>
                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearImageSelection()">
                                                <i class="fas fa-times me-1"></i>Clear Selection
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Details Section -->
                        <div class="form-section mb-4">
                            <div class="section-header mb-3">
                                <h5 class="section-title text-gold fw-bold">
                                    <i class="fas fa-info-circle me-2"></i>Activity Details
                                </h5>
                            </div>
                            
                            <div class="form-group mb-4">
    <label for="detailsOverview" class="form-label form-label-gold">
        <i class="fas fa-eye me-1"></i>Activity Overview
        <span class="text-danger">*</span>
    </label>
    
    <!-- Quill Editor with Dark Theme -->
    <div id="quill-editor" style="height: 200px;"></div>
    
    <!-- Hidden textarea for form submission -->
    <textarea 
        class="form-control d-none @error('detailsOverview') is-invalid @enderror" 
        id="detailsOverview" 
        name="detailsOverview"
    >{{ old('detailsOverview', $details->detailsOverview ?? '') }}</textarea>
    
    @error('detailsOverview')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<!-- Add this at the bottom of your edit form, before closing </body> or </form> -->
<!-- Only include once per page -->
@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
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
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
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

        // Load existing content from textarea into Quill editor
        var existingContent = document.getElementById("detailsOverview").value;
        if(existingContent && existingContent.trim() !== ''){
            quill.root.innerHTML = existingContent;
        }

        // Function to sync Quill to textarea
        function syncQuillToTextarea() {
            var html = quill.root.innerHTML;
            if (html === '<p><br></p>' || html.trim() === '') {
                html = '';
            }
            document.getElementById("detailsOverview").value = html;
            console.log('Synced detailsOverview for edit:', html);
            return html;
        }

        // Real-time sync on text change
        quill.on('text-change', function() {
            syncQuillToTextarea();
        });

        // Sync on form submit
        var forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var content = syncQuillToTextarea();
                console.log('Edit form submitting with detailsOverview:', content);
            });
        });
    });
</script>
@endpush

                            <!-- Dynamic Important Information -->
                            <div class="form-group mb-4">
                                <label class="form-label form-label-gold">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Important Information
                                    <span class="text-danger">*</span>
                                </label>
                                <div id="importantInfoContainer">
                                    @if(isset($detailsIminfo) && count($detailsIminfo) > 0)
                                        @foreach($detailsIminfo as $info)
                                        <div class="dynamic-input-group mb-2">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control form-control-dark" 
                                                       name="detailsIminfo[]" 
                                                       value="{{ trim($info) }}" 
                                                       placeholder="Enter important information"
                                                       required>
                                                <button type="button" class="btn btn-danger-custom btn-sm" onclick="removeInput(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="dynamic-input-group mb-2">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control form-control-dark" 
                                                       name="detailsIminfo[]" 
                                                       placeholder="Enter important information (e.g., age restrictions, dress code, etc.)" 
                                                       required>
                                                <button type="button" class="btn btn-danger-custom btn-sm" onclick="removeInput(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-success-custom btn-sm mt-2" onclick="addImportantInfo()">
                                    <i class="fas fa-plus me-1"></i>Add More Information
                                </button>
                                @error('detailsIminfo')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Dynamic Highlights -->
                            <div class="form-group mb-4">
                                <label class="form-label form-label-gold">
                                    <i class="fas fa-star me-1"></i>Activity Highlights
                                    <span class="text-danger">*</span>
                                </label>
                                <div id="highlightsContainer">
                                    @if(isset($detailsHighlights) && count($detailsHighlights) > 0)
                                        @foreach($detailsHighlights as $highlight)
                                        <div class="dynamic-input-group mb-2">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control form-control-dark" 
                                                       name="detailsHighlights[]" 
                                                       value="{{ trim($highlight) }}" 
                                                       placeholder="Enter activity highlight"
                                                       required>
                                                <button type="button" class="btn btn-danger-custom btn-sm" onclick="removeInput(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="dynamic-input-group mb-2">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control form-control-dark" 
                                                       name="detailsHighlights[]" 
                                                       placeholder="Enter activity highlight (e.g., breathtaking views, expert guides, etc.)" 
                                                       required>
                                                <button type="button" class="btn btn-danger-custom btn-sm" onclick="removeInput(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-success-custom btn-sm mt-2" onclick="addHighlight()">
                                    <i class="fas fa-plus me-1"></i>Add More Highlights
                                </button>
                                @error('detailsHighlights')
                                    <div class="text-danger mt-2">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden input for replace_images flag -->
                        <input type="hidden" name="replace_images" id="replace_images_input" value="0">
                    </div>
                    
                    <!-- Mobile-Responsive Footer -->
                    <div class="card-footer bg-transparent border-top-gold p-2 p-sm-3 p-md-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-center align-items-stretch align-items-sm-center gap-2 gap-sm-3">
                            <button type="submit" class="btn btn-gold btn-lg flex-fill flex-sm-grow-0 animate-scale order-1" id="submitBtn"
                                    @if($emirates->count() == 0) disabled @endif>
                                <i class="fas fa-save me-2"></i>
                                <span class="d-none d-sm-inline">
                                    @if($emirates->count() == 0)
                                        Cannot Update Activity
                                    @else
                                        Update UAE Activity
                                    @endif
                                </span>
                                <span class="d-sm-none">
                                    @if($emirates->count() == 0)
                                        Cannot Update
                                    @else
                                        Update Activity
                                    @endif
                                </span>
                            </button>
                            <a href="{{ route('admin.uaeactivities.index') }}" 
                               class="btn btn-outline-secondary btn-lg flex-fill flex-sm-grow-0 animate-scale order-2">
                                <i class="fas fa-times me-1"></i>
                                <span class="d-none d-sm-inline">Cancel</span>
                                <span class="d-sm-none">Cancel</span>
                            </a>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Mobile-First Responsive Styles -->
<style>
    /* Root Variables */
    :root {
        --primary-gold: #FFD700;
        --secondary-gold: #FFA500;
        --dark-bg: #1a1a1a;
        --darker-bg: #0d0d0d;
        --light-dark: #2d2d2d;
        --text-gold: #FFD700;
        --text-light: #f8f9fa;
        --text-light-muted: #e9ecef;
        --shadow-gold: rgba(255, 215, 0, 0.3);
        --gradient-primary: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        --gradient-dark: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
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

    /* Custom Font Size Classes */
    .fs-8 { font-size: 0.7rem !important; }
    .fs-7 { font-size: 0.8rem !important; }
    .fs-6 { font-size: 0.9rem !important; }

    /* Main Card Styling */
    .gold-theme-card {
        background: var(--gradient-dark);
        border: 2px solid var(--primary-gold);
        color: var(--text-light);
    }

    .bg-gold {
        background: var(--gradient-primary) !important;
        color: var(--darker-bg) !important;
    }

    /* Button Styling */
    .btn-mobile {
        min-height: 44px !important;
        padding: 0.6rem 1rem !important;
        font-size: 0.875rem !important;
    }

    .btn-gold {
        background: var(--gradient-primary);
        color: var(--darker-bg);
        border: 1px solid var(--primary-gold);
        font-weight: 600;
        transition: all 0.3s ease;
        min-height: 48px;
    }

    .btn-gold:hover {
        background: var(--secondary-gold);
        color: var(--darker-bg);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    }

    .btn-outline-dark {
        border-color: #495057;
        color: #495057;
        background-color: transparent;
    }

    .btn-outline-dark:hover {
        background-color: #495057;
        border-color: #495057;
        color: var(--primary-gold);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-success-custom:hover {
        background: linear-gradient(135deg, #218838, #1e7e34);
        transform: translateY(-1px);
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-danger-custom:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-1px);
    }

    /* Form Controls */
    .form-control-dark {
        background-color: rgba(45, 45, 45, 0.9) !important;
        border: 2px solid rgba(255, 215, 0, 0.3) !important;
        color: #ffffff !important;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-size: 16px; /* Prevents zoom on iOS */
        min-height: 44px;
    }

    .form-control-dark:focus {
        background-color: rgba(45, 45, 45, 1) !important;
        border-color: var(--primary-gold) !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25) !important;
    }

    .form-control-dark::placeholder {
        color: #adb5bd !important;
        opacity: 1;
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

    /* Form Labels */
    .form-label-gold {
        color: var(--primary-gold) !important;
        font-weight: 600 !important;
        margin-bottom: 0.5rem !important;
    }

    /* Text Colors with Better Visibility */
    .text-light {
        color: #ffffff !important;
    }

    .text-light-muted {
        color: var(--text-light-muted) !important;
    }

    /* Section Styling */
    .form-section {
        padding: 1.5rem;
        background: rgba(45, 45, 45, 0.3);
        border-radius: 10px;
        border: 1px solid rgba(255, 215, 0, 0.2);
    }

    .section-title {
        color: var(--primary-gold);
        border-bottom: 2px solid var(--primary-gold);
        padding-bottom: 0.5rem;
    }

    /* Current Images Styling */
    .current-images-section {
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 10px;
        padding: 1rem;
    }

    .current-image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--primary-gold);
        aspect-ratio: 16 / 9;
    }

    .current-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .main-image-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: var(--gradient-primary);
        color: var(--darker-bg);
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: bold;
    }

    /* Image Options Styling */
    .image-options-section {
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 8px;
        padding: 1rem;
    }

    .form-check-custom {
        padding: 0.75rem;
        border-radius: 6px;
        background: rgba(45, 45, 45, 0.3);
        transition: all 0.3s ease;
    }

    .form-check-custom:hover {
        background: rgba(255, 215, 0, 0.1);
    }

    .form-check-input:checked {
        background-color: var(--primary-gold);
        border-color: var(--primary-gold);
    }

    .form-check-label {
        color: var(--text-light) !important;
        cursor: pointer;
        font-weight: 500;
    }

    /* Enhanced Image Upload Styles */
    .image-upload-area {
        border: 3px dashed var(--primary-gold);
        border-radius: 10px;
        padding: 2rem 1rem;
        background: rgba(255, 215, 0, 0.05);
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-upload-area:hover {
        background: rgba(255, 215, 0, 0.1);
        border-color: var(--secondary-gold);
        transform: scale(1.02);
    }

    .image-upload-area.dragover {
        background: rgba(255, 215, 0, 0.15);
        border-color: var(--secondary-gold);
        transform: scale(1.05);
    }

    /* Image Preview Container */
    .image-preview-container {
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid var(--primary-gold);
        border-radius: 10px;
        padding: 1rem;
    }

    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
    }

    .image-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--primary-gold);
        aspect-ratio: 1;
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Dynamic Input Styling */
    .dynamic-input-group {
        background: rgba(255, 215, 0, 0.05);
        border: 1px solid rgba(255, 215, 0, 0.2);
        border-radius: 8px;
        padding: 0.5rem;
    }

    /* Badge Styling */
    .bg-info-custom {
        background: linear-gradient(135deg, #17a2b8, #6f42c1) !important;
        color: #ffffff !important;
    }

    /* Card Footer */
    .border-top-gold {
        border-top: 2px solid var(--primary-gold) !important;
    }

    /* Form Validation */
    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        color: #f5c6cb !important;
        background-color: rgba(220, 53, 69, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border-left: 3px solid #dc3545;
    }

    /* Responsive Breakpoints */
    @media (max-width: 575.98px) {
        .container-fluid { 
            padding-left: 0.5rem !important; 
            padding-right: 0.5rem !important; 
        }
        
        .form-section { 
            padding: 1rem; 
            margin-bottom: 1rem !important;
        }
        
        .card-body { 
            padding: 1rem !important; 
        }
        
        .image-preview-grid { 
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); 
        }
        
        .image-upload-area { 
            padding: 1.5rem 1rem; 
            min-height: 120px;
        }
        
        .btn { 
            font-size: 0.875rem !important; 
            padding: 0.6rem 1rem !important; 
        }
        
        h3 { 
            font-size: 1.1rem !important; 
        }
    }

    @media (min-width: 576px) and (max-width: 767.98px) {
        .fs-sm-7 { font-size: 0.8rem !important; }
        .fs-sm-6 { font-size: 0.9rem !important; }
        .fs-sm-5 { font-size: 1rem !important; }
        
        .image-preview-grid { 
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); 
        }
    }

    @media (min-width: 768px) {
        .form-section { 
            padding: 2rem; 
        }
        
        .image-preview-grid { 
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); 
        }
    }

    @media (min-width: 992px) {
        .fs-md-5 { font-size: 1rem !important; }
        .fs-md-4 { font-size: 1.25rem !important; }
    }

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
        
        .form-control-dark { 
            min-height: 48px !important; 
        }
        
        .animate-scale:hover { 
            transform: none !important; 
        }
        
        .image-upload-area:hover { 
            transform: none !important; 
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .animate-fade-in, .animate-scale, .btn-gold {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Landscape Mobile */
    @media (max-height: 500px) and (orientation: landscape) and (max-width: 991px) {
        .form-section { 
            padding: 1rem; 
            margin-bottom: 1rem !important;
        }
        
        .image-upload-area { 
            min-height: 100px; 
        }
    }
</style>

<!-- Enhanced Mobile-Responsive JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const form = document.getElementById('activityEditForm');
    const submitBtn = document.getElementById('submitBtn');
    const imageInput = document.getElementById('activityImageFiles');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const imageCount = document.getElementById('imageCount');
    const uploadArea = document.querySelector('.image-upload-area');
    const imageUploadSection = document.getElementById('imageUploadSection');
    const replaceImagesInput = document.getElementById('replace_images_input');

    // Image action radio buttons
    const imageActionRadios = document.querySelectorAll('input[name="image_action"]');
    
    imageActionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'keep') {
                imageUploadSection.style.display = 'none';
                replaceImagesInput.value = '0';
                clearImagePreview();
            } else {
                imageUploadSection.style.display = 'block';
                replaceImagesInput.value = this.value === 'replace' ? '1' : '0';
            }
        });
    });

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
                imageCount.innerHTML = `<span class="badge bg-info-custom"><i class="fas fa-images me-1"></i>${files.length} new image(s) selected</span>`;
                
                Array.from(files).forEach((file, index) => {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        showAlert(`File "${file.name}" is not an image.`, 'danger');
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        showAlert(`File "${file.name}" is too large. Maximum size is 5MB.`, 'danger');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'image-preview-item';
                        
                        const selectedAction = document.querySelector('input[name="image_action"]:checked').value;
                        const isMainImage = (selectedAction === 'replace' && index === 0) || 
                                          (selectedAction === 'add' && index === 0 && !hasCurrentImages());
                        
                        imgDiv.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            ${isMainImage ? '<div class="main-image-badge"><i class="fas fa-star me-1"></i>New Main</div>' : ''}
                        `;
                        previewContainer.appendChild(imgDiv);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                clearImagePreview();
            }
        }
    }

    function clearImagePreview() {
        if (previewContainer && imagePreview && imageCount) {
            previewContainer.innerHTML = '';
            imagePreview.style.display = 'none';
            imageCount.innerHTML = '';
            if (imageInput) {
                imageInput.value = '';
            }
        }
    }

    function hasCurrentImages() {
        return document.querySelector('.current-images-section') !== null;
    }

    // Form submission with loading state
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const selectedAction = document.querySelector('input[name="image_action"]:checked').value;
            
            // Check if emirates dropdown has valid selection
            const emiratesSelect = document.getElementById('emiratesID');
            if (!emiratesSelect.value || emiratesSelect.disabled) {
                isValid = false;
                showAlert('Please select a valid Emirates. Contact administrator if no emirates are available.', 'danger');
                emiratesSelect.focus();
            }
            
            // Check if images are required for replace/add action
            if (selectedAction !== 'keep' && (!imageInput.files || imageInput.files.length === 0)) {
                isValid = false;
                showAlert('Please select images when choosing to add or replace images.', 'danger');
                imageInput.focus();
            }

            // Check required fields
            const requiredFields = form.querySelectorAll('[required]:not([disabled])');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.focus();
                    showAlert('Please fill in all required fields.', 'danger');
                }
            });

            if (isValid) {
                const isMobile = window.innerWidth < 576;
                submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span>${isMobile ? 'Updating...' : 'Updating Activity...'}`;
                submitBtn.disabled = true;
            } else {
                e.preventDefault();
            }
        });
    }

    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            // Trigger any resize-dependent updates
        }, 100);
    });
});

// Global clear image selection function
function clearImageSelection() {
    const imageInput = document.getElementById('activityImageFiles');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const imageCount = document.getElementById('imageCount');
    
    if (previewContainer && imagePreview && imageCount && imageInput) {
        previewContainer.innerHTML = '';
        imagePreview.style.display = 'none';
        imageCount.innerHTML = '';
        imageInput.value = '';
    }
}

// Dynamic input functions
function addImportantInfo() {
    const container = document.getElementById('importantInfoContainer');
    const newInput = document.createElement('div');
    newInput.className = 'dynamic-input-group mb-2';
    newInput.innerHTML = `
        <div class="input-group">
            <input type="text" 
                   class="form-control form-control-dark" 
                   name="detailsIminfo[]" 
                   placeholder="Enter important information" 
                   required>
            <button type="button" class="btn btn-danger-custom btn-sm" onclick="removeInput(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newInput);
    
    // Focus on new input
    const newInputField = newInput.querySelector('input');
    if (newInputField) {
        newInputField.focus();
    }
}

function addHighlight() {
    const container = document.getElementById('highlightsContainer');
    const newInput = document.createElement('div');
    newInput.className = 'dynamic-input-group mb-2';
    newInput.innerHTML = `
        <div class="input-group">
            <input type="text" 
                   class="form-control form-control-dark" 
                   name="detailsHighlights[]" 
                   placeholder="Enter activity highlight" 
                   required>
            <button type="button" class="btn btn-danger-custom btn-sm" onclick="removeInput(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(newInput);
    
    // Focus on new input
    const newInputField = newInput.querySelector('input');
    if (newInputField) {
        newInputField.focus();
    }
}

function removeInput(button) {
    const container = button.closest('#importantInfoContainer, #highlightsContainer');
    if (container.children.length > 1) {
        button.closest('.dynamic-input-group').remove();
    } else {
        showAlert('At least one field is required.', 'warning');
    }
}

// Global alert function
function showAlert(message, type = 'info') {
    const icons = {
        success: 'check-circle',
        danger: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };

    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show animate-fade-in" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${icons[type] || 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert[style*="position: fixed"]');
    existingAlerts.forEach(alert => alert.remove());
    
    // Add new alert
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert[style*="position: fixed"]');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}
</script>
@endsection
