@extends('layouts.master')

@section('title')
    {{ __('hr.edit_payslip') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">
                    <i class="ri-file-edit-line align-middle me-2"></i>
                    {{ __('hr.edit_payslip') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('hr.payroll') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr_payslips.index') }}">{{ __('hr.payslips') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('hr.edit_payslip') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            <i class="ri-user-settings-line align-middle me-2"></i>
                            {{ __('hr.payslip_details') }}
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_payslips.update', ['hr_payslip' => $payslip->id]) }}" method="POST" id="payslip-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">
                                    <i class="ri-user-line me-1"></i>{{ __('hr.employee') }}
                                </label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">{{ __('hr.select_employee') }}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $payslip->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contract Information will be displayed here -->
                        <div id="contract-info-container"></div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_from" class="form-label">
                                    <i class="ri-calendar-line me-1"></i>{{ __('hr.date_from') }}
                                </label>
                                <input type="date" class="form-control @error('date_from') is-invalid @enderror" 
                                    id="date_from" name="date_from" value="{{ old('date_from', $payslip->date_from->format('Y-m-d')) }}" required>
                                @error('date_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_to" class="form-label">
                                    <i class="ri-calendar-line me-1"></i>{{ __('hr.date_to') }}
                                </label>
                                <input type="date" class="form-control @error('date_to') is-invalid @enderror" 
                                    id="date_to" name="date_to" value="{{ old('date_to', $payslip->date_to->format('Y-m-d')) }}" required>
                                @error('date_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border shadow-none">
                                    <div class="card-header bg-light">
                                        <div class="d-flex align-items-center">
                                            <h5 class="card-title mb-0 flex-grow-1">
                                                <i class="ri-money-dollar-circle-line align-middle me-2"></i>
                                                {{ __('hr.salary_lines') }}
                                            </h5>
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-primary" id="addSalaryLine">
                                                    <i class="ri-add-line align-bottom me-1"></i> {{ __('hr.add_salary_line') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="salary-lines-container" class="salary-lines-wrapper">
                                            @foreach($payslip->salary_lines as $index => $line)
                                                <div class="salary-line mb-3 p-3 border rounded bg-light">
                                                    <div class="row g-3">
                                                        <div class="col-md-5">
                                                            <label class="form-label">
                                                                <i class="ri-money-dollar-circle-line me-1"></i>
                                                                {{ __('hr.salary_rule') }}
                                                            </label>
                                                            <select name="salary_lines[{{ $index }}][salary_rule_id]" class="form-select salary-rule-select" required>
                                                                <option value="">{{ __('hr.select_salary_rule') }}</option>
                                                                @foreach($salaryRules as $rule)
                                                                <option value="{{ $rule->id }}" data-type="{{ $rule->type }}" {{ $line->salary_rule_id == $rule->id ? 'selected' : '' }}>
                                                                    {{ $rule->name }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <label class="form-label">
                                                                <i class="ri-money-dollar-circle-line me-1"></i>
                                                                {{ __('hr.amount') }}
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text rule-type-badge"></span>
                                                                <input type="number" step="0.01" min="0" name="salary_lines[{{ $index }}][amount]" class="form-control" value="{{ abs($line->amount) }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger remove-line">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('hr_payslips.index') }}" class="btn btn-light">
                                    <i class="ri-close-line align-bottom me-1"></i>{{ __('hr.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line align-bottom me-1"></i>{{ __('hr.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .salary-lines-wrapper {
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
    }
    .salary-line {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    .salary-line:hover {
        background-color: #e9ecef;
    }
    .form-label {
        font-weight: 500;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .btn-danger {
        padding: 0.5rem;
    }
    .btn-danger i {
        font-size: 1.1rem;
    }
</style>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lineIndex = {{ count($payslip->salary_lines) }};
        let employeeContractInfo = null;

        // Function to fetch employee contract information
        async function fetchEmployeeContractInfo(employeeId) {
            try {
                const response = await fetch(`/{{ app()->getLocale() }}/hr_payslips/employee-contract/${employeeId}`);
                const data = await response.json();
                
                if (data.success) {
                    employeeContractInfo = data.contract;
                    showContractInfo(data.contract);
                    return true;
                } else {
                    employeeContractInfo = null;
                    hideContractInfo();
                    showError(data.message);
                    return false;
                }
            } catch (error) {
                console.error('Error fetching contract info:', error);
                employeeContractInfo = null;
                hideContractInfo();
                showError('Error fetching employee contract information');
                return false;
            }
        }

        // Function to show contract information
        function showContractInfo(contract) {
            // Calculate net salary
            const totalAdditions = contract.basic_salary + contract.total_allowances;
            const totalDeductions = contract.calculated_tax + contract.calculated_absence + contract.calculated_lateness;
            const netSalary = totalAdditions - totalDeductions;
            
            let contractInfoHtml = `
                <div class="alert alert-info" id="contract-info">
                    <h6><i class="ri-file-list-line me-2"></i>Contract Information</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Contract:</strong> ${contract.contract_name}
                        </div>
                        <div class="col-md-3">
                            <strong>Basic Salary:</strong> ${formatCurrency(contract.basic_salary)}
                        </div>
                        <div class="col-md-3">
                            <strong>Total Allowances:</strong> ${formatCurrency(contract.total_allowances)}
                        </div>
                        <div class="col-md-3">
                            <strong>Gross Salary:</strong> ${formatCurrency(contract.gross_salary)}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <strong>Estimated Tax:</strong> <span class="text-danger">${formatCurrency(contract.calculated_tax)}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Absence Deduction:</strong> <span class="text-danger">${formatCurrency(contract.calculated_absence)}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Lateness Deduction:</strong> <span class="text-danger">${formatCurrency(contract.calculated_lateness)}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Period:</strong> ${contract.period_from} to ${contract.period_to}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <strong>Total Additions:</strong> <span class="text-success">${formatCurrency(totalAdditions)}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Deductions:</strong> <span class="text-danger">${formatCurrency(totalDeductions)}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Net Salary:</strong> <span class="text-primary fw-bold">${formatCurrency(netSalary)}</span>
                        </div>
                    </div>
                    ${contract.allowances.length > 0 ? `
                        <div class="mt-2">
                            <strong>Allowances:</strong>
                            <ul class="mb-0 mt-1">
                                ${contract.allowances.map(allowance => 
                                    `<li>${allowance.name}: ${formatCurrency(allowance.amount)}</li>`
                                ).join('')}
                            </ul>
                        </div>
                    ` : ''}
                </div>
            `;
            
            // Remove existing contract info if any
            const existingInfo = document.getElementById('contract-info');
            if (existingInfo) {
                existingInfo.remove();
            }
            
            // Insert contract info in the container
            document.getElementById('contract-info-container').innerHTML = contractInfoHtml;
        }

        // Function to hide contract information
        function hideContractInfo() {
            document.getElementById('contract-info-container').innerHTML = '';
        }

        // Function to show error message
        function showError(message) {
            let errorHtml = `
                <div class="alert alert-danger" id="contract-error">
                    <i class="ri-error-warning-line me-2"></i>${message}
                </div>
            `;
            
            // Remove existing error if any
            const existingError = document.getElementById('contract-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Insert error in the container
            document.getElementById('contract-info-container').innerHTML = errorHtml;
        }

        // Function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'EGP'
            }).format(amount);
        }

        // Function to calculate automatic amounts based on rule type
        function calculateAutomaticAmount(ruleType) {
            if (!employeeContractInfo) return 0;
            
            switch (ruleType) {
                case 'basic_salary':
                    return employeeContractInfo.basic_salary || 0;
                case 'allowances':
                    return employeeContractInfo.total_allowances || 0;
                case 'gross_salary':
                    return employeeContractInfo.gross_salary || 0;
                case 'tax':
                    // Tax will be calculated on the backend based on gross salary
                    // Return a placeholder that will be calculated on the server
                    return 'auto';
                default:
                    return 0;
            }
        }

        // Employee selection change handler
        document.getElementById('employee_id').addEventListener('change', async function() {
            const employeeId = this.value;
            
            if (employeeId) {
                await fetchEmployeeContractInfo(employeeId);
            } else {
                hideContractInfo();
            }
        });

        // Load contract info on page load if employee is selected
        const selectedEmployeeId = document.getElementById('employee_id').value;
        if (selectedEmployeeId) {
            fetchEmployeeContractInfo(selectedEmployeeId);
        }

        // Add new salary line
        document.getElementById('addSalaryLine').addEventListener('click', function() {
            const template = `
                <div class="salary-line mb-3 p-3 border rounded bg-light">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="ri-money-dollar-circle-line me-1"></i>
                                {{ __('hr.salary_rule') }}
                            </label>
                            <select name="salary_lines[${lineIndex}][salary_rule_id]" class="form-select salary-rule-select" required>
                                <option value="">{{ __('hr.select_salary_rule') }}</option>
                                @foreach($salaryRules as $rule)
                                <option value="{{ $rule->id }}" data-type="{{ $rule->type }}" data-rule-type="{{ $rule->rule_type }}">{{ $rule->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">
                                <i class="ri-money-dollar-circle-line me-1"></i>
                                {{ __('hr.amount') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text rule-type-badge"></span>
                                <input type="number" step="0.01" min="0" name="salary_lines[${lineIndex}][amount]" class="form-control amount-input" required>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-line">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('salary-lines-container').insertAdjacentHTML('beforeend', template);
            lineIndex++;
        });

        // Remove salary line
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-line')) {
                e.target.closest('.salary-line').remove();
            }
        });

        // Update rule type badge when salary rule is selected
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('salary-rule-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const ruleType = selectedOption.dataset.type;
                const ruleTypeValue = selectedOption.dataset.ruleType;
                const badge = e.target.closest('.salary-line').querySelector('.rule-type-badge');
                const amountInput = e.target.closest('.salary-line').querySelector('.amount-input');
                
                if (ruleType === 'addition') {
                    badge.textContent = '+';
                    badge.classList.remove('bg-danger');
                    badge.classList.add('bg-success');
                } else {
                    badge.textContent = '-';
                    badge.classList.remove('bg-success');
                    badge.classList.add('bg-danger');
                }

                // Auto-calculate amount for specific rule types
                if (['basic_salary', 'allowances', 'gross_salary'].includes(ruleTypeValue)) {
                    const autoAmount = calculateAutomaticAmount(ruleTypeValue);
                    amountInput.value = autoAmount;
                    amountInput.setAttribute('readonly', true);
                    amountInput.classList.add('bg-light');
                } else if (ruleTypeValue === 'tax') {
                    // For tax, set a placeholder but allow editing (will be calculated on backend)
                    amountInput.value = '';
                    amountInput.removeAttribute('readonly');
                    amountInput.classList.remove('bg-light');
                    amountInput.setAttribute('placeholder', 'Will be calculated automatically');
                    amountInput.classList.add('border-warning');
                    
                    // Add a note about tax calculation
                    const noteDiv = document.createElement('div');
                    noteDiv.className = 'form-text text-warning mt-1';
                    noteDiv.innerHTML = '<i class="ri-information-line me-1"></i>Tax will be calculated automatically based on gross salary and tax configuration';
                    amountInput.parentNode.parentNode.appendChild(noteDiv);
                } else if (ruleTypeValue === 'absence') {
                    // For absence, set a placeholder but allow editing (will be calculated on backend)
                    amountInput.value = '';
                    amountInput.removeAttribute('readonly');
                    amountInput.classList.remove('bg-light');
                    amountInput.setAttribute('placeholder', 'Will be calculated automatically');
                    amountInput.classList.add('border-warning');
                    
                    // Add a note about absence calculation
                    const noteDiv = document.createElement('div');
                    noteDiv.className = 'form-text text-warning mt-1';
                    noteDiv.innerHTML = '<i class="ri-information-line me-1"></i>Absence will be calculated automatically based on attendance records and schedule';
                    amountInput.parentNode.parentNode.appendChild(noteDiv);
                } else if (ruleTypeValue === 'lateness') {
                    // For lateness, set a placeholder but allow editing (will be calculated on backend)
                    amountInput.value = '';
                    amountInput.removeAttribute('readonly');
                    amountInput.classList.remove('bg-light');
                    amountInput.setAttribute('placeholder', 'Will be calculated automatically');
                    amountInput.classList.add('border-warning');
                    
                    // Add a note about lateness calculation
                    const noteDiv = document.createElement('div');
                    noteDiv.className = 'form-text text-warning mt-1';
                    noteDiv.innerHTML = '<i class="ri-information-line me-1"></i>Lateness will be calculated automatically based on penalty hours';
                    amountInput.parentNode.parentNode.appendChild(noteDiv);
                } else if (ruleTypeValue === 'net_salary') {
                    // For net salary, set a placeholder but allow editing (will be calculated on backend)
                    amountInput.value = '';
                    amountInput.removeAttribute('readonly');
                    amountInput.classList.remove('bg-light');
                    amountInput.setAttribute('placeholder', 'Will be calculated automatically');
                    amountInput.classList.add('border-success');
                    
                    // Add a note about net salary calculation
                    const noteDiv = document.createElement('div');
                    noteDiv.className = 'form-text text-success mt-1';
                    noteDiv.innerHTML = '<i class="ri-information-line me-1"></i>Net salary will be calculated automatically (sum of all additions minus deductions)';
                    amountInput.parentNode.parentNode.appendChild(noteDiv);
                } else {
                    amountInput.removeAttribute('readonly');
                    amountInput.classList.remove('bg-light');
                    amountInput.classList.remove('border-warning');
                    amountInput.classList.remove('border-success');
                    amountInput.removeAttribute('placeholder');
                    
                    // Remove any existing notes
                    const existingNote = amountInput.parentNode.parentNode.querySelector('.form-text.text-warning, .form-text.text-success');
                    if (existingNote) {
                        existingNote.remove();
                    }
                }
            }
        });

        // Initialize badges for existing salary lines
        document.querySelectorAll('.salary-rule-select').forEach(function(select) {
            const selectedOption = select.options[select.selectedIndex];
            const ruleType = selectedOption.dataset.type;
            const badge = select.closest('.salary-line').querySelector('.rule-type-badge');
            
            if (ruleType === 'addition') {
                badge.textContent = '+';
                badge.classList.add('bg-success');
            } else {
                badge.textContent = '-';
                badge.classList.add('bg-danger');
            }
        });

        // Form validation
        document.getElementById('payslip-form').addEventListener('submit', function(e) {
            if (document.querySelectorAll('.salary-line').length === 0) {
                e.preventDefault();
                alert('{{ __("hr.add_at_least_one_salary_line") }}');
                return;
            }

            // Validate employee has contract
            const employeeId = document.getElementById('employee_id').value;
            if (employeeId && !employeeContractInfo) {
                e.preventDefault();
                alert('Employee must have an active contract to update a payslip.');
                return;
            }
        });

        // Date validation
        document.getElementById('date_to').addEventListener('change', function() {
            const dateFrom = new Date(document.getElementById('date_from').value);
            const dateTo = new Date(this.value);
            
            if (dateTo < dateFrom) {
                alert('{{ __("hr.date_to_must_be_after_date_from") }}');
                this.value = '';
            }
        });
    });
</script>

