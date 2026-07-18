@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.title') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('inpatient_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('room_settings.index') }}">{{ trans('inpatient_trans.beds') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.create_beds') }}</li>
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

                    <form action="{{ route('bed_settings.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ trans('inpatient_trans.name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type Select -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">{{ trans('inpatient_trans.type') }}</label>
                                    <select class="js-example-basic-single" id="type" name="type" required>
                                        <option value="">{{ trans('inpatient_trans.select_type') }}</option>
                                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.general') }}</option>
                                        <option value="icu" {{ old('type') == 'icu' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.icu') }}</option>
                                        <option value="nursery"
                                            {{ old('type') == 'nursery' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.nursery') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Manager Select -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manager_id">{{ trans('inpatient_trans.room') }}</label>
                                    <select class="js-example-basic-single" id="room_id"
                                        name="room_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_room') }}
                                        </option>
                                        @foreach ($rooms as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('room_id') == $data->id ? 'selected' : '' }}>
                                                {{
                                                 trans('inpatient_trans.room'). ' :- '
                                                 .$data->name .
                                                    ' - ' .  trans('inpatient_trans.nursing_station'). ' :- '.
                                                    ($data->nursingstation
                                                        ? (App::getLocale() == 'ar'
                                                            ? $data->nursingstation->name_ar
                                                            : $data->nursingstation->name_en)
                                                        : trans('inpatient_trans.no_nursing_station')) .
                                                    ' - ' .
                                                      trans('inpatient_trans.floor'). ' :- '.
                                                    ($data->floor
                                                        ? (App::getLocale() == 'ar'
                                                            ? $data->floor->name_ar
                                                            : $data->floor->name_en)
                                                        : trans('inpatient_trans.no_floor')) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('room_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>



                        <!-- Submit and Cancel Buttons -->
                        <div class="form-group text-center" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">{{ trans('inpatient_trans.save') }}</button>
                            <a href="{{ route('bed_settings.index') }}"
                                class="btn btn-secondary">{{ trans('inpatient_trans.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
