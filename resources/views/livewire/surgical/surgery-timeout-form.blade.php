<div class="container-fluid" dir="rtl">
    <h4 class="mb-3 text-primary">
        <i class="mdi mdi-clock-outline me-2"></i> {{ __('surgical_trans.timeout_form') }}
    </h4>

    {{-- ✅ رسالة النجاح --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

   {{-- 🩺 الإجراءات والاتجاهات --}}
<div class="mb-3">
    <label class="form-label fw-bold text-primary">
        {{ __('surgical_trans.procedures_and_sides') }}:
    </label>

    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>{{ __('surgical_trans.procedure') }}</th>
                <th>{{ __('surgical_trans.procedure_side') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($procedures as $p)
                <tr>
                    <td>{{ $p['name'] }}</td>
                    <td>{{ $p['side'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">{{ __('surgical_trans.no_procedures_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    {{-- 🔹 التشخيص قبل العملية --}}
    <div class="mb-3">
        <label class="form-label fw-bold">{{ __('surgical_trans.pre_op_diagnosis') }}:</label>
        <input type="text" class="form-control" wire:model="pre_op_diagnosis">
    </div>

    {{-- ✅ جدول التحقق (Timeout Checklist) --}}
    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr><th colspan="4">{{ __('surgical_trans.timeout_checklist') }}</th></tr>
            <tr>
                <th>{{ __('surgical_trans.item') }}</th>
                <th>Yes</th><th>No</th><th>N/A</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['patient_identity', 'correct_side', 'correct_procedure', 'availability_of_implants', 'anesthesia_concerns', 'antibiotic_prophylaxis_given', 'essential_imaging_displayed'] as $field)
                <tr>
                    <td>{{ __('surgical_trans.' . $field) }}</td>
                    @foreach(['Yes', 'No', 'N/A'] as $option)
                        <td>
                            <input type="radio" wire:model="{{ $field }}" value="{{ $option }}">
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- 🔹 التوثيق --}}
    <div class="form-check mb-4">
        <input type="checkbox" class="form-check-input" wire:model="timeout_documented" id="timeout_doc">
        <label for="timeout_doc" class="form-check-label">{{ __('surgical_trans.timeout_documented') }}</label>
    </div>

    {{-- 👨‍⚕️ الفريق الطبي --}}
    <div class="row g-3 mb-4">
        {{-- 🩺 الجراح --}}
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ __('surgical_trans.surgeon_signature') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث باسم الجراح أو الكود" wire:model.debounce.500ms="surgeonSearch">
            <select class="form-select" wire:model="surgeon_signature">
                <option value="">{{ __('اختر الجراح') }}</option>
                @foreach ($surgeons as $surgeon)
                    <option value="{{ $surgeon->full_name }}">
                        {{ $surgeon->full_name }} ({{ $surgeon->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 👩‍⚕️ التمريض --}}
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ __('surgical_trans.nurse_signature') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث باسم التمريض أو الكود" wire:model.debounce.500ms="nurseSearch">
            <select class="form-select" wire:model="nurse_signature">
                <option value="">{{ __('اختر التمريض') }}</option>
                @foreach ($nurses as $nurse)
                    <option value="{{ $nurse->full_name }}">
                        {{ $nurse->full_name }} ({{ $nurse->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 🧑‍⚕️ المساعد الجراح --}}
        <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('surgical_trans.assistant_surgeon') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث عن المساعد" wire:model.debounce.500ms="assistantSearch">
            <select class="form-select" wire:model="assistant_surgeon">
                <option value="">اختر المساعد الجراح</option>
                @foreach ($assistants as $a)
                    <option value="{{ $a->full_name }}">{{ $a->full_name }} ({{ $a->employee_code }})</option>
                @endforeach
            </select>
        </div>

        {{-- 🩸 طبيب التخدير --}}
        <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('surgical_trans.anesthesiologist') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث باسم طبيب التخدير أو الكود" wire:model.debounce.500ms="anesthSearch">
            <select class="form-select" wire:model="anesthesiologist">
                <option value="">اختر طبيب التخدير</option>
                @foreach ($anesthesiologists as $an)
                    <option value="{{ $an->full_name }}">{{ $an->full_name }} ({{ $an->employee_code }})</option>
                @endforeach
            </select>
        </div>

        {{-- 🧴 الممرضة المعقمة --}}
        <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('surgical_trans.sterile_nurse') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث عن الممرضة المعقمة" wire:model.debounce.500ms="sterileSearch">
            <select class="form-select" wire:model="sterile_nurse">
                <option value="">اختر الممرضة المعقمة</option>
                @foreach ($steriles as $s)
                    <option value="{{ $s->full_name }}">{{ $s->full_name }} ({{ $s->employee_code }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- 🕓 التواريخ والأوقات --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <label class="form-label fw-bold">{{ __('surgical_trans.timeout_date') }}:</label>
            <input type="date" class="form-control" wire:model="timeout_date">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">{{ __('surgical_trans.timeout_time') }}:</label>
            <input type="time" class="form-control" wire:model="timeout_time">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">{{ __('surgical_trans.procedure_start_time') }}:</label>
            <input type="time" class="form-control" wire:model="procedure_start_time">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">{{ __('surgical_trans.procedure_end_time') }}:</label>
            <input type="time" class="form-control" wire:model="procedure_end_time">
        </div>
    </div>

    {{-- 💾 زر الحفظ --}}
    <div class="text-end">
        <button type="button" class="btn btn-primary px-4" wire:click="save">
            <i class="mdi mdi-content-save-outline me-1"></i> {{ __('general.save') }}
        </button>
    </div>
</div>
