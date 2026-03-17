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
                    <a href="{{ route('admin.referrers.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.referrers.update', $referrer->id) }}" method="POST">
                    @csrf
                    @method('put')
                    
                    <div class="form-group row">
    <label for="referrerName" class="col-sm-2 col-form-label"><strong>{{ __('Name') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="referrerName"  name="referrerName"  value="{{ old('name', $referrer->referrerName) }}" />
    </div>
</div>
                 
                <div class="form-group row">
                        <label for="referrerEmail" class="col-sm-2 col-form-label"><strong>{{ __('Email') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="email" class="form-control" id="referrerEmail"  name="referrerEmail" value="{{ old('name', $referrer->referrerEmail) }}" />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="referrerMobile" class="col-sm-2 col-form-label"><strong>{{ __('Mobile') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="referrerMobile"  name="referrerMobile" value="{{ old('name', $referrer->referrerMobile) }}" />
                    </div>
                 </div>
               


               
                
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection