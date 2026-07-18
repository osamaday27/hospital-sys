@extends('layouts.master_table')

@section('title')
    {{ trans('hr.edit_offer') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ trans('hr.edit_offer') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.hr') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.recruitment') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr_offers.index') }}">{{ trans('hr.offers_management') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.edit_offer') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ trans('hr.edit_offer') }}</h4>

                    <form method="POST" action="{{ route('hr_offers.update', $offer->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Candidate Selection -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="candidate_id" class="form-label">{{ trans('hr.candidate') }} <span class="text-danger">*</span></label>
                                    <select class="form-select js-example-basic-single @error('candidate_id') is-invalid @enderror" 
                                            id="candidate_id" name="candidate_id" required>
                                        <option value="">{{ trans('hr.select_candidate') }}</option>
                                        @forelse($candidates as $candidate)
                                            <option value="{{ $candidate->id }}" 
                                                    data-job="{{ $candidate->requisition->job->name_ar ?? $candidate->requisition->job->job_title }}"
                                                    data-requisition="{{ $candidate->requisition->requisition_name }}"
                                                    {{ old('candidate_id', $offer->candidate_id) == $candidate->id ? 'selected' : '' }}>
                                                {{ $candidate->full_name }} - {{ $candidate->requisition->requisition_name }} - {{ $candidate->requisition->job->name_ar ?? $candidate->requisition->job->job_title }}
                                            </option>
                                        @empty
                                            <option value="" disabled>{{ trans('hr.no_available_candidates') }}</option>
                                        @endforelse
                                    </select>
                                    @error('candidate_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="salary" class="form-label">{{ trans('hr.salary') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" value="{{ old('salary', $offer->salary) }}"
                                           placeholder="{{ trans('hr.enter_salary') }}" required>
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Offer Text -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="offer_text" class="form-label">{{ trans('hr.offer_text') }} <span class="text-danger">*</span></label>
                                    <textarea name="offer_text" 
                                              id="offer_text" 
                                              class="form-control @error('offer_text') is-invalid @enderror" 
                                              rows="6" 
                                              placeholder="{{ trans('hr.enter_offer_text') }}" required>{{ old('offer_text', $offer->offer_text) }}</textarea>
                                    @error('offer_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Status -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6>{{ trans('hr.current_status') }}:</h6>
                                    <span class="badge bg-{{ $offer->status === 'approved' ? 'success' : ($offer->status === 'rejected' ? 'danger' : 'secondary') }}">
                                        {{ trans('hr.' . $offer->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Display Selected Info -->
                        <div id="candidate_info" class="row {{ old('candidate_id', $offer->candidate_id) ? '' : 'd-none' }}">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6>{{ trans('hr.selected_candidate_info') }}:</h6>
                                    <p class="mb-1"><strong>{{ trans('hr.job_title') }}:</strong> <span id="selected_job">{{ $offer->candidate->requisition->job->name_ar ?? $offer->candidate->requisition->job->job_title ?? '' }}</span></p>
                                    <p class="mb-0"><strong>{{ trans('hr.requisition') }}:</strong> <span id="selected_requisition">{{ $offer->candidate->requisition->requisition_name ?? '' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('hr_offers.index') }}" class="btn btn-secondary">
                                        <i class="ri-arrow-left-line"></i> {{ trans('hr.back') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line"></i> {{ trans('hr.update') }}
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
        $('.js-example-basic-single').select2({
            placeholder: "{{ trans('hr.select_candidate') }}",
            allowClear: true
        });

        $('#candidate_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                var job = selectedOption.data('job');
                var requisition = selectedOption.data('requisition');
                
                $('#selected_job').text(job);
                $('#selected_requisition').text(requisition);
                $('#candidate_info').removeClass('d-none');
            } else {
                $('#candidate_info').addClass('d-none');
            }
        });
    });
</script>
@endsection
