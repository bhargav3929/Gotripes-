@extends('layouts.manager')

@section('title', 'Manage Package Departures')

@section('page-title')
    Manage Departures: {{ $package->title }}
@endsection

@section('content')
<div class="row">
    <!-- List of departures -->
    <div class="col-lg-8">
        <div class="card shadow-lg border-0 mb-4 animate-fade-in">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title"><i class="fas fa-calendar-alt me-2"></i>Departure Dates</h3>
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
                                <th>Departure Date</th>
                                <th>Day</th>
                                <th>Seats Available</th>
                                <th>Seats Booked</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departures as $dep)
                            <tr>
                                <td>{{ $dep->departure_date->format('d M Y') }}</td>
                                <td><span class="badge bg-info">Wednesday</span></td>
                                <td>
                                    <form action="{{ route('manager.umrah-packages.departures.update', [$package->id, $dep->id]) }}" method="POST" class="d-inline-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="seats_available" value="{{ $dep->seats_available }}" class="form-control form-control-sm" style="width: 80px;" min="0">
                                </td>
                                <td>{{ $dep->seats_booked }}</td>
                                <td>
                                        <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                            <option value="available" {{ $dep->status == 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="sold_out" {{ $dep->status == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                            <option value="inactive" {{ $dep->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm ms-1" title="Save changes"><i class="fas fa-save"></i></button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{ route('manager.umrah-packages.departures.destroy', [$package->id, $dep->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this departure date?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No departure dates configured for this package.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Departure Form -->
    <div class="col-lg-4">
        <div class="card shadow-lg border-0 animate-fade-in">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title"><i class="fas fa-plus me-2"></i>Add Departure Date</h3>
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

                <form action="{{ route('manager.umrah-packages.departures.store', $package->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Departure Date <span class="text-danger">*</span></label>
                        <input type="date" name="departure_date" class="form-control" required value="{{ old('departure_date') }}">
                        <small class="text-muted">Must be a Wednesday.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Seats Available <span class="text-danger">*</span></label>
                        <input type="number" name="seats_available" class="form-control" required min="1" value="{{ old('seats_available', 50) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="available">Available</option>
                            <option value="sold_out">Sold Out</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-plus me-1"></i>Add Departure</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
