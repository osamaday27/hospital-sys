@extends('layouts.master')

@section('title')
    {{ trans('hr.investigation_details') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">
                    <i class="ri-file-list-3-line align-middle me-2"></i>
                    {{ trans('hr.investigation_details') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ trans('hr.employees') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr_investigations.index') }}">{{ trans('hr.investigations') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.investigation_details') }}</li>
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
                            {{ trans('hr.investigation_details') }}
                        </h5>
                        <div class="flex-shrink-0">
                            <a href="{{ route('hr_investigations.edit', $investigation->id) }}" class="btn btn-primary">
                                <i class="ri-edit-line align-bottom me-1"></i>{{ trans('hr.edit') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-user-line me-1"></i>{{ trans('hr.employee') }}
                                </label>
                                <p class="mb-0">{{ $investigation->employee->full_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-file-list-line me-1"></i>{{ trans('hr.investigation_type') }}
                                </label>
                                <p class="mb-0">{{ $investigation->investigation_type }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-map-pin-line me-1"></i>{{ trans('hr.investigation_address') }}
                                </label>
                                <p class="mb-0">{{ $investigation->investigation_address }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-file-text-line me-1"></i>{{ trans('hr.investigation_details') }}
                                </label>
                                <p class="mb-0">{{ $investigation->investigation_details }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="ri-check-line me-1"></i>{{ trans('hr.action_taken') }}
                                </label>
                                <p class="mb-0">{{ $investigation->action_taken }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('hr_investigations.index') }}" class="btn btn-light">
                                    <i class="ri-arrow-left-line align-bottom me-1"></i>{{ trans('hr.back') }}
                                </a>
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
    .form-label {
        font-weight: 500;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endpush 