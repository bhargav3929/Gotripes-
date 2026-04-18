@extends('layouts.superadmin')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fas fa-user-edit me-2"></i>Edit User</h1>
    <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

<form action="{{ route('superadmin.users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-user me-2"></i>User Details</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="company_staff" {{ old('role', $user->role) === 'company_staff' ? 'selected' : '' }}>Company Staff</option>
                                <option value="company_admin" {{ old('role', $user->role) === 'company_admin' ? 'selected' : '' }}>Company Admin</option>
                                <option value="company_owner" {{ old('role', $user->role) === 'company_owner' ? 'selected' : '' }}>Company Owner</option>
                                <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <select name="company_id" class="form-select @error('company_id') is-invalid @enderror">
                                <option value="">No Company</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Leave blank to keep current">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-outline-secondary w-100">
                        Cancel
                    </a>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>User Info</div>
                <div class="card-body">
                    <small class="text-muted d-block mb-2">
                        <i class="fas fa-calendar me-1"></i>Created: {{ $user->created_at->format('M d, Y') }}
                    </small>
                    <small class="text-muted d-block">
                        <i class="fas fa-clock me-1"></i>Updated: {{ $user->updated_at->format('M d, Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
