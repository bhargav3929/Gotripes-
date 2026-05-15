@extends('layouts.manager')

@section('title', 'Edit Tour Package')
@section('page-title', 'Tour Packages')

@php $countries = \App\Support\CountryCodes::all(); @endphp

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Tour Package</h1>
    <a href="{{ route('manager.packages.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<form action="{{ route('manager.packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
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
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Package Title <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="title" value="{{ old('title', $package->title) }}" required maxlength="255">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Price (AED) <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="price" value="{{ old('price', $package->price) }}" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Duration <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="duration" value="{{ old('duration', $package->duration) }}" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Description <span class="required">*</span></label>
                        <textarea class="wp-input" name="description" rows="6" required>{{ old('description', $package->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-image text-secondary-wp"></i> Package Image</div>
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

            <div class="wp-card">
                <div class="wp-card-body" style="display:flex; gap:8px;">
                    <button type="submit" class="wp-btn wp-btn-primary" style="flex:1;">
                        <i class="fas fa-save"></i> Save Changes
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
