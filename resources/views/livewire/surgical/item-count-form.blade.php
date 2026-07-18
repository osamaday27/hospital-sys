<div>
    <h5 class="mb-3 text-primary">
        <i class="mdi mdi-format-list-checks me-1"></i>
        {{ trans('surgical_trans.count_form') }}
    </h5>

    {{-- ✅ إشعارات النجاح --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ⚠️ تنبيهات العد --}}
    @if (!empty($countAlerts))
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($countAlerts as $alert)
                    <li>{!! $alert['message'] !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 🧮 جدول العد --}}
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>{{ trans('surgical_trans.item') }}</th>
                <th>{{ trans('surgical_trans.initial_count') }}</th>
                <th>{{ trans('surgical_trans.additional_item') }}</th>
                <th>{{ trans('surgical_trans.closure_count') }}</th>
                <th>{{ trans('surgical_trans.final_count') }}</th>
                <th>{{ trans('surgical_trans.actions') }}</th>
            </tr>
        </thead>

        <tbody>
            {{-- ✅ الأدوات المسجلة مسبقًا --}}
            @foreach ($existingItems as $index => $existingItem)
                <tr @class(['table-danger' => isset($countAlerts[$index])])>
                    <td>
                        <select class="form-select" wire:model="existingItems.{{ $index }}.item_name">
                            <option value="">{{ trans('surgical_trans.select_item') }}</option>
                            @foreach ($availableAssets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ App::getLocale() == 'ar' ? ($asset->name_ar ?? $asset->name_en) : ($asset->name_en ?? $asset->name_ar) }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td><input type="number" class="form-control text-center"
                            wire:model="existingItems.{{ $index }}.initial_count"></td>

                    <td><input type="number" class="form-control text-center"
                            wire:model="existingItems.{{ $index }}.additional_count"></td>

                    <td><input type="number" class="form-control text-center"
                            wire:model="existingItems.{{ $index }}.closure_count"></td>

                    <td>
                        <input type="number"
                            class="form-control text-center @isset($countAlerts[$index]) is-invalid @endisset"
                            wire:model="existingItems.{{ $index }}.final_count">
                        @isset($countAlerts[$index])
                            <small class="text-danger fw-bold">{{ $countAlerts[$index]['message'] }}</small>
                        @endisset
                    </td>

                    <td>
                        <button type="button" class="btn btn-sm btn-danger"
                            wire:click="deleteExistingItem({{ $existingItem['id'] }})">
                            <i class="mdi mdi-delete"></i> {{ trans('surgical_trans.remove') }}
                        </button>
                    </td>
                </tr>
            @endforeach

            {{-- ➕ الأدوات الجديدة --}}
            @foreach ($items as $index => $item)
                <tr @class(['table-danger' => isset($countAlerts[$index])])>
                    <td>
                        <select class="form-select" wire:model.lazy="items.{{ $index }}.item_name">
                            <option value="">{{ __('اختر الأداة') }}</option>
                            @foreach ($allAssets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ App::getLocale() == 'ar' ? ($asset->name_ar ?? $asset->name_en) : ($asset->name_en ?? $asset->name_ar) }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td><input type="number" class="form-control text-center"
                            wire:model="items.{{ $index }}.initial_count"></td>

                    <td><input type="number" class="form-control text-center"
                            wire:model="items.{{ $index }}.additional_count"></td>

                    <td><input type="number" class="form-control text-center"
                            wire:model="items.{{ $index }}.closure_count"></td>

                    <td>
                        <input type="number"
                            class="form-control text-center @isset($countAlerts[$index]) is-invalid @endisset"
                            wire:model="items.{{ $index }}.final_count">
                        @isset($countAlerts[$index])
                            <small class="text-danger fw-bold">{{ $countAlerts[$index]['message'] }}</small>
                        @endisset
                    </td>

                    <td>
                        <button type="button" class="btn btn-sm btn-danger"
                            wire:click="removeItem({{ $index }})">
                            <i class="mdi mdi-trash-can-outline"></i> {{ trans('surgical_trans.remove') }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ➕ زر إضافة أداة جديدة --}}
    <div class="text-start">
        <button type="button" class="btn btn-sm btn-secondary mb-3" wire:click="addItem">
            <i class="mdi mdi-plus-circle-outline me-1"></i> {{ trans('surgical_trans.add_item') }}
        </button>
    </div>

    {{-- 🧾 الحقول الإضافية --}}
    <div class="mb-3">
        <label class="form-label fw-bold">{{ trans('surgical_trans.final_count_verified') }}:</label>
        <input type="checkbox" wire:model="final_count_verified" class="form-check-input ms-2">
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">{{ trans('surgical_trans.notes') }}:</label>
        <textarea class="form-control" wire:model="notes" rows="3"
            placeholder="{{ __('أدخل الملاحظات الخاصة بعد الأدوات...') }}"></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ trans('surgical_trans.date') }}:</label>
            <input type="date" class="form-control text-center" wire:model="count_date">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ trans('surgical_trans.time') }}:</label>
            <input type="time" class="form-control text-center" wire:model="count_time">
        </div>
    </div>

    {{-- 🧍‍♂️ التوقيعات --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">{{ trans('surgical_trans.surgeon_signature') }}:</label>
            <input type="text" class="form-control mb-2" placeholder="ابحث باسم الجراح أو الكود"
                wire:model.debounce.500ms="surgeonSearch">
            <select class="form-select" wire:model="surgeon_id">
                <option value="">{{ __('اختر الجراح') }}</option>
                @foreach ($surgeons as $surgeon)
                    <option value="{{ $surgeon->id }}">
                        {{ $surgeon->full_name }} ({{ $surgeon->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>
<div class="col-md-6">
    <label class="form-label fw-bold">{{ trans('surgical_trans.nurse_signature') }}:</label>
    <input type="text" class="form-control mb-2" placeholder="ابحث باسم الممرضة أو الكود"
        wire:model.debounce.500ms="nurseSearch">

    <select class="form-select" wire:model="nurse_id">
        <option value="">{{ __('اختر الممرضة') }}</option>
        @foreach ($nurses as $nurse)
            <option value="{{ $nurse->id }}">
                {{ $nurse->full_name }} ({{ $nurse->employee_code }})
            </option>
        @endforeach
    </select>
</div>

    </div>

    {{-- 💾 زر الحفظ --}}
    <div class="text-end">
        <button type="button" class="btn btn-primary px-4 shadow-sm" wire:click="save">
            <i class="mdi mdi-content-save-outline me-1"></i> {{ trans('surgical_trans.save') }}
        </button>
    </div>

    {{-- ⚡ Toast Script --}}
    <script>
        document.addEventListener('livewire:load', () => {
            window.addEventListener('show-toast', event => {
                const data = event.detail;
                const toast = document.createElement('div');
                toast.className =
                    `toast align-items-center text-bg-${data.type} border-0 position-fixed bottom-0 end-0 m-3 show`;
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${data.message}</div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            });
        });
    </script>
</div>
