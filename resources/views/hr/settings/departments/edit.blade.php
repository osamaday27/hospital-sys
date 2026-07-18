
@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_department') }}
@stop

@section('content')

    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_department') }}</h4>
                <div class="page-title-right">
                    <button type="button" class="btn btn-warning">
                        <a class="text-white" href="{{ route('hr_department.index') }}">{{ trans('hr.back') }}</a>
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
                <form action="{{ route('hr_department.update', $department_edit->id) }}" method="POST" class="p-4">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Department Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name_ar" class="form-label">{{ trans('hr.dep_name_ar') }}</label>
                                <input type="text" name="name_ar" id="name_ar" 
                                    class="form-control" 
                                    placeholder="{{ trans('hr.enter_department_name_ar') }}" 
                                    required value="{{ $department_edit->name_ar }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="name_ne" class="form-label">{{ trans('hr.dep_name_en') }}</label>
                                <input type="text" name="name_ne" id="name_ne" 
                                    class="form-control" 
                                    placeholder="{{ trans('hr.enter_department_name_en') }}" 
                                    required value="{{ $department_edit->name_ne }}">
                            </div>

                            <!-- Parent Department -->
                            <div class="col-md-6 mb-3">
                                <label for="parent_department" class="form-label">{{ trans('hr.parent_department') }}</label>
                                <select name="parent_department" id="parent_department" class="js-example-basic-single">
                                    <option value="">--Select--</option>
                                    @foreach($hr_department as $department)
                                        <option value="{{ $department->id }}" 
                                            @if(old('parent_department', $department_edit->parent_department) == $department->id) selected @endif>
                                            {{ $department->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-6 mb-3">
                                <label for="manager_id" class="form-label">{{ trans('hr.manager') }}</label>
                                <select name="manager_id" id="manager_id" class="js-example-basic-single">
                                    <option value="">--Select--</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                            @if(old('manager_id', $department_edit->manager_id) == $employee->id) selected @endif>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- status -->
                            <div class="col-3 mb-3">
                                <label for="status" class="form-label">{{ trans('hr.status') }}</label>
                                <select id="status" class="js-example-basic-single" name="status" onchange="get();">
                                    <option value="active" {{ $department_edit->status == 'active' ? 'selected' : '' }}>
                                        {{ trans('hr.active') }}
                                    </option>
                                    <option value="inactive" {{ $department_edit->status == 'inactive' ? 'selected' : '' }}>
                                        {{ trans('hr.inactive') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-success">{{ trans('hr.edit') }}</button>
                        <a href="{{ route('hr_department.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection