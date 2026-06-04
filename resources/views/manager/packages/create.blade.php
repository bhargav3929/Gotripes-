@extends('layouts.manager')

@section('title', 'Add Tour Package')
@section('page-title', 'Tour Packages')

@php
    $allCountries = \App\Support\CountryCodes::all();
    // If super admin assigned specific countries, filter to only those
    $countries = ($allowedCountries ?? null)
        ? array_filter($allCountries, fn($c) => in_array($c['name'], $allowedCountries, true))
        : $allCountries;
@endphp

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Add Tour Package</h1>
    <a href="{{ route('manager.packages.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<form action="{{ route('manager.packages.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
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
                                <option value="{{ $c['name'] }}" @selected(old('country') === $c['name'])>
                                    {{ $c['flag'] }} &nbsp;{{ $c['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @if($allowedCountries ?? null)
                            <p class="wp-form-help">Showing {{ count($countries) }} countries assigned by admin.</p>
                        @else
                            <p class="wp-form-help">Customers will see packages grouped by country on your site.</p>
                        @endif
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Package Title <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="e.g. Dubai City Tour Premium">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="price" value="{{ old('price') }}" step="0.01" min="0" required placeholder="e.g. 299.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Duration <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="duration" value="{{ old('duration') }}" required maxlength="255" placeholder="e.g. 3 Days / 2 Nights">
                            </div>
                        </div>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Description <span class="required">*</span></label>
                        <textarea class="wp-input" name="description" rows="6" required placeholder="Itinerary, inclusions, highlights…">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-image text-secondary-wp"></i> Package Image <span class="required">*</span></div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <input type="file" class="wp-input" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required onchange="previewPkgImage(this)">
                        <p class="wp-form-help">JPEG, PNG, GIF, or WebP. Max 5 MB.</p>
                    </div>
                    <div id="pkgPreviewWrap" style="display:none; margin-top:12px; border:1px solid var(--wp-border-light); border-radius:6px; overflow:hidden;">
                        <img id="pkgPreviewImg" src="" style="width:100%; display:block;">
                    </div>
                </div>
            </div>

            <div class="wp-card">
                <div class="wp-card-body" style="display:flex; gap:8px;">
                    <button type="submit" class="wp-btn wp-btn-primary" style="flex:1;">
                        <i class="fas fa-save"></i> Save Package
                    </button>
                    <a href="{{ route('manager.packages.index') }}" class="wp-btn wp-btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewPkgImage(input) {
    const wrap = document.getElementById('pkgPreviewWrap');
    const img  = document.getElementById('pkgPreviewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        wrap.style.display = 'none';
    }
}
</script>
@endsection
