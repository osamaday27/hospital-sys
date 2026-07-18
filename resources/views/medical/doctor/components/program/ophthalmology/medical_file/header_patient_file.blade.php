<div class="profile-foreground position-relative mx-n4 mt-n4">
    <div class="profile-wid-bg">
        <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="" class="profile-wid-img" />
    </div>
</div>
<div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
    <div class="row g-4">
        <div class="col-auto">
            <div class="avatar-lg">
                <img style="height: 95px !important; width: 120px !important; background-color: transparent;"
                    src="{{ asset(optional($patient)->img ?? 'assets/images/users/user-dummy-img.jpg') }}"
                    alt="user-img" class="img-thumbnail rounded-circle" />
            </div>
        </div>
        <!--end col-->
        <div class="col">
            <div class="p-2">
                <h3 class="text-white mb-1">{{ $patient->full_name }}</h3>
                <p class="text-white-75">{{ $patient->date_of_birth }}</p>
                <div class="hstack text-white-50 gap-1">
                    <div class="me-2"><i
                            class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>{{ $patient->full_address }}
                    </div>
                    <div>
                        <i class="ri-building-line me-1 text-white-75 fs-16 align-middle"></i>
                        {{ $patient->countries->name ?? null }}
                        @if ($patient->government)
                            /{{ $patient->government->name }}
                        @endif
                        @if ($patient->City)
                            / {{ $patient->City->name }}
                        @endif
                        @if ($patient->area)
                            / {{ $patient->areas->name ?? '' }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-12 col-lg-auto order-last order-lg-0" style="min-width: 700px">

            <div class="row text text-white-50 text-center">            
                <div class="col-lg-6 col-4">
                    <div class="p-2">
                        @livewire('alertallergy', ['id_patient' => $patient->id])
                    </div>
                </div>
                <div class="col-lg-2 col-4">
                    <div class="p-2">
                        <h4 class="text-white mb-1"></h4>
                        <p class="fs-14 mb-0">{{ trans('doctor_trans.examination') }}</p>
                    </div>
                </div>
                <div class="col-lg-2 col-4">
                    <div class="p-2">
                        <h4 class="text-white mb-1"></h4>
                        <p class="fs-14 mb-0">{{ trans('doctor_trans.follow_up') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
</div>
<input id="id_patient" name="id_patient" value="{{ $patient->id }}" type="hidden" />
<input id="id_reservation" name="id_reservation" value="{{ $reservations->id }}" type="hidden" />
<input id="type_visit" name="type_visit" value="{{ $patient_visits->type }}" type="hidden" />
<input id="id_visit" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />
