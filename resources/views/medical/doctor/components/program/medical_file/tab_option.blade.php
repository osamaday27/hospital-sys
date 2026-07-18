<!-- Improved Nav Tabs -->
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist" style="font-size: 15px;">
        @php
            $tabs = [
                ['id' => 'overview-tab', 'icon' => 'bx bx-receipt', 'text' => trans('doctor_trans.overview')],
                ['id' => 'medical_history', 'icon' => 'bx bx-history', 'text' => trans('doctor_trans.medical_history')],
                ['id' => 'general_examination', 'icon' => 'bx bx-spreadsheet', 'text' => trans('doctor_trans.general_examination'), 'onclick' => 'get_allergies(),get_complain(),get_surgeries(),get_finding(),get_medical_history()'],
                ['id' => 'complaints_diagnosis', 'icon' => 'bx bx-spreadsheet', 'text' => trans('doctor_trans.complaints_diagnosis')],
                ['id' => 'prescription2', 'icon' => 'bx bxs-capsule', 'text' => trans('doctor_trans.prescription')],
                ['id' => 'doctor_order', 'icon' => 'bx bxs-bookmark-alt-plus', 'text' => trans('doctor_trans.doctor_order')],
                ['id' => 'medical_sheet', 'icon' => 'bx bx-spreadsheet', 'text' => trans('doctor_trans.medical_sheet'), 'active' => true],
                ['id' => 'surgery_request', 'icon' => 'bx bx-list-plus', 'text' => trans('doctor_trans.request_surgery')],
                ['id' => 'medical_reports', 'icon' => 'bx bxs-message-alt-edit', 'text' => trans('doctor_trans.medical_reports'), 'onclick' => 'get_get_report()'],
                ['id' => 'notes_tab', 'icon' => 'bx bxs-message-rounded-detail', 'text' => trans('doctor_trans.notes'), 'onclick' => 'get_notes()'],
                ['id' => 'vital_signs', 'icon' => 'bx bx-pulse', 'text' => trans('doctor_trans.vital_signs'), 'onclick' => 'get_allergies(),get_complain(),get_surgeries(),get_finding(),get_medical_history()'],
            ];
        @endphp

        @foreach ($tabs as $tab)
            <li class="nav-item">
                <a class="nav-link px-3 py-2 fs-6 fw-semibold {{ $tab['active'] ?? false ? 'active' : '' }}"
                   data-bs-toggle="tab"
                   href="#{{ $tab['id'] }}"
                   role="tab"
                   {!! isset($tab['onclick']) ? "onclick=\"{$tab['onclick']}\"" : '' !!}>
                    <i class="{{ $tab['icon'] }} fs-5 me-1"></i>
                    <span class="d-none d-md-inline-block">{{ $tab['text'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Edit Button -->
    <div class="flex-shrink-0 mt-2 mt-lg-0">
        @can('patients-edit')
            <a href="{{ route('patient.edit', ['id' => Crypt::encryptString($patient->id)]) }}"
               class="btn btn-success d-flex align-items-center">
                <i class="ri-edit-box-line fs-5 me-1"></i> {{ trans('doctor_trans.update_patient_data') }}
            </a>
        @endcan
    </div>
</div>
