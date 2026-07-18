@extends('layouts.master_table')

@section('title')
    {{ trans('hr.create_social_insurance') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_social_insurance.index') }}">{{ trans('hr.social_insurance_title') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.create_social_insurance') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.create_social_insurance') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_social_insurance.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">{{ trans('hr.employee') }} <span class="text-danger">*</span></label>
                                    <select class="form-select js-select2 @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                        <option value="">{{ trans('hr.select_employee') }}</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
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
                                        <option value="1">{{ trans('hr.insured') }}</option>
                                        <option value="0">{{ trans('hr.not_insured') }}</option>
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
                                           value="{{ old('subscription_date') }}" required>
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
                                           value="{{ old('insurance_office') }}" required>
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
                                           value="{{ old('insurance_number') }}" required>
                                    @error('insurance_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="health_status" class="form-label">{{ trans('hr.health_status') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('health_status') is-invalid @enderror" id="health_status" name="health_status" required>
                                        <option value="1">{{ trans('hr.insured') }}</option>
                                        <option value="0">{{ trans('hr.not_insured') }}</option>
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
                                           value="{{ old('insurance_company') }}" required>
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
                                           value="{{ old('main_salary') }}" required>
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
                                           value="{{ old('shared_salary') }}" required readonly>
                                    @error('shared_salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ trans('hr.save') }}</button>
                            <a href="{{ route('hr_social_insurance.index') }}" class="btn btn-light">{{ trans('hr.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
    

        // Employee selection change handler
        document.getElementById('employee_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                // Get employee data from data attributes
                const employeeData = {
                    code: selectedOption.dataset.code,
                    job: selectedOption.dataset.job,
                    department: selectedOption.dataset.department,
                    hiringDate: selectedOption.dataset.hiringDate,
                    contract: selectedOption.dataset.contract,
                    marital: selectedOption.dataset.marital,
                    gender: selectedOption.dataset.gender,
                    phone: selectedOption.dataset.phone,
                    address: selectedOption.dataset.address,
                    idNumber: selectedOption.dataset.idNumber
                };

                // Update employee details fields
                document.getElementById('employee_code').value = employeeData.code || '';
                document.getElementById('job_position').value = employeeData.job || '';
                document.getElementById('department').value = employeeData.department || '';
                document.getElementById('hiring_date').value = employeeData.hiringDate || '';
                document.getElementById('contract_status').value = employeeData.contract ? '{{ trans("hr.yes") }}' : '{{ trans("hr.no") }}';
                document.getElementById('marital_status').value = employeeData.marital || '';
                document.getElementById('gender').value = employeeData.gender || '';
                document.getElementById('phone').value = employeeData.phone || '';
                document.getElementById('address').value = employeeData.address || '';
                document.getElementById('id_number').value = employeeData.idNumber || '';
            } else {
                // Clear all fields if no employee is selected
                const fields = [
                    'employee_code', 'job_position', 'department', 'hiring_date',
                    'contract_status', 'marital_status', 'gender', 'phone',
                    'address', 'id_number'
                ];
                fields.forEach(field => {
                    document.getElementById(field).value = '';
                });
            }
        });

        // Calculate shared salary when main salary changes
        document.getElementById('main_salary').addEventListener('input', function() {
            const mainSalary = parseFloat(this.value) || 0;
            const sharedSalary = mainSalary / 1.25;
            document.getElementById('shared_salary').value = sharedSalary.toFixed(2);
        });

        // Trigger change event on employee select to populate fields if value exists
        const employeeSelect = document.getElementById('employee_id');
        if (employeeSelect.value) {
            employeeSelect.dispatchEvent(new Event('change'));
        }

        // Form submission handling
        document.querySelector('form').addEventListener('submit', function() {
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans("hr.saving") }}';
        });
    });
</script>
@endsection