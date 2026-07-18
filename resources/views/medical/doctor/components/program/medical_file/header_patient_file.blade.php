<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        <img src="{{ asset('assets/images/his_logo.png') }}" alt="Profile Background" class="profile-wid-img" />
    </div>
</div>

<div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
    <div class="row g-4">
        <!-- صورة المريض -->
        <div class="col-auto">
            <div class="avatar-lg">
                <img style="height: 95px; width: 120px; background-color: transparent;"
                    src="@if ($patient->img) {{ asset($patient->img) }} @else {{ asset('assets/images/users/user-dummy-img.jpg') }} @endif"
                    alt="patient-img" class="img-thumbnail rounded-circle shadow" />
            </div>
        </div>

        <!-- بيانات المريض -->
        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">
                    <i class="ri-user-3-line me-2 text-info"></i>{{ $patient->full_name }}
                </h3>
                <p class="text-white-75 mb-2">
                    <i class="ri-calendar-line me-1"></i> {{ $patient->date_of_birth }}
                    <span class="badge bg-light text-dark ms-2">{{ $patient->age ?? '-' }}</span>
                </p>

                <!-- العنوان -->
                <div class="hstack text-white-50 gap-2 mb-2">
                    <div>
                        <i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $patient->full_address }}
                    </div>
                    <div>
                        <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $patient->countries->name ?? '-' }}
                        @if ($patient->government)
                            / {{ $patient->government->name }}
                        @endif
                        @if ($patient->City)
                            / {{ $patient->City->name }}
                        @endif
                        @if ($patient->area)
                            / {{ $patient->areas->name ?? '' }}
                        @endif
                    </div>
                </div>

                <!-- الحالة الاجتماعية + الوظيفة + التعاقد + الملحوظة -->
                <div class="hstack text-white-50 gap-4 mb-2">
                    <div>
                        <i class="ri-user-heart-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ __('patient_trans.marital_status') }}:
                        <span class="fw-semibold text-white">{{ $patient->marital_status_text ?? '-' }}</span>
                    </div>
                    <div>
                        <i class="ri-briefcase-4-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ __('patient_trans.job') }}:
                        <span class="fw-semibold text-white">{{ $patient->job->name_ar ?? '-' }}</span>
                    </div>
                    <div>
                        <i class="ri-hand-coin-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ __('patient_trans.contract') }}:
                        <span class="fw-semibold text-white">{{ $patient->contract->name ?? '-' }}</span>
                    </div>
                    <div>
                        <i class="ri-sticky-note-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ __('patient_trans.notes') }}:
                        <span class="fw-semibold text-white">{{ $reservations->reservation_note ?? '-' }}</span>
                    </div>
                </div>

                <!-- بيانات إضافية -->
                <div class="hstack text-white-50 gap-4">
                    <div>
                        <i class="ri-phone-line me-1 text-success fs-16 align-middle"></i>
                        {{ __('patient_trans.phone') }}:
                        <span class="fw-semibold text-white">{{ $patient->phone_number ?? '-' }}</span>
                    </div>
                    <div>
                        <i class="ri-whatsapp-line me-1 text-success fs-16 align-middle"></i>
                        WhatsApp:
                        <span class="fw-semibold text-white">{{ $patient->whatsapp ?? '-' }}</span>
                    </div>
                    <div>
                        <i class="ri-mail-line me-1 text-warning fs-16 align-middle"></i>
                        Email:
                        <span class="fw-semibold text-white">{{ $patient->email ?? '-' }}</span>
                    </div>
                    <div>
                        <i class="ri-passport-line me-1 text-info fs-16 align-middle"></i>
                        ID/Passport:
                        <span
                            class="fw-semibold text-white">{{ $patient->national_id ?? ($patient->passport ?? '-') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات صغيرة -->
        <div class="col-12 col-lg-auto order-last order-lg-0" style="width: 422px;">
            <div class="row text text-white-50 text-center">
                <div class="col-lg-4 col-6">
                    <div class="p-2">
                        <h4 class="text-white mb-1">{{ $patient->visits->count() }}</h4>
                        <p class="fs-14 mb-0"><i class="ri-hospital-line me-1"></i>{{ trans('doctor_trans.visits') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="p-2">
                        <h4 class="text-white mb-1">{{ $reservations->count() ?? 0 }}</h4>
                        <p class="fs-14 mb-0"><i
                                class="ri-calendar-check-line me-1"></i>{{ trans('doctor_trans.reservations') }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="p-2">
                        <h4 class="text-white mb-1">{{ $patient->contract?->name ?? '-' }}</h4>
                        <p class="fs-14 mb-0"><i
                                class="ri-shield-check-line me-1"></i>{{ trans('patient_trans.contract') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- زر المريض التالي -->
    <div>
        <a class="btn btn-success btn-sm"
            href="{{ route('nursing_outpatient_clinics.next', ['id' => $reservations->id]) }}">
            <i class="bx bx-skip-next-circle me-1"></i> المريض التالي
        </a>
    </div>
</div>

{{-- Hidden Inputs --}}
<input id="id_patient" name="id_patient" value="{{ $patient->id }}" type="hidden" />
<input id="id_reservation" name="id_reservation" value="{{ $reservations->id }}" type="hidden" />
<input id="type_visit" name="type_visit" value="{{ $patient_visits->type }}" type="hidden" />
<input id="id_visit" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />
