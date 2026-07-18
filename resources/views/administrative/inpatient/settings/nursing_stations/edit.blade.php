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
                            <a
                                href="{{ route('management_contract_frist_level.index') }}">{{ trans('inpatient_trans.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('inpatient_trans.edit_clinc') }}</li>
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

                    <form action="{{ route('nursing_station_settings.update','test') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <input type="hidden" name="id" value="{{ $nursingStation->id }}">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_ar">{{ trans('inpatient_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        value="{{ old('name_ar', $nursingStation->name_ar) }}" required>
                                    @error('name_ar')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_en">{{ trans('inpatient_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', $nursingStation->name_en) }}" required>
                                    @error('name_en')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor_id">{{ trans('inpatient_trans.floor') }}</label>
                                    <select class="form-control" id="floor_id" name="floor_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_floor') }}</option>
                                        @foreach($floors as $floor)
                                            <option value="{{ $floor->id }}" {{ old('floor_id', $nursingStation->floor_id) == $floor->id ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $floor->name_ar : $floor->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('floor_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_count">{{ trans('inpatient_trans.room_count') }}</label>
                                    <input type="number" class="form-control" id="room_count" name="room_count"
                                        value="{{ old('room_count', $nursingStation->room_count) }}" required>
                                    @error('room_count')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manager_id">{{ trans('inpatient_trans.manager') }}</label>
                                    <select class="js-example-basic-single" id="manager_id" name="manager_id" required>
                                        <option value="">{{ trans('inpatient_trans.select_manager') }}</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('manager_id', $nursingStation->manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->full_name }}
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
                                    <label for="type">{{ trans('inpatient_trans.type') }}</label>
                                    <select class="js-example-basic-single" id="type" name="type" required>
                                        <option value="">{{ trans('inpatient_trans.select_type') }}</option>
                                        <option value="general" {{ old('type', $nursingStation->type) == 'general' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.general') }}
                                        </option>
                                        <option value="specialized" {{ old('type', $nursingStation->type) == 'specialized' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.specialized') }}
                                        </option>
                                        <option value="emergency" {{ old('type', $nursingStation->type) == 'emergency' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.emergency') }}
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ trans('inpatient_trans.status') }}</label>
                                    <select class="js-example-basic-single" id="status" name="status" required>
                                        <option value="active" {{ old('status', $nursingStation->status) == 'active' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.active') }}
                                        </option>
                                        <option value="inactive" {{ old('status', $nursingStation->status) == 'inactive' ? 'selected' : '' }}>
                                            {{ trans('inpatient_trans.inactive') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">{{ trans('inpatient_trans.save') }}</button>
                            <a href="{{ route('nursing_station_settings.index') }}" class="btn btn-secondary">{{ trans('inpatient_trans.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
