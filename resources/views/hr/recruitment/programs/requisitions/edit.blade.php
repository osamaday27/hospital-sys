@extends('layouts.master_table')

@section('title')
    {{ trans('hr.edit_requisition') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr_requisitions.index') }}">{{ trans('hr.recruitment_requisitions') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_requisition') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.edit_requisition') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_requisitions.update', $requisition->id) }}" method="POST" id="requisition-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Requisition Name & Job Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="requisition_name" class="form-label">{{ trans('hr.requisition_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('requisition_name') is-invalid @enderror" 
                                           id="requisition_name" name="requisition_name" 
                                           value="{{ old('requisition_name') ?? $requisition->requisition_name }}" required>
                                    @error('requisition_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="job_id" class="form-label">{{ trans('hr.job_title') }} <span class="text-danger">*</span></label>
                                    <select name="job_id" id="job_id" class="form-select js-example-basic-single @error('job_id') is-invalid @enderror" required>
                                        <option value="">{{ trans('hr.select_job') }}</option>
                                        @foreach($jobs as $job)
                                            <option value="{{ $job->id }}" {{ old('job_id', $requisition->job_id) == $job->id ? 'selected' : '' }}>
                                                {{ app()->getLocale() == 'ar' ? $job->name_ar : $job->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('job_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Number of Candidates -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number_of_candidates" class="form-label">{{ trans('hr.number_of_candidates') }} <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="number_of_candidates" 
                                           id="number_of_candidates" 
                                           class="form-control @error('number_of_candidates') is-invalid @enderror" 
                                           value="{{ old('number_of_candidates', $requisition->number_of_candidates) }}" 
                                           placeholder="{{ trans('hr.enter_number_of_candidates') }}"
                                           min="1"
                                           required>
                                    @error('number_of_candidates')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Required Experience Years -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="required_experience_years" class="form-label">{{ trans('hr.required_experience_years') }}</label>
                                    <input type="number" 
                                           name="required_experience_years" 
                                           id="required_experience_years" 
                                           class="form-control @error('required_experience_years') is-invalid @enderror" 
                                           value="{{ old('required_experience_years', $requisition->required_experience_years) }}" 
                                           placeholder="{{ trans('hr.enter_required_experience_years') }}"
                                           min="0">
                                    @error('required_experience_years')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Badge for Display Only -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.status') }}</label>
                                    <div class="form-control-plaintext">
                                        @if($requisition->status === 'approved')
                                            <span class="badge bg-success">{{ trans('hr.approved') }}</span>
                                        @elseif($requisition->status === 'refused')
                                            <span class="badge bg-danger">{{ trans('hr.refused') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ trans('hr.pending') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salary -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salary" class="form-label">{{ trans('hr.salary') }}</label>
                                    <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" value="{{ old('salary', $requisition->salary) }}"
                                           placeholder="{{ trans('hr.enter_salary') }}">
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Specifications -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="specifications" class="form-label">{{ trans('hr.job_specifications') }}</label>
                                    <textarea name="specifications" 
                                              id="specifications" 
                                              class="form-control @error('specifications') is-invalid @enderror" 
                                              rows="5" 
                                              placeholder="{{ trans('hr.enter_job_specifications') }}">{{ old('specifications', $requisition->specifications) }}</textarea>
                                    @error('specifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('hr_requisitions.index') }}" class="btn btn-light">
                                        {{ trans('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                        <span class="spinner-border spinner-border-sm me-1 d-none" id="spinner"></span>
                                        {{ trans('hr.update') }}
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
        $('.js-example-basic-single').select2();
        
        $('#requisition-form').on('submit', function() {
            $('#submit-btn').prop('disabled', true);
            $('#spinner').removeClass('d-none');
        });
    });
</script>
@endsection
