@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_vacation') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_vacation') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/' . ($page = 'hr_vacations')) }}">{{ trans('hr.vacations_title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_vacation') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('hr_vacations.update', $vacation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">{{ trans('hr.employee') }}</label>
                                    <select name="employee_id" id="employee_id" class="form-control" required>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                data-code="{{ $employee->employee_code }}"
                                                data-job="{{ $employee->emp_pos->name_ar ?? '' }}"
                                                data-section="{{ $employee->emp_sec->name_ar ?? '' }}"
                                                @if($vacation->employee_id == $employee->id) selected @endif>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.employee_code') }}</label>
                                    <input type="text" id="employee_code" class="form-control" value="{{ $vacation->employee->employee_code ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.job_position') }}</label>
                                    <input type="text" id="job_position" class="form-control" value="{{ $vacation->employee->emp_pos->name_ar ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.section') }}</label>
                                    <input type="text" id="section" class="form-control" value="{{ $vacation->employee->emp_sec->name_ar ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vacation_type_id" class="form-label">{{ trans('hr.vacation_type') }}</label>
                                    <select name="vacation_type_id" id="vacation_type_id" class="form-control" required>
                                        @foreach($vacationTypes as $type)
                                            <option value="{{ $type->id }}" data-take-vacation-in="{{ $type->take_vacation_in }}" @if($vacation->vacation_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="remaining_balance" class="form-label">{{ trans('hr.remaining_balance') }}</label>
                                    <div id="remaining_balance" class="form-control" style="padding: 0.375rem 0.75rem; background-color: #e9ecef; opacity: 1;">
                                        {{ trans('hr.remaining_balance') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="requested_amount" class="form-label">{{ trans('hr.requested_amount') }}</label>
                                    <input type="text" id="requested_amount" class="form-control" value="{{ $vacation->days_taken }}" readonly>
                                    <small class="text-muted" id="requested_unit"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="take_with" class="form-label">{{ trans('hr.take_with') }}</label>
                                    <select name="take_with" id="take_with" class="form-control" required>
                                        <option value="days" @if($vacation->take_with == 'days') selected @endif>{{ trans('hr.days') }}</option>
                                        <option value="hours" @if($vacation->take_with == 'hours') selected @endif>{{ trans('hr.hours') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div id="request_type_fields" style="display: {{ $vacation->take_with == 'days' ? 'block' : 'none' }};">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="request_type" class="form-label">{{ trans('hr.request_type') }}</label>
                                        <select name="request_type" id="request_type" class="form-control">
                                            <option value="full_day" @if($vacation->request_type == 'full_day') selected @endif>{{ trans('hr.full_day') }}</option>
                                            <option value="half_day" @if($vacation->request_type == 'half_day') selected @endif>{{ trans('hr.half_day') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="days_fields" style="display: {{ $vacation->take_with == 'days' ? 'block' : 'none' }};">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="from" class="form-label">{{ trans('hr.from_date') }}</label>
                                        <input type="date" name="from" id="from" class="form-control" value="{{ $vacation->from ? $vacation->from->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="to" class="form-label">{{ trans('hr.to_date') }}</label>
                                        <input type="date" name="to" id="to" class="form-control" value="{{ $vacation->to ? $vacation->to->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div id="hours_fields" style="display: {{ $vacation->take_with == 'hours' ? 'block' : 'none' }};">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hours_date" class="form-label">{{ trans('hr.date') }}</label>
                                        <input type="date" name="hours_date" id="hours_date" class="form-control" value="{{ $vacation->date ? $vacation->date->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="from_time" class="form-label">{{ trans('hr.from_time') }}</label>
                                        <input type="time" name="from_time" id="from_time" class="form-control" step="3600" value="{{ $vacation->from_time ? $vacation->from_time->format('H:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="to_time" class="form-label">{{ trans('hr.to_time') }}</label>
                                        <input type="time" name="to_time" id="to_time" class="form-control" step="3600" value="{{ $vacation->to_time ? $vacation->to_time->format('H:i') : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="approval_status" class="form-label">{{ trans('hr.approval_status') }}</label>
                                    <input type="text" id="approval_status" name="approval_status" class="form-control" value="{{ $vacation->approval_status }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="reason" class="form-label">{{ trans('hr.reason') }}</label>
                                    <textarea name="reason" id="reason" class="form-control" rows="3">{{ $vacation->reason }}</textarea>
                                </div>
                            </div>
                    
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_vacations.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employee_id');
            const employeeCodeInput = document.getElementById('employee_code');
            const jobPositionInput = document.getElementById('job_position');
            const sectionInput = document.getElementById('section');
            function updateEmployeeDetails() {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                if (selectedOption.value) {
                    employeeCodeInput.value = selectedOption.dataset.code || '';
                    jobPositionInput.value = selectedOption.dataset.job || '';
                    sectionInput.value = selectedOption.dataset.section || '';
                } else {
                    employeeCodeInput.value = '';
                    jobPositionInput.value = '';
                    sectionInput.value = '';
                }
            }
            employeeSelect.addEventListener('change', updateEmployeeDetails);
            if (employeeSelect.value) {
                updateEmployeeDetails();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employee_id');
            const vacationTypeSelect = document.getElementById('vacation_type_id');
            const remainingBalanceDiv = document.getElementById('remaining_balance');
            function fetchRemainingBalance() {
                const employeeId = employeeSelect.value;
                const vacationTypeId = vacationTypeSelect.value;
                if (!employeeId || !vacationTypeId) {
                    remainingBalanceDiv.textContent = 'Select employee and vacation type';
                    return;
                }
                fetch(`/hr/vacations/remaining-balance?employee_id=${employeeId}&vacation_type_id=${vacationTypeId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        remainingBalanceDiv.textContent = `Remaining Balance: ${data.remaining_balance}`;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        remainingBalanceDiv.textContent = 'Error fetching balance';
                    });
            }
            employeeSelect.addEventListener('change', fetchRemainingBalance);
            vacationTypeSelect.addEventListener('change', fetchRemainingBalance);
            if (employeeSelect.value && vacationTypeSelect.value) {
                fetchRemainingBalance();
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const vacationTypeSelect = document.getElementById('vacation_type_id');
            const takeWithSelect = document.getElementById('take_with');
            const requestTypeFields = document.getElementById('request_type_fields');
            const daysFields = document.getElementById('days_fields');
            const hoursFields = document.getElementById('hours_fields');
            function updateTakeWithOptions() {
                const selectedVacationType = vacationTypeSelect.options[vacationTypeSelect.selectedIndex];
                const takeVacationIn = selectedVacationType.dataset.takeVacationIn;
                takeWithSelect.innerHTML = '';
                if (takeVacationIn === 'hours') {
                    takeWithSelect.innerHTML = '<option value="hours">{{ trans('hr.hours') }}</option>';
                } else if (takeVacationIn === 'days') {
                    takeWithSelect.innerHTML = '<option value="days">{{ trans('hr.days') }}</option>';
                }
                requestTypeFields.style.display = takeVacationIn === 'days' ? 'block' : 'none';
                daysFields.style.display = takeVacationIn === 'days' ? 'block' : 'none';
                hoursFields.style.display = takeVacationIn === 'hours' ? 'block' : 'none';
            }
            vacationTypeSelect.addEventListener('change', updateTakeWithOptions);
            updateTakeWithOptions();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const takeWithSelect = document.getElementById('take_with');
            const requestTypeSelect = document.getElementById('request_type');
            const fromDateInput = document.getElementById('from');
            const toDateInput = document.getElementById('to');
            const hoursDateInput = document.getElementById('hours_date');
            const fromTimeInput = document.getElementById('from_time');
            const toTimeInput = document.getElementById('to_time');
            const requestedAmountInput = document.getElementById('requested_amount');
            const requestedUnitSpan = document.getElementById('requested_unit');
            function calculateRequestedAmount() {
                const takeWith = takeWithSelect.value;
                let amount = 0;
                let unit = '';
                try {
                    switch (takeWith) {
                        case 'days':
                            if (fromDateInput.value && toDateInput.value) {
                                const from = new Date(fromDateInput.value);
                                const to = new Date(toDateInput.value);
                                amount = Math.floor((to - from) / (1000 * 60 * 60 * 24)) + 1;
                                if (requestTypeSelect.value === 'half_day') {
                                    amount = amount * 0.5;
                                }
                                unit = 'days';
                            }
                            break;
                        case 'hours':
                            if (hoursDateInput.value && fromTimeInput.value && toTimeInput.value) {
                                const from = new Date(`${hoursDateInput.value}T${fromTimeInput.value}`);
                                const to = new Date(`${hoursDateInput.value}T${toTimeInput.value}`);
                                amount = Math.abs((to - from) / (1000 * 60 * 60));
                                unit = 'hours';
                            }
                            break;
                    }
                } catch (e) {
                    console.error('Calculation error:', e);
                }
                requestedAmountInput.value = amount.toFixed(1);
                requestedUnitSpan.textContent = unit;
            }
            [takeWithSelect, requestTypeSelect, fromDateInput, toDateInput, hoursDateInput, fromTimeInput, toTimeInput].forEach(
                element => element.addEventListener('change', calculateRequestedAmount)
            );
            [fromDateInput, toDateInput, hoursDateInput, fromTimeInput, toTimeInput].forEach(
                element => element.addEventListener('input', calculateRequestedAmount)
            );
            calculateRequestedAmount();
        });
    </script>
@endsection 