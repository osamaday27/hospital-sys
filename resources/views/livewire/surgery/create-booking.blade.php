<div class="container-fluid py-4" dir="rtl">
    <div class="row g-4">

        {{-- 🔔 رسائل التنبيهات --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="mdi mdi-check-circle-outline me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ✅ عرض الأخطاء المجمعة في آخر الشاشة --}}
        @if (session()->has('validation_errors'))
            <div class="alert alert-danger mt-4">
                <h6 class="fw-bold"><i class="mdi mdi-alert-circle-outline"></i>
                    {{ __('surgical_trans.validation_error') }}</h6>
                <ul class="mb-0">
                    @foreach (session('validation_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 🔍 البحث عن المريض --}}
        <div class="col-md-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary bg-gradient text-white py-3 d-flex align-items-center">
                    <div class="row w-100">
                        <div class="col-md-9 d-flex align-items-center">
                            <i class="mdi mdi-account-search fs-5 me-2"></i>
                            <h6 class="mb-0 fw-semibold text-white">{{ __('surgical_trans.search_patient') }}</h6>
                        </div>
                        <div class="col-md-3 text-end">
                        </div>
                    </div>
                </div>

                <div class="card-body bg-light">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="mdi mdi-magnify text-secondary"></i>
                        </span>
                        <input type="text" wire:model.debounce.500ms="search" class="form-control border-start-0"
                            placeholder="{{ __('surgical_trans.search_patient') }}">
                    </div>

                    @if ($patients && count($patients) > 0)
                        <div class="row mt-3 g-3">
                            @foreach ($patients as $patient)
                                <div class="col-md-4 col-lg-3 mb-4">
                                    <div
                                        class="card patient-card border-0 shadow-lg rounded-4 overflow-hidden hover-card">
                                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                                            <!-- صورة / أيقونة -->
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                    style="width:60px; height:60px;">
                                                    <i class="mdi mdi-account-circle fs-1"></i>
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold mb-1 text-dark">{{ $patient->full_name }}</h5>
                                                    <span
                                                        class="badge bg-light text-dark shadow-sm">{{ __('surgical_trans.patient') }}</span>
                                                </div>
                                            </div>

                                            <!-- بيانات المريض -->
                                            <ul class="list-unstyled text-muted small mb-4">
                                                <li class="mb-2">
                                                    <i class="mdi mdi-card-account-details-outline text-primary me-2"></i>
                                                    {{ $patient->national_id }}
                                                </li>
                                                <li class="mb-2">
                                                    <i class="mdi mdi-phone-outline text-success me-2"></i>
                                                    {{ $patient->phone_number }}
                                                </li>
                                                 <li class="mb-2">
                                                    <i class="mdi mdi-card-outline text-success me-2"></i>
                                                    {{ $patient->medical_number }}
                                                </li>
                                            </ul>

                                            <!-- الأزرار -->
                                            <div class="d-flex justify-content-between">
                                                <button wire:click="selectPatient({{ $patient->id }})"
                                                    class="btn btn-success btn-sm px-3 rounded-pill shadow-sm">
                                                    <i class="mdi mdi-check"></i> {{ __('surgical_trans.select') }}
                                                </button>
                                                <a href="{{ route('medical_record.edit', ['medical_record' => Crypt::encryptString($patient->id)]) }}"
                                                    target="_blank" rel="noopener"
                                                    class="btn btn-warning btn-sm px-3 rounded-pill shadow-sm"
                                                    title="{{ trans('buttons.edit') }}">
                                                    <i class="mdi mdi-pencil-outline"></i> {{ trans('buttons.edit') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 🧾 بيانات المريض --}}
        @if ($selectedPatient)
            <div class="col-md-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-info bg-gradient text-white py-3 d-flex align-items-center">
                        <i class="mdi mdi-account-card-details-outline fs-5 me-2"></i>
                        <h6 class="mb-0 fw-semibold text-white">{{ __('surgical_trans.patient_info') }}</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row g-3">
                            <div class="col-3">
                                <div class="info-box bg-primary-subtle">
                                    <i class="mdi mdi-account-outline text-secondary fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">{{ __('surgical_trans.name') }}</small>
                                        <div class="fw-bold">{{ $selectedPatient->full_name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-info-subtle">
                                    <i class="mdi mdi-card-account-details text-secondary fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">{{ __('surgical_trans.national_id') }}</small>
                                        <div class="fw-bold">{{ $selectedPatient->national_id }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="info-box bg-success-subtle">
                                    <i class="mdi mdi-phone text-success fs-4 me-2"></i>
                                    <div>
                                        <small class="text-muted">{{ __('surgical_trans.phone') }}</small>
                                        <div class="fw-bold">{{ $selectedPatient->phone_number }}</div>
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

            {{-- 👨‍⚕️ اختيار الجرّاح --}}
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary bg-gradient text-white py-3 d-flex align-items-center">
                        <i class="mdi mdi-doctor fs-5 me-2"></i>
                        <h6 class="mb-0 fw-semibold text-white">{{ __('surgical_trans.surgeon_selection') }}</h6>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-magnify me-1 text-dark"></i>
                                    {{ __('surgical_trans.search_surgeon') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="mdi mdi-magnify text-secondary"></i>
                                    </span>
                                    <input type="text" wire:model.debounce.400ms="surgeonSearch"
                                        class="form-control border-start-0"
                                        placeholder="{{ __('surgical_trans.search_surgeon_placeholder') }}">
                                </div>
                                @error('selectedSurgeonId')
                                    <small class="text-danger d-block mt-1">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                @if ($selectedSurgeon)
                                    <div
                                        class="alert alert-info d-flex align-items-center justify-content-between mb-0">
                                        <div>
                                            <i class="mdi mdi-account-tie me-2"></i>
                                            <strong>{{ __('surgical_trans.selected_surgeon') }}:</strong>
                                            <span class="ms-1">{{ $selectedSurgeon->full_name }}</span>
                                            @if (!empty($selectedSurgeon->employee_code))
                                                <span
                                                    class="badge bg-secondary ms-2">#{{ $selectedSurgeon->employee_code }}</span>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="clearSelectedSurgeon">
                                            <i class="mdi mdi-close"></i> {{ __('surgical_trans.remove') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($surgeons && count($surgeons) > 0)
                            <div class="row mt-3 g-3">
                                @foreach ($surgeons as $doc)
                                    <div class="col-md-3">
                                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-dark bg-opacity-10 p-3 rounded-circle me-3">
                                                        <i class="mdi mdi-account-tie text-dark fs-3"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="fw-bold mb-1">{{ $doc->full_name }}</h6>
                                                        @if (!empty($doc->employee_code))
                                                            <div class="text-muted small">
                                                                <i
                                                                    class="mdi mdi-pound me-1"></i>#{{ $doc->employee_code }}
                                                            </div>
                                                        @endif
                                                        @if (!empty($doc->job_title))
                                                            <div class="text-muted small">
                                                                <i
                                                                    class="mdi mdi-briefcase-outline me-1"></i>{{ $doc->job_title }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-end">
                                                    <button class="btn btn-sm btn-primary rounded-pill"
                                                        wire:click="selectSurgeon({{ $doc->id }})">
                                                        <i class="mdi mdi-check"></i>
                                                        {{ __('surgical_trans.select') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ⚙️ اختيار العمليات --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
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
                                        class="card h-100 border-0 shadow-sm surgery-card hover-shadow 
                                        @error('surgery_sides.' . $surgery->id) border border-danger bg-danger-subtle @else bg-light @enderror">
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

            {{-- 📅 تفاصيل الحجز --}}
            <div class="col-md-4 mt-4">
                <div class="card shadow-sm border-0 h-100">
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
                                <input type="date" wire:model="booking_date" class="form-control bg-info-subtle"
                                    min="{{ $minBookingDate }}">
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
