@extends('layouts.manager')
@section('title', 'Add Umrah Hotel')
@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-gray-800">Add Hotel</h2>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('manager.umrah.hotels.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label>City</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Star Rating</label>
                        <input type="number" name="star_rating" class="form-control" min="1" max="7" value="3" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label>Amenities (Comma separated)</label>
                    <input type="text" name="amenities" class="form-control" placeholder="Wifi, Breakfast, Pool">
                </div>
                <div class="mb-3">
                    <label>Images</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isActive" id="isActive" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Hotel</button>
            </form>
        </div>
    </div>
</div>
@endsection
