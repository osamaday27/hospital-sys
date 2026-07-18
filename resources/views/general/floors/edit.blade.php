@extends('layouts.master')

@section('title')
    {{ trans('floors_trans.edit') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('floors_trans.edit') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('management_contract_frist_level.index') }}">{{ trans('floors_trans.setting') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('floors_trans.edit') }}</li>
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
                    <form action="{{ route('floors_settings.update', $floors_settings->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ $floors_settings->id }}">
                                    <label for="name_ar" class="form-label">{{ trans('floors_trans.name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" class="form-control"
                                        value="{{ old('name_ar', $floors_settings->name_ar) }}" required>
                                    @error('name_ar')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- English Name -->
                                <div class="mb-3">
                                    <label for="name_en" class="form-label">{{ trans('floors_trans.name_en') }}</label>
                                    <input type="text" name="name_en" id="name_en" class="form-control"
                                        value="{{ old('name_en', $floors_settings->name_en) }}" required>
                                    @error('name_en')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Floors Count -->
                                <div class="mb-3">
                                    <label for="rooms_count"
                                        class="form-label">{{ trans('floors_trans.rooms_count') }}</label>
                                    <input type="number" name="rooms_count" id="rooms_count" class="form-control"
                                        value="{{ old('rooms_count', $floors_settings->rooms_count) }}">
                                    @error('rooms_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">{{ trans('floors_trans.type') }}</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="undefined"
                                            {{ old('type', $floors_settings->type) == 'undefined' ? 'selected' : '' }}>
                                            {{ trans('floors_trans.undefined') }}
                                        </option>
                                        <option value="administrative"
                                            {{ old('type', $floors_settings->type) == 'administrative' ? 'selected' : '' }}>
                                            {{ trans('floors_trans.administrative') }}
                                        </option>
                                        <option value="medical"
                                            {{ old('type', $floors_settings->type) == 'medical' ? 'selected' : '' }}>
                                            {{ trans('floors_trans.medical') }}
                                        </option>
                                        <option value="all"
                                            {{ old('type', $floors_settings->type) == 'all' ? 'selected' : '' }}>
                                            {{ trans('floors_trans.all') }}
                                        </option>
                                    </select>

                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="d-flex ">
                                <button type="submit" class="btn btn-success" style="margin: 10px;">{{ trans('buttons.update') }}</button>
                                <a  href="{{ route('floors_settings.index', ['building_id' =>  encrypt($floors_settings->building_id)]) }}" class="btn btn-info" style="margin: 10px;">{{ trans('buttons.list') }}</a>
                                <a href="{{ route('building_settings.index') }}" style="margin: 10px;"class="btn btn-secondary">{{ trans('buttons.back_building_settings') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
