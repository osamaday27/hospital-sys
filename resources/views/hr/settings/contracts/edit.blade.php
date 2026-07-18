@extends('layouts.master')

@section('title')
    {{ trans('hr.edit_contract') }}
@stop

@section('content')
    <!-- Start Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.edit_contract') }}</h4>
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

                    <form action="{{ route('hr_contracts.update', $contract->id) }}" method="POST" class="p-4">
                        @csrf
                        @method('PUT')
                        <!-- Status Bar with Bootstrap Spacing -->
                        <div class="d-flex justify-content-center mb-4">
                            <input type="hidden" name="status" id="contract_status_input" value="{{ $contract->status }}">

                            <div class="status-bar d-flex gap-3">
                                <div class="status-item" onclick="setStatus('open')" data-status="open">
                                    <span class="status-icon text-success"><i class="fas fa-unlock"></i></span>
                                    <span>{{ trans('hr.contract_open') }}</span>
                                </div>
                                <div class="status-item" onclick="setStatus('expired')" data-status="expired">
                                    <span class="status-icon text-danger"><i class="fas fa-calendar-times"></i></span>
                                    <span>{{ trans('hr.expired') }}</span>
                                </div>
                                <div class="status-item" onclick="setStatus('closed')" data-status="closed">
                                    <span class="status-icon text-secondary"><i class="fas fa-lock"></i></span>
                                    <span>{{ trans('hr.contract_closed') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4"> <!-- Added g-4 for grid gap -->
                            <!-- Arabic Name -->
                            <div class="col-md-6">
                                <label for="contract_name" class="form-label">{{ trans('hr.contract_name') }}</label>
                                <input type="text" name="contract_name" id="contract_name" class="form-control"
                                    value="{{ old('contract_name', $contract->contract_name) }}" required>
                                @error('contract_name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Employee Selection -->
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">{{ trans('hr.name') }}</label>
                                <select name="employee_id" id="employee_id" class="js-example-basic-single">
                                    <option value="">--Select--</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-department="{{ $employee->department_id }}" {{ old('employee_id', $contract->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Contract Start and End Dates -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">{{ trans('hr.contract_start_date') }}</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ old('start_date', $contract->start_date) }}" required>
                                @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_date" class="form-label">{{ trans('hr.contract_end_date') }}</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ old('end_date', $contract->end_date) }}" required>
                                @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="department_id" class="form-label">{{ trans('hr.department') }}</label>
                                <select name="department_id" id="department_id" class="js-example-basic-single" readonly require>
                                <option value="">--Select--</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $contract->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Job Selection -->
                            <div class="col-md-6">
                                <label for="job_id" class="form-label">{{ trans('hr.job_position') }}</label>
                                <select name="job_id" id="job_id" class="js-example-basic-single" readonly>
                                <option value="">--Select--</option>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job->id }}" {{ old('job_id', $contract->job_id) == $job->id ? 'selected' : '' }}>{{ $job->name_ar }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="salary_structure_type" class="form-label">{{ trans('hr.salary_structure_type') }}</label>
                                <select id="salary_structure_type" class="js-example-basic-single" name="salary_structure_type">
                                    <option value="">--Select--</option>
                                    <option value="operations" {{ old('salary_structure_type', $contract->salary_structure_type) == 'operations' ? 'selected' : '' }}>{{ trans('hr.operations') }}</option>
                                    <option value="marketing" {{ old('salary_structure_type', $contract->salary_structure_type) == 'marketing' ? 'selected' : '' }}>{{ trans('hr.marketing') }}</option>
                                    <option value="warehouse" {{ old('salary_structure_type', $contract->salary_structure_type) == 'warehouse' ? 'selected' : '' }}>{{ trans('hr.warehouse') }}</option>
                                    <option value="physicians" {{ old('salary_structure_type', $contract->salary_structure_type) == 'physicians' ? 'selected' : '' }}>{{ trans('hr.physicians') }}</option>
                                    <option value="admins" {{ old('salary_structure_type', $contract->salary_structure_type) == 'admins' ? 'selected' : '' }}>{{ trans('hr.admins') }}</option>
                                </select>
                            </div>

                            <!-- Social Insurance Number -->
                            <div class="col-md-6">
                                <label for="salary_level" class="form-label">{{ trans('hr.salary_level') }}</label>
                                <select id="salary_level" class="js-example-basic-single" name="salary_level" required>
                                    <option value="">--Select--</option>
                                    <option value="hours" {{ old('salary_level', $contract->salary_level) == 'hours' ? 'selected' : '' }}>{{ trans('hr.hours') }}</option>
                                    <option value="shifts" {{ old('salary_level', $contract->salary_level) == 'shifts' ? 'selected' : '' }}>{{ trans('hr.shifts') }}</option>
                                    <option value="monthly" {{ old('salary_level', $contract->salary_level) == 'monthly' ? 'selected' : '' }}>{{ trans('hr.monthly') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="hour_amount_group" style="display:none;">
                                <label for="hour_amount" class="form-label">{{ trans('hr.hour_amount') }}</label>
                                <input type="number" step="0.01" name="hour_amount" id="hour_amount" class="form-control" value="{{ old('hour_amount', $contract->hour_amount) }}">
                            </div>
                            <div class="col-md-6" id="shift_amount_group" style="display:none;">
                                <label for="shift_amount" class="form-label">{{ trans('hr.shift_amount') }}</label>
                                <input type="number" step="0.01" name="shift_amount" id="shift_amount" class="form-control" value="{{ old('shift_amount', $contract->shift_amount) }}">
                            </div>
                            <div class="col-md-6" id="basic_salary_group" style="display:none;">
                                <label for="basic_salary" class="form-label">{{ trans('hr.basic_salary') }}</label>
                                <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control" value="{{ old('basic_salary', $contract->basic_salary) }}">
                            </div>
                        </div>

                        <!-- Allowances Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ trans('hr.allowances') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="allowances_container">
                                            <!-- Allowances will be added here dynamically -->
                                            @if($contract->allowances->count() > 0)
                                                @foreach($contract->allowances as $index => $allowance)
                                                    <div class="allowance-row">
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <label class="form-label">{{ trans('hr.allowance_name') }}</label>
                                                                <input type="text" name="allowances[{{ $index + 1 }}][name]" class="form-control" value="{{ $allowance->name }}" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">{{ trans('hr.allowance_type') }}</label>
                                                                <select name="allowances[{{ $index + 1 }}][allowance_id]" class="form-control" required>
                                                                    <option value="">--Select--</option>
                                                                    @foreach($allowance_types as $type)
                                                                        <option value="{{ $type->id }}" {{ $allowance->allowance_id == $type->id ? 'selected' : '' }}>
                                                                            {{ $type->name_ar }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label">{{ trans('hr.amount') }}</label>
                                                                <input type="number" step="0.01" name="allowances[{{ $index + 1 }}][amount]" class="form-control" value="{{ $allowance->amount }}" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger" onclick="removeAllowance(this)">
                                                                    <i class="fas fa-trash"></i> {{ trans('buttons.delete') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <script>
                                                    allowanceCounter = {{ $contract->allowances->count() }};
                                                </script>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-info" onclick="addAllowance()">
                                            <i class="fas fa-plus"></i> {{ trans('hr.add_allowance') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success">{{ trans('buttons.save') }}</button>
                            <a href="{{ route('hr_contracts.index') }}" class="btn btn-secondary">{{ trans('buttons.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Form -->
    <style>
        .status-bar {
            gap: 15px;
        }

        .status-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 95px;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            font-size: 0.9rem;
            font-weight: 500;
            color: #333;
        }

        .status-item .status-icon {
            font-size: 1.3rem;
            margin-bottom: 4px;
        }

        /* --- Status-Specific Colors --- */

        /* Open */
        .status-item[data-status="open"] { border-color: #a3d9b1; }
        .status-item[data-status="open"] .status-icon { color: #28a745; }
        .status-item[data-status="open"].active {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
        }
        .status-item[data-status="open"].active .status-icon { color: #fff; }

        /* Expired */
        .status-item[data-status="expired"] { border-color: #f5c6cb; }
        .status-item[data-status="expired"] .status-icon { color: #dc3545; }
        .status-item[data-status="expired"].active {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        .status-item[data-status="expired"].active .status-icon { color: #fff; }

        /* Closed */
        .status-item[data-status="closed"] { border-color: #ced4da; }
        .status-item[data-status="closed"] .status-icon { color: #6c757d; }
        .status-item[data-status="closed"].active {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.2);
        }
        .status-item[data-status="closed"].active .status-icon { color: #fff; }

        .status-item:hover {
            transform: translateY(-1px);
        }

        .allowance-row {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }

        .allowance-row .row {
            align-items: end;
        }
    </style>

    <script>
        let allowanceCounter = 0;

        function setStatus(status) {
            const statusInput = document.getElementById('contract_status_input');
            statusInput.value = status;
            document.querySelectorAll('.status-item').forEach(item => {
                item.classList.remove('active');
            });
            document.querySelector(`.status-item[data-status="${status}"]`).classList.add('active');
        }

        // Salary level dynamic fields logic (jQuery for Select2 compatibility)
        function updateSalaryFields() {
            const salaryLevel = $('#salary_level').val();
            const hourGroup = $('#hour_amount_group');
            const shiftGroup = $('#shift_amount_group');
            const basicGroup = $('#basic_salary_group');
            const hourInput = $('#hour_amount');
            const shiftInput = $('#shift_amount');
            const basicInput = $('#basic_salary');

            hourGroup.hide();
            shiftGroup.hide();
            basicGroup.hide();
            hourInput.prop('required', false);
            shiftInput.prop('required', false);
            basicInput.prop('required', false);

            if (salaryLevel === 'hours') {
                hourGroup.show();
                hourInput.prop('required', true);
            } else if (salaryLevel === 'shifts') {
                shiftGroup.show();
                shiftInput.prop('required', true);
            } else if (salaryLevel === 'monthly') {
                basicGroup.show();
                basicInput.prop('required', true);
            }
        }

        // Real-time status update based on dates
        function updateStatusByDates() {
            const start = $('#start_date').val();
            const end = $('#end_date').val();
            const today = new Date().toISOString().slice(0, 10);
            let status = 'open';
            if (start && end) {
                if (today < start) {
                    status = 'closed';
                } else if (today > end) {
                    status = 'expired';
                } else {
                    status = 'open';
                }
            }
            setStatus(status);
        }

        function addAllowance() {
            allowanceCounter++;
            const container = document.getElementById('allowances_container');
            const allowanceRow = document.createElement('div');
            allowanceRow.className = 'allowance-row';
            allowanceRow.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ trans('hr.allowance_name') }}</label>
                        <input type="text" name="allowances[${allowanceCounter}][name]" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('hr.allowance_type') }}</label>
                        <select name="allowances[${allowanceCounter}][allowance_id]" class="form-control" required>
                            <option value="">--Select--</option>
                            @foreach($allowance_types as $type)
                                <option value="{{ $type->id }}">{{ $type->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ trans('hr.amount') }}</label>
                        <input type="number" step="0.01" name="allowances[${allowanceCounter}][amount]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger" onclick="removeAllowance(this)">
                            <i class="fas fa-trash"></i> {{ trans('buttons.delete') }}
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(allowanceRow);
        }

        function removeAllowance(button) {
            button.closest('.allowance-row').remove();
        }

        $(document).ready(function() {
            // Initialize Select2 if used
            if ($.fn.select2) {
                $('.js-example-basic-single').select2();
            }
            
            // Set initial status
            setStatus('{{ $contract->status }}');
            
            // Salary level logic
            $('#salary_level').on('change', updateSalaryFields);
            updateSalaryFields();
            
            // Real-time status logic
            $('#start_date, #end_date').on('change', updateStatusByDates);
            updateStatusByDates();
        });

        // Existing employee_id logic
        $('#employee_id').on('change', function () {
            var employeeId = $(this).val();
            if (employeeId) {
                $.ajax({
                    url: "{{ route('hr_contracts.getDepartmentJobByEmployee', '') }}/" + employeeId,
                    type: 'GET',
                    success: function (data) {
                        if (data.department_id && data.job_id) {
                            $('#department_id').val(data.department_id).change();
                            $('#job_id').val(data.job_id).change();
                        } else {
                            alert('No department or job found for this employee.');
                        }
                    },
                    error: function () {
                        alert('Error fetching department data.');
                    }
                });
            } else {
                $('#department_id').val('').change();
            }
        });
    </script>
@endsection
