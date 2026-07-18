@extends('layouts.master')

@section('title')
    {{ trans('hr.add_attendance') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.add_attendance') }}</h4>
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

                    <form action="{{ route('hr_attendance.store') }}" method="POST" class="p-4">
                        @csrf

                        <div class="row g-4"> <!-- Added g-4 for grid gap -->

                            <!-- Employee Selection -->
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">{{ trans('hr.employee') }}</label>
                                <select name="employee_id" id="employee_id" class="js-example-basic-single">
                                    <option value="">--Select--</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" 
                                                data-salary-level="{{ $employee->activeContract->salary_level ?? 'monthly' }}">
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Salary Level Info -->
                            <div class="col-md-6" id="salary-level-info" style="display: none;">
                                <div class="alert alert-info mb-3">
                                    <strong>{{ trans('hr.note') }}:</strong> 
                                    <span id="salary-level-message"></span>
                                </div>
                            </div>

                            <!-- Check In/Out Times (for monthly and shift employees) -->
                            <div class="col-md-6" id="check-in-section">
                                <label for="check_in" class="form-label">{{ trans('hr.check_in') }}</label>
                                <input type="datetime-local" name="check_in" id="check_in" class="form-control"
                                    value="{{ old('check_in') }}" required>
                                @error('check_in')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6" id="check-out-section">
                                <label for="check_out" class="form-label">{{ trans('hr.check_out') }}</label>
                                <input type="datetime-local" name="check_out" id="check_out" class="form-control"
                                    value="{{ old('check_out') }}" required>
                                @error('check_out')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Worked Hours -->
                            <div class="col-md-6">
                                <label for="worked_hours" class="form-label">{{ trans('hr.worked_hours') }}</label>
                                <input type="number" step="0.25" min="0" max="24" id="worked_hours" name="worked_hours" class="form-control">
                                <small class="form-text text-muted" id="hours-help">
                                    {{ trans('hr.hours_calculated_automatically') }}
                                </small>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_attendance.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
    <style>
        .status-bar {
            gap: 10px;
        }

        .status-item {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            background-color: #f0f0f0;
            transition: all 0.3s;
        }

        .status-item.active {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .status-item:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const employeeSelect = document.getElementById('employee_id');
            const checkInInput = document.getElementById('check_in');
            const checkOutInput = document.getElementById('check_out');
            const workedHoursInput = document.getElementById('worked_hours');
            const checkInSection = document.getElementById('check-in-section');
            const checkOutSection = document.getElementById('check-out-section');
            const salaryLevelInfo = document.getElementById('salary-level-info');
            const salaryLevelMessage = document.getElementById('salary-level-message');
            const hoursHelp = document.getElementById('hours-help');

            function handleSalaryLevelChange() {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                const salaryLevel = selectedOption.getAttribute('data-salary-level') || 'monthly';
                
                if (salaryLevel === 'hours') {
                    // Hourly employee: hide check-in/out fields, make them optional
                    checkInSection.style.display = 'none';
                    checkOutSection.style.display = 'none';
                    checkInInput.removeAttribute('required');
                    checkOutInput.removeAttribute('required');
                    workedHoursInput.setAttribute('required', 'required');
                    workedHoursInput.removeAttribute('readonly');
                    
                    // Show info message
                    salaryLevelInfo.style.display = 'block';
                    salaryLevelMessage.textContent = '{{ trans("hr.hourly_employee_enter_hours_directly") }}';
                    hoursHelp.textContent = '{{ trans("hr.enter_worked_hours_manually") }}';
                    
                } else {
                    // Monthly/shift employee: show check-in/out fields, make them required
                    checkInSection.style.display = 'block';
                    checkOutSection.style.display = 'block';
                    checkInInput.setAttribute('required', 'required');
                    checkOutInput.setAttribute('required', 'required');
                    workedHoursInput.removeAttribute('required');
                    workedHoursInput.setAttribute('readonly', 'readonly');
                    
                    // Hide info message
                    salaryLevelInfo.style.display = 'none';
                    hoursHelp.textContent = '{{ trans("hr.hours_calculated_automatically") }}';
                }
            }

            function calculateWorkedHours() {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                const salaryLevel = selectedOption.getAttribute('data-salary-level') || 'monthly';
                
                // Only auto-calculate for non-hourly employees
                if (salaryLevel !== 'hours') {
                    const checkIn = new Date(checkInInput.value);
                    const checkOut = new Date(checkOutInput.value);

                    if (!isNaN(checkIn) && !isNaN(checkOut) && checkOut > checkIn) {
                        const diffInMs = checkOut - checkIn; // Difference in milliseconds
                        const diffInHours = (diffInMs / (1000 * 60 * 60)).toFixed(2); // Convert to hours and round to 2 decimal places
                        workedHoursInput.value = diffInHours;
                    } else {
                        workedHoursInput.value = ''; // Clear the field if invalid
                    }
                }
            }

            // Event listeners
            employeeSelect.addEventListener('change', handleSalaryLevelChange);
            checkInInput.addEventListener('input', calculateWorkedHours);
            checkOutInput.addEventListener('input', calculateWorkedHours);
            
            // Initialize on page load
            handleSalaryLevelChange();
        });
    </script>
@endsection
