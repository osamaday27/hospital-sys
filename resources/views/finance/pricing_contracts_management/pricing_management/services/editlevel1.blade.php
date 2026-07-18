@extends('layouts.master')

@section('title')
    {{ trans('service_settings.edit_level') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('service_settings.edit_level') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('service_management_level1.index') }}">{{ trans('service_settings.levels') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('service_settings.edit_level') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Title -->

    <!-- Start Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form
                        action="{{ route('service_management_level1.update', ['service_management_level1' => $level->id]) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            
                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('service_settings.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar', $level->getTranslation('name', 'ar')) }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('service_settings.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en', $level->getTranslation('name', 'en')) }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6"> <!-- Hospital Code -->
                                <div class="mb-3">
                                    <label for="hospital_code"
                                        class="form-label">{{ trans('service_settings.hospital_code') }}</label>
                                    <input type="text" name="hospital_code" id="hospital_code" class="form-control"
                                        value="{{ old('hospital_code', $level->hospital_code) }}" required>
                                    @error('hospital_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6"> <!-- Level Type -->
                                <div class="mb-3">
                                    <label for="level_type"
                                        class="form-label">{{ trans('service_settings.level_type') }}</label>
                                    <select name="level_type" id="level_type" class="js-example-basic-single" required>
                                        <option value="">{{ trans('service_settings.select_level_type') }}</option>
                                        <option value="outpatient"
                                            {{ $level->level_type == 'outpatient' ? 'selected' : '' }}>
                                            {{ trans('service_settings.outpatient_clinics') }}
                                        </option>
                                        <option value="laboratory"
                                            {{ $level->level_type == 'laboratory' ? 'selected' : '' }}>
                                            {{ trans('service_settings.laboratory') }}
                                        </option>
                                        <option value="radiology"
                                            {{ $level->level_type == 'radiology' ? 'selected' : '' }}>
                                            {{ trans('service_settings.radiology') }}
                                        </option>
                                        <option value="emergency"
                                            {{ $level->level_type == 'emergency' ? 'selected' : '' }}>
                                            {{ trans('service_settings.emergency') }}
                                        </option>
                                        <option value="inpatient"
                                            {{ $level->level_type == 'inpatient' ? 'selected' : '' }}>
                                            {{ trans('service_settings.inpatient') }}
                                        </option>
                                        <option value="operations"
                                            {{ $level->level_type == 'operations' ? 'selected' : '' }}>
                                            {{ trans('service_settings.operations') }}
                                        </option>
                                        <option value="marketing"
                                            {{ $level->level_type == 'marketing' ? 'selected' : '' }}>
                                            {{ trans('service_settings.marketing') }}
                                        </option>
                                        <option value="other" {{ $level->level_type == 'other' ? 'selected' : '' }}>
                                            {{ trans('service_settings.other') }}
                                        </option>
                                    </select>
                                    @error('level_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Status -->
                                <div class="mb-3" style="display: block ruby;padding: 10px;">
                                    <label class="form-label">{{ trans('service_settings.status') }}</label>
                                    <div class="form-check">
                                        <input type="radio" name="status" id="status_active" class="form-check-input"
                                            value="active" {{ $level->status == 'active' ? 'checked' : '' }} required>
                                        <label for="status_active"
                                            class="form-check-label">{{ trans('service_settings.active') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="status" id="status_inactive" class="form-check-input"
                                            value="inactive" {{ $level->status == 'inactive' ? 'checked' : '' }}>
                                        <label for="status_inactive"
                                            class="form-check-label">{{ trans('service_settings.inactive') }}</label>
                                    </div>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                        </div>
                        <!-- English Name -->







                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">{{ trans('service_settings.update') }}</button>
                            <a href="{{ route('service_management_level1.index') }}"
                                class="btn btn-secondary">{{ trans('service_settings.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Form -->
@endsection
