@extends('layouts.masterbooking')

@section('title')
    {{ trans('patient_trans.info_patient') }}
@stop
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="container-fluid py-4" dir="rtl">
        @if (session('info'))
            <div class="alert alert-warning">{{ session('info') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="patientForm"
            action="{{ route('medical_record_add_s.store') }}" method="post" enctype="multipart/form-data"
            class="needs-validation" novalidate>
            {{ csrf_field() }}

            <!-- Progress Tracker -->
            <div class="card progress-card mb-4 border-0 shadow-none">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0 ">
                                <i class="ri-profile-line align-middle me-2"></i>
                                {{ trans('patient_trans.complete_profile') }}
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary rounded-pill" id="progressPercentage">0%</span>
                        </div>
                    </div>
                    <div class="progress animated-progress custom-progress">
                        <div id="progressBar" class="progress-bar bg-gradient" role="progressbar" style="width: 0%"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

            <!-- Accordion Sections - All Expanded by Default -->
            <div class="accordion custom-accordion" id="patientInfoAccordion">
                <!-- Personal Information Section -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="personalInfoHeading">
                        <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#personalInfoCollapse" aria-expanded="true"
                            aria-controls="personalInfoCollapse">
                            <i class="ri-user-3-line me-2"></i>
                            {{ trans('patient_trans.personal_information') }}
                        </button>
                    </h2>
                    <div id="personalInfoCollapse" class="accordion-collapse collapse show"
                        aria-labelledby="personalInfoHeading" data-bs-parent="#patientInfoAccordion">
                        <div class="accordion-body pt-4">
                            <div class="row g-3">
                                <!-- Name Section -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-none mb-4">
                                        <div class="card-header bg-light border-0 py-2">
                                            <h6 class="mb-0">
                                                <i class="ri-id-card-line me-2"></i>
                                                {{ trans('patient_trans.patient_information') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @include('administrative.medical_record.master_data_info.secssion_name')
                                        </div>
                                    </div>
                                </div>

                                <!-- Gender & Age Section -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-none">
                                        <div class="card-header bg-light border-0 py-2">
                                            <h6 class="mb-0">
                                                <i class="ri-calendar-2-line me-2"></i>
                                                {{ trans('patient_trans.gender_age') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @include('administrative.medical_record.master_data_info.gender_age')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="contactInfoHeading">
                        <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#contactInfoCollapse" aria-expanded="true"
                            aria-controls="contactInfoCollapse">
                            <i class="ri-phone-line me-2"></i>
                            {{ trans('patient_trans.contact_information') }}
                        </button>
                    </h2>
                    <div id="contactInfoCollapse" class="accordion-collapse collapse show"
                        aria-labelledby="contactInfoHeading" data-bs-parent="#patientInfoAccordion">
                        <div class="accordion-body pt-4">
                            @include('administrative.medical_record.master_data_info.contact_information')
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div class="accordion-item border-0 shadow-sm rounded-3 overflow-hidden">
                    <h2 class="accordion-header" id="addressInfoHeading">
                        <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#addressInfoCollapse" aria-expanded="true"
                            aria-controls="addressInfoCollapse">
                            <i class="ri-map-pin-line me-2"></i>
                            {{ trans('patient_trans.address_information') }}
                        </button>
                    </h2>
                    <div id="addressInfoCollapse" class="accordion-collapse collapse show"
                        aria-labelledby="addressInfoHeading" data-bs-parent="#patientInfoAccordion">
                        <div class="accordion-body pt-4">
                            @include('administrative.medical_record.master_data_info.address_information')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                    <i class="ri-arrow-left-line align-middle me-1"></i>
                    {{ trans('patient_trans.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                    <i class="ri-save-line align-middle me-1"></i>
                    {{ trans('patient_trans.save_patient') }}
                </button>
            </div>
        </form>
    </div>

    {{-- سكريبتات المعاينة والتحقق البسيط + البروجريس (بدون AJAX) --}}
    @include('administrative.medical_record.partials.scripts_no_ajax')
@endsection
