@extends('layouts.master_table')

@section('title')
    {{ trans('hr.view_appraisal') }}
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
                        <li class="breadcrumb-item active">{{ trans('hr.view_appraisal') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ trans('hr.view_appraisal') }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('hr_appraisal.edit', $appraisal->id) }}" class="btn btn-warning">
                            <i class="ri-pencil-line"></i> {{ trans('hr.edit') }}
                        </a>
                        <a href="{{ route('hr_appraisal.index') }}" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> {{ trans('hr.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.employee') }}</label>
                                <p class="form-control-static">{{ $appraisal->employee->full_name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.employee_code') }}</label>
                                <p class="form-control-static">{{ $appraisal->employee_code }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.job') }}</label>
                                <p class="form-control-static">{{ $appraisal->job->name_en ?? '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.department') }}</label>
                                <p class="form-control-static">{{ $appraisal->department()->first()->name_ne ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.hiring_date') }}</label>
                                <p class="form-control-static">{{ $appraisal->hiring_date ? $appraisal->hiring_date->format('Y-m-d') : '' }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.appraisal_interval') }}</label>
                                <p class="form-control-static">{{ trans('hr.' . $appraisal->appraisal_interval) }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.total_achieved') }}</label>
                                <p class="form-control-static text-primary fw-bold">{{ $appraisal->total_achieved_percentage }}%</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.total_difference') }}</label>
                                <p class="form-control-static {{ $appraisal->total_difference >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                    {{ $appraisal->total_difference >= 0 ? '+' : '' }}{{ $appraisal->total_difference }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Appraisal Details -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">{{ trans('hr.appraisal_details') }}</h5>
                            
                            @php
                                $detailsByCategory = $appraisal->details->groupBy('term.category');
                            @endphp

                            @foreach($detailsByCategory as $category => $details)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-primary">
                                                <h6 class="mb-0 text-white">{{ $category }}</h6>
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
                                                            @foreach($details as $detail)
                                                                <tr>
                                                                    <td>{{ $detail->term->term_name }}</td>
                                                                    <td>{{ $detail->term->percentage }}%</td>
                                                                    <td>{{ $detail->achieved_percentage }}%</td>
                                                                    <td class="{{ $detail->difference >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                                                        {{ $detail->difference >= 0 ? '+' : '' }}{{ $detail->difference }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h6 class="mb-0 text-white">{{ trans('hr.summary') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text">{{ $appraisal->total_achieved_percentage }}%</h4>
                                                <p class="text-muted">{{ trans('hr.total_achieved_percentage') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="{{ $appraisal->total_difference >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $appraisal->total_difference >= 0 ? '+' : '' }}{{ $appraisal->total_difference }}
                                                </h4>
                                                <p class="text-muted">{{ trans('hr.total_difference') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h4 class="text-info">{{ $appraisal->details->count() }}</h4>
                                                <p class="text-muted">{{ trans('hr.total_terms') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 