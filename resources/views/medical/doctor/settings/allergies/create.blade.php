@extends('layouts.master')

@section('title')
    {{ trans('doctor_trans.title') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('doctor_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('setting_allergies.index') }}">{{ trans('doctor_trans.allergies') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('doctor_trans.create') }}</li>
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

                    <form action="{{ route('setting_allergies.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_ar">{{ trans('doctor_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        value="{{ old('name_ar') }}" required>
                                    @error('name_ar')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_en">{{ trans('doctor_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- code -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">{{ trans('doctor_trans.code') }}</label>
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">{{ trans('doctor_trans.description') }}</label>
                                    <input type="text" class="form-control" id="description" name="description"
                                        value="{{ old('description') }}" required>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor">{{ trans('doctor_trans.type') }}</label>
                                    <select class="js-example-basic-single" id="type" name="type"
                                        required>
                                        <option value="physical">{{ trans('doctor_trans.physical') }}</option>
                                        <option value="laboratory">{{ trans('doctor_trans.laboratory') }}</option>
                                        <option value="imaging">{{ trans('doctor_trans.imaging') }}</option>
                                        <option value="other">{{ trans('doctor_trans.other') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor">{{ trans('doctor_trans.severity') }}</label>
                                    <select class="js-example-basic-single" id="severity" name="severity" required>
                                        <option value="low">{{ trans('doctor_trans.mild') }}</option>
                                        <option value="medium">{{ trans('doctor_trans.moderate') }}</option>
                                        <option value="high">{{ trans('doctor_trans.severe') }}</option>
                                    </select>
                                    @error('severity')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                           
                            <!-- Submit and Cancel Buttons -->
                            <div class="form-group text-center" style="margin-top: 10px;">
                                <button type="submit" class="btn btn-primary">{{ trans('doctor_trans.save') }}</button>
                                <a href="{{ route('setting_allergies.index') }}"
                                    class="btn btn-secondary">{{ trans('doctor_trans.cancel') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
