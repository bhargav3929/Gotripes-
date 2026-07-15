@extends('layouts.manager')
@section('title', 'Add Umrah Category')
@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-gray-800">Add Category</h2>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('manager.umrah.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isActive" id="isActive" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
