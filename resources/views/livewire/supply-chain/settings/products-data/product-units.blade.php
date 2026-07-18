<div>
    {{-- تحذير إذا لم يُمرّر معرّف المنتج --}}
    @unless($productId)
        <div class="alert alert-warning">
            {{ __('supply_chain_trans.please_enter_product_data_first') }}
        </div>
    @endunless

    @if($productId)
        <!-- رسالة نجاح -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show text-center mt-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- نموذج إدخال/تعديل الوحدة -->
        <div class="card mb-4">
            <div class="card-header">{{ trans('supply_chain_trans.add_product_unit') }}</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ trans('supply_chain_trans.unit') }}</label>
                    <select class="form-select" wire:model="selected_unit_id">
                        <option value="">{{ trans('supply_chain_trans.select_unit') }}</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('selected_unit_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ trans('supply_chain_trans.conversion_rate') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="conversion_rate">
                    @error('conversion_rate') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ trans('supply_chain_trans.cost_price') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="cost_price">
                    @error('cost_price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ trans('supply_chain_trans.sale_price') }}</label>
                    <input type="number" step="0.01" class="form-control" wire:model="sale_price">
                    @error('sale_price') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ trans('supply_chain_trans.unit_type') }}</label>
                    <select class="form-select" wire:model="unit_type">
                        <option value="">{{ trans('supply_chain_trans.select_type') }}</option>
                        <option value="purchase">{{ trans('supply_chain_trans.purchase') }}</option>
                        <option value="sale">{{ trans('supply_chain_trans.sale') }}</option>
                        <option value="both">{{ trans('supply_chain_trans.both') }}</option>
                        <option value="conversion">{{ trans('supply_chain_trans.conversion') }}</option>
                    </select>
                    @error('unit_type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-12 d-flex justify-content-between align-items-center mt-3">
                    @if($isEditMode)
                        <button class="btn btn-secondary" wire:click="$emit('goToTab', 'tab1')">
                            ← {{ trans('supply_chain_trans.back_to_basic_data') }}
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="d-flex gap-2">
                        @if($isEditMode)
                            <button class="btn btn-success" wire:click="saveUnit">
                                {{ trans('general.update') }}
                            </button>
                            <button class="btn btn-secondary" wire:click="cancelEdit">
                                {{ trans('general.cancel') }}
                            </button>
                            <button class="btn btn-outline-info" wire:click="$emit('goToTab', 'tab3')">
                                {{ trans('supply_chain_trans.goto_suppliers_conditions') }} →
                            </button>
                        @else
                            <button class="btn btn-primary" wire:click="saveUnit">
                                {{ trans('general.add') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الوحدات -->
        <div class="card">
            <div class="card-header">{{ trans('supply_chain_trans.added_units') }}</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ trans('supply_chain_trans.unit_large') }}</th>
                            <th>{{ trans('supply_chain_trans.unit_small') }}</th>
                            <th>{{ trans('supply_chain_trans.conversion_rate') }}</th>
                            <th>{{ trans('supply_chain_trans.cost_price') }}</th>
                            <th>{{ trans('supply_chain_trans.sale_price') }}</th>
                            <th>{{ trans('supply_chain_trans.unit_type') }}</th>
                            <th>{{ trans('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productUnits as $unit)
                            <tr>
                                <td>{{ $unit->unitLarge->name_ar ?? '-' }}</td>
                                <td>{{ $unit->unitSmall->name_ar ?? '-' }}</td>
                                <td>{{ $unit->conversion_rate }}</td>
                                <td>{{ $unit->cost_price }}</td>
                                <td>{{ $unit->sale_price }}</td>
                                <td>
                                    @switch($unit->unit_type)
                                        @case('purchase') {{ trans('supply_chain_trans.purchase') }} @break
                                        @case('sale') {{ trans('supply_chain_trans.sale') }} @break
                                        @case('both') {{ trans('supply_chain_trans.both') }} @break
                                        @case('conversion') {{ trans('supply_chain_trans.conversion') }} @break
                                    @endswitch
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" wire:click="editUnit({{ $unit->id }})">
                                        {{ trans('general.edit') }}
                                    </button>
                                    <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $unit->id }})">
                                        {{ trans('general.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ trans('supply_chain_trans.no_units_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

        <!-- مودال تأكيد الحذف -->
        <div wire:ignore.self class="modal fade" id="deleteModal1" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <lord-icon
                            src="https://cdn.lordicon.com/gsqxdxog.json"
                            trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548"
                            style="width:100px;height:100px">
                        </lord-icon>
                        <h4 class="mt-3">{{ __('call_center_trans.h4_delete') }}</h4>
                        <p>{{ __('call_center_trans.p_delete') }}</p>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button
                                type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal"
                            >
                                {{ __('call_center_trans.cancel') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-danger"
                                id="confirm-delete-btn1"
                            >
                                {{ __('call_center_trans.yes_delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toastify & Modals Script -->
        <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script>
        document.addEventListener('livewire:load', function () {
            // عرض التنبيهات
            window.addEventListener('show-toast', event => {
                Toastify({
                    text: event.detail.message,
                    duration: 3000,
                    gravity: 'top',
                    position: 'center',
                    backgroundColor: event.detail.type === 'success' ? "green" : "red",
                }).showToast();
            });

            // فتح المودال
            window.addEventListener('open-delete-modal1', () => {
                new bootstrap.Modal(document.getElementById('deleteModal1')).show();
            });

            // إغلاق المودال
            window.addEventListener('close-delete-modal1', () => {
                const m = document.getElementById('deleteModal1');
                bootstrap.Modal.getInstance(m)?.hide();
            });

            // زر "نعم، احذف"
            document.getElementById('confirm-delete-btn1')
                .addEventListener('click', () => Livewire.emit('deleteConfirmed1'));
        });
        </script>
</div>
