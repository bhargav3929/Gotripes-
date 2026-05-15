@extends('layouts.manager')

@section('title', 'Edit Hajj/Umrah Package')
@section('page-title', 'Hajj & Umrah Packages')

@php
    $featuresText = is_array($package->features) ? implode("\n", $package->features) : '';
@endphp

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Hajj/Umrah Package</h1>
    <a href="{{ route('manager.umrah-packages.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<form action="{{ route('manager.umrah-packages.update', $package->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-kaaba text-secondary-wp"></i> Package Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Title <span class="required">*</span></label>
                        <input type="text" class="wp-input" name="title" value="{{ old('title', $package->title) }}" required maxlength="255">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Price <span class="required">*</span></label>
                                <input type="number" class="wp-input" name="price" value="{{ old('price', $package->price) }}" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Currency <span class="required">*</span></label>
                                <select class="wp-select" name="currency" required>
                                    @foreach(['AED','SAR','USD','EUR','GBP','INR'] as $cur)
                                        <option value="{{ $cur }}" @selected(old('currency', $package->currency) === $cur)>{{ $cur }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Duration <span class="required">*</span></label>
                                <input type="text" class="wp-input" name="duration" value="{{ old('duration', $package->duration) }}" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Tag (badge text)</label>
                                <input type="text" class="wp-input" name="tag" value="{{ old('tag', $package->tag) }}" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="wp-form-group">
                                <label class="wp-form-label">Sort Order</label>
                                <input type="number" class="wp-input" name="sortOrder" value="{{ old('sortOrder', $package->sortOrder) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-3" style="display:flex; align-items:flex-end;">
                            <div class="wp-form-group" style="padding-bottom:6px;">
                                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                                    <input type="checkbox" name="isFeatured" value="1" {{ old('isFeatured', $package->isFeatured) ? 'checked' : '' }}>
                                    <span class="wp-form-label" style="margin:0;">Featured</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Description <span class="required">*</span></label>
                        <textarea class="wp-input" name="description" rows="5" required>{{ old('description', $package->description) }}</textarea>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Features (one per line)</label>
                        <textarea class="wp-input" name="features" rows="5">{{ old('features', $featuresText) }}</textarea>
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
                        <input type="file" class="wp-input" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" onchange="previewUmrahImage(this)">
                        <p class="wp-form-help">Upload a new image to replace the current one.</p>
                    </div>
                    <div id="umrahPreviewWrap" style="display:none; margin-top:12px; border:1px solid var(--wp-border-light); border-radius:6px; overflow:hidden;">
                        <img id="umrahPreviewImg" src="" style="width:100%; display:block;">
                    </div>
                </div>
            </div>

            <div class="wp-card">
                <div class="wp-card-body" style="display:flex; gap:8px;">
                    <button type="submit" class="wp-btn wp-btn-primary" style="flex:1;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('manager.umrah-packages.index') }}" class="wp-btn wp-btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewUmrahImage(input) {
    const wrap = document.getElementById('umrahPreviewWrap');
    const img  = document.getElementById('umrahPreviewImg');
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
