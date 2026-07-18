@extends('layouts.master')

@section('title')
    {{ trans('specialties_trans.edit_title') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('specialties_trans.edit_title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('management_contract_frist_level.index') }}">{{ trans('specialties_trans.setting') }}</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('specialties_settings.index') }}">{{ trans('specialties_trans.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('specialties_trans.edit_title') }}</li>
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

                    @if (session('error'))
                        <div class="alert alert-error">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('specialties_settings.update', $specialty->id) }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row">
                               <div class="col-12 text-center" style="margin-bottom: 10px;margin-top: 40px;">
                                <div class="profile-user position-relative d-inline-block mx-auto mb-2">
                                    <!-- Display existing logo if available -->
                                    <img src="{{ asset($specialty->logo) }}" class="rounded-circle avatar-lg img-thumbnail user-profile-image" alt="user-profile-image">
                                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                        <input id="profile-img-file-input" type="file" class="profile-img-file-input"
                                            accept="image/png, image/jpeg" name="logo">
                                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                            <span class="avatar-title rounded-circle bg-light text-body">
                                                <i class="ri-camera-fill"></i>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                <p class="text-muted mb-0">{{ trans('specialties_trans.upload_logo') }}</p>
                            </div>

                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name_ar" class="form-label">{{ trans('specialties_trans.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar', $specialty->name_ar) }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- English Name -->
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('specialties_trans.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en', $specialty->name_en) }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                                <a href="{{ route('specialties_settings.index') }}"
                                    class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Form -->
@endsection
