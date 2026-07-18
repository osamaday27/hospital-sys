@extends('layouts.master_table')

@section('title')
    {{ trans('hr.view_offer') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ trans('hr.view_offer') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.hr') }}</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('hr.recruitment') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr_offers.index') }}">{{ trans('hr.offers_management') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('hr.view_offer') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">{{ trans('hr.offer_details') }}</h4>
                        <div class="d-flex gap-2">
                            @if($offer->status === 'new')
                                <button type="button" class="btn btn-success btn-sm" onclick="approveOffer({{ $offer->id }})">
                                    <i class="ri-check-line"></i> {{ trans('hr.approve') }}
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectOffer({{ $offer->id }})">
                                    <i class="ri-close-line"></i> {{ trans('hr.reject') }}
                                </button>
                            @endif
                            <a href="{{ route('hr_offers.edit', $offer->id) }}" class="btn btn-warning btn-sm">
                                <i class="ri-edit-line"></i> {{ trans('hr.edit') }}
                            </a>
                            <a href="{{ route('hr_offers.index') }}" class="btn btn-secondary btn-sm">
                                <i class="ri-arrow-left-line"></i> {{ trans('hr.back') }}
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Candidate Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ trans('hr.candidate_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ trans('hr.full_name') }}:</strong></td>
                                            <td>{{ $offer->candidate->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.email') }}:</strong></td>
                                            <td>{{ $offer->candidate->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.national_id') }}:</strong></td>
                                            <td>{{ $offer->candidate->national_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.gender') }}:</strong></td>
                                            <td>{{ $offer->candidate->gender === 'male' ? trans('hr.male') : trans('hr.female') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.age') }}:</strong></td>
                                            <td>{{ $offer->candidate->age }} {{ trans('hr.years') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.status') }}:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $offer->candidate->status_badge }}">
                                                    {{ $offer->candidate->status_text }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($offer->candidate->cv_file)
                                        <tr>
                                            <td><strong>{{ trans('hr.cv_file') }}:</strong></td>
                                            <td>
                                                <a href="{{ asset('attachments/' . ltrim($offer->candidate->cv_file, '/')) }}" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-download-line"></i> {{ trans('hr.download') }}
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Job & Requisition Information -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ trans('hr.job_requisition_information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ trans('hr.requisition_name') }}:</strong></td>
                                            <td>{{ $offer->candidate->requisition->requisition_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.job_title') }}:</strong></td>
                                            <td>{{ $offer->job->name_ar ?? $offer->job->job_title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.required_experience') }}:</strong></td>
                                            <td>{{ $offer->candidate->requisition->required_experience_years ?? 0 }} {{ trans('hr.years') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.requisition_salary') }}:</strong></td>
                                            <td>{{ number_format($offer->candidate->requisition->salary ?? 0, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.total_positions') }}:</strong></td>
                                            <td>{{ $offer->candidate->requisition->number_of_candidates }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ trans('hr.filled_positions') }}:</strong></td>
                                            <td>{{ $offer->candidate->requisition->accepted_candidates_count }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Offer Details -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ trans('hr.offer_details') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ trans('hr.offered_salary') }}:</strong></td>
                                                    <td class="text-success fs-5">{{ number_format($offer->salary, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ trans('hr.offer_status') }}:</strong></td>
                                                    <td>
                                                        <span class="badge fs-6 bg-{{ $offer->status === 'approved' ? 'success' : ($offer->status === 'rejected' ? 'danger' : 'secondary') }}">
                                                            {{ trans('hr.' . $offer->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ trans('hr.created_by') }}:</strong></td>
                                                    <td>{{ $offer->creator->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ trans('hr.created_at') }}:</strong></td>
                                                    <td>{{ $offer->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>{{ trans('hr.offer_text') }}:</h6>
                                            <div class="border p-3 bg-light rounded">
                                                {!! nl2br(e($offer->offer_text)) !!}
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
    
<script>
    function approveOffer(id) {
        if (confirm('{{ trans("hr.confirm_approve_offer") }}')) {
            $.ajax({
                url: '/hr_offers/' + id + '/approve',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error approving offer');
                }
            });
        }
    }

    function rejectOffer(id) {
        if (confirm('{{ trans("hr.confirm_reject_offer") }}')) {
            $.ajax({
                url: '/hr_offers/' + id + '/reject',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error rejecting offer');
                }
            });
        }
    }
</script>
@endsection
