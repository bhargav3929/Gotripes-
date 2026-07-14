@extends('layouts.admin')

@section('title', 'Manage Saudi Visa Types')

@section('page-title', 'Manage Saudi Visas')

@section('content')
<div class="row">
    <!-- List of Saudi Visa Types -->
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 mb-4 animate-fade-in">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title"><i class="fas fa-passport me-2"></i>Saudi Visa Types & Pricing</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Visa Name</th>
                                <th>Price (AED)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visaTypes as $visa)
                            <tr>
                                <form action="{{ route('admin.saudi-visas.update', $visa->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <td>
                                        <input type="text" name="name" value="{{ $visa->name }}" class="form-control form-control-sm" required>
                                    </td>
                                    <td>
                                        <input type="number" name="price" value="{{ $visa->price }}" class="form-control form-control-sm" required min="0" step="0.01">
                                    </td>
                                    <td>
                                        <select name="isActive" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="1" {{ $visa->isActive ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ !$visa->isActive ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-sm me-1" title="Save changes"><i class="fas fa-save"></i> Save</button>
                                </form>
                                        <form action="{{ route('admin.saudi-visas.destroy', $visa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this visa type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Deactivate"><i class="fas fa-ban"></i></button>
                                        </form>
                                    </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No Saudi Visa Types defined.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Visa Form -->
    <div class="col-lg-4">
        <div class="card shadow-lg border-0 animate-fade-in">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="fas fa-plus me-2"></i>Add Visa Type</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.saudi-visas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Visa Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. 1-Year Multiple Entry" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (AED) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="e.g. 450" required min="0" step="0.01" value="{{ old('price') }}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>Add Visa Type</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
