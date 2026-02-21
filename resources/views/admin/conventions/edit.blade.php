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
                    <a href="{{ route('admin.conventions.index') }}" class="btn btn-primary btn-sm shadow-sm">{{ __('Go Back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.conventions.update', $convention->id) }}" method="POST">
                    @csrf
                    @method('put')
                    
                    <div class="form-group row">
    <label for="title" class="col-sm-2 col-form-label"><strong>{{ __('Title') }} :</strong></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="title"  name="title"  value="{{ old('name', $convention->title) }}" />
    </div>
</div>
                 
                    <div class="form-group row">
                        <label for="content" class="col-sm-2 col-form-label"><strong>{{ __('Content') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="content"  name="content" value="{{ old('name', $convention->content) }}" />
                    </div>
                 </div>
               

<div class="form-group row">
    <label for="experience" class="col-sm-2 col-form-label"><strong>{{ __('Experience') }} :</strong></label>
     <label for="experience_to" class="col-form-label"> From </label>
    <div class="col-sm-4">
        <input type="number" class="form-control" id="experience" name="experience" value="{{ old('name', $convention->experience) }}" />
    </div>
    <label for="experience_to" class="col-form-label"> To </label>
    <div class="col-sm-4">
        <input type="number" class="form-control" id="experience_to" name="experience_to" value="{{ old('name', $convention->experience_to) }}" />
    </div>
    <label for="experience_to" class="col-form-label"> Years</label>
</div>
               
                    <div class="form-group row">
                        <label for="description" class="col-sm-2 col-form-label"><strong>{{ __('Description :') }}</strong></label>
                        <div class="col-sm-10">
                        <textarea  class="form-control" style="height:150px" id="description" placeholder="{{ __('Description') }}" name="description" value="{{ old('name', $convention->description) }}" >
                         {{ old('name', $convention->description) }}
                        </textarea >  
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location" class="col-sm-2 col-form-label"><strong>{{ __('Location') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="location"  name="location"  value="{{ old('name', $convention->location) }}"/>
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="workShift" class="col-sm-2 col-form-label"><strong>{{ __('Work Shift') }} :</strong></label>
                        <div class="col-sm-10">
                        <input type="text" class="form-control" id="workShift"  name="workShift" value="{{ old('name', $convention->workShift) }}" />
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="essentialJobFunctions" class="col-sm-2 col-form-label"><strong>{{ __('Essential Job Functions') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control"style="height:150px" id="essentialJobFunctions"  name="essentialJobFunctions" value=" {{ old('name', $convention->essentialJobFunctions) }}" > {{ old('name', $convention->essentialJobFunctions) }}</textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="technicalSkillsAndKnowledge" class="col-sm-2 col-form-label"><strong>{{ __('Technical Skills And Knowledge') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="technicalSkillsAndKnowledge"  name="technicalSkillsAndKnowledge" value=" {{ old('name', $convention->technicalSkillsAndKnowledge) }}" > {{ old('name', $convention->technicalSkillsAndKnowledge) }}</textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="otherSkillsAndAbilities" class="col-sm-2 col-form-label"><strong>{{ __('Other Skills And Abilities') }} :</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="otherSkillsAndAbilities"  name="otherSkillsAndAbilities" value=" {{ old('name', $convention->otherSkillsAndAbilities) }}" > {{ old('name', $convention->otherSkillsAndAbilities) }}</textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="experienceRequired" class="col-sm-2 col-form-label"><strong>{{ __('Desired Candidate Profile') }}:</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="experienceRequired"  name="experienceRequired"  value=" {{ old('name', $convention->experienceRequired) }}"> {{ old('name', $convention->experienceRequired) }}</textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="perksAndBenefits" class="col-sm-2 col-form-label"><strong>{{ __('Perks And Benefits') }}:</strong></label>
                        <div class="col-sm-10">
                        <textarea class="form-control" style="height:150px" id="perksAndBenefits"  name="perksAndBenefits" value=" {{ old('name', $convention->perksAndBenefits) }}" > {{ old('name', $convention->perksAndBenefits) }}</textarea > 
                        </div>
                    </div>
                 <div class="form-group row">
                        <label for="education" class="col-sm-2 col-form-label"><strong>{{ __('Education') }}:</strong></label>
                        <div class="col-sm-10">
                        <input class="form-control" id="education"  name="education"  value=" {{ old('name', $convention->education) }}"/>
                        </div>
                    </div>
                 
                    <button type="submit" class="btn btn-primary btn-block">{{ __('Save')}}</button>
                </form>
            </div>
        </div>
    

    <!-- Content Row -->

</div>
@endsection