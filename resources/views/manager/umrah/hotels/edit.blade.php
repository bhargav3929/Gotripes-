@extends('layouts.manager')
@section('title', 'Edit Umrah Hotel')
@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-gray-800">Edit Hotel</h2>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('manager.umrah.hotels.update', $hotel->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $hotel->name }}" required>
                    </div>
                    <div class="col-md-3">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="{{ $hotel->city }}">
                    </div>
                    <div class="col-md-3">
                        <label>Star Rating</label>
                        <input type="number" name="star_rating" class="form-control" min="1" max="7" value="{{ $hotel->star_rating }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="{{ $hotel->address }}">
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $hotel->description }}</textarea>
                </div>
                <div class="mb-3">
                    <label>Amenities (Comma separated)</label>
                    <input type="text" name="amenities" class="form-control" value="{{ is_array($hotel->amenities) ? implode(', ', $hotel->amenities) : '' }}">
                </div>
                <div class="mb-3">
                    <label>Add More Images (Leave blank to keep existing)</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    @if(is_array($hotel->images))
                        <div class="mt-2 d-flex gap-2">
                            @foreach($hotel->images as $img)
                                <img src="{{ $img }}" width="50" height="50" style="object-fit: cover;">
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isActive" id="isActive" {{ $hotel->isActive ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Hotel</button>
            </form>
        </div>
    </div>
</div>
@endsection
