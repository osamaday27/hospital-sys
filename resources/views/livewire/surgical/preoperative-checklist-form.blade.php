<div>
    <h4 class="mb-3 text-primary">{{ __('surgical_trans.preoperative_checklist') }}</h4>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- ✅ جدول المعدات --}}
    <h5 class="mt-3">{{ __('surgical_trans.equipment_status') }}</h5>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>{{ __('surgical_trans.equipment') }}</th>
                <th>N/A</th>
                <th>{{ __('surgical_trans.not_present') }}</th>
                <th>{{ __('surgical_trans.present_working') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($equipmentList as $equip)
                <tr>
                    <td>{{ __('surgical_trans.' . $equip) }}</td>
                    <td><input type="radio" wire:model="equipment_status.{{ $equip }}" value="N/A"></td>
                    <td><input type="radio" wire:model="equipment_status.{{ $equip }}" value="Not Present"></td>
                    <td><input type="radio" wire:model="equipment_status.{{ $equip }}" value="Present/working"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ✅ جدول الفحص المسبق --}}
    <h5 class="mt-4">{{ __('surgical_trans.preoperative_verification') }}</h5>
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>{{ __('surgical_trans.verification_item') }}</th>
                <th>N/A</th>
                <th>{{ __('surgical_trans.not_present') }}</th>
                <th>{{ __('surgical_trans.present_working') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach (['consent_signed', 'site_marked', 'physical_exam_done', 'images_available', 'labs_available', 'allergy_recorded'] as $field)
                <tr>
                    <td>{{ __('surgical_trans.' . $field) }}</td>
                    <td><input type="radio" wire:model="{{ $field }}" value="N/A"></td>
                    <td><input type="radio" wire:model="{{ $field }}" value="Not Present"></td>
                    <td><input type="radio" wire:model="{{ $field }}" value="Present/working"></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ✅ التوقيعات --}}
    <div class="row mt-4">
         <div class="col-md-6">
            <label class="form-label fw-bold">{{ __('surgical_trans.surgeon_signature') }}</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث باسم الجراح أو الكود" wire:model.debounce.500ms="surgeonSearch">
            <select class="form-select" wire:model="surgeon_signature">
                <option value="">{{ __('اختر الجراح') }}</option>
                @foreach ($surgeons as $s)
                    <option value="{{ $s->full_name }}">{{ $s->full_name }} ({{ $s->employee_code }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ __('surgical_trans.nurse_signature') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث باسم التمريض أو الكود" wire:model.debounce.500ms="nurseSearch">
            <select class="form-select" wire:model="nurse_signature">
                <option value="">{{ __('اختر التمريض') }}</option>
                @foreach ($nurses as $n)
                    <option value="{{ $n->full_name }}">{{ $n->full_name }} ({{ $n->employee_code }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ✅ التاريخ والوقت --}}
    <div class="row mt-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ __('surgical_trans.checklist_date') }}</label>
            <input type="date" class="form-control" wire:model="checklist_date">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ __('surgical_trans.checklist_time') }}</label>
            <input type="time" class="form-control" wire:model="checklist_time">
        </div>
    </div>

    {{-- ✅ زر الحفظ --}}
    <div class="text-end mt-4">
        <button type="button" class="btn btn-primary px-4" wire:click="save">
            <i class="ri-save-line me-1"></i> {{ __('general.save') }}
        </button>
    </div>
</div>
