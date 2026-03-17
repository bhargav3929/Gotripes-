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
                    <h1 class="h3 mb-0 text-gray-800">{{ __('Create IndiaJob Page') }}</h1>
                    <a href="{{ route('admin.conventions.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.conventions.store') }}" method="POST">
                    @csrf
             <div class="form-group row">
    <label for="title" class="col-sm-2 col-form-label"><strong>{{ __('Title') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="title"  name="title" />
    </div>
</div>

                 <div class="form-group row">
                        <label for="content" class="col-sm-2 col-form-label"><strong>{{ __('Content') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="content"  name="content"  />
                    </div>
                 </div>
                 

<div class="form-group row">
    <label for="experience" class="col-sm-2 col-form-label"><strong>{{ __('Experience') }} :</strong></label>
     <label for="experience_to" class="col-form-label"> From </label>
    <div class="col-sm-4">
        <input type="number" class="form-control" id="experience" name="experience" value="0" />
    </div>
    <label for="experience_to" class="col-form-label"> To </label>
    <div class="col-sm-4">
        <input type="number" class="form-control" id="experience_to" name="experience_to" value="1" />
    </div>
    <label for="experience_to" class="col-form-label"> Years</label>
</div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label"><strong>{{ __('Description :') }}</strong></label>
                        <div class="col-sm-10">
                        <textarea  class="form-control" style="height:150px" id="description" placeholder="{{ __('Description') }}" name="description"  >
                        </textarea >  
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label"><strong>{{ __('Location') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="location"  name="location"  />
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="workShift" class="col-sm-2 col-form-label"><strong>{{ __('Work Shift') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="workShift"  name="workShift"  />
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="essentialJobFunctions" class="col-sm-2 col-form-label"><strong>{{ __('Essential Job Functions') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control"style="height:150px" id="essentialJobFunctions"  name="essentialJobFunctions"  ></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="technicalSkillsAndKnowledge" class="col-sm-2 col-form-label"><strong>{{ __('Technical Skills And Knowledge') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="technicalSkillsAndKnowledge"  name="technicalSkillsAndKnowledge"  ></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="otherSkillsAndAbilities" class="col-sm-2 col-form-label"><strong>{{ __('Other Skills And Abilities') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="otherSkillsAndAbilities"  name="otherSkillsAndAbilities"  ></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="experienceRequired" class="col-sm-2 col-form-label"><strong>{{ __('Desired Candidate Profile') }}:</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="experienceRequired"  name="experienceRequired"  ></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="perksAndBenefits" class="col-sm-2 col-form-label"><strong>{{ __('Perks And Benefits') }}:</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="perksAndBenefits"  name="perksAndBenefits"  ></textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="education" class="col-sm-2 col-form-label"><strong>{{ __('Education') }}:</strong></label>
                        <div class="col-sm-10">
                        <input class="form-control" id="education"  name="education"  />
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