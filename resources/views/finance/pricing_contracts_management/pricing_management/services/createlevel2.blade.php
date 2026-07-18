@extends('layouts.master')

@section('title')
    {{ trans('service_settings.create_level2') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('service_settings.create_level2') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('service_management_level2.index') }}">{{ trans('service_settings.levels') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('service_settings.create_level2') }}</li>
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

                    <form action="{{ route('service_management_level2.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- First Level Selection -->

                           

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
                                    <label for="name_en" class="form-label">{{ trans('service_settings.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level1_id"
                                        class="form-label">{{ trans('service_settings.select_level1') }}</label>
                                    <select name="level1_id" id="level1_id"class="js-example-basic-single" required>
                                        <option value="">{{ trans('service_settings.select_level1') }}</option>
                                        @foreach ($level1s as $level1)
                                            <option value="{{ $level1->id }}"
                                                {{ old('level1_id') == $level1->id ? 'selected' : '' }}>
                                                {{ $level1->getTranslation('name', app()->getLocale()) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('level1_id')
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
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">{{ trans('service_settings.save') }}</button>
                            <a href="{{ route('service_management_level2.index') }}"
                                class="btn btn-secondary">{{ trans('service_settings.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
