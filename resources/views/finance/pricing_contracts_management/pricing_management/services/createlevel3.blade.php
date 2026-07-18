@extends('layouts.master')

@section('title')
    {{ trans('service_settings.create_level3') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('service_settings.create_level3') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('service_management_level3.index') }}">{{ trans('service_settings.levels') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('service_settings.create_level3') }}</li>
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

                    <form action="{{ route('service_management_level3.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level2_id"
                                        class="form-label">{{ trans('service_settings.select_level2') }}</label>
                                    <select name="level2_id" id="level2_id"class="js-example-basic-single" required>
                                        <option value="">{{ trans('service_settings.select_level2') }}</option>
                                        @foreach ($level2s as $level2)
                                            <option value="{{ $level2->id }}"
                                                {{ old('level2_id') == $level2->id ? 'selected' : '' }}>
                                                {{ $level2->getTranslation('name', app()->getLocale()) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('level2_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Hospital Code -->
                            <div class="col-md-6">
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
                            <!-- Arabic Name -->
                            <div class="col-md-6">
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

                            <!-- English Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name_en"
                                        class="form-label">{{ trans('service_settings.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Arabic Short Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contract_service_name_ar"
                                        class="form-label">{{ trans('service_settings.contract_service_name_ar') }}</label>
                                    <input type="text" name="contract_service_name_ar" id="contract_service_name_ar"
                                        class="form-control" value="{{ old('contract_service_name_ar') }}" required>
                                    @error('contract_service_name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contract_service_name_en"
                                        class="form-label">{{ trans('service_settings.contract_service_name_en') }}</label>
                                    <input type="text" name="contract_service_name_en" id="contract_service_name_en"
                                        class="form-control" value="{{ old('contract_service_name_en') }}" required>
                                    @error('contract_service_name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Description Arabic -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_ar"
                                        class="form-label">{{ trans('service_settings.description_ar') }}</label>
                                    <textarea name="description_ar" id="description_ar" class="form-control" rows="3">{{ old('description_ar') }}</textarea>
                                    @error('description_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description English -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_en"
                                        class="form-label">{{ trans('service_settings.description_en') }}</label>
                                    <textarea name="description_en" id="description_en" class="form-control" rows="3">{{ old('description_en') }}</textarea>
                                    @error('description_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Age Restriction -->
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="age_restriction"
                                                class="form-label">{{ trans('service_settings.age_restriction_from') }}</label>
                                            <input type="text" name="age_restriction" id="age_restriction"
                                                class="form-control" value="{{ old('age_restriction') }}">
                                            @error('age_restriction')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="mb-3">
                                            <label for="age_restriction"
                                                class="form-label">{{ trans('service_settings.age_restriction_to') }}</label>
                                            <input type="text" name="age_restriction" id="age_restriction"
                                                class="form-control" value="{{ old('age_restriction') }}">
                                            @error('age_restriction')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Repeat Duration -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="repeat_duration"
                                        class="form-label">{{ trans('service_settings.repeat_duration') }}</label>
                                    <input type="text" name="repeat_duration" id="repeat_duration"
                                        class="form-control" value="{{ old('repeat_duration') }}">
                                    @error('repeat_duration')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- confidentiality -->

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('service_settings.confidentiality') }}</label>
                                    <div>
                                        <!-- مستوى السرية - عالي -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="confidentiality"
                                                value="high" id="high">
                                            <label class="form-check-label"
                                                for="high">{{ trans('service_settings.high') }}</label>
                                        </div>
                                        <!-- مستوى السرية - عادي -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="confidentiality" checked
                                                value="normal" id="normal">
                                            <label class="form-check-label"
                                                for="normal">{{ trans('service_settings.normal') }}</label>
                                        </div>
                                        <!-- مستوى السرية - منخفض -->
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="confidentiality"
                                                value="low" id="low">
                                            <label class="form-check-label"
                                                for="low">{{ trans('service_settings.low') }}</label>
                                        </div>
                                    </div>
                                    @error('confidentiality')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                            <!-- Patient Type -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('service_settings.patient_type') }}</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="patient_type"
                                                value="male" id="male">
                                            <label class="form-check-label"
                                                for="male">{{ trans('service_settings.male') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="patient_type"
                                                value="female" id="female">
                                            <label class="form-check-label"
                                                for="female">{{ trans('service_settings.female') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="patient_type" checked
                                                value="both" id="both">
                                            <label class="form-check-label"
                                                for="both">{{ trans('service_settings.both') }}</label>
                                        </div>
                                    </div>
                                    @error('patient_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>



                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">{{ trans('service_settings.save') }}</button>
                            <a href="{{ route('service_management_level3.index') }}"
                                class="btn btn-secondary">{{ trans('service_settings.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
