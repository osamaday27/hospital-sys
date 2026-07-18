@extends('layouts.masterbooking')

@section('title')
    {{ trans('patient_trans.info_patient') }}
@stop
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ trans('patient_trans.info_patient') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url()->previous() }}">
                                <i class="ri-arrow-left-line align-middle me-1"></i>
                                {{ trans('patient_trans.back') }}
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                @foreach (['success','error','warning','info'] as $msg)
                    @if(session()->has($msg))
                        <div class="alert alert-{{ $msg }} alert-dismissible fade show" role="alert">
                            {{ session($msg) }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif
                @endforeach

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="card-body p-4">
                    <form id="patientForm" action="{{ route('medical_record.update', $patient->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Progress Tracker -->
                        <div class="card progress-card mb-4 border-0 shadow-none">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="ri-profile-line me-2"></i>{{ trans('patient_trans.complete_profile') }}
                                </h5>
                                <span class="badge bg-primary rounded-pill" id="progressPercentage">0%</span>
                            </div>
                            <div class="progress animated-progress custom-progress">
                                <div id="progressBar" class="progress-bar bg-gradient" role="progressbar"
                                     style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- Accordion Sections -->
                        <div class="accordion custom-accordion" id="patientInfoAccordion">

                            <!-- Personal Information -->
                            <div class="accordion-item mb-3 shadow-sm rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="personalInfoHeading">
                                    <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#personalInfoCollapse">
                                        <i class="ri-user-3-line me-2"></i>{{ trans('patient_trans.personal_information') }}
                                    </button>
                                </h2>
                                <div id="personalInfoCollapse" class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-4">
                                        @include('administrative.medical_record.master_data_info_edit.secssion_name')
                                        @include('administrative.medical_record.master_data_info_edit.gender_age')
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="accordion-item mb-3 shadow-sm rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="contactInfoHeading">
                                    <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#contactInfoCollapse">
                                        <i class="ri-phone-line me-2"></i>{{ trans('patient_trans.contact_information') }}
                                    </button>
                                </h2>
                                <div id="contactInfoCollapse" class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-4">
                                        @include('administrative.medical_record.master_data_info_edit.contact_information')
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="accordion-item mb-3 shadow-sm rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="addressInfoHeading">
                                    <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#addressInfoCollapse">
                                        <i class="ri-map-pin-line me-2"></i>{{ trans('patient_trans.address_information') }}
                                    </button>
                                </h2>
                                <div id="addressInfoCollapse" class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-4">
                                        @include('administrative.medical_record.master_data_info_edit.address_information')
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Information -->
                            <div class="accordion-item mb-3 shadow-sm rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="medicalInfoHeading">
                                    <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#medicalInfoCollapse">
                                        <i class="ri-hospital-line me-2"></i>{{ trans('patient_trans.medical_data') }}
                                    </button>
                                </h2>
                                <div id="medicalInfoCollapse" class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-4">
                                        @include('administrative.medical_record.master_data_info_edit.medical_data')
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Information -->
                            <div class="accordion-item mb-3 shadow-sm rounded-3 overflow-hidden">
                                <h2 class="accordion-header" id="emergencyInfoHeading">
                                    <button class="accordion-button bg-primary bg-opacity-10 text-primary fw-semibold"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#emergencyInfoCollapse">
                                        <i class="ri-first-aid-kit-line me-2"></i>{{ trans('patient_trans.emergency_contact') }}
                                    </button>
                                </h2>
                                <div id="emergencyInfoCollapse" class="accordion-collapse collapse show">
                                    <div class="accordion-body pt-4">
                                        @include('administrative.medical_record.master_data_info_edit.emergency_contact')
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                <i class="ri-arrow-left-line me-1"></i>{{ trans('patient_trans.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                <i class="ri-save-line me-1"></i>{{ trans('patient_trans.save_patient') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <style>
        /* Custom Styles */
        .progress-card {
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .custom-accordion .accordion-button {
            border-radius: 8px !important;
            transition: all 0.3s ease;
        }

        .custom-accordion .accordion-button:not(.collapsed) {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            box-shadow: none;
        }

        .custom-accordion .accordion-item {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .animated-progress {
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }

        .bg-gradient {
            background: linear-gradient(90deg, #3b5df9 0%, #9543f9 100%);
        }

        /* Form Validation Styles */
        .is-invalid {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .is-valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .is-invalid~.invalid-feedback {
            display: block;
        }

        .required-field label:after {
            content: " *";
            color: #dc3545;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.25rem;
            }

            .accordion-body {
                padding: 1rem;
            }
        }
    </style>

    <script>
        // Real-time Form Validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('patientForm');
            const inputs = form.querySelectorAll('input, select, textarea');

            // Add event listeners for all inputs
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateField(this);
                    updateProgress();
                });

                input.addEventListener('blur', function() {
                    validateField(this);
                });
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                let isValid = true;

                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Show toast notification
                        showToast('{{ trans('patient_trans.form_validation_error') }}', 'danger');
                    }
                }
            });

            // Initial progress update
            updateProgress();

            // Initialize all accordion sections as expanded
            const accordionButtons = document.querySelectorAll('.accordion-button');
            accordionButtons.forEach(button => {
                button.classList.remove('collapsed');
                button.setAttribute('aria-expanded', 'true');
            });

            const accordionCollapses = document.querySelectorAll('.accordion-collapse');
            accordionCollapses.forEach(collapse => {
                collapse.classList.add('show');
            });
        });

        // Field validation function
        function validateField(field) {
            const feedback = field.nextElementSibling;

            if (field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('is-invalid');
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = '{{ trans('patient_trans.field_required') }}';
                }
                return false;
            }

            // Additional validation rules can be added here

            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        }

        // Progress calculation and update
        function updateProgress() {
            const form = document.getElementById('patientForm');
            const inputs = form.querySelectorAll('input, select, textarea');
            let filledCount = 0;

            inputs.forEach(input => {
                if (input.value.trim() !== '') {
                    filledCount++;
                }
            });

            const progressPercentage = Math.round((filledCount / inputs.length) * 100);
            const progressBar = document.getElementById('progressBar');
            const progressPercentageElement = document.getElementById('progressPercentage');

            progressBar.style.width = `${progressPercentage}%`;
            progressBar.setAttribute('aria-valuenow', progressPercentage);
            progressPercentageElement.textContent = `${progressPercentage}%`;

            // Update progress bar color based on percentage
            if (progressPercentage < 30) {
                progressBar.classList.remove('bg-warning', 'bg-success');
                progressBar.classList.add('bg-danger');
            } else if (progressPercentage < 70) {
                progressBar.classList.remove('bg-danger', 'bg-success');
                progressBar.classList.add('bg-warning');
            } else {
                progressBar.classList.remove('bg-danger', 'bg-warning');
                progressBar.classList.add('bg-success');
            }
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            const toastContainer = document.createElement('div');
            toastContainer.className = `toast align-items-center text-white bg-${type} border-0 show`;
            toastContainer.setAttribute('role', 'alert');
            toastContainer.setAttribute('aria-live', 'assertive');
            toastContainer.setAttribute('aria-atomic', 'true');
            toastContainer.style.position = 'fixed';
            toastContainer.style.bottom = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';

            toastContainer.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

            document.body.appendChild(toastContainer);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toastContainer.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toastContainer);
                }, 300);
            }, 5000);
        }
    </script>
@endsection
