@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_vacation_type') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_vacation_type') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                            href="{{ url('/' . ($page = 'hr_vacation_types')) }}">{{ trans('hr.vacation_types_title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_vacation_type') }}</li>
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


                    <form action="{{ route('hr_vacation_types.update', $vacation_type->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Arabic Name -->
                                <div class="mb-3">
                                    <label for="name"
                                        class="form-label">{{ trans('hr.vacation_name') }}</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ $vacation_type->name }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-3 mb-3">
                                <label for="take_vacation_in" class="form-label">{{ trans('hr.take_vacation_in') }}</label>
                                <select id="take_vacation_in" class="js-example-basic-single" name="take_vacation_in" onchange="get();">
                                    <option value="hours" {{ $vacation_type->take_vacation_in == 'hours' ? 'selected' : '' }}>
                                        {{ trans('hr.hours') }}
                                    </option>
                                    <option value="days" {{ $vacation_type->take_vacation_in == 'days' ? 'selected' : '' }}>
                                        {{ trans('hr.days') }}
                                    </option>
                                </select>
                            </div>


                            <div class="col-3 mb-3">
                                <label for="approval_type" class="form-label">{{ trans('hr.approval_type') }}</label>
                                <select id="approval_type" class="js-example-basic-single" name="approval_type" onchange="get();">
                                    <option value="no_validation" {{ $vacation_type->approval_type == 'no_validation' ? 'selected' : '' }}>
                                        {{ trans('hr.no_validation') }}
                                    </option>
                                    <option value="manager" {{ $vacation_type->approval_type == 'manager' ? 'selected' : '' }}>
                                        {{ trans('hr.manager') }}
                                    </option>
                                    <option value="hr_manager" {{ $vacation_type->approval_type == 'hr_manager' ? 'selected' : '' }}>
                                        {{ trans('hr.hr_and_manager') }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3 form-check">
                                    <label for="is_active" class="form-check-label">{{ trans('hr.has_allocation') }}</label>
                                    <input type="checkbox" name="has_allocation" id="has_allocation" class="form-check-input" value="1" 
                                        {{ $vacation_type->has_allocation ? 'checked' : '' }}>
                                </div>
                                <div class="mb-3 form-check">
                                    <label for="no_salary" class="form-check-label">{{ trans('hr.no_salary') }}</label>
                                    <input type="checkbox" name="no_salary" id="no_salary" class="form-check-input" value="1" {{ $vacation_type->no_salary ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <button type="submit"
                                    class="btn btn-success">{{ trans('buttons.save') }}</button>
                                <a href="{{ route('hr_vacation_types.index') }}"
                                    class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
@endsection
