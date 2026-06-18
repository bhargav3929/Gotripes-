@extends('layouts.agent')

@section('title', 'Edit Tour Package')
@section('page-title', 'Tour Packages')

@php
    $allCountries = \App\Support\CountryCodes::all();
    // If super admin assigned specific countries, filter to only those.
    // Always include the package's current country so it can be re-selected on edit.
    $countries = ($allowedCountries ?? null)
        ? array_filter($allCountries, fn($c) => in_array($c['name'], $allowedCountries, true) || $c['name'] === $package->country)
        : $allCountries;
@endphp

@section('content')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Tour Package</h1>
    <a href="{{ route('agent.packages.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="wp-card" style="border-left:4px solid #dc3545; margin-bottom:16px;">
        <div class="wp-card-body" style="color:#dc3545;">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0 18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form action="{{ route('agent.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data" id="pkgForm">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-suitcase-rolling text-secondary-wp"></i> Package Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Country / Destination <span class="required">*</span></label>
                        <select name="country" class="wp-select" required>
                            <option value="">Select destination country…</option>
                            @foreach($countries as $c)
                                <option value="{{ $c['name'] }}" @selected(old('country', $package->country) === $c['name'])>
                                    {{ $c['flag'] }} &nbsp;{{ $c['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @if($allowedCountries ?? null)
                            <p class="wp-form-help">Showing {{ count($countries) }} countries assigned by admin.</p>
                        @endif
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Package Title <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="title" value="{{ old('title', $package->title) }}" required maxlength="255">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Duration <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="duration" value="{{ old('duration', $package->duration) }}" required maxlength="255">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Description <span class="required">*</span></label>
                        <div id="descEditor" style="height:240px; background:#fff;">{!! old('description', $package->description) !!}</div>
                        <textarea name="description" id="descInput" style="display:none;">{{ old('description', $package->description) }}</textarea>
                        <p class="wp-form-help">Use the toolbar for bold, bullet points and links.</p>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-tags text-secondary-wp"></i> Pricing</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">"From" Price (AED) <span class="required">*</span></label>
                        <input type="number" class="wp-input" name="price" value="{{ old('price', $package->price) }}" step="0.01" min="0" required>
                        <p class="wp-form-help">Shown on the package card (e.g. "From AED 299").</p>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Per Adult (AED)</label>
                                <input type="number" class="wp-input" name="price_adult" value="{{ old('price_adult', $package->price_adult) }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Per Child (AED)</label>
                                <input type="number" class="wp-input" name="price_child" value="{{ old('price_child', $package->price_child) }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Per Infant (AED)</label>
                                <input type="number" class="wp-input" name="price_infant" value="{{ old('price_infant', $package->price_infant) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <p class="wp-form-help">Per-person prices power the booking calculator on "Purchase" packages.</p>
                </div>
            </div>

            {{-- Partner contact --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-handshake text-secondary-wp"></i> Local Partner Contact <span style="font-weight:400;color:#888;">(for enquiries)</span></div>
                <div class="wp-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Partner Email</label>
                                <input type="email" class="wp-input" name="partner_email" value="{{ old('partner_email', $package->partner_email) }}" maxlength="255" placeholder="partner@example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Partner WhatsApp</label>
                                <input type="text" class="wp-input" name="partner_whatsapp" value="{{ old('partner_whatsapp', $package->partner_whatsapp) }}" maxlength="30" placeholder="+971501234567">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Booking notifications (internal — not shown to customers) --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-bell text-secondary-wp"></i> Booking Notifications <span style="font-weight:400;color:#888;">(your team)</span></div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Notification Emails</label>
                        <textarea class="wp-input" name="notification_emails" rows="2" placeholder="ops@yourbusiness.com, owner@yourbusiness.com">{{ old('notification_emails', $package->notification_emails) }}</textarea>
                        <p class="wp-form-help">Comma-separated. Emailed when a customer sends an enquiry for this package. Leave blank to use your company email. Not shown to customers.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Package type --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-sliders-h text-secondary-wp"></i> Package Type <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <select name="package_type" class="wp-select" required>
                            <option value="enquire" @selected(old('package_type', $package->package_type)==='enquire')>Enquire — custom, customer contacts you</option>
                            <option value="purchase" @selected(old('package_type', $package->package_type)==='purchase')>Purchase — ready-made, customer pays online</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Cover image --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-image text-secondary-wp"></i> Cover Image</div>
                <div class="wp-card-body">
                    @if($package->image)
                        <div style="margin-bottom:10px; border:1px solid var(--wp-border-light); border-radius:6px; overflow:hidden;">
                            <img src="{{ asset($package->image) }}" style="width:100%; display:block;">
                        </div>
                    @endif
                    <div class="wp-form-group">
                        <input type="file" class="wp-input" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" onchange="previewPkgImage(this)">
                        <p class="wp-form-help">Upload a new image to replace the current one.</p>
                    </div>
                    <div id="pkgPreviewWrap" style="display:none; margin-top:12px; border:1px solid var(--wp-border-light); border-radius:6px; overflow:hidden;">
                        <img id="pkgPreviewImg" src="" style="width:100%; display:block;">
                    </div>
                </div>
            </div>

            {{-- Gallery --}}
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-images text-secondary-wp"></i> Gallery</div>
                <div class="wp-card-body">
                    @if($package->images->count())
                        <p class="wp-form-help" style="margin-bottom:8px;">Tick to remove an existing photo:</p>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-bottom:12px;">
                            @foreach($package->images as $img)
                                <label style="position:relative; display:block; cursor:pointer;">
                                    <img src="{{ asset($img->image_path) }}" style="width:100%; height:60px; object-fit:cover; border-radius:4px;">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img->id }}"
                                           style="position:absolute; top:4px; right:4px; width:18px; height:18px;">
                                </label>
                            @endforeach
                        </div>
                    @endif
                    <div class="wp-form-group">
                        <label class="wp-form-label">Add photos <span style="font-weight:400;color:#888;">(up to 7)</span></label>
                        <input type="file" class="wp-input" name="gallery[]" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" multiple onchange="previewGallery(this)">
                    </div>
                    <div id="galleryPreview" style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px; margin-top:10px;"></div>
                </div>
            </div>

            <div class="wp-card">
                <div class="wp-card-body" style="display:flex; gap:8px;">
                    <button type="submit" class="wp-btn wp-btn-primary" style="flex:1;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('agent.packages.index') }}" class="wp-btn wp-btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
const quill = new Quill('#descEditor', {
    theme: 'snow',
    modules: { toolbar: [['bold','italic','underline'], [{list:'ordered'},{list:'bullet'}], ['link'], ['clean']] }
});
const descInput = document.getElementById('descInput');
function syncDesc() {
    descInput.value = quill.getText().trim().length ? quill.root.innerHTML : '';
}
quill.on('text-change', syncDesc);
document.getElementById('pkgForm').addEventListener('submit', syncDesc);

function previewPkgImage(input) {
    const wrap = document.getElementById('pkgPreviewWrap');
    const img  = document.getElementById('pkgPreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else { wrap.style.display = 'none'; }
}
function previewGallery(input) {
    const box = document.getElementById('galleryPreview');
    box.innerHTML = '';
    Array.from(input.files).slice(0, 7).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const im = document.createElement('img');
            im.src = e.target.result;
            im.style.cssText = 'width:100%;height:60px;object-fit:cover;border-radius:4px;';
            box.appendChild(im);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endsection
