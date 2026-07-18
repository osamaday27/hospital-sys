@extends('layouts.master')

@section('title')
    {{ trans('doctor_trans.edit_complaint') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('doctor_trans.edit_complaint') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('setting_medical_history.index') }}">{{ trans('doctor_trans.medical_history') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('doctor_trans.edit') }}</li>
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

                    <form action="{{ route('setting_medical_history.update', 'text') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_ar">{{ trans('doctor_trans.name_ar') }}</label>
                                    <input type="hidden" value="{{ $complaints->id }}" name="id" />
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        value="{{ old('name_ar', $complaints->getTranslation('name', 'ar')) }}" required>
                                    @error('name_ar')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_en">{{ trans('doctor_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', $complaints->getTranslation('name', 'en')) }}" required>
                                    @error('name_en')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Code -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">{{ trans('doctor_trans.code') }}</label>
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ old('code', $complaints->code) }}" required>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">{{ trans('doctor_trans.description') }}</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ old('description', $complaints->description) }}" required>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">{{ trans('doctor_trans.type') }}</label>
                                    <select class="js-example-basic-single"  id="type" name="type" required>
                                        <option value="chronic" {{ $complaints->type == 'chronic' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.chronic') }}</option>
                                        <option value="genetic" {{ $complaints->type == 'genetic' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.genetic') }}</option>
                                        <option value="infectious"
                                            {{ $complaints->type == 'infectious' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.infectious') }}</option>
                                        <option value="acute" {{ $complaints->type == 'acute' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.acute') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <!-- Specialization -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specialization">{{ trans('doctor_trans.specialization') }}</label>
                                    <select class="js-example-basic-single"  id="specialization" name="specialization" required>
                                        <option value="">{{ trans('doctor_trans.select_specialization') }}</option>
                                        @foreach ($specialties as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $complaints->specialization == $data->id ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialization')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Severity -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="severity">{{ trans('doctor_trans.severity') }}</label>
                                    <select class="js-example-basic-single"  id="severity" name="severity" required>
                                        <option value="mild" {{ $complaints->severity == 'mild' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.mild') }}</option>
                                        <option value="moderate"
                                            {{ $complaints->severity == 'moderate' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.moderate') }}</option>
                                        <option value="severe" {{ $complaints->severity == 'severe' ? 'selected' : '' }}>
                                            {{ trans('doctor_trans.severe') }}</option>
                                    </select>
                                    @error('severity')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ trans('inpatient_trans.status') }}</label>
                                    <select class="js-example-basic-single"id="status" name="status" required>
                                        <option value="">{{ trans('inpatient_trans.select_status') }}</option>
                                        <option value="active"
                                            {{ old('status', $complaints->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.active') }}
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $complaints->status) == 'inactive' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.inactive') }}
                                        </option>

                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <!-- Submit and Cancel Buttons -->
                            <div class="form-group text-center" style="margin-top: 10px;">
                                <button type="submit" class="btn btn-primary">{{ trans('doctor_trans.save') }}</button>
                                <a href="{{ route('setting_medical_history.index') }}"
                                    class="btn btn-secondary">{{ trans('doctor_trans.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
