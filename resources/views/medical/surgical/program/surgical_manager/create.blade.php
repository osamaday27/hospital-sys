@extends('layouts.masterbooking')

@section('title')
    {{ trans('surgical_trans.title') }}
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
                <h4 class="mb-sm-0 font">{{ trans('surgical_trans.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('surgical_booking.index') }}">{{ trans('surgical_trans.surgical_booking') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('surgical_trans.create_booking') }}</li>
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

            <form action="{{ route('surgical_booking.store') }}" method="POST" method="post"
                enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0" id="header_view">{{ trans('surgical_trans.create_booking') }}
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
                                                {{ trans('surgical_trans.booking_info') }}
                                            </button>
                                        </li>

                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="step1">
                                        <div class="row">
                                            @include('administrative.surgical.components.program.get_patients')
                                            <input name="patient_id" id="patient_id" value="" type="hidden" required>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade " id="step2">
                                        @include('administrative.surgical.components.program.bookinginfo')
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fixed-bottom bg-light p-3 text-end shadow border-top">
                    <button type="button" class="btn btn-light previestab d-none" id="prev-btn">
                        <i
                            class="ri-arrow-{{ app()->getLocale() === 'en' ? 'left' : 'right' }}-line label-icon align-middle me-2"></i>
                        {{ trans('surgical_trans.previous') }}
                    </button>

                    <button type="button" class="btn btn-success nexttab" id="next-btn">
                        {{ trans('surgical_trans.next') }}
                        <i
                            class="ri-arrow-{{ app()->getLocale() === 'en' ? 'right' : 'left' }}-line label-icon align-middle ms-2"></i>
                    </button>

                    <button type="submit" id="submit-btn" class="btn btn-primary d-none" onclick="validateTimeSlot()">
                        {{ trans('inpatient_trans.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                        text: `${translations.please_fill_required_fields}: ${
                        missingFields.map(field => translations[field] || field).join(', ')
                    }`,
                        showConfirmButton: true
                    });
                }
                return isValid;
            }

            function updateButtons() {
                // إخفاء زر "الرجوع" في أول خطوة
                document.getElementById("prev-btn").classList.toggle("d-none", currentStep === 1);

                // إخفاء زر "التالي" في الخطوة الرابعة
                document.getElementById("next-btn").classList.toggle("d-none", currentStep === 2);

                // إظهار زر "الحفظ" فقط في الخطوة الرابعة
                document.getElementById("submit-btn").classList.toggle("d-none", currentStep !== 2);
            }

            document.getElementById("next-btn").addEventListener("click", function() {
                if (currentStep === 2 && !validateTimeSlot()) {
                    return; // إذا كانت الخطوة 2 ولم يتم تحديد الوقت، لا نكمل الانتقال
                }

                if (validateStep(currentStep)) {
                    document.getElementById(`step${currentStep}-tab`).classList.remove("active");
                    document.getElementById(`step${currentStep}`).classList.remove("show", "active");

                    currentStep++;
                    document.getElementById(`step${currentStep}-tab`).classList.add("active");
                    document.getElementById(`step${currentStep}`).classList.add("show", "active");

                    updateButtons();
                }
            });

            document.getElementById("prev-btn").addEventListener("click", function() {
                document.getElementById(`step${currentStep}-tab`).classList.remove("active");
                document.getElementById(`step${currentStep}`).classList.remove("show", "active");

                currentStep--;
                document.getElementById(`step${currentStep}-tab`).classList.add("active");
                document.getElementById(`step${currentStep}`).classList.add("show", "active");

                updateButtons();
            });

            updateButtons();
        });

        function validateTimeSlot() {
            let isValid = true;

            // تحقق من وجود خيار time slot محدد فقط في المرحلة الثانية
            if (currentStep === 2) {
                 let isValid = true;
            // التحقق من وجود خيار time slot محدد
            const timeslot = document.querySelector('input[name="time_slots[]"]:checked'); // تحديد الوقت المحدد
            if (!timeslot) {
                isValid = false;
                // عرض رسالة تحذيرية عند عدم تحديد وقت
                let translations = @json(trans('surgical_trans'));

                Swal.fire({
                    title: translations.error_saving_reservation, // الخطأ في الحفظ
                    icon: "error",
                    text: translations.please_select_timeslot, // "من فضلك اختر الوقت"
                    showConfirmButton: true
                });
            }
            return isValid;
        }
        }
    </script>

@endsection
