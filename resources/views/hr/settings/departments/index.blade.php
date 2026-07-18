@extends('layouts.master_table')
@section('title')
    {{ trans('hr.departments') }}
@stop

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('hr.departments') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.departments') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.departments_management') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light" data-dismiss="alert"><i
                    class="fa fa-times"></i></button>
            <strong>Success!</strong> {{ session('success') }}
        </div>
    @endif

    @if (Session::has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light" data-dismiss="alert"><i
                    class="fa fa-times"></i></button>
            <strong>Error!</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Add Department Button -->
    <div class="d-grid gap-2">
        <button data-bs-toggle="modal" data-bs-target="#addEmployeeModal" class="btn btn-primary waves-effect waves-light"
            type="button"><a class="nav-link font" href="{{ url('/' . ($page = 'hr_department/create')) }}">{{ trans('hr.add_department') }}</a></button>
    </div>

    <!-- Departments Table -->
    <div class="card mt-4">
        <div class="card-body">
            <table id="employeeTable" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                style="width:100%">
                <thead>
                    <tr>
                        <th>{{ trans('hr.Number') }}</th>
                        <th>{{ trans('hr.dep_name_ar') }}</th>
                        <th>{{ trans('hr.dep_parent') }}</th>
                        <th>{{ trans('hr.manager') }}</th>
                        <th>{{ trans('hr.status') }}</th>
                        <th>{{ trans('hr.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    @foreach ($hr_department as $department)
                        <tr>
                            <?php $i++; ?>
                            <td>{{ $i }}</td>
                            <td>{{ $department->name_ar }}</td>
                            <td>{{ $department->parent ? $department->parent->name_ar : 'N/A' }}</td>
                            <td>{{ $department->manager ? $department->manager->full_name : 'N/A' }}</td>
                            <td>{{ in_array(strtolower((string)$department->status), ['active','enabled','1','true']) ? trans('hr.active') : trans('hr.inactive') }}</td>
                            <td>
                                <!-- Actions -->
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="{{ route('hr_department.edit', $department->id) }}"
                                                class="dropdown-item edit-item-btn"><i
                                                    class="ri-pencil-fill align-bottom me-2"></i> {{ trans('hr.edit') }}</a></li>
                                        <li>
                                            <form action="{{ route('hr_department.destroy', $department->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item delete-item-btn"
                                                    onclick="return confirm('Are you sure you want to delete this department?');">
                                                    <i class="ri-delete-bin-fill align-bottom me-2"></i> {{ trans('hr.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
