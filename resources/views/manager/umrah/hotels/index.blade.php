@extends('layouts.manager')
@section('title', 'Umrah Hotels')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-gray-800">Umrah Hotels</h2>
        <a href="{{ route('manager.umrah.hotels.create') }}" class="btn btn-primary">Add Hotel</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hotels as $hotel)
                    <tr>
                        <td>
                            @if(is_array($hotel->images) && count($hotel->images) > 0)
                                <img src="{{ $hotel->images[0] }}" alt="Hotel" width="50" height="50" style="object-fit: cover;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $hotel->name }}</td>
                        <td>{{ $hotel->city }}</td>
                        <td>{{ $hotel->star_rating }} Stars</td>
                        <td>
                            @if($hotel->isActive)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Disabled</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('manager.umrah.hotels.edit', $hotel->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('manager.umrah.hotels.destroy', $hotel->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this hotel?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $hotels->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
