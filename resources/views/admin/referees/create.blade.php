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
                <div class="d-sm-flex align-items-center justify-content-between ">
                    <h1 class="h3 mb-0 text-gray-800">{{ __('Create Referee') }}</h1>
                    <a href="{{ route('admin.referees.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.referees.store') }}" method="POST">
                    @csrf
                <div class="form-group row">
                    <label for="refereeName" class="col-sm-2 col-form-label"><strong>{{ __('Referee Name') }} :</strong></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="refereeName"  name="refereeName" />
                    </div>
                </div> 
                <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><strong>{{ __('Referrer Name') }} :</strong><span style="text-color:red">*</span> </label>
                           <div class="col-sm-10">
                        <select class="form-control" name="referrerID" id="referrerID" required>
                             <option value="" selected disabled>Select Referrer Name</option>
                            @foreach ($referrers as $referrer)
                            <option value="{{ $referrer->id }}">{{ $referrer->referrerName }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                 <div class="form-group row">
                        <label for="refereeEmail" class="col-sm-2 col-form-label"><strong>{{ __('Referrer Email') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="email" class="form-control" id="refreeEmail"  name="refereeEmail"  />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="refereeMobile" class="col-sm-2 col-form-label"><strong>{{ __('Referrer Mobile') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="refreeMobile"  name="refereeMobile"  />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="refereeAddress" class="col-sm-2 col-form-label"><strong>{{ __('Referrer Address') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="refereeAddress"  name="refereeAddress"  />
                    </div>
                 </div> 
                 
                 <div class="form-group row">
                        <label class="col-sm-2 col-form-label"><strong>{{ __('Loan Type') }} :</strong><span style="text-color:red">*</span> </label>
                           <div class="col-sm-10">
                        <select class="form-control" name="tolID" id="tolID" required>
                            <option value="" selected disabled>Select Loan Type</option>
                            @foreach ($loantypes as $loantype)
                            <option value="{{ $loantype->id }}">{{ $loantype->tolName }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                 <div class="form-group row">
        <label for="comment" class="col-sm-2 col-form-label mt-3"><strong>{{ __('Messages') }} :</strong></label>
        <!-- SMS Toggle -->
        <div class="form-check form-switch col-sm-3 mt-4 ">
            
            <input class="form-check-input" type="checkbox" id="smsSwitch" name="sms" value="1" >
            <label class="form-check-label" for="smsSwitch">{{ __('Send SMS') }}</label>
        </div>
        
        <!-- Email Toggle -->
        <div class="form-check form-switch col-sm-6 mt-4">
         
            <input class="form-check-input" type="checkbox" id="emailSwitch" name="email" value="1" checked>
            <label class="form-check-label" for="emailSwitch">{{ __('Send Email') }}</label>
        </div>
    </div>
     <div class="form-group row">
                <label class="col-sm-2 col-form-label"><strong>{{ __('previous State') }} :</strong><span style="text-color:red">*</span> </label>
                    <div class="col-sm-10">
                <select class="form-control" name="previousState" id="previousState" required>
                    <option value="" selected disabled>Select previous State</option>
                    @foreach ($tolvals as $previousState)
                    <option value="{{ $previousState->id }}">{{ $previousState->name }}</option>
                    @endforeach
                </select>
             </div>
            </div>
          <div class="form-group row">
                <label class="col-sm-2 col-form-label"><strong>{{ __('current State') }} :</strong><span style="text-color:red">*</span> </label>
                    <div class="col-sm-10">
                <select class="form-control" name="currentState" id="currentState" required>
                    <option value="" selected disabled>Select current state</option>
                    @foreach ($tolvals as $currentState)
                    <option value="{{ $currentState->id }}">{{ $currentState->name }}</option>
                    @endforeach
                </select>
             </div>
          </div>
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
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