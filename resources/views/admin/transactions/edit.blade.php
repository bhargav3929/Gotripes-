@extends('layouts.admin')

@section('content')
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                    <a href="{{ route('admin.tolvals.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tolvals.update', $tolval->id) }}" method="POST">
                    @csrf
                    @method('put')
                    
                    <div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label"><strong>{{ __('Validation Name') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="name"  name="name"  value="{{ old('name', $tolval->name) }}" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="comment" class="col-sm-2 col-form-label"><strong>{{ __('Comment') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="comment"  name="comment" value="{{ old('name', $tolval->comment) }}" />
                    </div>
                 </div>
    <div class="form-group row">
        <label for="comment" class="col-sm-2 col-form-label mt-3"><strong>{{ __('Messages') }} :</strong></label>
        
        <!-- SMS Toggle -->
        <div class="form-check form-switch col-sm-3 mt-4">
            <input class="form-check-input" type="checkbox" id="smsSwitch" name="sms" value="1" {{ old('sms', $tolval->sms) == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="smsSwitch">{{ __('Send SMS') }}</label>
        </div>
        
        <!-- Email Toggle -->
        <div class="form-check form-switch col-sm-6 mt-4">
            <input class="form-check-input" type="checkbox" id="emailSwitch" name="email" value="1" {{ old('email', $tolval->email) == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="emailSwitch">{{ __('Send Email') }}</label>
        </div>
    </div>


               
                
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection