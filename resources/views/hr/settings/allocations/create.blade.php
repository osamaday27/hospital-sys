@extends('layouts.master')

@section('title')
    {{ trans('hr.add_allocation') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.add_allocation') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/' . ($page = 'hr_allocations')) }}">{{ trans('hr.allocations_title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.add_allocation') }}</li>
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
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('hr_allocations.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Allocation Name -->
                                <div class="mb-3">
                                    <label for="allocation_name" class="form-label">{{ trans('hr.allocation_name') }}</label>
                                    <input type="text" name="allocation_name" id="allocation_name" class="form-control" value="{{ old('allocation_name') }}" required>
                                    @error('allocation_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Vacation Type -->
                                <div class="mb-3">
                                    <label for="vacation_type" class="form-label">{{ trans('hr.vacation_type') }}</label>
                                    <select id="vacation_type" class="form-control" name="vacation_type" required>
                                        <option value="">--Select--</option>
                                        @foreach($vacation_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('vacation_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Mode -->
                                <div class="mb-3">
                                    <label for="mode" class="form-label">{{ trans('hr.supply_mode') }}</label>
                                    <select id="mode" class="form-control" name="mode" required onchange="toggleEmployeeSelection()">
                                        <option value="">--Select--</option>
                                        <option value="by_employee">{{ trans('hr.by_employee') }}</option>
                                        <option value="by_department">{{ trans('hr.by_department') }}</option>
                                        <option value="by_hospital">{{ trans('hr.by_hospital') }}</option>
                                    </select>
                                    @error('mode')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="employee_selection" style="display:none;">
                                <!-- Employees Selection (Shown when mode is by_employee) -->
                                <div class="mb-3">
                                    <label for="employees" class="form-label">{{ trans('hr.select_employees') }}</label>
                                    <select id="employees" class="form-control" name="employees[]" multiple>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employees')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">{{ trans('hr.allocation_amount') }}</label>
                                    <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ trans('hr.description') }}</label>
                                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Valid From -->
                                <div class="mb-3">
                                    <label for="valid_from" class="form-label">{{ trans('hr.valid_from') }}</label>
                                    <input type="date" name="valid_from" id="valid_from" class="form-control" required>
                                    @error('valid_from')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Valid To -->
                                <div class="mb-3">
                                    <label for="valid_to" class="form-label">{{ trans('hr.valid_to') }}</label>
                                    <input type="date" name="valid_to" id="valid_to" class="form-control" required>
                                    @error('valid_to')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_allocations.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->

    <script>
        function toggleEmployeeSelection() {
            var mode = document.getElementById("mode").value;
            var employeeSelection = document.getElementById("employee_selection");
            if (mode === "by_employee") {
                employeeSelection.style.display = "block";
            } else {
                employeeSelection.style.display = "none";
            }
        }
    </script>
@endsection
