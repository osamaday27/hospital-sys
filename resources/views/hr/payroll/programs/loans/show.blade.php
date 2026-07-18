@extends('layouts.master_table')

@section('title')
    {{ trans('hr.view_loan') }}
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('hr_loans.index') }}">{{ trans('hr.loans_management') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('hr.view_loan') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">{{ trans('hr.view_loan') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">

                    <!-- Employee -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.employee') }}</label>
                            <input type="text" class="form-control" 
                                   value="{{ $loan->employee->full_name }} ({{ $loan->employee->employee_code }})" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Loan Type -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.loan_type') }}</label>
                            <input type="text" class="form-control" 
                                   value="{{ trans('hr.' . $loan->loan_type) }}" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Loan Amount -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.loan_amount') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ number_format($loan->amount, 2) }}" readonly>
                                <span class="input-group-text">{{ trans('hr.currency') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Date -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.loan_date') }}</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($loan->loan_date)->format('Y-m-d') }}" readonly>
                        </div>
                    </div>

                    <!-- Number of Installments -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.no_installments') }}</label>
                            <input type="text" class="form-control" value="{{ $loan->no_installments }}" readonly>
                        </div>
                    </div>

                    <!-- Installment Value -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.installment_value') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="{{ number_format($loan->amount / max($loan->no_installments, 1), 2) }}" 
                                       readonly>
                                <span class="input-group-text">{{ trans('hr.currency') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Remaining Installments -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.remaining_installments') }}</label>
                            <input type="text" class="form-control" 
                                   value="{{ $loan->loan_type == 'instant' ? 1 : max($loan->no_installments - \Carbon\Carbon::parse($loan->loan_date)->diffInMonths(now()), 0) }}" 
                                   readonly>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">{{ trans('hr.notes') }}</label>
                            <textarea class="form-control" rows="3" readonly>{{ $loan->notes }}</textarea>
                        </div>
                    </div>

                </div>

                <div class="mt-4">
                    <a href="{{ route('hr_loans.index') }}" class="btn btn-light">
                        <i class="ri-arrow-go-back-line align-bottom me-1"></i> {{ trans('hr.back') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
