@extends('layouts.master_table')

@section('title')
    {{ trans('hr.edit_appraisal') }}
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
                        <li class="breadcrumb-item active">{{ trans('hr.edit_appraisal') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.edit_appraisal') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr_appraisal.update', $appraisal->id) }}" method="POST" id="appraisal-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Employee Information (Read-only) -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.employee') }}</label>
                                    <input type="text" value="{{ $appraisal->employee->full_name ?? '' }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.employee_code') }}</label>
                                    <input type="text" value="{{ $appraisal->employee_code }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.job') }}</label>
                                    <input type="text" value="{{ $appraisal->job->name_en ?? '' }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ trans('hr.department') }}</label>
                                    <input type="text" value="{{ $appraisal->department()->first()->name_ne ?? '' }}" class="form-control" readonly>
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
                                        <option value="monthly" {{ $appraisal->appraisal_interval == 'monthly' ? 'selected' : '' }}>{{ trans('hr.monthly') }}</option>
                                        <option value="quarterly" {{ $appraisal->appraisal_interval == 'quarterly' ? 'selected' : '' }}>{{ trans('hr.quarterly') }}</option>
                                        <option value="semi_annual" {{ $appraisal->appraisal_interval == 'semi_annual' ? 'selected' : '' }}>{{ trans('hr.semi_annual') }}</option>
                                        <option value="annual" {{ $appraisal->appraisal_interval == 'annual' ? 'selected' : '' }}>{{ trans('hr.annual') }}</option>
                                    </select>
                                    @error('appraisal_interval')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Terms Section -->
                        <div id="terms-section">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-3">{{ trans('hr.appraisal_terms') }}</h5>
                                </div>
                            </div>
                            <div id="terms-container">
                                @foreach($terms as $category => $details)
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="mb-0">{{ $category }}</h6>
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
                                                                        <td>
                                                                            <input type="number" 
                                                                                   name="terms[{{ $loop->index }}][achieved_percentage]" 
                                                                                   class="form-control achieved-percentage" 
                                                                                   min="0" max="100" step="0.01" required
                                                                                   value="{{ $detail->achieved_percentage }}"
                                                                                   data-term-percentage="{{ $detail->term->percentage }}">
                                                                            <input type="hidden" name="terms[{{ $loop->index }}][term_id]" value="{{ $detail->term_id }}">
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" class="form-control difference" value="{{ $detail->difference }}" readonly>
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

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('hr_appraisal.index') }}" class="btn btn-secondary">
                                        {{ trans('hr.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
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
        // Calculate differences when achieved percentage changes
        $(document).on('input', '.achieved-percentage', function() {
            const achievedPercentage = parseFloat($(this).val()) || 0;
            const termPercentage = parseFloat($(this).data('term-percentage')) || 0;
            const difference = achievedPercentage - termPercentage;
            
            $(this).closest('tr').find('.difference').val(difference.toFixed(2));
        });
    });
</script>
@endsection 