@extends('layouts.manager')
@section('title', 'Edit Umrah Category')
@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-gray-800">Edit Category</h2>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('manager.umrah.categories.update', $category->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isActive" id="isActive" {{ $category->isActive ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
