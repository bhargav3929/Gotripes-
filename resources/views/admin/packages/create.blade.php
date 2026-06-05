@extends('layouts.admin')

@section('title', 'Create Travel Package')
@section('page-title', 'Create New Travel Package')

@php $allCountries = \App\Support\CountryCodes::all(); @endphp

@section('content')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>
    #descEditor { background:#141414; border-radius:0 0 6px 6px; }
    #descEditor .ql-editor { min-height:180px; color:#fff; font-size:14px; }
    .ql-editor.ql-blank::before { color:#777; font-style:normal; }
    .ql-toolbar.ql-snow { background:#1f1f1f; border-color:#3a3a3a !important; border-top-left-radius:6px; border-top-right-radius:6px; }
    .ql-container.ql-snow { border-color:#3a3a3a !important; }
    .ql-snow .ql-stroke { stroke:#cfcfcf; }
    .ql-snow .ql-fill { fill:#cfcfcf; }
    .ql-snow .ql-picker, .ql-snow .ql-picker-options { color:#cfcfcf; background:#1f1f1f; }
</style>
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card shadow-lg border-0">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <h3 class="card-title"><i class="fas fa-suitcase-rolling me-2"></i>Create New Travel Package</h3>
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger m-3">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.packages.store') }}" enctype="multipart/form-data" id="packageForm">
                    @csrf
                    <div class="card-body p-2 p-sm-3 p-md-4">
                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col-12 col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-gold">Package Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title') }}" required maxlength="255" placeholder="e.g., Dubai City Tour Premium">
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-gold">Country / Destination <span class="text-danger">*</span></label>
                                        <select name="country" class="form-control" required>
                                            <option value="">Select destination country…</option>
                                            @foreach($allCountries as $c)
                                                <option value="{{ $c['name'] }}" @selected(old('country') === $c['name'])>{{ $c['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label fw-semibold text-gold">Duration <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="duration" value="{{ old('duration') }}" required maxlength="255" placeholder="e.g., 3 Days / 2 Nights">
                                    </div>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label class="form-label fw-semibold text-gold">Description <span class="text-danger">*</span></label>
                                    <div id="descEditor" style="height:220px;">{!! old('description') !!}</div>
                                    <textarea name="description" id="descInput" class="d-none">{{ old('description') }}</textarea>
                                    <small class="text-muted">Use the toolbar for bold, bullet points and links.</small>
                                </div>

                                <!-- Pricing -->
                                <div class="card bg-light-dark border-gold mb-3">
                                    <div class="card-body">
                                        <h6 class="text-gold mb-3"><i class="fas fa-tags me-2"></i>Pricing</h6>
                                        <div class="row g-3">
                                            <div class="col-12 col-sm-3">
                                                <label class="form-label">"From" Price (AED) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="price" value="{{ old('price') }}" step="0.01" min="0" required placeholder="299.00">
                                            </div>
                                            <div class="col-4 col-sm-3">
                                                <label class="form-label">Per Adult</label>
                                                <input type="number" class="form-control" name="price_adult" value="{{ old('price_adult') }}" step="0.01" min="0">
                                            </div>
                                            <div class="col-4 col-sm-3">
                                                <label class="form-label">Per Child</label>
                                                <input type="number" class="form-control" name="price_child" value="{{ old('price_child') }}" step="0.01" min="0">
                                            </div>
                                            <div class="col-4 col-sm-3">
                                                <label class="form-label">Per Infant</label>
                                                <input type="number" class="form-control" name="price_infant" value="{{ old('price_infant') }}" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <small class="text-muted">Per-person prices power the booking calculator on "Purchase" packages.</small>
                                    </div>
                                </div>

                                <!-- Partner -->
                                <div class="card bg-light-dark border-gold">
                                    <div class="card-body">
                                        <h6 class="text-gold mb-3"><i class="fas fa-handshake me-2"></i>Local Partner Contact <small class="text-muted">(for enquiries)</small></h6>
                                        <div class="row g-3">
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label">Partner Email</label>
                                                <input type="email" class="form-control" name="partner_email" value="{{ old('partner_email') }}" maxlength="255" placeholder="partner@example.com">
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="form-label">Partner WhatsApp</label>
                                                <input type="text" class="form-control" name="partner_whatsapp" value="{{ old('partner_whatsapp') }}" maxlength="30" placeholder="+971501234567">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-12 col-lg-4">
                                <div class="card bg-light-dark border-gold mb-3">
                                    <div class="card-body">
                                        <h6 class="text-gold mb-2"><i class="fas fa-sliders-h me-2"></i>Package Type <span class="text-danger">*</span></h6>
                                        <select name="package_type" class="form-control" required>
                                            <option value="enquire" @selected(old('package_type','enquire')==='enquire')>Enquire — customer contacts you</option>
                                            <option value="purchase" @selected(old('package_type')==='purchase')>Purchase — customer pays online</option>
                                        </select>
                                        <small class="text-muted d-block mt-2"><strong>Enquire:</strong> enquiry/WhatsApp button.<br><strong>Purchase:</strong> price calculator + Pay.</small>
                                    </div>
                                </div>

                                <div class="card bg-light-dark border-gold mb-3">
                                    <div class="card-body">
                                        <h6 class="text-gold mb-2"><i class="fas fa-image me-2"></i>Cover Image <span class="text-danger">*</span></h6>
                                        <input type="file" class="form-control" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" required onchange="previewImage(this)">
                                        <small class="text-muted">JPEG, PNG, GIF, WebP | Max 5MB</small>
                                        <div id="imagePreview" class="mt-2 d-none">
                                            <img id="previewImg" src="" class="rounded" style="max-width:100%; max-height:180px; object-fit:cover;">
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light-dark border-gold">
                                    <div class="card-body">
                                        <h6 class="text-gold mb-2"><i class="fas fa-images me-2"></i>Gallery <small class="text-muted">(up to 7)</small></h6>
                                        <input type="file" class="form-control" name="gallery[]" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" multiple onchange="previewGallery(this)">
                                        <div id="galleryPreview" class="mt-2" style="display:grid; grid-template-columns:repeat(3,1fr); gap:6px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent p-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-primary"><i class="fas fa-times me-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save me-1"></i> Create Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
const quill = new Quill('#descEditor', {
    theme: 'snow',
    placeholder: 'Inclusions, highlights, itinerary…',
    modules: { toolbar: [['bold','italic','underline'], [{list:'ordered'},{list:'bullet'}], ['link'], ['clean']] }
});
const descInput = document.getElementById('descInput');
function syncDesc(){ descInput.value = quill.getText().trim().length ? quill.root.innerHTML : ''; }
quill.on('text-change', syncDesc);
document.getElementById('packageForm').addEventListener('submit', syncDesc);

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { previewImg.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
function previewGallery(input) {
    const box = document.getElementById('galleryPreview');
    box.innerHTML = '';
    Array.from(input.files).slice(0,7).forEach(file => {
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
