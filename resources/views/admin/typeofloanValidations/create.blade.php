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
                <div class="d-sm-flex align-items-center justify-content-between ">
                    <h1 class="h3 mb-0 text-gray-800">{{ __('Create Loan Status') }}</h1>
                    <a href="{{ route('admin.tolvals.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tolvals.store') }}" method="POST">
                    @csrf
             <div class="form-group row">
    <label for="name" class="col-sm-2 col-form-label"><strong>{{ __('Loan Name') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="name"  name="name" />
    </div>
</div> 

                 <div class="form-group row">
                        <label for="comment" class="col-sm-2 col-form-label"><strong>{{ __('Comment') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea  class="form-control" id="comment"  name="comment"  ></textarea>
                    </div>
                 </div>
    {{-- <div class="form-group row">
        <label for="comment" class="col-sm-2 col-form-label mt-3"><strong>{{ __('Messages') }} :</strong></label>
     
        <div class="form-check form-switch col-sm-3 mt-4 ">
            
            <input class="form-check-input" type="checkbox" id="smsSwitch" name="sms" value="1" >
            <label class="form-check-label" for="smsSwitch">{{ __('Send SMS') }}</label>
        </div>
        
   
        <div class="form-check form-switch col-sm-6 mt-4">
         
            <input class="form-check-input" type="checkbox" id="emailSwitch" name="email" value="1" >
            <label class="form-check-label" for="emailSwitch">{{ __('Send Email') }}</label>
        </div>
    </div> --}}


            
                    <button type="submit" class="btn btn-primary btn-block mt-4">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection
@push('style-alt')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endpush

@push('script-alt')
<script src="https://cdn.datatables.net/select/1.2.0/js/dataTables.select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $('.datetimepicker').datetimepicker({
             format: 'YYYY-MM-DD HH:mm',
            //format: 'YYYY-MM-DD',
            locale: 'en',
            sideBySide: true,
            icons: {
            up: 'fas fa-chevron-up',
            down: 'fas fa-chevron-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right'
            },
            stepping: 10
        });
    </script>
@endpush