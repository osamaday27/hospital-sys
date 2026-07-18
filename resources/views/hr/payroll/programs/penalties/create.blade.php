@extends('layouts.master')

@section('title')
    {{ trans('hr.add_penalty') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('hr_penalties.index') }}">{{ trans('hr.penalties_title') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.add_penalty') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.add_penalty') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_penalties.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="employee_id" class="form-label">{{ trans('hr.employee') }} <span class="text-danger">*</span></label>
                                    <select class="js-example-basic-single" 
                                            id="employee_id" name="employee_id" required>
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
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="violation_id" class="form-label">{{ trans('hr.violation') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('violation_id') is-invalid @enderror" 
                                            id="violation_id" name="violation_id" required>
                                        <option value="">{{ trans('hr.select_violation') }}</option>
                                        @foreach($violations as $violation)
                                            <option value="{{ $violation->id }}" {{ old('violation_id') == $violation->id ? 'selected' : '' }}>
                                                {{ $violation->violation_name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('violation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date" class="form-label">{{ trans('hr.date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                           id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="penalty_type" class="form-label">{{ trans('hr.penalty_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('penalty_type') is-invalid @enderror" 
                                            id="penalty_type" name="penalty_type" required>
                                        <option value="">{{ trans('hr.select_penalty_type') }}</option>
                                        <option value="amount" {{ old('penalty_type') == 'amount' ? 'selected' : '' }}>{{ trans('hr.penalty_amount') }}</option>
                                        <option value="days" {{ old('penalty_type') == 'days' ? 'selected' : '' }}>{{ trans('hr.penalty_days') }}</option>
                                    </select>
                                    @error('penalty_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="no_days_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="no_days" class="form-label">{{ trans('hr.no_days') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.5" class="form-control @error('no_days') is-invalid @enderror" 
                                           id="no_days" name="no_days" value="{{ old('no_days') }}" min="0.5">
                                    @error('no_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="amount_field" style="display: none;">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">{{ trans('hr.penalty_amount_value') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" min="0.01">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="reason" class="form-label">{{ trans('hr.reason') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('reason') is-invalid @enderror" 
                                              id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ trans('hr.save') }}</button>
                            <a href="{{ route('hr_penalties.index') }}" class="btn btn-light">{{ trans('hr.cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const penaltyTypeSelect = document.getElementById('penalty_type');
        const noDaysField = document.getElementById('no_days_field');
        const amountField = document.getElementById('amount_field');

        function toggleFields() {
            const selectedType = penaltyTypeSelect.value;
            
            if (selectedType === 'days') {
                noDaysField.style.display = 'block';
                amountField.style.display = 'none';
                document.getElementById('no_days').required = true;
                document.getElementById('amount').required = false;
            } else if (selectedType === 'amount') {
                noDaysField.style.display = 'none';
                amountField.style.display = 'block';
                document.getElementById('no_days').required = false;
                document.getElementById('amount').required = true;
            } else {
                noDaysField.style.display = 'none';
                amountField.style.display = 'none';
                document.getElementById('no_days').required = false;
                document.getElementById('amount').required = false;
            }
        }

        penaltyTypeSelect.addEventListener('change', toggleFields);
        
        // Initialize on page load
        toggleFields();
    });
</script>

@endsection