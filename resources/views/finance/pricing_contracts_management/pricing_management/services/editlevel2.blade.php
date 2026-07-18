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
                                href="{{ route('service_management_level2.index') }}">{{ trans('service_settings.levels') }}</a>
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

                    <form action="{{ route('service_management_level2.update','test') }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                           <input type="hidden" name="id" id="id" value="{{$level->id}}">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="level1" class="form-label">{{ trans('service_settings.select_level1') }}</label>
                                    <select name="level1_id" id="level1" class="form-control">
                                        <option value="">{{ trans('service_settings.select_level1') }}</option>
                                        @foreach($level1 as $lvl)
                                            <option value="{{ $lvl->id }}" 
                                                {{ $level->level_id == $lvl->id ? 'selected' : '' }}>
                                                {{ $lvl->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('level1')
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
