@extends('layouts.master')

@section('title')
    {{ trans('surgical_trans.edit_room') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('surgical_trans.edit_room') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('roomsurgicalsettings.index') }}">{{ trans('surgical_trans.room') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('surgical_trans.edit_room') }}</li>
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

                    <form action="{{ route('roomsurgicalsettings.update', 'test') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" name="id" value="{{ $room->id }}">
                                    <label for="name_ar">{{ trans('surgical_trans.name_ar') }}</label>
                                    <input type="text" class="form-control" id="name_ar" name="name_ar"
                                        value="{{ old('name_ar', $room->name_ar) }}" required>
                                    @error('name_ar')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_en">{{ trans('surgical_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en', $room->name_en) }}" required>
                                    @error('name_en')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Type Select -->
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="floor">{{ trans('surgical_trans.floor') }}</label>
                                    <select class="form-control" id="floor" name="floor" required>
                                        <option value="">{{ trans('surgical_trans.select_floor') }}</option>
                                        @foreach ($floors as $floor)
                                            <option value="{{ $floor->id }}"
                                                {{ old('floor', $room->floor) == $floor->id ? 'selected' : '' }}>
                                                {{ App::getLocale() == 'ar' ? $floor->name_ar : $floor->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('floor')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ trans('surgical_trans.status') }}</label>
                                    <select class="js-example-basic-single" id="status" name="status" required>
                                        <option value="">{{ trans('surgical_trans.select_status') }}</option>
                                        <option value="available"
                                            {{ isset($room) && $room->status == 'available' ? 'selected' : '' }}>
                                            {{ trans('surgical_trans.available') }}
                                        </option>
                                        <option value="occupied"
                                            {{ isset($room) && $room->status == 'occupied' ? 'selected' : '' }}>
                                            {{ trans('surgical_trans.occupied') }}
                                        </option>
                                        <option value="under_maintenance"
                                            {{ isset($room) && $room->status == 'under_maintenance' ? 'selected' : '' }}>
                                            {{ trans('surgical_trans.under_maintenance') }}
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
                            <button type="submit" class="btn btn-primary">{{ trans('surgical_trans.update') }}</button>
                            <a href="{{ route('roomsurgicalsettings.index') }}"
                                class="btn btn-secondary">{{ trans('surgical_trans.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
