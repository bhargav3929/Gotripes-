@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<!-- Content Row -->
        <div class="card shadow">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">{{ __('edit')}}</h1>
                    <a href="{{ route('admin.tols.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tols.update', $tol->id) }}" method="POST">
                    @csrf
                    @method('put')
                    
                    <div class="form-group row">
    <label for="tolName" class="col-sm-2 col-form-label"><strong>{{ __('Loan Name') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="tolName"  name="tolName"  value="{{ old('name', $tol->tolName) }}" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="comment" class="col-sm-2 col-form-label"><strong>{{ __('Comment') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="comment"  name="comment" value="{{ old('name', $tol->comment) }}" />
                    </div>
                 </div>
               


               
                
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection