@extends('layouts.master_table')

@section('title')
    {{ trans('hr.create_appraisal') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr_appraisal.index') }}">{{ trans('hr.appraisal_management') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.create_appraisal') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.create_appraisal') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_appraisal.store') }}" method="POST" id="appraisal-form">
                        @csrf
                        
                        <!-- Employee Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">{{ trans('hr.employee') }} <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select js-example-basic-single @error('employee_id') is-invalid @enderror" required>
                                        <option value="">{{ trans('hr.select_employee') }}</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->employee_code }})</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information (Auto-filled) -->
                        <div id="employee-info" style="display: none;">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">{{ trans('hr.employee_code') }}</label>
                                        <input type="text" id="employee_code" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">{{ trans('hr.job') }}</label>
                                        <input type="text" id="job_name" class="form-control" readonly>
                                        <input type="hidden" id="job_id" name="job_id">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">{{ trans('hr.department') }}</label>
                                        <input type="text" id="department_name" class="form-control" readonly>
                                        <input type="hidden" id="department" name="department">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">{{ trans('hr.hiring_date') }}</label>
                                        <input type="text" id="hiring_date" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appraisal Interval -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="appraisal_interval" class="form-label">{{ trans('hr.appraisal_interval') }} <span class="text-danger">*</span></label>
                                    <select name="appraisal_interval" id="appraisal_interval" class="form-select @error('appraisal_interval') is-invalid @enderror" required>
                                        <option value="">{{ trans('hr.select_interval') }}</option>
                                        <option value="monthly">{{ trans('hr.monthly') }}</option>
                                        <option value="quarterly">{{ trans('hr.quarterly') }}</option>
                                        <option value="semi_annual">{{ trans('hr.semi_annual') }}</option>
                                        <option value="annual">{{ trans('hr.annual') }}</option>
                                    </select>
                                    @error('appraisal_interval')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Terms Section -->
                        <div id="terms-section" style="display: none;">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">{{ trans('hr.appraisal_terms') }}</h5>
                                </div>
                            </div>
                            <div id="terms-container">
                                <!-- Terms will be loaded here dynamically -->
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hr_appraisal.index') }}" class="btn btn-secondary">
                                        {{ trans('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('hr.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.js-example-basic-single').select2();

        // Employee selection change
        $('#employee_id').on('change', function() {
            const employeeId = $(this).val();
            
            if (employeeId) {
                $.ajax({
                    url: '{{ route("hr.appraisal.get_employee_data") }}',
                    type: 'GET',
                    data: { employee_id: employeeId },
                    success: function(response) {
                        if (response.error) {
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.error);
                            } else {
                                alert(response.error);
                            }
                            return;
                        }
                        
                        // Fill employee information
                        $('#employee_code').val(response.employee_code);
                        $('#job_name').val(response.job_name);
                        $('#job_id').val(response.job_id);
                        $('#department_name').val(response.department_name);
                        $('#department').val(response.department);
                        $('#hiring_date').val(response.hiring_date);
                        
                        // Show employee info section
                        $('#employee-info').show();
                        
                        // Load terms
                        loadTerms(response.terms);
                    },
                    error: function() {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('{{ trans("hr.error_loading_employee_data") }}');
                        } else {
                            alert('{{ trans("hr.error_loading_employee_data") }}');
                        }
                    }
                });
            } else {
                $('#employee-info').hide();
                $('#terms-section').hide();
            }
        });

        function loadTerms(terms) {
            const container = $('#terms-container');
            container.empty();
            
            if (!terms || Object.keys(terms).length === 0) {
                container.html('<div class="alert alert-warning">{{ trans("hr.no_terms_found") }}</div>');
                return;
            }

            let termsHtml = '';
            let termIndex = 0;

            Object.keys(terms).forEach(category => {
                let cardColor = 'bg-primary';
                if (category === 'discipline') cardColor = 'bg-warning';
                if (category === 'efficiency') cardColor = 'bg-success';

                termsHtml += `
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header ${cardColor} text-white">
                                    <h6 class="mb-0">${category}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('hr.term_name') }}</th>
                                                    <th>{{ trans('hr.percentage') }}</th>
                                                    <th>{{ trans('hr.achieved_percentage') }}</th>
                                                    <th>{{ trans('hr.difference') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                `;

                terms[category].forEach(term => {
                    termsHtml += `
                        <tr>
                            <td>${term.term_name}</td>
                            <td>${term.percentage}%</td>
                            <td>
                                <input type="number" 
                                       name="terms[${termIndex}][achieved_percentage]" 
                                       class="form-control achieved-percentage" 
                                       min="0" max="100" step="0.01" required
                                       data-term-percentage="${term.percentage}">
                                <input type="hidden" name="terms[${termIndex}][term_id]" value="${term.id}">
                            </td>
                            <td>
                                <input type="text" class="form-control difference" readonly>
                            </td>
                        </tr>
                    `;
                    termIndex++;
                });

                termsHtml += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.html(termsHtml);
            $('#terms-section').show();

            // Calculate differences when achieved percentage changes
            $(document).on('input', '.achieved-percentage', function() {
                const achievedPercentage = parseFloat($(this).val()) || 0;
                const termPercentage = parseFloat($(this).data('term-percentage')) || 0;
                const difference = achievedPercentage - termPercentage;
                
                $(this).closest('tr').find('.difference').val(difference.toFixed(2));
            });
        }
    });
</script>
@endsection 