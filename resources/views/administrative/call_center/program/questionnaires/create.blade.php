@extends('layouts.masterbooking')

@section('title')
{{ trans('call_center_trans.create_new_questionnaire') }}
@stop

<style>
    .fixed-bottom {
        position: fixed !important;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('call_center_trans.patient_questionnaires') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('all_questionnaires.index') }}">{{ trans('call_center_trans.questionnaires_mangment') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.create_new_questionnaire') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form action="" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0" id="header_view">
                                    {{ trans('call_center_trans.create_new_questionnaire') }}
                                </h4>
                                <div id="patient-details" name="patient-details"></div>
                            </div>
                            <div class="card-body">
                                <div class="step-arrow-nav mb-4">
                                    <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link active" id="step1-tab" data-bs-toggle="pill"
                                                data-bs-target="#step1" type="button" disabled>
                                                {{ trans('surgical_trans.select_patient') }}
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link " id="step2-tab" data-bs-toggle="pill"
                                                data-bs-target="#step2" type="button" disabled>
                                                {{ trans('call_center_trans.visit_information') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="step1">
                                        <div class="row">
                                            @include('administrative.call_center.components.program.get_patients')
                                            <input name="patient_id" id="patient_id" value="" type="hidden" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentStep = 1;
            const totalSteps = document.querySelectorAll('.tab-pane').length;
            function validateStep(step) {
                let isValid = true;
                let missingFields = [];
                const requiredFields = document.querySelector(`#step${step}`).querySelectorAll("[required]");
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add("is-invalid");
                        missingFields.push(field.getAttribute("placeholder") || field.name);
                    } else {
                        field.classList.remove("is-invalid");
                    }
                });
                if (!isValid) {
                    let translations = @json(trans('inpatient_trans'));

                    Swal.fire({
                        title: translations.error_saving_reservation,
                        icon: "error",
                        text: `${translations.please_fill_required_fields}: ${missingFields.map(field => translations[field] || field).join(', ')
                            }`,
                        showConfirmButton: true
                    });
                }
                return isValid;
            }
        });
    </script>
@endsection
