@extends('layouts.master')

@section('title')
    {{ __('hr.payslip_details') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">
                    <i class="ri-file-list-3-line align-middle me-2"></i>
                    {{ __('hr.payslip_details') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ __('hr.payroll') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr_payslips.index') }}">{{ __('hr.payslips') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('hr.payslip_details') }}</li>
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
                            <i class="ri-file-info-line align-middle me-2"></i>
                            {{ __('hr.payslip_details') }}
                        </h5>
                        <div class="flex-shrink-0">
                            <a href="{{ route('hr_payslips.edit', $payslip) }}" class="btn btn-warning">
                                <i class="ri-pencil-line align-bottom me-1"></i> {{ __('hr.edit') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border shadow-none">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="ri-user-settings-line align-middle me-2"></i>
                                        {{ __('hr.employee_information') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm">
                                                <span class="avatar-title bg-primary rounded-circle">
                                                    <i class="ri-user-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $payslip->employee->full_name }}</h5>
                                            <p class="text-muted mb-0">{{ __('hr.employee') }}</p>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="ps-0">
                                                        <i class="ri-calendar-line me-2"></i>{{ __('hr.period') }}:
                                                    </th>
                                                    <td class="text-muted">
                                                        {{ $payslip->date_from->format('Y-m-d') }} - {{ $payslip->date_to->format('Y-m-d') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" class="ps-0">
                                                        <i class="ri-checkbox-circle-line me-2"></i>{{ __('hr.status') }}:
                                                    </th>
                                                    <td>
                                                        <span class="badge bg-{{ $payslip->status === 'draft' ? 'warning' : ($payslip->status === 'confirmed' ? 'info' : 'success') }}">
                                                            {{ __('hr.status_' . $payslip->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border shadow-none">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="ri-money-dollar-circle-line align-middle me-2"></i>
                                {{ __('hr.salary_details') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle table-nowrap mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th scope="col" class="fw-semibold">{{ __('hr.salary_rule') }}</th>
                                            <th scope="col" class="fw-semibold">{{ __('hr.type') }}</th>
                                            <th scope="col" class="fw-semibold text-end">{{ __('hr.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payslip->salary_lines as $line)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-money-dollar-circle-line me-2 text-primary"></i>
                                                        {{ $line->salary_rule->name }}
                                                    </div>
                                                </td>
                                                <td class="text-{{ $line->salary_rule_type === 'addition' ? 'success' : 'danger' }}">
                                                    {{ $line->salary_rule_type === 'addition' ? '+' : '-' }}{{ number_format($line->amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th class="text-end">{{ __('hr.total_amount') }}</th>
                                            <th class="text-{{ $payslip->total_amount >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($payslip->total_amount, 2) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        height: 3rem;
        width: 3rem;
    }
    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        font-size: 1.5rem;
    }
    .table > :not(caption) > * > * {
        padding: 1rem;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .table-borderless > :not(caption) > * > * {
        border-bottom-width: 0;
    }
</style>
@endpush 