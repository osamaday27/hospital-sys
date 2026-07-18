@extends('layouts.master')

@section('title')
    {{ trans('surgical_trans.title') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('surgical_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('roomsurgicalsettings.index') }}">{{ trans('surgical_trans.room') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('surgical_trans.create_room') }}</li>
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

                    <form action="{{ route('roomsurgicalsettings.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_ar">{{ trans('surgical_trans.name_ar') }}</label>
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
                                    <label for="name_en">{{ trans('surgical_trans.name_en') }}</label>
                                    <input type="text" class="form-control" id="name_en" name="name_en"
                                        value="{{ old('name_en') }}" required>
                                    @error('name_en')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                              
                             <!-- Manager Select -->
                           <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="floor">{{ trans('inpatient_trans.floor') }}</label>
                                        <select class="js-example-basic-single" id="floor" name="floor" required>
                                            <option value="">{{ trans('inpatient_trans.select_floor') }}</option>
                                            @foreach($floors as $floor)
                                                <option value="{{ $floor->id }}" {{ old('floor_id') == $floor->id ? 'selected' : '' }}>
                                                    {{ App::getLocale() == 'ar' ? $floor->name_ar : $floor->name_en }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('floor')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                        <!-- Submit and Cancel Buttons -->
                        <div class="form-group text-center" style="margin-top: 10px;">
                            <button type="submit" class="btn btn-primary">{{ trans('surgical_trans.save') }}</button>
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
