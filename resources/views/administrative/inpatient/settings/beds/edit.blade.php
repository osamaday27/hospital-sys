@extends('layouts.master')

@section('title')
    {{ trans('inpatient_trans.edit_bed') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('inpatient_trans.edit_bed') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('room_settings.index') }}">{{ trans('inpatient_trans.beds') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.edit_bed') }}</li>
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

                    <form action="{{ route('bed_settings.update', 'test') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $bed->id }}">
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ trans('inpatient_trans.name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $bed->name) }}" required>
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
                                        <option value="general"
                                            {{ old('type', $bed->type) == 'general' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.general') }}</option>
                                        <option value="icu" {{ old('type', $bed->type) == 'icu' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.icu') }}</option>
                                        <option value="nursery"
                                            {{ old('type', $bed->type) == 'nursery' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.nursery') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Room Select -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">{{ trans('inpatient_trans.room') }}</label>
                                    <select class="js-example-basic-single" id="room_id" name="room_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_room') }}</option>
                                        @foreach ($rooms as $data)
                                            <option value="{{ $data->id }}"
                                                {{ old('room_id', $bed->room_id) == $data->id ? 'selected' : '' }}>
                                                {{ trans('inpatient_trans.room') .
                                                    ' :- ' .
                                                    $data->name .
                                                    ' - ' .
                                                    trans('inpatient_trans.nursing_station') .
                                                    ' :- ' .
                                                    ($data->nursingstation
                                                        ? (App::getLocale() == 'ar'
                                                            ? $data->nursingstation->name_ar
                                                            : $data->nursingstation->name_en)
                                                        : trans('inpatient_trans.no_nursing_station')) .
                                                    ' - ' .
                                                    trans('inpatient_trans.floor') .
                                                    ' :- ' .
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ trans('inpatient_trans.status') }}</label>
                                    <select class="js-example-basic-single"id="status" name="status" required>
                                        <option value="">{{ trans('inpatient_trans.select_status') }}</option>
                                        <option value="available"
                                            {{ old('status', $bed->status ?? 'available') == 'available' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.available') }}
                                        </option>
                                        <option value="occupied"
                                            {{ old('status', $bed->status) == 'occupied' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.occupied') }}
                                        </option>
                                        <option value="maintenance"
                                            {{ old('status', $bed->status) == 'maintenance' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.under_maintenance') }}
                                        </option>
                                        <option value="reserved"
                                            {{ old('status', $bed->status) == 'reserved' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.reserved') }}
                                        </option>
                                        <option value="out_of_service"
                                            {{ old('status', $bed->status) == 'out_of_service' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.out_of_service') }}
                                        </option>
                                        <option value="sanitized"
                                            {{ old('status', $bed->status) == 'sanitized' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.sanitized') }}
                                        </option>
                                        <option value="pending_cleaning"
                                            {{ old('status', $bed->status) == 'pending_cleaning' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.pending_cleaning') }}
                                        </option>
                                        <option value="allocated"
                                            {{ old('status', $bed->status) == 'allocated' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.allocated') }}
                                        </option>
                                        <option value="isolation"
                                            {{ old('status', $bed->status) == 'isolation' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.isolation') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="form-group text-center" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">{{ trans('inpatient_trans.update') }}</button>
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
