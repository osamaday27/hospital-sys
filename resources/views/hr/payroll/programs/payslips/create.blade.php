@extends('layouts.master_table')

@section('title')
    {{ isset($payslip) ? __('hr.edit_payslip') : __('hr.create_payslip') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">
                    <i class="ri-file-list-3-line align-middle me-2"></i>
                    {{ isset($payslip) ? __('hr.edit_payslip') : __('hr.create_payslip') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('hr.payroll') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr_payslips.index') }}">{{ __('hr.payslips') }}</a></li>
                        <li class="breadcrumb-item active">{{ isset($payslip) ? __('hr.edit_payslip') : __('hr.create_payslip') }}</li>
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
                    <form action="{{ isset($payslip) ? route('hr_payslips.update', $payslip) : route('hr_payslips.store') }}" method="POST" id="payslip-form">
                        @csrf
                        @if(isset($payslip))
                            @method('PUT')
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="employee_id" class="form-label">
                                    <i class="ri-user-line me-1"></i>{{ __('hr.employee') }}
                                </label>
                                <select class="form-select select2 @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required data-placeholder="{{ __('hr.select_employee') }}">
                                    <option value="">{{ __('hr.select_employee') }}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ (isset($payslip) && $payslip->employee_id == $employee->id) ? 'selected' : '' }}>
                                            {{ $employee->full_name }} - {{ $employee->employee_code ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="date_from" class="form-label">
                                    <i class="ri-calendar-line me-1"></i>{{ __('hr.date_from') }}
                                </label>
                                <input type="date" class="form-control @error('date_from') is-invalid @enderror" id="date_from" name="date_from" 
                                    value="{{ isset($payslip) ? $payslip->date_from->format('Y-m-d') : old('date_from') }}" required>
                                @error('date_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="date_to" class="form-label">
                                    <i class="ri-calendar-line me-1"></i>{{ __('hr.date_to') }}
                                </label>
                                <input type="date" class="form-control @error('date_to') is-invalid @enderror" id="date_to" name="date_to" 
                                    value="{{ isset($payslip) ? $payslip->date_to->format('Y-m-d') : old('date_to') }}" required>
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
                                        <div id="salaryLinesContainer" class="salary-lines-wrapper">
                                            @if(isset($payslip) && $payslip->salaryLines->count() > 0)
                                                @foreach($payslip->salaryLines as $index => $line)
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
                                                                    <option value="{{ $rule->id }}" data-type="{{ $rule->type }}">{{ $rule->name }}</option>
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
                                                                    <input type="number" step="0.01" min="0" name="salary_lines[{{ $index }}][amount]" class="form-control" required>
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
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hr_payslips.index') }}" class="btn btn-light">
                                        <i class="ri-close-line align-bottom me-1"></i>{{ __('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-bottom me-1"></i>{{ __('hr.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
        border: 1px solid #e0e0e0;
    }
    .salary-line:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .form-label {
        font-weight: 500;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    /* Enhanced Contract Info Styling */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #405189 0%, #5a67d8 100%);
    }
    .avatar-sm {
        height: 3rem;
        width: 3rem;
        font-size: 1rem;
    }
    .avatar {
        height: 4rem;
        width: 4rem;
        font-size: 1.5rem;
    }
    .bg-success-subtle {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .bg-danger-subtle {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-warning-subtle {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1);
    }
    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .bg-white.bg-opacity-25 {
        background-color: rgba(255, 255, 255, 0.25) !important;
    }
    
    /* Animation for contract info appearance */
    #contract-info {
        animation: slideInDown 0.5s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Enhanced salary line styling */
    .rule-type-badge {
        font-weight: bold;
        font-size: 1.1rem;
        min-width: 40px;
    }
    
    /* Form input enhancements */
    .amount-input.border-warning {
        border-color: #ffc107 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }
    .amount-input.border-success {
        border-color: #198754 !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }
    .amount-input.border-danger {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .amount-input.border-primary {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .amount-input.border-info {
        border-color: #0dcaf0 !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
    }
    
    /* Auto-populated salary line styling */
    .salary-line:has(.amount-input[readonly]) {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #28a745;
    }
    
    .salary-line:has(.amount-input[readonly]) .rule-type-badge {
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    /* Employee Select2 dropdown styling */
    .employee-option {
        padding: 8px 0;
        line-height: 1.4;
    }
    .employee-name {
        font-weight: 500;
        color: #333;
        font-size: 14px;
        margin-bottom: 2px;
    }
    .employee-code {
        font-size: 12px;
        color: #6c757d;
        font-weight: 400;
    }
    
    /* Ensure employee code remains visible on hover */
    .select2-results__option--highlighted .employee-option .employee-name {
        color: #fff !important;
    }
    .select2-results__option--highlighted .employee-option .employee-code {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-body .row.g-3 .col-md-4,
        .card-body .row.g-3 .col-md-6 {
            margin-bottom: 1rem;
        }
        .avatar-sm {
            height: 2.5rem;
            width: 2.5rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lineCount = 0;
        const container = document.getElementById('salaryLinesContainer');
        let employeeContractInfo = null;

        // Initialize Select2 for employee dropdown
        $('#employee_id').select2({
            placeholder: '{{ __("hr.select_employee") }}',
            allowClear: true,
            width: '100%',
            templateResult: function(state) {
                if (!state.id) return state.text;
                
                // Split the text to extract name and employee code
                const parts = state.text.split(' - ');
                const name = parts[0];
                const empCode = parts[1] || '';
                
                var $state = $(
                    '<div class="employee-option">' +
                        '<div class="employee-name">' + name + '</div>' +
                        '<div class="employee-code">Code: ' + empCode + '</div>' +
                    '</div>'
                );
                return $state;
            },
            templateSelection: function(state) {
                if (!state.id) return state.text;
                return state.text.split(' - ')[0]; // Show only name in selection
            }
        });

        // Function to fetch employee contract information
        async function fetchEmployeeContractInfo(employeeId) {
            try {
                const dateFrom = document.getElementById('date_from').value;
                const dateTo = document.getElementById('date_to').value;
                let url = `/{{ app()->getLocale() }}/hr_payslips/employee-contract/${employeeId}`;
                if (dateFrom && dateTo) {
                    url += `?date_from=${dateFrom}&date_to=${dateTo}`;
                }
                const response = await fetch(url);
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
            // Handle different salary levels
            if (contract.salary_level === 'hours') {
                // Hourly employee: show simplified info
                const hourlyInfoHtml = `
                    <div class="card border-0 shadow-sm mb-4" id="contract-info">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="ri-time-line me-2"></i>{{ __('hr.hourly_employee_contract') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-primary-subtle rounded">
                                        <h6 class="text-primary mb-1">{{ __('hr.hour_wage') }}</h6>
                                        <h4 class="mb-0">${formatCurrency(contract.hour_wage || 0)} / {{ __('hr.hour') }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-info-subtle rounded">
                                        <h6 class="text-info mb-1">{{ __('hr.worked_hours') }}</h6>
                                        <h4 class="mb-0">${contract.worked_hours || 0} {{ __('hr.hours') }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-warning-subtle rounded">
                                        <h6 class="text-warning mb-1">{{ __('hr.gross_salary') }}</h6>
                                        <h4 class="mb-0">${formatCurrency(contract.gross_salary || 0)}</h4>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3 bg-success-subtle rounded">
                                        <h6 class="text-success mb-1">{{ __('hr.net_salary') }}</h6>
                                        <h4 class="mb-0">${formatCurrency(contract.net_salary || 0)}</h4>
                                    </div>
                                </div>
                            </div>
                            ${(contract.calculated_loan_installment > 0 || contract.calculated_penalties > 0) ? `
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">{{ __('hr.deductions') }}</h6>
                                </div>
                                ${contract.calculated_loan_installment > 0 ? `
                                <div class="col-md-6">
                                    <div class="text-center p-3 bg-danger-subtle rounded">
                                        <h6 class="text-danger mb-1">{{ __('hr.loans') }}</h6>
                                        <h5 class="mb-0">${formatCurrency(contract.calculated_loan_installment)}</h5>
                                    </div>
                                </div>
                                ` : ''}
                                ${contract.calculated_penalties > 0 ? `
                                <div class="col-md-6">
                                    <div class="text-center p-3 bg-danger-subtle rounded">
                                        <h6 class="text-danger mb-1">{{ __('hr.penalties') }}</h6>
                                        <h5 class="mb-0">${formatCurrency(contract.calculated_penalties)}</h5>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                document.getElementById('contract-info').innerHTML = hourlyInfoHtml;
                return;
            }

            // Calculate net salary for monthly and shift employees
            const totalAdditions = (contract.basic_salary || 0) + (contract.total_allowances || 0);
            const totalDeductions = (contract.calculated_tax || 0) + (contract.calculated_insurance || 0) + (contract.calculated_absence || 0) + (contract.calculated_lateness || 0) + (contract.calculated_penalties || 0) + (contract.calculated_loan_installment || 0);
            const netSalary = totalAdditions - totalDeductions;
            
            let contractInfoHtml = `
                <div class="card border-0 shadow-sm mb-4" id="contract-info">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="ri-file-list-3-line me-2"></i>{{ __('hr.employee_contract_summary') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Contract Basic Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-file-text-line text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-1 fw-medium">{{ __('hr.contract_name') }}</p>
                                        <h6 class="mb-0">${contract.contract_name || ''}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-money-dollar-circle-line text-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-1 fw-medium">{{ __('hr.basic_salary') }}</p>
                                        <h6 class="mb-0 text-success">${formatCurrency(contract.basic_salary || 0)}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-sm bg-info-subtle rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ri-calendar-line text-info"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-muted mb-1 fw-medium">{{ __('hr.period') }}</p>
                                        <h6 class="mb-0 text-info">${contract.period_from || ''} → ${contract.period_to || ''}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Salary Breakdown -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="text-success mb-3">
                                        <i class="ri-add-circle-line me-2"></i>{{ __('hr.additions') }}
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-success-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.basic_salary') }}</p>
                                                <h6 class="mb-0 text-success">${formatCurrency(contract.basic_salary || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-success-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.allowances') }}</p>
                                                <h6 class="mb-0 text-success">${formatCurrency(contract.total_allowances || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-center p-3 bg-success rounded text-white">
                                                <p class="mb-1 fw-medium">{{ __('hr.gross_salary') }}</p>
                                                <h5 class="mb-0">${formatCurrency(contract.gross_salary || 0)}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="text-danger mb-3">
                                        <i class="ri-subtract-line me-2"></i>{{ __('hr.deductions') }}
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6 col-md-2">
                                            <div class="text-center p-2 bg-danger-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.tax_amount') }}</p>
                                                <h6 class="mb-0 text-danger">${formatCurrency(contract.calculated_tax || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <div class="text-center p-2 bg-warning-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.insurance') }}</p>
                                                <h6 class="mb-0 text-warning">${formatCurrency(contract.calculated_insurance || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <div class="text-center p-2 bg-warning-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.absence') }}</p>
                                                <h6 class="mb-0 text-warning">${formatCurrency(contract.calculated_absence || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <div class="text-center p-2 bg-warning-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.lateness') }}</p>
                                                <h6 class="mb-0 text-warning">${formatCurrency(contract.calculated_lateness || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <div class="text-center p-2 bg-danger-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.penalties') }}</p>
                                                <h6 class="mb-0 text-danger">${formatCurrency(contract.calculated_penalties || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <div class="text-center p-2 bg-info-subtle rounded">
                                                <p class="text-muted mb-1 small">{{ __('hr.loans') }}</p>
                                                <h6 class="mb-0 text-info">${formatCurrency(contract.calculated_loan_installment || 0)}</h6>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-center p-3 bg-danger rounded text-white">
                                                <p class="mb-1 fw-medium">{{ __('hr.total_deductions') }}</p>
                                                <h5 class="mb-0">${formatCurrency(totalDeductions)}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Net Salary -->
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center p-4 bg-gradient-primary rounded text-white">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div class="avatar bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <i class="ri-wallet-3-line text-white fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 fw-medium fs-5">{{ __('hr.estimated_net_salary') }}</p>
                                            <h3 class="mb-0 fw-bold">${formatCurrency(netSalary)}</h3>
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="bg-white bg-opacity-25 rounded p-2">
                                                <p class="mb-1 small opacity-75">{{ __('hr.gross') }}</p>
                                                <p class="mb-0 fw-medium">${formatCurrency(totalAdditions)}</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="bg-white bg-opacity-25 rounded p-2">
                                                <p class="mb-1 small opacity-75">{{ __('hr.deductions') }}</p>
                                                <p class="mb-0 fw-medium">-${formatCurrency(totalDeductions)}</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="bg-white bg-opacity-25 rounded p-2">
                                                <p class="mb-1 small opacity-75">{{ __('hr.net') }}</p>
                                                <p class="mb-0 fw-medium">${formatCurrency(netSalary)}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${contract.allowances && contract.allowances.length > 0 ? `
                            <div class="mt-4">
                                <h6 class="text-muted mb-3">
                                    <i class="ri-gift-line me-2"></i>{{ __('hr.allowances_breakdown') }}
                                </h6>
                                <div class="row g-2">
                                    ${contract.allowances.map(allowance => `
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                <span class="fw-medium">${allowance.name}</span>
                                                <span class="badge bg-success">${formatCurrency(allowance.amount)}</span>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            // Remove existing contract info if any
            const existingInfo = document.getElementById('contract-info');
            if (existingInfo) {
                existingInfo.remove();
            }
            
            // Insert contract info after the employee selection
            const employeeSelect = document.getElementById('employee_id').closest('.col-md-4');
            employeeSelect.parentNode.insertAdjacentHTML('afterend', contractInfoHtml);
        }

        // Function to hide contract information
        function hideContractInfo() {
            const contractInfo = document.getElementById('contract-info');
            if (contractInfo) {
                contractInfo.remove();
            }
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
            
            // Insert error after the employee selection
            const employeeSelect = document.getElementById('employee_id').closest('.col-md-4');
            employeeSelect.parentNode.insertAdjacentHTML('afterend', errorHtml);
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
                    if (employeeContractInfo.salary_level === 'shifts') {
                        // For shift employees: calculated basic salary
                        return (employeeContractInfo.attendance_days || 0) * (employeeContractInfo.shift_amount || 0);
                    }
                    return employeeContractInfo.basic_salary || 0;
                case 'allowances':
                    return employeeContractInfo.total_allowances || 0;
                case 'gross_salary':
                    return employeeContractInfo.gross_salary || 0;
                case 'tax':
                    return employeeContractInfo.calculated_tax || 0;
                case 'insurance':
                    return employeeContractInfo.calculated_insurance || 0;
                case 'absence':
                    return employeeContractInfo.calculated_absence || 0;
                case 'lateness':
                    return employeeContractInfo.calculated_lateness || 0;
                case 'penalties':
                    return employeeContractInfo.calculated_penalties || 0;
                case 'loans':
                    return employeeContractInfo.calculated_loan_installment || 0;
                case 'net_salary':
                    if (employeeContractInfo.salary_level === 'hours') {
                        // For hourly employees: direct net salary calculation
                        return employeeContractInfo.net_salary || 0;
                    }
                    // Calculate net salary: gross - (tax + insurance + absence + lateness + penalties + loans)
                    const gross = employeeContractInfo.gross_salary || 0;
                    const tax = employeeContractInfo.calculated_tax || 0;
                    const insurance = employeeContractInfo.calculated_insurance || 0;
                    const absence = employeeContractInfo.calculated_absence || 0;
                    const lateness = employeeContractInfo.calculated_lateness || 0;
                    const penalties = employeeContractInfo.calculated_penalties || 0;
                    const loans = employeeContractInfo.calculated_loan_installment || 0;
                    return gross - (tax + insurance + absence + lateness + penalties + loans);
                default:
                    return 0;
            }
        }

        // Function to add a salary line
        function addSalaryLine(salaryRule = null, amount = '0') {
            const line = document.createElement('div');
            line.className = 'salary-line mb-3 p-3 border rounded bg-light';
            line.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">
                            <i class="ri-money-dollar-circle-line me-1"></i>
                            {{ __('hr.salary_rule') }}
                        </label>
                        <select name="salary_lines[${lineCount}][salary_rule_id]" class="form-select salary-rule-select" required>
                            <option value="">{{ __('hr.select_salary_rule') }}</option>
                            @foreach($salaryRules->sortBy('sequence') as $rule)
                            <option value="{{ $rule->id }}" data-type="{{ $rule->type }}" data-rule-type="{{ $rule->rule_type }}" data-sequence="{{ $rule->sequence }}">
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
                            <input type="number" step="0.01" min="0" name="salary_lines[${lineCount}][amount]" class="form-control amount-input" value="${amount}" required>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-line">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(line);
            lineCount++;

            // Add event listener to the new remove button
            line.querySelector('.remove-line').addEventListener('click', function() {
                line.remove();
            });

            // Update the rule type badge and handle automatic calculation
            const select = line.querySelector('.salary-rule-select');
            const badge = line.querySelector('.rule-type-badge');
            const amountInput = line.querySelector('.amount-input');

            if (salaryRule) {
                select.value = salaryRule.id;
                
                // Set badge based on rule type
                if (salaryRule.type === 'addition') {
                    badge.textContent = '+';
                    badge.classList.add('bg-success');
                } else {
                    badge.textContent = '-';
                    badge.classList.add('bg-danger');
                }
                
                // Set amount with proper formatting and trigger change event to apply styling
                amountInput.value = parseFloat(amount).toFixed(2);
                
                // Trigger the change event to apply automatic styling and readonly behavior
                const changeEvent = new Event('change');
                select.dispatchEvent(changeEvent);
            }

            // Add event listener for salary rule selection
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const type = selectedOption.dataset.type;
                const ruleType = selectedOption.dataset.ruleType;

                if (type === 'addition') {
                    badge.textContent = '+';
                    badge.classList.remove('bg-danger');
                    badge.classList.add('bg-success');
                } else {
                    badge.textContent = '-';
                    badge.classList.remove('bg-success');
                    badge.classList.add('bg-danger');
                }

                // Auto-calculate amount for specific rule types
                if (['basic_salary', 'allowances', 'gross_salary', 'tax', 'insurance', 'absence', 'lateness', 'penalties', 'loans', 'net_salary'].includes(ruleType)) {
                    const autoAmount = calculateAutomaticAmount(ruleType);
                    amountInput.value = parseFloat(autoAmount).toFixed(2);
                    amountInput.setAttribute('readonly', true);
                    amountInput.classList.add('bg-light');
                    
                    // Add appropriate styling and notes for different rule types
                    let noteClass = 'text-info';
                    let noteIcon = 'ri-information-line';
                    let noteText = 'Value calculated automatically from contract';
                    
                    if (ruleType === 'tax') {
                        amountInput.classList.add('border-danger');
                        noteClass = 'text-danger';
                        noteIcon = 'ri-percent-line';
                        noteText = 'Tax calculated using Egyptian progressive tax law (2025)';
                    } else if (ruleType === 'insurance') {
                        amountInput.classList.add('border-warning');
                        noteClass = 'text-warning';
                        noteIcon = 'ri-shield-line';
                        noteText = 'Insurance = (Basic Salary × 0.8) × Employee Share Rate';
                    } else if (ruleType === 'absence') {
                        amountInput.classList.add('border-warning');
                        noteClass = 'text-warning';
                        noteIcon = 'ri-calendar-close-line';
                        noteText = 'Absence deduction based on attendance records for this period';
                    } else if (ruleType === 'lateness') {
                        amountInput.classList.add('border-warning');
                        noteClass = 'text-warning';
                        noteIcon = 'ri-time-line';
                        noteText = 'Lateness deduction based on penalty hours for this period';
                    } else if (ruleType === 'penalties') {
                        amountInput.classList.add('border-danger');
                        noteClass = 'text-danger';
                        noteIcon = 'ri-alert-triangle-line';
                        noteText = 'Penalties for violations during this period';
                    } else if (ruleType === 'loans') {
                        amountInput.classList.add('border-info');
                        noteClass = 'text-info';
                        noteIcon = 'ri-bank-line';
                        noteText = 'Loan installment deduction for active loans';
                    } else if (ruleType === 'net_salary') {
                        amountInput.classList.add('border-success');
                        noteClass = 'text-success';
                        noteIcon = 'ri-wallet-3-line';
                        noteText = 'Net salary = Gross - (Tax + Insurance + Absence + Lateness + Penalties + Loans)';
                    } else if (ruleType === 'basic_salary') {
                        amountInput.classList.add('border-primary');
                        noteClass = 'text-primary';
                        noteIcon = 'ri-money-dollar-circle-line';
                        noteText = 'Basic salary from active contract';
                    } else if (ruleType === 'allowances') {
                        amountInput.classList.add('border-success');
                        noteClass = 'text-success';
                        noteIcon = 'ri-gift-line';
                        noteText = 'Total allowances from active contract';
                    } else if (ruleType === 'gross_salary') {
                        amountInput.classList.add('border-info');
                        noteClass = 'text-info';
                        noteIcon = 'ri-calculator-line';
                        noteText = 'Gross salary = Basic salary + Allowances';
                    }
                    
                    // Add a note about the calculation
                    const noteDiv = document.createElement('div');
                    noteDiv.className = `form-text ${noteClass} mt-1`;
                    noteDiv.innerHTML = `<i class="${noteIcon} me-1"></i>${noteText}`;
                    amountInput.parentNode.parentNode.appendChild(noteDiv);
                } else {
                    amountInput.removeAttribute('readonly');
                    amountInput.classList.remove('bg-light');
                    amountInput.classList.remove('border-warning', 'border-success', 'border-danger', 'border-primary', 'border-info');
                    amountInput.removeAttribute('placeholder');
                    
                    // Remove any existing notes
                    const existingNote = amountInput.parentNode.parentNode.querySelector('.form-text');
                    if (existingNote) {
                        existingNote.remove();
                    }
                }
            });
        }

        // Function to automatically populate salary lines with calculated values
        function populateAutomaticSalaryLines() {
            if (!employeeContractInfo) return;
            
            container.innerHTML = '';
            lineCount = 0;
            
            // Define rule types based on salary level
            let ruleTypesToPopulate = [];
            
            if (employeeContractInfo.salary_level === 'hours') {
                // Hourly employees: basic salary, loans, penalties, net salary
                ruleTypesToPopulate = ['basic_salary', 'loans', 'penalties', 'net_salary'];
            } else {
                // Monthly and shift employees: full calculation
                ruleTypesToPopulate = [
                    'basic_salary',
                    'allowances', 
                    'gross_salary',
                    'tax',
                    'insurance',
                    'absence',
                    'lateness',
                    'penalties',
                    'loans',
                    'net_salary'
                ];
            }
            
            // Get all available salary rules with proper null checks
            let allRules;
            try {
                allRules = @json($salaryRules->sortBy('sequence')->values()) || [];
            } catch (e) {
                console.error('Error parsing salary rules:', e);
                allRules = [];
            }
            
            // Ensure allRules is an array
            if (!Array.isArray(allRules)) {
                console.warn('allRules is not an array, converting...', allRules);
                allRules = [];
            }
            
            console.log('Available salary rules:', allRules);
            
            // Add salary lines for each rule type that exists and has a value
            ruleTypesToPopulate.forEach(ruleType => {
                const calculatedAmount = calculateAutomaticAmount(ruleType);
                
                // Only add the rule if it exists in the system and has a meaningful value
                // Always show net_salary, and show others if they have positive values or are explicitly 0 for deductions
                if (calculatedAmount > 0 || ruleType === 'net_salary' || (calculatedAmount >= 0 && ['absence', 'lateness', 'penalties', 'loans'].includes(ruleType))) {
                    const matchingRule = allRules.find(rule => rule.rule_type === ruleType);
                    
                    if (matchingRule) {
                        console.log(`Adding rule for ${ruleType}:`, matchingRule, 'Amount:', calculatedAmount);
                        addSalaryLine({
                            id: matchingRule.id,
                            type: matchingRule.type,
                            rule_type: matchingRule.rule_type,
                            name: matchingRule.name
                        }, parseFloat(calculatedAmount).toFixed(2));
                    } else {
                        console.warn(`No matching rule found for rule_type: ${ruleType}`);
                    }
                }
            });
            
            // Add any other automatic rules that weren't covered above
            @if(isset($automaticRules) && $automaticRules->count() > 0)
                @foreach($automaticRules->sortBy('sequence') as $rule)
                    @if(!in_array($rule->rule_type, ['basic_salary', 'allowances', 'gross_salary', 'tax', 'insurance', 'absence', 'lateness', 'penalties', 'loans', 'net_salary']))
                        // Check if this rule already exists in the container
                        const existingLine = container.querySelector(`select[value="{{ $rule->id }}"]`);
                        if (!existingLine) {
                            addSalaryLine({
                                id: {{ $rule->id }},
                                type: '{{ $rule->type }}',
                                rule_type: '{{ $rule->rule_type ?? "" }}',
                                name: '{{ $rule->name }}'
                            }, '0.00');
                        }
                    @endif
                @endforeach
            @endif
        }

        // Employee selection change handler
        $('#employee_id').on('change', async function() {
            const employeeId = this.value;
            
            if (employeeId) {
                const hasContract = await fetchEmployeeContractInfo(employeeId);
                
                // Clear existing salary lines if no contract
                if (!hasContract) {
                    container.innerHTML = '';
                    lineCount = 0;
                } else {
                    // Populate all relevant salary lines with calculated values
                    populateAutomaticSalaryLines();
                }
            } else {
                hideContractInfo();
                container.innerHTML = '';
                lineCount = 0;
            }
        });

        // Add automatic rules on page load in sequence order (only if employee is selected)
        @if(isset($payslip))
            // If editing existing payslip, populate with current values
            const currentEmployeeId = {{ $payslip->employee_id ?? 'null' }};
            if (currentEmployeeId) {
                document.getElementById('employee_id').value = currentEmployeeId;
                fetchEmployeeContractInfo(currentEmployeeId).then(hasContract => {
                    if (hasContract) {
                        populateAutomaticSalaryLines();
                    }
                });
            }
        @endif

        // Add new salary line button
        document.getElementById('addSalaryLine').addEventListener('click', function() {
            addSalaryLine();
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const salaryLines = document.querySelectorAll('.salary-line');
            if (salaryLines.length === 0) {
                e.preventDefault();
                alert('{{ __("hr.at_least_one_salary_line") }}');
                return;
            }

            // Validate dates
            const dateFrom = new Date(document.getElementById('date_from').value);
            const dateTo = new Date(document.getElementById('date_to').value);
            if (dateTo < dateFrom) {
                e.preventDefault();
                alert('{{ __("hr.date_to_after_date_from") }}');
                return;
            }

                                    // Validate employee has contract
            const employeeId = document.getElementById('employee_id').value;
            if (employeeId && !employeeContractInfo) {
                e.preventDefault();
                alert('Employee must have an active contract to create a payslip.');
                return;
            }

            // For hourly employees, ensure we have the required rule
            if (employeeContractInfo && employeeContractInfo.salary_level === 'hours') {
                const hasNetSalaryRule = document.querySelector('select[value="net_salary"]');
                if (!hasNetSalaryRule) {
                    e.preventDefault();
                    alert('{{ __("hr.hourly_employee_requires_net_salary_rule") }}');
                    return;
                }
            }
        });

        // Listen for changes to date_from and date_to to refetch contract info
        document.getElementById('date_from').addEventListener('change', function() {
            const employeeId = document.getElementById('employee_id').value;
            if (employeeId) {
                fetchEmployeeContractInfo(employeeId).then(hasContract => {
                    if (hasContract) {
                        populateAutomaticSalaryLines();
                    }
                });
            }
        });
        document.getElementById('date_to').addEventListener('change', function() {
            const employeeId = document.getElementById('employee_id').value;
            if (employeeId) {
                fetchEmployeeContractInfo(employeeId).then(hasContract => {
                    if (hasContract) {
                        populateAutomaticSalaryLines();
                    }
                });
            }
        });
    });
</script>
@endsection


