@extends('layouts.master_table')

@section('title')
    {{ trans('hr.add_loan') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_loans.index') }}">{{ trans('hr.loans_management') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.add_loan') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.add_loan') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_loans.store') }}" method="POST" id="loan-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">{{ trans('hr.employee') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            id="employee_id" name="employee_id" required>
                                        <option value="">{{ trans('hr.select_employee') }}</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }} ({{ $employee->employee_code }})
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
                                    <label for="loan_type" class="form-label">{{ trans('hr.loan_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('loan_type') is-invalid @enderror" 
                                            id="loan_type" name="loan_type" required>
                                        <option value="">{{ trans('hr.select_loan_type') }}</option>
                                        <option value="instant" {{ old('loan_type') == 'instant' ? 'selected' : '' }}>{{ trans('hr.instant_loan') }}</option>
                                        <option value="loan" {{ old('loan_type') == 'loan' ? 'selected' : '' }}>{{ trans('hr.installment_loan') }}</option>
                                    </select>
                                    @error('loan_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">{{ trans('hr.loan_amount') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount') }}" 
                                               min="1" required>
                                        <span class="input-group-text">{{ trans('hr.currency') }}</span>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="loan_date" class="form-label">{{ trans('hr.loan_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('loan_date') is-invalid @enderror" 
                                           id="loan_date" name="loan_date" 
                                           value="{{ old('loan_date', date('Y-m-d')) }}" required>
                                    @error('loan_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_installments" class="form-label">{{ trans('hr.no_installments') }}</label>
                                    <input type="number" class="form-control @error('no_installments') is-invalid @enderror" 
                                           id="no_installments" name="no_installments" 
                                           value="{{ old('no_installments', 1) }}" min="1" 
                                           {{ old('loan_type', 'instant') == 'instant' ? 'readonly' : '' }}>
                                    @error('no_installments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.installment_value') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="installment_value" readonly>
                                        <span class="input-group-text">{{ trans('hr.currency') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.remaining_installments') }}</label>
                                    <input type="text" class="form-control" id="remaining_installments" readonly>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ trans('hr.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line align-bottom me-1"></i> {{ trans('hr.save') }}
                            </button>
                            <a href="{{ route('hr_loans.index') }}" class="btn btn-light">
                                <i class="ri-arrow-go-back-line align-bottom me-1"></i> {{ trans('hr.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize date picker

            function calculateValues() {
                const amount = parseFloat($('#amount').val()) || 0;
                const loanType = $('#loan_type').val();
                let installments = parseInt($('#no_installments').val()) || 1;
                const loanDate = $('#loan_date').val();
                const today = new Date();
                let monthsPassed = 0;

                if (loanType === 'instant') {
                    installments = 1;
                    $('#no_installments').val(1).prop('readonly', true);
                } else {
                    // Allow editing when loan type is "loan"
                    if ($('#no_installments').prop('readonly')) {
                        $('#no_installments').val('').prop('readonly', false);
                    }
                    installments = parseInt($('#no_installments').val()) || 1;
                }

                // Calculate installment value
                if (amount > 0 && installments > 0) {
                    const installmentValue = amount / installments;
                    $('#installment_value').val(installmentValue.toFixed(2));
                } else {
                    $('#installment_value').val('0.00');
                }

                // Calculate months passed
                if (loanDate) {
                    const loanDateObj = new Date(loanDate);
                    monthsPassed = (today.getFullYear() - loanDateObj.getFullYear()) * 12 + (today.getMonth() - loanDateObj.getMonth());
                    if (today.getDate() < loanDateObj.getDate()) {
                        monthsPassed -= 1;
                    }
                }

                let remaining = Math.max(installments - monthsPassed, 0);
                $('#remaining_installments').val(remaining);
            }

            // When changing loan type
            $('#loan_type').change(function() {
                calculateValues();
            });

            // On amount or installments input
            $('#amount, #no_installments').on('input', function() {
                if ($(this).attr('id') === 'no_installments' && $(this).val() < 1) {
                    $(this).val(1);
                }
                calculateValues();
            });

            // On loan_date input
            $('#loan_date').on('input', function() {
                calculateValues();
            });

            // Initialize on page load
            calculateValues();

            // Form submit handling
            $('#loan-form').submit(function() {
                $('button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ trans("hr.saving") }}');
            });
        });


    </script>
@endsection
