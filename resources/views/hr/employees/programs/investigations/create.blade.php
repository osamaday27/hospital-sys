@extends('layouts.master')

@section('title')
    {{ trans('hr.add_investigation') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">
                    <i class="ri-file-list-3-line align-middle me-2"></i>
                    {{ trans('hr.add_investigation') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_investigations.index') }}">{{ trans('hr.investigations') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.add_investigation') }}</li>
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
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_investigations.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">
                                    <i class="ri-user-line me-1"></i>{{ trans('hr.employee') }}
                                </label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">{{ trans('hr.select_employee') }}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="investigation_type" class="form-label">
                                    <i class="ri-file-list-line me-1"></i>{{ trans('hr.investigation_type') }}
                                </label>
                                <input type="text" class="form-control @error('investigation_type') is-invalid @enderror" 
                                       id="investigation_type" name="investigation_type" value="{{ old('investigation_type') }}" required>
                                @error('investigation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="investigation_address" class="form-label">
                                    <i class="ri-map-pin-line me-1"></i>{{ trans('hr.investigation_address') }}
                                </label>
                                <input type="text" class="form-control @error('investigation_address') is-invalid @enderror" 
                                       id="investigation_address" name="investigation_address" value="{{ old('investigation_address') }}" required>
                                @error('investigation_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="investigation_details" class="form-label">
                                    <i class="ri-file-text-line me-1"></i>{{ trans('hr.investigation_details') }}
                                </label>
                                <textarea class="form-control @error('investigation_details') is-invalid @enderror" 
                                          id="investigation_details" name="investigation_details" rows="4" required>{{ old('investigation_details') }}</textarea>
                                @error('investigation_details')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="action_taken" class="form-label">
                                    <i class="ri-check-line me-1"></i>{{ trans('hr.action_taken') }}
                                </label>
                                <textarea class="form-control @error('action_taken') is-invalid @enderror" 
                                          id="action_taken" name="action_taken" rows="4" required>{{ old('action_taken') }}</textarea>
                                @error('action_taken')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hr_investigations.index') }}" class="btn btn-light">
                                        <i class="ri-close-line align-bottom me-1"></i>{{ trans('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line align-bottom me-1"></i>{{ trans('hr.save') }}
                                    </button>
                                </div>
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
    .form-label {
        font-weight: 500;
    }
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endpush 