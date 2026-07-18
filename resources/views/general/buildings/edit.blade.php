@extends('layouts.master')

@section('title')
    {{ trans('buildings_trans.edit') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('buildings_trans.edit') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('management_contract_frist_level.index') }}">{{ trans('buildings_trans.setting') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('buildings_trans.edit') }}</li>
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

                    @if (session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('building_settings.update',$building_settings->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ $building_settings->id }}">
                                    <label for="name_ar" class="form-label">{{ trans('buildings_trans.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar', $building_settings->name_ar) }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- English Name -->
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('buildings_trans.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en', $building_settings->name_en) }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Floors Count -->
                                <div class="mb-3">
                                    <label for="floors_count"
                                        class="form-label">{{ trans('buildings_trans.floors_count') }}</label>
                                    <input type="number" name="floors_count" id="floors_count" class="form-control"
                                        value="{{ old('floors_count', $building_settings->floors_count) }}">
                                    @error('floors_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ trans('buildings_trans.address') }}</label>
                                    <input type="text" name="address" id="address" class="form-control"
                                        value="{{ old('address', $building_settings->address) }}">
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- latitude -->
                                <div class="mb-3">
                                    <label for="latitude"
                                        class="form-label">{{ trans('buildings_trans.latitude') }}</label>
                                    <input type="text" name="latitude" id="latitude" class="form-control"
                                        value="{{ old('latitude', $building_settings->latitude) }}">
                                    @error('latitude')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- longitude -->
                                <div class="mb-3">
                                    <label for="longitude"
                                        class="form-label">{{ trans('buildings_trans.longitude') }}</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control"
                                        value="{{ old('longitude', $building_settings->longitude) }}">
                                    @error('longitude')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">{{ trans('buttons.update') }}</button>
                                <a href="{{ route('building_settings.index') }}"
                                    class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
