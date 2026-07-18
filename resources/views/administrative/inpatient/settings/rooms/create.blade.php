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
                            <a href="{{ route('room_settings.index') }}">{{ trans('inpatient_trans.room') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.create_room') }}</li>
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

                    <form action="{{ route('room_settings.store') }}" method="POST">
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
                                        <option value="office" {{ old('type') == 'office' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.office') }}</option>
                                        <option value="patient_room" {{ old('type') == 'patient_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.patient_room') }}</option>
                                        <option value="operation_room"
                                            {{ old('type') == 'operation_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.operation_room') }}</option>
                                        <option value="icu" {{ old('type') == 'icu' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.icu') }}</option>
                                        <option value="emergency_room"
                                            {{ old('type') == 'emergency_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.emergency_room') }}</option>
                                        <option value="delivery_room"
                                            {{ old('type') == 'delivery_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.delivery_room') }}</option>
                                        <option value="waiting_room" {{ old('type') == 'waiting_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.waiting_room') }}</option>
                                        <option value="x_ray_room" {{ old('type') == 'x_ray_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.x_ray_room') }}</option>
                                        <option value="laboratory" {{ old('type') == 'laboratory' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.laboratory') }}</option>
                                        <option value="pharmacy" {{ old('type') == 'pharmacy' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.pharmacy') }}</option>
                                        <option value="consultation_room"
                                            {{ old('type') == 'consultation_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.consultation_room') }}</option>
                                        <option value="recovery_room"
                                            {{ old('type') == 'recovery_room' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.recovery_room') }}</option>
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
                                    <label for="manager_id">{{ trans('inpatient_trans.residencies') }}</label>
                                    <select class="js-example-basic-single" id="residency_id"
                                        name="residency_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_residencies') }}
                                        </option>
                                        @foreach ($residencies as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('residencies') == $data->id ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manager_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manager_id">{{ trans('inpatient_trans.nursing_station') }}</label>
                                    <select class="js-example-basic-single" id="nursing_stations_id"
                                        name="nursing_stations_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_nursing_stations') }}
                                        </option>
                                        @foreach ($nursingstation as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('nursing_stations_id') == $data->id ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manager_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        <!-- Submit and Cancel Buttons -->
                        <div class="form-group text-center" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">{{ trans('inpatient_trans.save') }}</button>
                            <a href="{{ route('nursing_station_settings.index') }}"
                                class="btn btn-secondary">{{ trans('inpatient_trans.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
