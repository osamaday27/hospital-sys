@extends('layouts.master_table')

@section('title')
    {{ trans('hr.edit_social_insurance') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_social_insurance.index') }}">{{ trans('hr.social_insurance_title') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_social_insurance') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.edit_social_insurance') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_social_insurance.update', $insurance->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">{{ trans('hr.employee') }} <span class="text-danger">*</span></label>
                                    <select class="form-select js-select2 @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                        <option value="">{{ trans('hr.select_employee') }}</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ $insurance->employee_id == $employee->id ? 'selected' : '' }}
                                                data-code="{{ $employee->employee_code }}"
                                                data-job="{{ $employee->emp_pos->name_ar ?? '' }}"
                                                data-department="{{ $employee->emp_dep->name_ar ?? '' }}"
                                                data-hiring-date="{{ $employee->hiring_date }}"
                                                data-contract="{{ $employee->has_valid_contract }}"
                                                data-marital="{{ $employee->marital_status }}"
                                                data-gender="{{ $employee->gender }}"
                                                data-phone="{{ $employee->phone }}"
                                                data-address="{{ $employee->full_address }}"
                                                data-id-number="{{ $employee->identification_number }}">
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_status" class="form-label">{{ trans('hr.insurance_status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('insurance_status') is-invalid @enderror" id="insurance_status" name="insurance_status" required>
                                        <option value="1" {{ $insurance->insurance_status ? 'selected' : '' }}>{{ trans('hr.insured') }}</option>
                                        <option value="0" {{ !$insurance->insurance_status ? 'selected' : '' }}>{{ trans('hr.not_insured') }}</option>
                                    </select>
                                    @error('insurance_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employee Details Fields -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.employee_code') }}</label>
                                <input type="text" class="form-control" id="employee_code" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.job_position') }}</label>
                                <input type="text" class="form-control" id="job_position" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.department') }}</label>
                                <input type="text" class="form-control" id="department" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.hiring_date') }}</label>
                                <input type="text" class="form-control" id="hiring_date" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.has_valid_contract') }}</label>
                                <input type="text" class="form-control" id="contract_status" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.marital_status') }}</label>
                                <input type="text" class="form-control" id="marital_status" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.gender') }}</label>
                                <input type="text" class="form-control" id="gender" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ trans('hr.phone') }}</label>
                                <input type="text" class="form-control" id="phone" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('hr.identification_number') }}</label>
                                <input type="text" class="form-control" id="id_number" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('hr.full_address') }}</label>
                                <input type="text" class="form-control" id="address" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subscription_date" class="form-label">{{ trans('hr.subscription_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('subscription_date') is-invalid @enderror" 
                                           id="subscription_date" name="subscription_date" 
                                           value="{{ old('subscription_date', $insurance->subscription_date->format('Y-m-d')) }}" required>
                                    @error('subscription_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_office" class="form-label">{{ trans('hr.insurance_office') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('insurance_office') is-invalid @enderror" 
                                           id="insurance_office" name="insurance_office" 
                                           value="{{ old('insurance_office', $insurance->insurance_office) }}" required>
                                    @error('insurance_office')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_number" class="form-label">{{ trans('hr.insurance_number') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('insurance_number') is-invalid @enderror" 
                                           id="insurance_number" name="insurance_number" 
                                           value="{{ old('insurance_number', $insurance->insurance_number) }}" required>
                                    @error('insurance_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="health_status" class="form-label">{{ trans('hr.health_status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('health_status') is-invalid @enderror" id="health_status" name="health_status" required>
                                        <option value="1" {{ $insurance->health_status ? 'selected' : '' }}>{{ trans('hr.insured') }}</option>
                                        <option value="0" {{ !$insurance->health_status ? 'selected' : '' }}>{{ trans('hr.not_insured') }}</option>
                                    </select>
                                    @error('health_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="insurance_company" class="form-label">{{ trans('hr.insurance_company') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('insurance_company') is-invalid @enderror" 
                                           id="insurance_company" name="insurance_company" 
                                           value="{{ old('insurance_company', $insurance->insurance_company) }}" required>
                                    @error('insurance_company')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="main_salary" class="form-label">{{ trans('hr.main_salary') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('main_salary') is-invalid @enderror" 
                                           id="main_salary" name="main_salary" 
                                           value="{{ old('main_salary', $insurance->main_salary) }}" required>
                                    @error('main_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="shared_salary" class="form-label">{{ trans('hr.shared_salary') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('shared_salary') is-invalid @enderror" 
                                           id="shared_salary" name="shared_salary" 
                                           value="{{ old('shared_salary', $insurance->shared_salary) }}" required readonly>
                                    @error('shared_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ trans('hr.update') }}</button>
                            <a href="{{ route('hr_social_insurance.index') }}" class="btn btn-light">{{ trans('hr.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.js-select2').select2({
            placeholder: "{{ trans('hr.select_employee') }}",
            allowClear: true
        });

        // Function to update employee details
        function updateEmployeeDetails(selectedOption) {
            var employeeData = {
                code: selectedOption.data('code'),
                job: selectedOption.data('job'),
                department: selectedOption.data('department'),
                hiringDate: selectedOption.data('hiring-date'),
                contract: selectedOption.data('contract'),
                marital: selectedOption.data('marital'),
                gender: selectedOption.data('gender'),
                phone: selectedOption.data('phone'),
                address: selectedOption.data('address'),
                idNumber: selectedOption.data('id-number')
            };

            // Update employee details fields
            $('#employee_code').val(employeeData.code);
            $('#job_position').val(employeeData.job);
            $('#department').val(employeeData.department);
            $('#hiring_date').val(employeeData.hiringDate);
            $('#contract_status').val(employeeData.contract ? '{{ trans("hr.yes") }}' : '{{ trans("hr.no") }}');
            $('#marital_status').val(employeeData.marital);
            $('#gender').val(employeeData.gender);
            $('#phone').val(employeeData.phone);
            $('#address').val(employeeData.address);
            $('#id_number').val(employeeData.idNumber);
        }

        // Update employee details on page load if an employee is selected
        var initialEmployee = $('#employee_id option:selected');
        if (initialEmployee.val()) {
            updateEmployeeDetails(initialEmployee);
        }

        // Employee selection change handler
        $('#employee_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                updateEmployeeDetails(selectedOption);
            } else {
                // Clear all fields if no employee is selected
                $('#employee_code, #job_position, #department, #hiring_date, #contract_status, #marital_status, #gender, #phone, #address, #id_number').val('');
            }
        });

        // Calculate shared salary when main salary changes
        $('#main_salary').on('input', function() {
            var mainSalary = parseFloat($(this).val()) || 0;
            var sharedSalary = mainSalary / 1.25;
            $('#shared_salary').val(sharedSalary.toFixed(2));
        });

        // Calculate initial shared salary on page load
        var initialMainSalary = parseFloat($('#main_salary').val()) || 0;
        if (initialMainSalary > 0) {
            var initialSharedSalary = initialMainSalary / 1.25;
            $('#shared_salary').val(initialSharedSalary.toFixed(2));
        }

        // Form submission handling
        $('form').submit(function() {
            $('button[type="submit"]').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans("hr.updating") }}');
        });
    });
</script>
@endpush 