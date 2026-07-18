@extends('layouts.master_table')

@section('title')
    {{ trans('hr.view_requisition') }}
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
                        <li class="breadcrumb-item active">{{ trans('hr.view_requisition') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ trans('hr.requisition_details') }}</h4>
                    <div>
                        @if($requisition->status === 'approved')
                            <span class="badge bg-success">{{ trans('hr.approved') }}</span>
                        @elseif($requisition->status === 'refused')
                            <span class="badge bg-danger">{{ trans('hr.refused') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ trans('hr.pending') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                                                    <!-- Job Title -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.job_title') }}</label>
                                    <p class="form-control-plaintext">{{ $requisition->job ? (app()->getLocale() == 'ar' ? $requisition->job->name_ar : $requisition->job->name_en) : '' }}</p>
                                </div>
                            </div>

                        <!-- Number of Candidates -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.number_of_candidates') }}</label>
                                <p class="form-control-plaintext">{{ $requisition->number_of_candidates }}</p>
                            </div>
                        </div>

                        <!-- Required Experience -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.required_experience_years') }}</label>
                                <p class="form-control-plaintext">
                                    {{ $requisition->required_experience_years ? $requisition->required_experience_years . ' ' . trans('hr.years') : trans('hr.not_specified') }}
                                </p>
                            </div>
                        </div>

                        <!-- Created By -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.created_by') }}</label>
                                <p class="form-control-plaintext">{{ $requisition->creator->name ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Approved By -->
                        @if($requisition->approved_by)
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.approved_by') }}</label>
                                <p class="form-control-plaintext">{{ $requisition->approver->name ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Approved At -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.approved_at') }}</label>
                                <p class="form-control-plaintext">{{ $requisition->approved_at ? $requisition->approved_at->format('Y-m-d H:i:s') : '' }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Created At -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.create_date') }}</label>
                                <p class="form-control-plaintext">{{ $requisition->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>

                        <!-- Specifications -->
                        @if($requisition->specifications)
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ trans('hr.job_specifications') }}</label>
                                <div class="border rounded p-3">
                                    {{ $requisition->specifications }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('hr_requisitions.index') }}" class="btn btn-light">
                            {{ trans('hr.back') }}
                        </a>
                        <a href="{{ route('hr_requisitions.edit', $requisition->id) }}" class="btn btn-warning">
                            <i class="ri-edit-line"></i> {{ trans('hr.edit') }}
                        </a>
                        @if($requisition->status === 'pending')
                            <button type="button" class="btn btn-success" onclick="approveFunction({{ $requisition->id }})">
                                <i class="ri-check-line"></i> {{ trans('hr.approve') }}
                            </button>
                            <button type="button" class="btn btn-danger" onclick="refuseFunction({{ $requisition->id }})">
                                <i class="ri-close-line"></i> {{ trans('hr.refuse') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Candidates Section -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ trans('hr.candidates') }}</h5>
                    @if($requisition->isApproved())
                        <a href="{{ route('hr_candidates.create') }}?requisition_id={{ $requisition->id }}" class="btn btn-sm btn-success">
                            <i class="ri-add-line"></i> {{ trans('hr.add_candidate') }}
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            {{ trans('hr.candidates_count') }}: {{ $requisition->candidates->count() }}/{{ $requisition->number_of_candidates }}
                        </small>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $requisition->number_of_candidates > 0 ? ($requisition->candidates->count() / $requisition->number_of_candidates) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    @if($requisition->candidates->count() > 0)
                        <div class="list-group">
                            @foreach($requisition->candidates as $candidate)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $candidate->full_name }}</div>
                                        <small class="text-muted">{{ $candidate->email }}</small>
                                        <br>
                                        <small class="text-muted">{{ $candidate->city }}, {{ $candidate->nationality }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hr_candidates.show', $candidate->id) }}" class="btn btn-outline-info btn-sm">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">{{ trans('hr.no_candidates_yet') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

<script>
    function approveFunction(id) {
        if (confirm('{{ __('hr.confirm_approve_requisition') }}')) {
            var url = '{{ route('hr_requisitions.approve', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'There was an error approving the requisition.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        }
    }

    function refuseFunction(id) {
        if (confirm('{{ __('hr.confirm_refuse_requisition') }}')) {
            var url = '{{ route('hr_requisitions.refuse', ':id') }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'There was an error refusing the requisition.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        }
    }
</script>
@endsection
