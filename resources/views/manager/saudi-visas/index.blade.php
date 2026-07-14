@extends('layouts.manager')

@section('title', 'Saudi Visa Management')
@section('page-title', 'Saudi Visa Types & Pricing')

@section('content')

<div class="wp-page-header">
    <h1 class="wp-page-title">
        <i class="fas fa-passport" style="color:var(--wp-primary); margin-right:8px;"></i>
        Saudi Visa Management
    </h1>
</div>

@if(session('success'))
<div style="padding:12px 16px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:8px; color:#22c55e; margin-bottom:18px; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="padding:12px 16px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:8px; color:#ef4444; margin-bottom:18px;">
    @foreach($errors->all() as $error)<div><i class="fas fa-exclamation-triangle me-1"></i>{{ $error }}</div>@endforeach
</div>
@endif

<div class="row g-4">

    {{-- Visa Types List --}}
    <div class="col-lg-7">
        <div class="wp-card">
            <div style="padding:16px 20px; border-bottom:1px solid var(--wp-border-light);">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--wp-text);">
                    <i class="fas fa-list" style="color:var(--wp-primary); margin-right:8px;"></i>
                    Visa Types ({{ $visaTypes->count() }})
                </h3>
            </div>

            @forelse($visaTypes as $visa)
            <div style="border-bottom:1px solid var(--wp-border-light); padding:18px 20px;" id="visa-block-{{ $visa->id }}">
                {{-- View mode --}}
                <div class="visa-view-{{ $visa->id }}">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <h4 style="margin:0; font-size:15px; font-weight:700; color:var(--wp-text);">{{ $visa->name }}</h4>
                            @if($visa->description)
                                <p style="font-size:12.5px; color:var(--wp-text-muted); margin:4px 0 0;">{{ $visa->description }}</p>
                            @endif
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:20px; font-weight:800; color:var(--wp-primary);">AED {{ number_format($visa->price, 0) }}</div>
                            <div style="font-size:11px; color:var(--wp-text-muted);">{{ $visa->processing_days }} day{{ $visa->processing_days != 1 ? 's' : '' }} processing</div>
                        </div>
                    </div>

                    @if($visa->required_documents && count($visa->required_documents))
                    <div style="margin-bottom:10px;">
                        <span style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; letter-spacing:0.4px;">Required Documents</span>
                        <div style="display:flex; flex-wrap:wrap; gap:5px; margin-top:5px;">
                            @foreach($visa->required_documents as $doc)
                                <span style="font-size:11px; background:rgba(255,215,0,0.08); border:1px solid rgba(255,215,0,0.2); color:var(--wp-text-secondary); padding:3px 9px; border-radius:50px;">
                                    {{ $doc }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div style="display:flex; gap:8px; align-items:center; margin-top:10px;">
                        @if($visa->isActive)
                            <span class="wp-badge" style="background:rgba(34,197,94,0.12); color:#22c55e;">Active</span>
                        @else
                            <span class="wp-badge" style="background:rgba(107,114,128,0.12); color:#9ca3af;">Inactive</span>
                        @endif
                        <button onclick="showEditVisa({{ $visa->id }})" class="wp-btn wp-btn-secondary wp-btn-sm">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <form action="{{ route('manager.saudi-visas.destroy', $visa->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Deactivate this visa type?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Edit mode (hidden by default) --}}
                <div class="visa-edit-{{ $visa->id }}" style="display:none;">
                    <form action="{{ route('manager.saudi-visas.update', $visa->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-2 mb-2">
                            <div class="col-sm-6">
                                <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Visa Name *</label>
                                <input type="text" name="name" class="wp-input" value="{{ $visa->name }}" required>
                            </div>
                            <div class="col-sm-3">
                                <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Price (AED) *</label>
                                <input type="number" name="price" class="wp-input" value="{{ $visa->price }}" required min="0" step="0.01">
                            </div>
                            <div class="col-sm-3">
                                <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Processing Days</label>
                                <input type="number" name="processing_days" class="wp-input" value="{{ $visa->processing_days }}" min="1" max="90">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Description</label>
                            <textarea name="description" class="wp-input" rows="2" placeholder="Brief description of this visa type">{{ $visa->description }}</textarea>
                        </div>
                        <div class="mb-2">
                            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Required Documents (one per line)</label>
                            <textarea name="required_documents" class="wp-input" rows="3"
                                      placeholder="Valid Passport&#10;UAE Residence Visa&#10;Passport Photo">{{ $visa->required_documents ? implode("\n", $visa->required_documents) : '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:5px;">Status</label>
                            <select name="isActive" class="wp-select">
                                <option value="1" {{ $visa->isActive ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$visa->isActive ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="wp-btn wp-btn-primary wp-btn-sm">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                            <button type="button" onclick="hideEditVisa({{ $visa->id }})" class="wp-btn wp-btn-secondary wp-btn-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:32px; text-align:center; color:var(--wp-text-muted);">
                <i class="fas fa-passport" style="font-size:28px; display:block; margin-bottom:10px; color:var(--wp-border);"></i>
                No Saudi Visa types defined yet.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Add Visa Form --}}
    <div class="col-lg-5">
        <div class="wp-card" style="position:sticky; top:85px;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--wp-border-light);">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--wp-text);">
                    <i class="fas fa-plus" style="color:var(--wp-primary); margin-right:8px;"></i>
                    Add New Visa Type
                </h3>
            </div>
            <div style="padding:20px;">
                <form action="{{ route('manager.saudi-visas.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom:13px;">
                        <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:6px;">Visa Name *</label>
                        <input type="text" name="name" class="wp-input" placeholder="e.g. 1-Year Multiple Entry" required value="{{ old('name') }}">
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:6px;">Price (AED) *</label>
                            <input type="number" name="price" class="wp-input" placeholder="450" required min="0" step="0.01" value="{{ old('price') }}">
                        </div>
                        <div class="col-6">
                            <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:6px;">Processing Days</label>
                            <input type="number" name="processing_days" class="wp-input" placeholder="3" min="1" max="90" value="{{ old('processing_days', 3) }}">
                        </div>
                    </div>
                    <div style="margin-bottom:13px;">
                        <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:6px;">Description</label>
                        <textarea name="description" class="wp-input" rows="2" placeholder="Brief description of this visa type">{{ old('description') }}</textarea>
                    </div>
                    <div style="margin-bottom:18px;">
                        <label style="font-size:11px; font-weight:600; color:var(--wp-text-muted); text-transform:uppercase; display:block; margin-bottom:6px;">Required Documents</label>
                        <textarea name="required_documents" class="wp-input" rows="4"
                                  placeholder="One per line, e.g.:&#10;Valid Passport (6+ months)&#10;UAE Residence Visa&#10;Passport-size Photo&#10;Return Flight Ticket">{{ old('required_documents') }}</textarea>
                        <small style="font-size:11px; color:var(--wp-text-muted);">Enter each document on a new line</small>
                    </div>
                    <button type="submit" class="wp-btn wp-btn-primary w-100">
                        <i class="fas fa-plus me-2"></i> Add Visa Type
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showEditVisa(id) {
    document.querySelector('.visa-view-' + id).style.display = 'none';
    document.querySelector('.visa-edit-' + id).style.display = 'block';
}
function hideEditVisa(id) {
    document.querySelector('.visa-view-' + id).style.display = 'block';
    document.querySelector('.visa-edit-' + id).style.display = 'none';
}
</script>

@endsection
