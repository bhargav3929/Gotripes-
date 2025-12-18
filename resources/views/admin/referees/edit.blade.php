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
                    <h1 class="h3 mb-0 text-gray-800">{{ __('Edit Referee Status')}}</h1>
                    <a href="{{ route('admin.referees.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.referees.update', $referee->id) }}" method="POST">
                    @csrf
                    @method('put')

<div class="form-group row">
    <label class="col-sm-2 col-form-label"><strong>{{ __('previous State') }} :</strong><span style="text-color:red">*</span> </label>
    <div class="col-sm-10">
        <select class="form-control" name="previousState" id="previousState" required>
            <option value="" disabled>Select previous State</option>
            @foreach ($tolvals as $tolval)
                <option value="{{ $tolval->id }}" {{ $referee->previousState == $tolval->id ? 'selected' : '' }}>
                    {{ $tolval->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-2 col-form-label"><strong>{{ __('current State') }} :</strong><span style="text-color:red">*</span> </label>
    <div class="col-sm-10">
        <select class="form-control" name="currentState" id="currentState" required>
            <option value="" disabled>Select current state</option>
            @foreach ($tolvals as $tolval)
                <option value="{{ $tolval->id }}" {{ $referee->currentState == $tolval->id ? 'selected' : '' }}>
                    {{ $tolval->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>


                    
                    <div class="form-group row">
    <label for="refereeName" class="col-sm-2 col-form-label"><strong>{{ __('Name') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="refereeName"  name="refereeName"  value="{{ old('name', $referee->refereeName) }}" />
    </div>
</div>
            <div class="form-group row">
    <label class="col-sm-2 col-form-label"><strong>{{ __('Referrer Name') }} :</strong></label>
    <div class="col-sm-10">
        <select class="form-control" name="referrerID" id="referrerID" required>
            <option value="" disabled>Select Referrer Name</option>
            @foreach ($referrers as $referrer)
            <option value="{{ $referrer->id }}" {{ $referee->referrerID == $referrer->id ? 'selected': ''}}>
                {{ $referrer->referrerName }}
            </option>
            @endforeach
        </select>
    </div>
</div>       
                <div class="form-group row">
                        <label for="refereeEmail" class="col-sm-2 col-form-label"><strong>{{ __('Email') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="email" class="form-control" id="refereeEmail"  name="refereeEmail" value="{{ old('name', $referee->refereeEmail) }}" />
                    </div>
                 </div>
                  <div class="form-group row">
                        <label for="refereeMobile" class="col-sm-2 col-form-label"><strong>{{ __('Mobile') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="refereeMobile"  name="refereeMobile" value="{{ old('name', $referee->refereeMobile) }}" />
                    </div>
                 </div>
                   <div class="form-group row">
                        <label for="refereeAddress" class="col-sm-2 col-form-label"><strong>{{ __('Address') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="refereeAddress"  name="refereeAddress" value="{{ old('name', $referee->refereeAddress) }}" />
                    </div>
                 </div>
                 
  
<style>
    #referrerID {
        pointer-events: none;
        opacity: 0.6;
    }
</style>

                 <div class="form-group row">
    <label class="col-sm-2 col-form-label"><strong>{{ __('Loan Type') }} :</strong><span style="text-color:red">*</span> </label>
    <div class="col-sm-10">
        <select class="form-control" name="tolID" id="tolID" required>
            <option value="" disabled>Select Loan Type</option>
            @foreach ($loantypes as $loantype)
                <option value="{{ $loantype->id }}" {{ $referee->tolID == $loantype->id ? 'selected' : '' }}>
                    {{ $loantype->tolName }}
                </option>
            @endforeach
        </select>
    </div>
</div>


    <div class="form-group row">
        <label for="comment" class="col-sm-2 col-form-label mt-3"><strong>{{ __('Messages') }} :</strong></label>
        <!-- SMS Toggle -->
        <div class="form-check form-switch col-sm-3 mt-4 ">
            
            <input class="form-check-input" type="checkbox" id="smsSwitch" name="sms" value="1" {{ old('sms', $referee->sms) == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="smsSwitch">{{ __('Send SMS') }}</label>
        </div>
        
        <!-- Email Toggle -->
        <div class="form-check form-switch col-sm-6 mt-4">
         
            <input class="form-check-input" type="checkbox" id="emailSwitch" name="email" value="1" {{ old('email', $referee->email) == '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="emailSwitch">{{ __('Send Email') }}</label>
        </div>
    </div> 
  <div class="form-group row">
                         <label for="account" class="col-sm-2 col-form-label mt-3"><strong>{{ __('Comment') }} :</strong></label>
                          <div class="col-sm-10">
                         <textarea type="text" class="form-control" id="account"  name="account" value="{{ old('name', $referee->account) }}" ></textarea>
                        </div>
                </div>
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection