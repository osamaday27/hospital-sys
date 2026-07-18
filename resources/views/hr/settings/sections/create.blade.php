@extends('layouts.master')

@section('title')
    {{ trans('hr.add_section') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.add_section') }}</h4>
                <div class="page-title-right">
                    <button type="button" class="btn btn-warning">
                        <a class="text-white" href="{{ route('hr_section.index') }}">{{ trans('hr.back') }}</a>
                    </button>
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                    <form action="{{ route('hr_section.store') }}" method="POST" class="p-4">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <!-- Department Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name_ar" class="form-label">{{ trans('hr.section_name_ar') }}</label>
                                    <input type="text" name="name_ar" id="name_ar" 
                                        class="form-control" 
                                        placeholder="{{ trans('hr.enter_section_name_ar') }}" 
                                        required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="name_ne" class="form-label">{{ trans('hr.section_name_en') }}</label>
                                    <input type="text" name="name_ne" id="name_ne" 
                                        class="form-control" 
                                        placeholder="{{ trans('hr.enter_section_name_en') }}" 
                                        required>
                                </div>

                                <!-- Parent Department -->
                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">{{ trans('hr.department') }}</label>
                                    <select name="department" id="department" class="js-example-basic-single">
                                        <option value=""></option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="manager_id" class="form-label">{{ trans('hr.manager') }}</label>
                                    <select name="manager_id" id="manager_id" class="js-example-basic-single">
                                        <option value=""></option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ trans('hr.status') }}</label>
                                    <select name="status" id="status" class="js-example-basic-single" required>
                                        <option value="active">{{ trans('hr.active') }}</option>
                                        <option value="inactive">{{ trans('hr.inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_section.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection