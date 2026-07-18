@extends('layouts.master_create')

@section('title')
    {{ trans('outpatient_trans.settings') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('outpatient_trans.settings') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('management_contract_frist_level.index') }}">{{ trans('outpatient_trans.settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('outpatient_trans.create_clinc') }}</li>
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

                    <!-- عرض رسائل النجاح أو الخطأ -->
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


                    <form action="{{ route('outpatient_settings.store') }}"method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-12 text-center" style="margin-bottom: 10px;margin-top: 40px;">
                                <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                    <img src="{{ asset('assets/images/chat-bg-pattern.png') }}"
                                        class="rounded-circle avatar-lg img-thumbnail user-profile-image"
                                        alt="user-profile-image">
                                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                        <input id="profile-img-file-input" type="file" class="profile-img-file-input"
                                            accept="image/png, image/jpeg" name="logo" >
                                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                            <span class="avatar-title rounded-circle bg-light text-body">
                                                <i class="ri-camera-fill"></i>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-muted mb-0">{{ trans('outpatient_trans.upload_logo') }}</p>
                            </div>

                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar"
                                        class="form-label">{{ trans('outpatient_trans.name_ar') }}</label>
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
                                    <label for="name_en"
                                        class="form-label">{{ trans('outpatient_trans.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Company Code -->
                                <div class="mb-3">
                                    <label for="clinc_code"
                                        class="form-label">{{ trans('outpatient_trans.clinc_code') }}</label>
                                    <input type="text" name="clinic_code" id="clinic_code" class="form-control"
                                        value="{{ old('clinc_code') }}" required>
                                    @error('clinc_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Price List -->
                                <div class="mb-3">
                                    <label for="specialty_id"
                                        class="form-label">{{ trans('outpatient_trans.specialty') }}</label>
                                    <select name="specialty_id" id="specialty_id" class="js-example-basic-single" required>
                                        <option value="">{{ trans('outpatient_trans.specialty') }}</option>
                                        @foreach ($specialties as $data)
                                            <option value="{{ $data->id }}">
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Building -->
                                <div class="mb-3">
                                    <label for="building_id"
                                        class="form-label">{{ trans('outpatient_trans.building') }}</label>
                                    <select name="building_id" id="building_id" class="js-example-basic-single" required>
                                        <option value="">{{ trans('outpatient_trans.select_building') }}</option>
                                        @foreach ($buildings as $building)
                                            <option value="{{ $building->id }}">
                                                {{ App::getLocale() == 'ar' ? $building->name_ar : $building->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('building_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Floor -->
                                <div class="mb-3">
                                    <label for="floor_id" class="form-label">{{ trans('outpatient_trans.floor') }}</label>
                                    <select name="floor_id" id="floor_id" class="js-example-basic-single" required>
                                        <option value="">{{ trans('outpatient_trans.select_floor') }}</option>
                                    </select>
                                    @error('floor_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="submit"
                                    class="btn btn-success">{{ trans('outpatient_trans.save') }}</button>
                                <a href="{{ route('management_contract_frist_level.index') }}"
                                    class="btn btn-secondary">{{ trans('outpatient_trans.cancel') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
