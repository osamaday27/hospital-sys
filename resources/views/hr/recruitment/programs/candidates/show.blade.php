@extends('layouts.master_table')

@section('title')
    {{ trans('hr.view_candidate') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('hr_candidates.index') }}">{{ trans('hr.recruitment_candidates') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('hr.view_candidate') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('hr.candidate_details') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Personal Information Section -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">{{ trans('hr.personal_information') }}</h5>
                        <div class="row">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.full_name') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->full_name }}</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.email') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->email }}</p>
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.gender') }}</label>
                                    <p class="form-control-plaintext">
                                        {{ $candidate->gender === 'male' ? trans('hr.male') : trans('hr.female') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Age -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.age') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->age }} {{ trans('hr.years') }}</p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.status') }}</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $candidate->status_badge }}">
                                            {{ $candidate->status_text }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Birth Date -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.date_birth') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->birth_date ? $candidate->birth_date->format('Y-m-d') : '' }}</p>
                                </div>
                            </div>

                            <!-- National ID -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.identification_number') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->national_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information Section -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">{{ trans('hr.location_information') }}</h5>
                        <div class="row">
                            <!-- City -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.city') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->city }}</p>
                                </div>
                            </div>

                            <!-- Nationality -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.nationality') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->nationality }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Names Section -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">{{ trans('hr.detailed_names') }}</h5>
                        <div class="row">
                            <!-- First Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.first_name') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->first_name }}</p>
                                </div>
                            </div>

                            <!-- Second Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.second_name') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->second_name }}</p>
                                </div>
                            </div>

                            <!-- Third Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.third_name') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->third_name }}</p>
                                </div>
                            </div>

                            <!-- Fourth Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.fourth_name') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->fourth_name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Information Section -->
                    <div>
                        <h5 class="text-primary mb-3">{{ trans('hr.system_information') }}</h5>
                        <div class="row">
                            <!-- Created At -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.create_date') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <!-- Updated At -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.last_update') }}</label>
                                    <p class="form-control-plaintext">{{ $candidate->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            <!-- CV File -->
                            @if($candidate->cv_file)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ trans('hr.cv_file') }}</label>
                                    <div class="form-control-plaintext">
                                        <a href="{{ asset('attachments/' . ltrim($candidate->cv_file, '/')) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="ri-download-line"></i> {{ trans('hr.download_cv') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('hr_candidates.index') }}" class="btn btn-light">
                            {{ trans('hr.back') }}
                        </a>
                        <a href="{{ route('hr_candidates.edit', $candidate->id) }}" class="btn btn-warning">
                            <i class="ri-edit-line"></i> {{ trans('hr.edit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requisition Information Section -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ trans('hr.job_requisition_details') }}</h5>
                </div>
                <div class="card-body">
                    @if($candidate->requisition)
                        <!-- Job Title -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ trans('hr.job_title') }}</label>
                            <p class="form-control-plaintext">{{ $candidate->requisition->job_title }}</p>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ trans('hr.status') }}</label>
                            <div class="form-control-plaintext">
                                @if($candidate->requisition->status === 'approved')
                                    <span class="badge bg-success">{{ trans('hr.approved') }}</span>
                                @elseif($candidate->requisition->status === 'refused')
                                    <span class="badge bg-danger">{{ trans('hr.refused') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ trans('hr.pending') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Required Experience -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ trans('hr.required_experience') }}</label>
                            <p class="form-control-plaintext">
                                {{ $candidate->requisition->required_experience_years ? $candidate->requisition->required_experience_years . ' ' . trans('hr.years') : trans('hr.not_specified') }}
                            </p>
                        </div>

                        <!-- Candidates Count -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ trans('hr.candidates_progress') }}</label>
                            <div class="form-control-plaintext">
                                <small class="text-muted">
                                    {{ $candidate->requisition->candidates->count() }}/{{ $candidate->requisition->number_of_candidates }} {{ trans('hr.candidates') }}
                                </small>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $candidate->requisition->number_of_candidates > 0 ? ($candidate->requisition->candidates->count() / $candidate->requisition->number_of_candidates) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications -->
                        @if($candidate->requisition->specifications)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ trans('hr.job_specifications') }}</label>
                            <div class="border rounded p-2 small">
                                {{ Str::limit($candidate->requisition->specifications, 200) }}
                            </div>
                        </div>
                        @endif

                        <!-- View Requisition Button -->
                        <div class="text-center">
                            <a href="{{ route('hr_requisitions.show', $candidate->requisition->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-eye-line"></i> {{ trans('hr.view_requisition') }}
                            </a>
                        </div>
                    @else
                        <p class="text-muted text-center">{{ trans('hr.no_requisition_found') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
