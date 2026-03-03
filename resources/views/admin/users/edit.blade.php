@extends('layouts.admin')

@section('title', 'Edit User')

@section('page-title', 'Edit User')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->name }}
                        </h3>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @php
                        $currentAccessType = $user->access_type ?? ($user->isAdmin() ? 'full' : 'specific');
                    @endphp

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')

                        {{-- Account Details Section --}}
                        <div class="mb-4">
                            <h6 class="section-label">
                                Account Details
                            </h6>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="name" class="form-label">Username <span class="text-required">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}"
                                           placeholder="Enter username" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-required">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $user->email) }}"
                                           placeholder="Enter email address" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label">Change Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                               id="password" name="password" placeholder="Leave blank to keep current">
                                        <button class="btn btn-outline-primary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Only fill this if you want to change the password</small>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

                        {{-- Current Role Info --}}
                        <div class="mb-4">
                            <h6 class="section-label">
                                Current Roles
                            </h6>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @forelse($user->roles as $role)
                                    <span class="badge rounded-pill px-3 py-1 badge-gold">
                                        <i class="fas fa-tag me-1"></i>{{ $role->title }}
                                    </span>
                                @empty
                                    <span class="text-muted fs-7">No roles assigned</span>
                                @endforelse
                            </div>
                        </div>

                        <hr class="section-divider">

                        {{-- Access Control Section --}}
                        <div class="mb-4">
                            <h6 class="section-label">
                                Access Control
                            </h6>

                            <label class="form-label mb-3">Access Type <span class="text-required">*</span></label>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <div class="access-type-card" id="fullAccessCard" onclick="selectAccessType('full')">
                                        <input type="radio" name="access_type" value="full" id="accessFull"
                                               {{ old('access_type', $currentAccessType) === 'full' ? 'checked' : '' }} class="d-none">
                                        <div class="access-card-inner">
                                            <div class="access-card-icon">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block" class="text-light">Full Access</strong>
                                                <small class="text-muted">Complete admin privileges to all modules</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="access-type-card" id="specificAccessCard" onclick="selectAccessType('specific')">
                                        <input type="radio" name="access_type" value="specific" id="accessSpecific"
                                               {{ old('access_type', $currentAccessType) === 'specific' ? 'checked' : '' }} class="d-none">
                                        <div class="access-card-inner">
                                            <div class="access-card-icon">
                                                <i class="fas fa-key"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block" class="text-light">Specific Access</strong>
                                                <small class="text-muted">Limited to selected modules only</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Module Checkboxes (shown when Specific Access is selected) --}}
                            <div id="moduleSection" style="display: none;">
                                <label class="form-label mb-3">Select Modules <span class="text-required">*</span></label>
                                <div class="row g-3">
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="module-checkbox-card">
                                            <input type="checkbox" name="modules[]" value="uaeactivities"
                                                   {{ old('modules') ? (in_array('uaeactivities', old('modules', [])) ? 'checked' : '') : ($user->hasRole('Activities Manager') ? 'checked' : '') }}>
                                            <div class="module-card-inner">
                                                <i class="fas fa-map-marked-alt module-icon"></i>
                                                <span>UAE Activities</span>
                                                <i class="fas fa-check check-icon"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="module-checkbox-card">
                                            <input type="checkbox" name="modules[]" value="announcements"
                                                   {{ old('modules') ? (in_array('announcements', old('modules', [])) ? 'checked' : '') : ($user->hasRole('Announcements Manager') ? 'checked' : '') }}>
                                            <div class="module-card-inner">
                                                <i class="fas fa-bullhorn module-icon"></i>
                                                <span>Announcements</span>
                                                <i class="fas fa-check check-icon"></i>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <label class="module-checkbox-card">
                                            <input type="checkbox" name="modules[]" value="homepageads"
                                                   {{ old('modules') ? (in_array('homepageads', old('modules', [])) ? 'checked' : '') : ($user->hasRole('Carousel Manager') ? 'checked' : '') }}>
                                            <div class="module-card-inner">
                                                <i class="fas fa-images module-icon"></i>
                                                <span>Travel Ads</span>
                                                <i class="fas fa-check check-icon"></i>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

                        {{-- Submit --}}
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary order-2 order-sm-1">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary order-1 order-sm-2">
                                <i class="fas fa-save me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Access card and module card styles now in layout --}}

@push('scripts')
<script>
    function selectAccessType(type) {
        document.querySelectorAll('.access-type-card').forEach(function(card) {
            card.classList.remove('selected');
        });

        if (type === 'full') {
            document.getElementById('accessFull').checked = true;
            document.getElementById('fullAccessCard').classList.add('selected');
            document.getElementById('moduleSection').style.display = 'none';
            document.querySelectorAll('#moduleSection input[type="checkbox"]').forEach(function(cb) {
                cb.checked = false;
            });
        } else {
            document.getElementById('accessSpecific').checked = true;
            document.getElementById('specificAccessCard').classList.add('selected');
            document.getElementById('moduleSection').style.display = 'block';
        }
    }

    // Password toggle
    document.getElementById('togglePassword').addEventListener('click', function() {
        var pwd = document.getElementById('password');
        var icon = this.querySelector('i');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Initialize state on page load
    document.addEventListener('DOMContentLoaded', function() {
        var checked = document.querySelector('input[name="access_type"]:checked');
        if (checked) {
            selectAccessType(checked.value);
        } else {
            selectAccessType('full');
        }
    });
</script>
@endpush
