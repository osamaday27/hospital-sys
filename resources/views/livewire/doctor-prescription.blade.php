<div class="container-fluid py-3" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">



    {{-- 🧾 Tabs --}}
    <ul class="nav nav-tabs mb-3" id="prescriptionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="add-tab" data-bs-toggle="tab" data-bs-target="#addPrescription"
                type="button" role="tab">
                <i class="ri-add-circle-line me-2"></i> {{ __('doctor_trans.add_prescription') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="previous-tab" data-bs-toggle="tab"
                data-bs-target="#previousPrescriptions" type="button" role="tab">
                <i class="ri-file-list-3-line me-2"></i> {{ __('doctor_trans.previous_prescriptions') }}
            </button>
        </li>
    </ul>

    <div class="tab-content" id="prescriptionTabsContent">

        {{-- ✅ تبويب الإضافة --}}
        <div class="tab-pane fade show active" id="addPrescription" role="tabpanel">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="ri-capsule-line me-2"></i> {{ __('doctor_trans.add_prescription') }}
                    </h6>
                </div>

                <div class="card-body">

                    {{-- 🔍 بحث عام --}}
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light"><i class="ri-search-line"></i></span>
                        <input type="text" wire:model.debounce.400ms="searchGlobal" class="form-control"
                            placeholder="{{ __('doctor_trans.search_drug_global') }}">
                    </div>

                    {{-- 🧾 جدول الأدوية --}}
                    <div class="table-responsive" style="height: 350px;">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('doctor_trans.drug') }}</th>
                                    <th>{{ __('doctor_trans.dose') }}</th>
                                    <th>{{ __('doctor_trans.frequency') }}</th>
                                    <th>{{ __('doctor_trans.unit') }}</th>
                                    <th>{{ __('doctor_trans.duration') }}</th>
                                    <th>{{ __('doctor_trans.route') }}</th>
                                    <th>{{ __('doctor_trans.eye') }}</th>
                                    <th>{{ __('doctor_trans.notes') }}</th>
                                    <th>{{ __('doctor_trans.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($selectedMedications as $index => $med)
                                    <tr wire:key="row-{{ $index }}">
                                        {{-- 🔹 اسم الدواء --}}
                                        <td style="width: 250px;">
                                            <div class="position-relative">
                                                <input type="text"
                                                    wire:model.debounce.400ms="searchMedications.{{ $index }}"
                                                    class="form-control"
                                                    placeholder="{{ __('doctor_trans.search_drug') }}"
                                                    autocomplete="off">
                                                @if (!empty($medicationResults[$index]))
                                                    <ul class="list-group position-absolute w-100 shadow mt-1"
                                                        style="z-index: 9999; max-height: 200px; overflow-y: auto;">
                                                        @foreach ($medicationResults[$index] as $m)
                                                            @php
                                                                $mId = is_array($m) ? $m['id'] ?? null : $m->id ?? null;
                                                                $mName = is_array($m)
                                                                    ? $m['name'][app()->getLocale()] ??
                                                                        reset($m['name'] ?? [])
                                                                    : $m->getTranslation('name', app()->getLocale());
                                                            @endphp

                                                            @if ($mId)
                                                                <li class="list-group-item list-group-item-action"
                                                                    wire:click="selectMedication({{ $index }}, {{ $mId }})"
                                                                    style="cursor: pointer;">
                                                                    {{ $mName }}
                                                                </li>
                                                            @endif
                                                        @endforeach

                                                    </ul>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 🔹 الجرعة --}}
                                        <td>
                                            <input type="number" class="form-control text-center" min="0.1"
                                                step="0.5"
                                                wire:model.lazy="selectedQuantities.{{ $index }}">
                                        </td>

                                        {{-- 🔹 التكرار --}}
                                        <td style="width: 200px;">
                                            <div class="position-relative" style="z-index: 1049;">
                                                <input type="text" class="form-control text-center"
                                                    wire:model.debounce.400ms="searchFrequencies.{{ $index }}"
                                                    autocomplete="off"
                                                    placeholder="{{ __('doctor_trans.frequency_example') }}">

                                                {{-- ✅ قائمة التكرارات المقترحة --}}
                                                @foreach ($frequencyResults[$index] as $freq)
                                                    <li class="list-group-item list-group-item-action"
                                                        style="cursor: pointer;"
                                                        wire:click="selectFrequency({{ $index }}, {{ $freq->id }})">
                                                        {{ $freq->getTranslation('name', app()->getLocale()) }}
                                                    </li>
                                                @endforeach

                                            </div>
                                        </td>


                                        {{-- 🔹 الوحدة --}}
                                        <td>
                                            <div class="position-relative">
                                                <input type="text" class="form-control text-center"
                                                    wire:model.debounce.300ms="searchUnits.{{ $index }}"
                                                    placeholder="{{ __('doctor_trans.unit_example') }}">
                                                @if (!empty($unitResults[$index]))
                                                    <ul class="list-group position-absolute w-100 shadow mt-1"
                                                        style="z-index: 9999; max-height: 200px; overflow-y: auto;">
                                                        @foreach ($unitResults[$index] as $unit)
                                                            @php
                                                                $unitId = is_array($unit)
                                                                    ? $unit['id'] ?? null
                                                                    : $unit->id ?? null;
                                                                $unitName = is_array($unit)
                                                                    ? $unit['name'][app()->getLocale()] ??
                                                                        reset($unit['name'] ?? [])
                                                                    : $unit->getTranslation('name', app()->getLocale());
                                                            @endphp

                                                            @if ($unitId)
                                                                <li class="list-group-item list-group-item-action"
                                                                    wire:click="selectUnit({{ $index }}, {{ $unitId }})"
                                                                    style="cursor: pointer;">
                                                                    {{ $unitName }}
                                                                </li>
                                                            @endif
                                                        @endforeach

                                                    </ul>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 🔹 المدة --}}
                                        <td>
                                            <div class="position-relative">
                                                <input type="text" class="form-control text-center"
                                                    wire:model.debounce.300ms="searchDurations.{{ $index }}"
                                                    placeholder="{{ __('doctor_trans.duration_example') }}">
                                                @if (!empty($durationResults[$index]))
                                                    <ul class="list-group position-absolute w-100 shadow mt-1"
                                                        style="z-index: 9999; max-height: 200px; overflow-y: auto;">
                                                        @foreach ($durationResults[$index] as $duration)
                                                            <li class="list-group-item list-group-item-action"
                                                                wire:click="selectDuration({{ $index }}, {{ $duration->id }})">
                                                                {{ $duration->getTranslation('name', app()->getLocale()) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 🔹 طريقة الإعطاء --}}
                                        <td>
                                            <div class="position-relative">
                                                <input type="text" class="form-control text-center"
                                                    wire:model.debounce.300ms="searchTakens.{{ $index }}"
                                                    placeholder="{{ __('doctor_trans.route_example') }}">
                                                @if (!empty($takenResults[$index]))
                                                    <ul class="list-group position-absolute w-100 shadow mt-1"
                                                        style="z-index: 9999; max-height: 200px; overflow-y: auto;">
                                                        @foreach ($takenResults[$index] as $taken)
                                                            <li class="list-group-item list-group-item-action"
                                                                wire:click="selectTaken({{ $index }}, {{ $taken->id }})">
                                                                {{ $taken->getTranslation('name', app()->getLocale()) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 🔹 العين --}}
                                        <td>
                                            <select wire:model="selectedEyes.{{ $index }}"
                                                class="form-select text-center">
                                                <option value="right">{{ __('doctor_trans.right_eye') }}</option>
                                                <option value="left">{{ __('doctor_trans.left_eye') }}</option>
                                                <option value="both">{{ __('doctor_trans.both_eyes') }}</option>
                                            </select>
                                        </td>

                                        {{-- 🔹 الملاحظات --}}
                                        <td>
                                            <input type="text" class="form-control"
                                                wire:model.lazy="notes.{{ $index }}"
                                                placeholder="{{ __('doctor_trans.notes') }}">
                                        </td>

                                        {{-- 🔹 الإجراءات --}}
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-outline-danger"
                                                    wire:click="removeMedication({{ $index }})">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    wire:click="$emit('duplicateRow', {{ $index }})">
                                                    <i class="ri-file-copy-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ✅ الأزرار --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button class="btn btn-outline-success" wire:click="addMedication">
                            <i class="ri-add-line me-2"></i>{{ __('doctor_trans.add_drug') }}
                        </button>

                        <button class="btn btn-primary" wire:click="checkExistingPrescription">
                            <i class="ri-save-line me-2"></i>{{ __('doctor_trans.save_prescription') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ تبويب الوصفات السابقة --}}
        <div class="tab-pane fade" id="previousPrescriptions" role="tabpanel">
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary"><i
                            class="ri-file-list-3-line me-2"></i>{{ __('doctor_trans.previous_prescriptions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($previousPrescriptions as $p)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-header bg-primary text-white">
                                        <strong>#{{ $p->id }}</strong> – {{ $p->prescription_date }}
                                    </div>
                                    <div class="card-body">
                                        <p><i class="ri-capsule-line me-1"></i> {{ $p->items->count() }}
                                            {{ __('doctor_trans.medications') }}</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            <button class="btn btn-sm btn-outline-info"
                                                wire:click="showPrescription({{ $p->id }})"><i
                                                    class="ri-eye-line"></i></button>
                                            <button class="btn btn-sm btn-outline-secondary"
                                                wire:click="repeatPrescription({{ $p->id }})"><i
                                                    class="ri-refresh-line"></i></button>
                                            <button class="btn btn-sm btn-outline-warning"
                                                wire:click="editPrescription({{ $p->id }})"><i
                                                    class="ri-edit-line"></i></button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="deletePrescription({{ $p->id }})"><i
                                                    class="ri-delete-bin-line"></i></button>
                                            <button class="btn btn-sm btn-outline-primary"
                                                wire:click="printPrescription({{ $p->id }})"><i
                                                    class="ri-printer-line"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">
                                <i class="ri-file-list-3-line" style="font-size: 3rem;"></i>
                                <p class="mt-2">{{ __('doctor_trans.no_previous_prescriptions') }}</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        {{ $previousPrescriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ⚠️ مودال التنبيه في حالة وجود روشتة بنفس اليوم --}}
    <div class="modal fade" id="prescriptionChoiceModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg rounded-4">

                {{-- 🔹 Header --}}
                <div class="modal-header bg-gradient-warning text-dark rounded-top-4 py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="ri-error-warning-line me-2 fs-4 text-danger"></i>
                        {{ __('doctor_trans.alert') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- 🔹 Body --}}
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="ri-capsule-line text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">
                        {{ __('doctor_trans.existing_prescription_today') }}
                    </h5>
                    <p class="text-muted mb-0">
                        {{ __('doctor_trans.add_to_existing_or_new') }}
                    </p>
                </div>

                {{-- 🔹 Footer --}}
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button class="btn btn-success px-4 py-2 rounded-pill shadow-sm"
                        wire:click="confirmPrescriptionAction('append')" data-bs-dismiss="modal">
                        <i class="ri-add-line me-1"></i>{{ __('doctor_trans.add_to_existing') }}
                    </button>

                    <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm"
                        wire:click="confirmPrescriptionAction('new')" data-bs-dismiss="modal">
                        <i class="ri-file-add-line me-1"></i>{{ __('doctor_trans.create_new') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✨ Extra Styling --}}
    <style>
        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffdd57, #ffc107);
        }

        .modal-content {
            animation: slideInDown 0.4s ease;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
    <script>
        document.addEventListener('livewire:load', function() {

            // ⚠️ 1️⃣ مودال اختيار الروشتة (قديمة / جديدة)
            window.addEventListener('confirm-prescription-choice', () => {
                const modal = new bootstrap.Modal(document.getElementById('prescriptionChoiceModal'));
                modal.show();
            });

            // ✅ 2️⃣ Toast للتنبيهات السفلية (Bootstrap)
            window.addEventListener('show-toast', event => {
                const {
                    type,
                    message
                } = event.detail;
                const toast = document.createElement('div');
                toast.className =
                    `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3 show shadow-lg`;
                toast.style.zIndex = '2000';
                toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="toast-body fw-semibold">${message}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
                document.body.appendChild(toast);

                setTimeout(() => toast.remove(), 4000);
            });

            // 🚨 3️⃣ تنبيهات علوية (Alerts)
            window.addEventListener('show-alert', event => {
                const {
                    type,
                    message
                } = event.detail;
                const icon = type === 'success' ? '✅' :
                    type === 'danger' ? '❌' :
                    type === 'warning' ? '⚠️' : 'ℹ️';

                const alert = document.createElement('div');
                alert.className =
                    `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg`;
                alert.style.zIndex = '3000';
                alert.innerHTML = `
                <div class="d-flex align-items-center">
                    <span class="fs-5 me-2">${icon}</span>
                    <span class="fw-bold">${message}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            `;
                document.body.appendChild(alert);

                // إزالة التنبيه بعد 4 ثواني
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 400);
                }, 4000);
            });

        });
    </script>


</div>
