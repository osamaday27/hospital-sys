<div class="card modern-form">
    <div class="card-header bg-gradient-primary text-white">
        <h4 class="mb-0"><i class="fas fa-procedures me-2"></i> {{ __('surgical_trans.pre_anesthesia_assessment') }}
        </h4>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <!-- القسم الأول: المعلومات الأساسية -->
            <div class="section-container mb-4">
                <h5 class="section-title"><i class="fas fa-user-circle me-2"></i>
                    {{ __('surgical_trans.basic_information') }}</h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">{{ __('surgical_trans.weight_label') }}</label>
                        <div class="input-group">
                            <input type="number" wire:model="weight" class="form-control"
                                placeholder="{{ __('surgical_trans.weight_placeholder') }}">
                            <span class="input-group-text">{{ __('surgical_trans.kg') }}</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('surgical_trans.height_label') }}</label>
                        <div class="input-group">
                            <input type="number" wire:model="height" class="form-control"
                                placeholder="{{ __('surgical_trans.height_placeholder') }}">
                            <span class="input-group-text">{{ __('surgical_trans.cm') }}</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('surgical_trans.age') }}</label>
                        <input type="number" wire:model="age" class="form-control"
                            placeholder="{{ __('surgical_trans.age_placeholder') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.gender') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" wire:model="gender" value="male" class="btn-check" id="gender_male">
                            <label class="btn btn-outline-primary" for="gender_male"><i class="fas fa-male me-1"></i>
                                {{ __('surgical_trans.male') }}</label>

                            <input type="radio" wire:model="gender" value="female" class="btn-check"
                                id="gender_female">
                            <label class="btn btn-outline-primary" for="gender_female"><i
                                    class="fas fa-female me-1"></i> {{ __('surgical_trans.female') }}</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('surgical_trans.diagnosis') }}</label>
                        <input type="text" wire:model="diagnosis" class="form-control"
                            placeholder="{{ __('surgical_trans.enter_diagnosis') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.surgeon_unit') }}</label>
                        <input type="text" wire:model="surgeon_unit" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.operation_type') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" wire:model="proposed_operation" value="elective" class="btn-check"
                                id="op_elective">
                            <label class="btn btn-outline-success"
                                for="op_elective">{{ __('surgical_trans.elective') }}</label>

                            <input type="radio" wire:model="proposed_operation" value="emergency" class="btn-check"
                                id="op_emergency">
                            <label class="btn btn-outline-danger"
                                for="op_emergency">{{ __('surgical_trans.emergency') }}</label>
                        </div>
                    </div>
                </div>
            </div>


            <!-- القسم الثاني: التاريخ المرضي -->
            <div class="section-container mb-4">
                <h5 class="section-title"><i class="fas fa-history me-2"></i>
                    {{ __('surgical_trans.medical_history') }}</h5>
                <div class="row g-3">
                    @php
                        $historyOptions = [
                            [
                                __('surgical_trans.smoking') => 'fas fa-smoking',
                                __('surgical_trans.respiratory_disease') => 'fas fa-lungs',
                                __('surgical_trans.lower_limb_edema') => 'fas fa-swelling',
                            ],
                            [
                                __('surgical_trans.allergy') => 'fas fa-allergies',
                                __('surgical_trans.jaundice') => 'fas fa-tint',
                                __('surgical_trans.cyanosis') => 'fas fa-tint',
                            ],
                            [
                                __('surgical_trans.diabetes') => 'fas fa-syringe',
                                __('surgical_trans.kidney_problem') => 'fas fa-kidneys',
                                __('surgical_trans.neurological_disease') => 'fas fa-brain',
                            ],
                            [
                                __('surgical_trans.heart_disease') => 'fas fa-heartbeat',
                                __('surgical_trans.myocardial_infarction') => 'fas fa-heart-broken',
                                __('surgical_trans.right_side') => 'fas fa-hand-paper',
                            ],
                            [
                                __('surgical_trans.chest_pain') => 'fas fa-heart',
                                __('surgical_trans.shortness_of_breath') => 'fas fa-wind',
                                __('surgical_trans.left_side') => 'fas fa-hand-paper',
                            ],
                            [
                                __('surgical_trans.hypertension') => 'fas fa-tachometer-alt',
                                __('surgical_trans.palpitation') => 'fas fa-heart-rate',
                                __('surgical_trans.stroke') => 'fas fa-stroopwafel',
                            ],
                            [
                                __('surgical_trans.previous_operations') => 'fas fa-procedures',
                                __('surgical_trans.other_1') => 'fas fa-ellipsis-h',
                                __('surgical_trans.other_2') => 'fas fa-ellipsis-h',
                            ],
                        ];
                    @endphp

                    @foreach ($historyOptions as $row)
                        <div class="col-md-12">
                            <div class="d-flex flex-wrap">
                                @foreach ($row as $option => $icon)
                                    <div class="me-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="history"
                                                value="{{ $option }}"
                                                id="hist_{{ str_replace(' ', '_', $option) }}">
                                            <label class="form-check-label"
                                                for="hist_{{ str_replace(' ', '_', $option) }}">
                                                <i class="{{ $icon }} me-1"></i> {{ $option }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- القسم الثالث: الأدوية -->
            <div class="section-container mb-4">
                <h5 class="section-title"><i class="fas fa-pills me-2"></i>
                    {{ __('surgical_trans.preoperative_drugs') }}</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="40%">{{ __('surgical_trans.drug_name') }}</th>
                                <th width="25%">{{ __('surgical_trans.dose') }}</th>
                                <th width="25%">{{ __('surgical_trans.duration') }}</th>
                                <th width="10%" class="text-center">{{ __('surgical_trans.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($drug_therapy as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" wire:model="drug_therapy.{{ $index }}.drug"
                                            class="form-control form-control-sm"
                                            placeholder="{{ __('surgical_trans.enter_drug_name') }}">
                                    </td>
                                    <td>
                                        <input type="text" wire:model="drug_therapy.{{ $index }}.dose"
                                            class="form-control form-control-sm"
                                            placeholder="{{ __('surgical_trans.enter_dose') }}">
                                    </td>
                                    <td>
                                        <input type="text" wire:model="drug_therapy.{{ $index }}.duration"
                                            class="form-control form-control-sm"
                                            placeholder="{{ __('surgical_trans.enter_duration') }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" wire:click="removeDrugTherapy({{ $index }})"
                                            class="btn btn-sm btn-danger rounded-circle"
                                            title="{{ __('surgical_trans.remove_drug') }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" wire:click="addDrugTherapy" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('surgical_trans.add_new_drug') }}
                    </button>
                </div>
            </div>

            <!-- القسم الرابع: الفحص السريري والتحاليل -->
            <div class="section-container mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h5 class="section-title"><i class="fas fa-stethoscope me-2"></i>
                            {{ __('surgical_trans.physical_exam') }}</h5>
                        @php
                            $examFields = [
                                'general_condition',
                                'airway_assessment',
                                'blood_pressure',
                                'heart_rate',
                                'temperature',
                                'heart',
                                'chest',
                                'abdomen',
                            ];
                            $examLabels = [
                                __('surgical_trans.general_condition'),
                                __('surgical_trans.airway_assessment'),
                                __('surgical_trans.blood_pressure'),
                                __('surgical_trans.heart_rate'),
                                __('surgical_trans.temperature'),
                                __('surgical_trans.heart'),
                                __('surgical_trans.chest'),
                                __('surgical_trans.abdomen'),
                            ];
                        @endphp
                        @foreach ($examFields as $i => $field)
                            <div class="mb-3">
                                <label class="form-label">{{ $examLabels[$i] }}</label>
                                <input type="text" wire:model="physical_exam.{{ $field }}"
                                    class="form-control">
                            </div>
                        @endforeach
                    </div>

                    <div class="col-md-6">
                        <h5 class="section-title"><i class="fas fa-microscope me-2"></i>
                            {{ __('surgical_trans.investigations') }}</h5>
                        @php
                            $investFields = ['ecg', 'chest_xray', 'abd_ultrasound', 'echocardiography'];
                            $investLabels = [
                                __('surgical_trans.ecg'),
                                __('surgical_trans.chest_xray'),
                                __('surgical_trans.abd_ultrasound'),
                                __('surgical_trans.echocardiography'),
                            ];
                        @endphp
                        @foreach ($investFields as $i => $field)
                            <div class="mb-3">
                                <label class="form-label">{{ $investLabels[$i] }}</label>
                                <input type="text" wire:model="investigations.{{ $field }}"
                                    class="form-control">
                            </div>
                        @endforeach

                        <div class="mb-3">
                            <label class="form-label">{{ __('surgical_trans.investigations_comments') }}</label>
                            <textarea wire:model="investigations_comments" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- القسم الخامس: التحاليل المخبرية -->
            <div class="section-container mb-4">
                <h5 class="section-title"><i class="fas fa-flask me-2"></i> {{ __('surgical_trans.lab_tests') }}</h5>
                <div class="row g-3">
                    @php
                        $labFields = [
                            'hb' => [__('surgical_trans.hb'), 'g/dL'],
                            'urea' => [__('surgical_trans.urea'), 'mg/dL'],
                            'fbs' => [__('surgical_trans.fbs'), 'mg/dL'],
                            'blood_group' => [__('surgical_trans.blood_group'), ''],
                            'creatinine' => [__('surgical_trans.creatinine'), 'mg/dL'],
                            'bilirubin_dt' => [__('surgical_trans.bilirubin_dt'), 'mg/dL'],
                            'esr' => [__('surgical_trans.esr'), 'mm/hr'],
                            'ast' => [__('surgical_trans.ast'), 'U/L'],
                            'alt' => [__('surgical_trans.alt'), 'U/L'],
                            'na' => [__('surgical_trans.na'), 'mEq/L'],
                            'k' => [__('surgical_trans.k'), 'mEq/L'],
                            'platelets' => [__('surgical_trans.platelets'), '/mm³'],
                            'hbs_ag' => [__('surgical_trans.hbs_ag'), ''],
                            'hcv_ab' => [__('surgical_trans.hcv_ab'), ''],
                            'hiv_ab' => [__('surgical_trans.hiv_ab'), ''],
                            'pt' => [__('surgical_trans.pt'), 'sec'],
                            'pc' => [__('surgical_trans.pc'), '%'],
                            'inr' => [__('surgical_trans.inr'), ''],
                            'ptt' => [__('surgical_trans.ptt'), 'sec'],
                        ];
                    @endphp

                    @foreach (array_chunk($labFields, 3, true) as $chunk)
                        <div class="col-md-4">
                            @foreach ($chunk as $key => $item)
                                <div class="mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-2">
                                            <input class="form-check-input" type="checkbox"
                                                wire:model="lab_works.{{ $key }}.checked"
                                                id="lab_{{ $key }}">
                                        </div>
                                        <label class="form-label me-2"
                                            for="lab_{{ $key }}">{{ $item[0] }}</label>
                                        <input type="text" class="form-control form-control-sm w-50"
                                            wire:model="lab_works.{{ $key }}.value">
                                        @if ($item[1])
                                            <span class="ms-2 text-muted small">{{ $item[1] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>


            <!-- القسم السادس: التقييم قبل التخدير -->
            <div class="section-container mb-4">
                <h5 class="section-title"><i class="fas fa-clipboard-check me-2"></i>
                    {{ __('surgical_trans.pre_anesthesia_evaluation') }}</h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('surgical_trans.asa_classification') }}</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach (['I', 'II', 'III', 'IV', 'V', 'E'] as $asaVal)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="asa"
                                        value="{{ $asaVal }}" id="asa_{{ $asaVal }}">
                                    <label
                                        class="form-check-label badge bg-{{ $asaVal == 'E' ? 'danger' : 'primary' }} py-2 px-3"
                                        for="asa_{{ $asaVal }}">ASA {{ $asaVal }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('surgical_trans.consent') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" wire:model="consent" value="yes" class="btn-check"
                                id="consent_yes">
                            <label class="btn btn-outline-success" for="consent_yes">
                                <i class="fas fa-check-circle me-1"></i> {{ __('surgical_trans.yes') }}
                            </label>

                            <input type="radio" wire:model="consent" value="no" class="btn-check"
                                id="consent_no">
                            <label class="btn btn-outline-danger" for="consent_no">
                                <i class="fas fa-times-circle me-1"></i> {{ __('surgical_trans.no') }}
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('surgical_trans.fit_for_operation') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" wire:model="fit_for_operation" value="yes" class="btn-check"
                                id="fit_yes">
                            <label class="btn btn-outline-success" for="fit_yes">
                                <i class="fas fa-check-circle me-1"></i> {{ __('surgical_trans.yes') }}
                            </label>

                            <input type="radio" wire:model="fit_for_operation" value="no" class="btn-check"
                                id="fit_no">
                            <label class="btn btn-outline-danger" for="fit_no">
                                <i class="fas fa-times-circle me-1"></i> {{ __('surgical_trans.no') }}
                            </label>
                        </div>
                        @if ($fit_for_operation === 'no')
                            <input type="text" wire:model="fit_for_operation_specify" class="form-control mt-2"
                                placeholder="{{ __('surgical_trans.specify_reason') }}">
                        @endif
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.prepare_fresh_blood') }}</label>
                        <input type="text" wire:model="prepare_fresh_blood" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.prepare_rbc') }}</label>
                        <input type="text" wire:model="prepare_rbc" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.prepare_ffp') }}</label>
                        <input type="text" wire:model="prepare_ffp" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('surgical_trans.prepare_platelets') }}</label>
                        <input type="text" wire:model="prepare_platelets" class="form-control">
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-12">
                        <label class="form-label">{{ __('surgical_trans.please_give') }}</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">1.</span>
                            <textarea wire:model="please_give_1" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">2.</span>
                            <textarea wire:model="please_give_2" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-12">
                        <label class="form-label">{{ __('surgical_trans.anesthesia_plan') }}</label>
                        <textarea wire:model="anesthesia_plan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>


            <!-- القسم السابع: التوقيع -->
            <div class="section-container">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('surgical_trans.physician_signature') }}</label>
                        <input type="text" wire:model="physician_signature" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('surgical_trans.date') }}</label>
                        <input type="date" wire:model="form_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('surgical_trans.time') }}</label>
                        <input type="time" wire:model="form_time" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> {{ __('surgical_trans.save_data') }}
                </button>
            </div>

        </form>
    </div>
</div>

<style>
    .modern-form {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header.bg-gradient-primary {
        background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
        border-radius: 10px 10px 0 0 !important;
        padding: 1.5rem;
    }

    .section-container {
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 1.5rem;
        border-left: 4px solid #3a7bd5;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        color: #3a7bd5;
        font-weight: 600;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
    }

    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3a7bd5;
        box-shadow: 0 0 0 0.25rem rgba(58, 123, 213, 0.25);
    }

    .btn-group .btn {
        border-radius: 6px !important;
    }

    .table {
        margin-bottom: 1rem;
    }

    .table thead th {
        background-color: #f1f5f9;
        color: #4a5568;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(58, 123, 213, 0.05);
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .input-group-text {
        background-color: #f1f5f9;
        color: #4a5568;
        font-weight: 500;
    }

    textarea.form-control {
        min-height: 80px;
    }
</style>
<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('show-toast', data => {
            const type = data.type || 'success';
            const message = data.message || 'تم الحفظ بنجاح';

            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3 show`;
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });

        // ✅ دعم للـ dispatchBrowserEvent مباشرة
        window.addEventListener('show-toast', event => {
            const { type, message } = event.detail;
            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3 show`;
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });
    });
</script>
