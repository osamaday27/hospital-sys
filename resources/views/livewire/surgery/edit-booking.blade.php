<div class="container-fluid py-4" dir="rtl">
    <div class="row g-4">

        {{-- رسائل نجاح / خطأ --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="mdi mdi-check-circle-outline me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="mdi mdi-alert-circle-outline me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('validation_errors'))
            <div class="alert alert-danger mt-0">
                <h6 class="fw-bold"><i class="mdi mdi-alert-circle-outline"></i>
                    {{ __('surgical_trans.validation_error') }}</h6>
                <ul class="mb-0">
                    @foreach (session('validation_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- بيانات المريض (ثابتة) --}}
        @if ($selectedPatient)
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info bg-gradient text-white py-3 d-flex align-items-center">
                        <i class="mdi mdi-account-card-details-outline fs-5 me-2"></i>
                        <h6 class="mb-0 fw-semibold text-white">{{ __('surgical_trans.patient_info') }}</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row g-3">
                            <div class="col-3">
                                <div class="info-box bg-primary-subtle p-3 rounded d-flex align-items-start">
                                    <i class="mdi mdi-account-outline text-secondary fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">{{ __('surgical_trans.name') }}</small>
                                        <div class="fw-bold">{{ $selectedPatient->full_name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-info-subtle p-3 rounded d-flex align-items-start">
                                    <i class="mdi mdi-card-account-details text-secondary fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">{{ __('surgical_trans.national_id') }}</small>
                                        <div class="fw-bold">{{ $selectedPatient->national_id }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-success-subtle p-3 rounded d-flex align-items-start">
                                    <i class="mdi mdi-phone text-success fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">{{ __('surgical_trans.phone') }}</small>
                                        <div class="fw-bold">{{ $selectedPatient->phone_number }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-warning-subtle p-3 rounded d-flex align-items-start">
                                    <i class="mdi mdi-hospital-building text-warning fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">الغرفة الحالية</small>
                                        <div class="fw-bold">
                                            @if ($booking?->room?->name_ar)
                                                <span class="badge bg-secondary">{{ $booking->room->name_ar }}</span>
                                            @else
                                                <span class="text-muted">غير محددة</span>
                                            @endif
                                        </div>
                                        <small class="d-block text-muted">لا يمكن تغيير الغرفة من شاشة التعديل.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="mdi mdi-history me-2 text-warning"></i>
                            {{ __('surgical_trans.last_5_visits') }}
                        </h6>

                        <div class="row g-3">
                            @forelse($latestVisits as $visit)
                                <div class="col-2">
                                    <div class="card border-0 shadow-sm visit-card bg-light">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold mb-2 text-secondary">
                                                <i class="mdi mdi-calendar me-1"></i> {{ $visit->date_visit }}
                                            </h6>
                                            <p class="mb-1">
                                                <i class="mdi mdi-hospital-building text-secondary me-1"></i>
                                                {{ $visit->type }}
                                            </p>
                                            <span
                                                class="badge bg-{{ $visit->status == 'completed' ? 'success' : ($visit->status == 'cancelled' ? 'danger' : 'secondary') }}">
                                                {{ $visit->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    {{ __('surgical_trans.no_visits') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- اختيار العمليات (بدون غرفة) --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary bg-gradient text-white py-3 d-flex align-items-center">
                        <i class="mdi mdi-medical-bag fs-5 me-2"></i>
                        <h6 class="mb-0 fw-semibold text-white">{{ __('surgical_trans.surgeries_selection') }}</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="mdi mdi-magnify text-secondary"></i>
                            </span>
                            <input type="text" wire:model.debounce.500ms="surgerySearch"
                                class="form-control border-start-0"
                                placeholder="{{ __('surgical_trans.search_surgery') }}">
                        </div>

                        <div class="row g-3">
                            @forelse($surgeries as $surgery)
                                <div class="col-4">
                                    <div
                                        class="card h-100 border-0 shadow-sm surgery-card hover-shadow @error('surgery_sides.' . $surgery->id) border border-danger bg-danger-subtle @else bg-light @enderror">
                                        <div class="card-body">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-2" type="checkbox"
                                                    wire:model="surgery_ids" value="{{ $surgery->id }}"
                                                    id="surgery_{{ $surgery->id }}">
                                                <label for="surgery_{{ $surgery->id }}" class="fw-semibold">
                                                    <i class="mdi mdi-hospital-box-outline text-secondary me-1"></i>
                                                    {{ $surgery->getTranslation('name', app()->getLocale()) }}
                                                </label>
                                            </div>
                                            <div class="mt-2">
                                                <label
                                                    class="form-label small fw-semibold">{{ __('surgical_trans.side') }}</label>
                                                <select wire:model="surgery_sides.{{ $surgery->id }}"
                                                    class="form-select form-select-sm @error('surgery_sides.' . $surgery->id) is-invalid @else bg-info-subtle @enderror">
                                                    <option value="">{{ __('surgical_trans.select') }}</option>
                                                    <option value="right">{{ __('surgical_trans.right') }}</option>
                                                    <option value="left">{{ __('surgical_trans.left') }}</option>
                                                    <option value="both">{{ __('surgical_trans.both') }}</option>
                                                </select>
                                                @error('surgery_sides.' . $surgery->id)
                                                    <small class="text-danger">
                                                        <i
                                                            class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                                    </small>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-3">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    {{ __('surgical_trans.no_surgeries') }}
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-3 d-flex justify-content-center">
                            {{ $surgeries->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- تفاصيل التاريخ/الوقت + ملاحظة (بدون اختيار غرفة) --}}
            <div class="col-md-4 mt-4 mt-md-0">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info bg-gradient text-white py-3 d-flex align-items-center">
                        <i class="mdi mdi-calendar-clock fs-5 me-2"></i>
                        <h6 class="mb-0 fw-semibold text-white">{{ __('surgical_trans.booking_details') }}</h6>
                    </div>
                    <div class="card-body bg-light">
                        <form wire:submit.prevent="save">
                            {{-- 🏥 نوع الحالة --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-hospital-building me-1 text-dark"></i>
                                    {{ __('surgical_trans.case_type') }}
                                </label>
                                <select wire:model="case_type" class="form-select bg-info-subtle">
                                    <option value="doctor">{{ __('surgical_trans.doctor_case') }}</option>
                                    <option value="hospital">{{ __('surgical_trans.hospital_case') }}</option>
                                </select>
                                @error('case_type')
                                    <small class="text-danger">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-calendar me-1 text-dark"></i>
                                    {{ __('surgical_trans.booking_date') }}
                                </label>
                                <input type="date" wire:model="booking_date" class="form-control bg-info-subtle">
                                @error('booking_date')
                                    <small class="text-danger">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-text-box-edit-outline me-1 text-dark"></i>
                                    {{ __('surgical_trans.note') }}
                                </label>
                                <textarea wire:model="note" rows="3" class="form-control bg-primary-subtle"
                                    placeholder="{{ __('surgical_trans.note_placeholder') }}"></textarea>
                                @error('note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="alert alert-warning d-flex align-items-start" role="alert">
                                <i class="mdi mdi-information-outline fs-4 me-2"></i>
                                <div>
                                    <div class="fw-bold mb-1">معلومة مهمة</div>
                                    لا يتم اختيار الغرفة من شاشة التعديل. لو كان الحجز مرتبطًا بغرفة ومعاد محدد،
                                    سيتم التحقق من إتاحة نفس الميعاد في نفس الغرفة لليوم الجديد قبل النقل.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill">
                                <i class="mdi mdi-content-save-check-outline me-1"></i>
                                {{ __('surgical_trans.save_booking') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
