@extends('layouts.master')

@section('title')
    {{ trans('service_settings.create_level') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('service_settings.create_level') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('service_management_level1.index') }}">{{ trans('service_settings.levels') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('service_settings.create_level') }}</li>
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
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('service_management_level1.store') }}" method="POST">
                        @csrf
                        <div class="row">
                           
                            <div class="col-md-6">

                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('service_settings.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar') }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <!-- English Name -->

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('service_settings.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">

                                <!-- Hospital Code -->
                                <div class="mb-3">
                                    <label for="hospital_code"
                                        class="form-label">{{ trans('service_settings.hospital_code') }}</label>
                                    <input type="text" name="hospital_code" id="hospital_code" class="form-control"
                                        value="{{ old('hospital_code') }}" required>
                                    @error('hospital_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Level Type -->
                                <div class="mb-3">
                                    <label for="level_type"
                                        class="form-label">{{ trans('service_settings.level_type') }}</label>
                                    <select name="level_type" id="level_type" class="js-example-basic-single" required>
                                        <option value="">{{ trans('service_settings.select_level_type') }}</option>
                                        <option value="outpatient">{{ trans('service_settings.outpatient_clinics') }}
                                        </option>
                                        <option value="laboratory">{{ trans('service_settings.laboratory') }}</option>
                                        <option value="radiology">{{ trans('service_settings.radiology') }}</option>
                                        <option value="emergency">{{ trans('service_settings.emergency') }}</option>
                                        <option value="inpatient">{{ trans('service_settings.inpatient') }}</option>
                                        <option value="operations">{{ trans('service_settings.operations') }}</option>
                                        <option value="marketing">{{ trans('service_settings.marketing') }}</option>
                                        <option value="other">{{ trans('service_settings.other') }}</option>
                                    </select>
                                    @error('level_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                                <!-- Buttons -->
                                <div class="d-flex justify-content-between">
                                    <button type="submit"
                                        class="btn btn-success">{{ trans('service_settings.save') }}</button>
                                    <a href="{{ route('service_management_level1.index') }}"
                                        class="btn btn-secondary">{{ trans('service_settings.cancel') }}</a>
                                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
