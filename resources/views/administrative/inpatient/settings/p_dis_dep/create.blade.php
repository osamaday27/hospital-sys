@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.title') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('inpatient_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('adm_res_pa_discharge.index') }}">{{ trans('inpatient_trans.adm_res_pa_discharge') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.create_adm_res_pa_discharge') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Start Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Display Success or Error Messages -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('adm_res_pa_discharge.store') }}" method="POST">
                        @csrf
                        <!-- Display Departments as Checkboxes -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <label>{{ trans('inpatient_trans.departments') }}</label>
                                <div class="row">
                                    @foreach ($departments as $department)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="department_{{ $department->id }}" 
                                                    name="departments[]" value="{{ $department->id }}"
                                                    @if(in_array($department->id, $existingData->pluck('hr_department_id')->toArray())) checked @endif>
                                                <label class="form-check-label" for="department_{{ $department->id }}">
                                                    {{ (App::getLocale() == 'ar'
                                                            ? $department->name_ar
                                                            : $department->name_en) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('departments')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="form-group text-center" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">{{ trans('inpatient_trans.save') }}</button>
                            <a href="{{ route('adm_res_pa_discharge.index') }}"
                                class="btn btn-secondary">{{ trans('inpatient_trans.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
