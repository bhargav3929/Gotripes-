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
                    <h1 class="h3 mb-0 text-gray-800">{{ __('Edit Position')}}</h1>
                    <a href="{{ route('admin.positions.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.positions.update', $position->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <div class="form-group">
                    <label for="Convention_Id">{{ __('Convention') }}</label>
                    <select class="form-control" id="country-dropdown" name="Convention_Id">
                        <option value="">--Select Convention--</option>
                        @foreach ($countries as $id => $country)
                            <option value="{{ $id }}" @if($country->id == $position->Convention_Id) selected @endif>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
<div class="form-group">
    <label for="hall_id">{{ __('Hall') }}</label>
    <select class="form-control" id="state-dropdown" name="hall_id">
        <option value="{{ $position->hall_id }}" selected>{{ $position->hall->name }}</option>
        <!-- You can populate Hall options dynamically here if needed -->
    </select>
</div>

                
                    
                     <div class="form-group">
                    <label for="name">{{ __('Position') }}</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $position->name }}" />
                </div>
               
                    
                   
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection
@push('script-alt')
<script>
 
$(document).ready(function() {
 
    $('#country-dropdown').on('change', function() {
            var Convention_Id = this.value;
            console.log(Convention_Id);
             $("#state-dropdown").html('');
            $.ajax({
                url:"{{url('get-states-by-country')}}",
                type: "POST",
                data: {
                    Convention_Id: Convention_Id,
                     _token: '{{csrf_token()}}' 
                },
                
                dataType : 'json',
                success: function(result){
                    $('#state-dropdown').html('<option value="">--Select Hall--</option>'); 
                    $.each(result.states ,function(key,value){
                    $("#state-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
                    $('#city-dropdown').html('<option value="">Select Hall First</option>'); 
                }
            });
         
         
    });    
 
    $('#state-dropdown').on('change', function() {
            var hall_id = this.value;
             $("#city-dropdown").html('');
            $.ajax({
                url:"{{url('get-cities-by-state')}}",
                type: "POST",
                data: {
                    hall_id: hall_id,
                     _token: '{{csrf_token()}}' 
                },
                dataType : 'json',
                success: function(result){
                    $('#city-dropdown').html('<option value="">Select Position</option>'); 
                    $.each(result.cities,function(key,value){
                    $("#city-dropdown").append('<option value="'+value.id+'">'+value.name+'</option>');
                    });
 
                }
            });
         
         
    });
});
</script>
@endpush


