@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.edit_room') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('inpatient_trans.edit_room') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('room_settings.index') }}">{{ trans('inpatient_trans.room') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.edit_room') }}</li>
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

                    <form action="{{ route('room_settings.update', 'test') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" name="id" value="{{ $room->id }}">
                                    <label for="name">{{ trans('inpatient_trans.name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $room->name) }}" required>
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
                                        @foreach ([
            'office' => trans('inpatient_trans.office'),
            'patient_room' => trans('inpatient_trans.patient_room'),
            'operation_room' => trans('inpatient_trans.operation_room'),
            'icu' => trans('inpatient_trans.icu'),
            'emergency_room' => trans('inpatient_trans.emergency_room'),
            'delivery_room' => trans('inpatient_trans.delivery_room'),
            'waiting_room' => trans('inpatient_trans.waiting_room'),
            'x_ray_room' => trans('inpatient_trans.x_ray_room'),
            'laboratory' => trans('inpatient_trans.laboratory'),
            'pharmacy' => trans('inpatient_trans.pharmacy'),
            'consultation_room' => trans('inpatient_trans.consultation_room'),
            'recovery_room' => trans('inpatient_trans.recovery_room'),
        ] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('type', $room->type) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">


                            <!-- Nursing Station Select -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nursing_stations_id">{{ trans('inpatient_trans.nursing_station') }}</label>
                                    <select class="js-example-basic-single" id="nursing_stations_id"
                                        name="nursing_stations_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_nursing_stations') }}
                                        </option>
                                        @foreach ($nursingstation as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('nursing_stations_id', $room->nursing_stations_id) == $data->id ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('nursing_stations_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label for="residency_id">{{ trans('inpatient_trans.residencies') }}</label>
                                    <select class="js-example-basic-single" id="residency_id"
                                        name="residency_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_residencies') }}
                                        </option>
                                        @foreach ($residencies as $data)
                                            <option value="{{ $data->id }}"  {{ old('residencies', $room->residency_id) == $data->id ? 'selected' : '' }}
                                              >
                                                {{ App::getLocale() == 'ar' ? $data->name_ar : $data->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('residency_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="form-group text-center" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">{{ trans('inpatient_trans.update') }}</button>
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
