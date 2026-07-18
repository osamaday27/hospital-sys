<div class="p-3">
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
    {{-- بطاقة الاختيار --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ __('supply_chain_trans.add_suppliers_and_conditions') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- اختيار الموردين --}}
                <div class="col-md-5">
                    <label class="form-label">{{ __('suppliers.suppliers') }}</label>
                    <select id="suppliersSelect"
                            class="form-select"
                            wire:model="selectedSupplierIds"
                            multiple wire:ignore>
                        @foreach($allSuppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ $supplier->name_ar }} — {{ $supplier->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('selectedSupplierIds.*')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                {{-- اختيار ظروف التخزين --}}
                <div class="col-md-5">
                    <label class="form-label">{{ __('supply_chain_trans.storage_conditions') }}</label>
                    <select id="conditionsSelect"
                            class="form-select"
                            wire:model="selectedStorageConditionIds"
                            multiple wire:ignore>
                        @foreach($allConditions as $condition)
                            <option value="{{ $condition->id }}">
                                {{ app()->getLocale() === 'ar' ? $condition->name_ar : $condition->name_en }}
                            </option>
                        @endforeach
                    </select>
                    @error('selectedStorageConditionIds.*')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                {{-- زر الإضافة/التعديل --}}
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"
                            wire:click.prevent="addEntry">
                        {{ $editId ? __('general.update') : __('supply_chain_trans.add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول العرض --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5>{{ __('supply_chain_trans.current_suppliers_and_conditions') }}</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('suppliers.suppliers') }}</th>
                        <th>{{ __('supply_chain_trans.storage_conditions') }}</th>
                        <th>{{ __('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr>
                            {{-- عمود الموردين --}}
                            <td>
                                @foreach($rec->supplier_ids as $sid)
                                    @php $sup = $allSuppliers->firstWhere('id', $sid); @endphp
                                    <div>{{ $sup->name_ar }} ({{ $sup->name_en }})</div>
                                @endforeach
                            </td>

                            {{-- عمود ظروف التخزين --}}
                            <td>
                                @foreach($rec->storage_condition_ids as $cid)
                                    @php $cond = $allConditions->firstWhere('id', $cid); @endphp
                                    <div>{{ app()->getLocale() === 'ar' ? $cond->name_ar : $cond->name_en }}</div>
                                @endforeach
                            </td>

                            {{-- عمود الإجراءات --}}
                            <td>
                                <button class="btn btn-sm btn-primary"
                                        wire:click.prevent="edit({{ $rec->id }})">
                                    {{ __('general.edit') }}
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        wire:click.prevent="confirmDelete({{ $rec->id }})">
                                    {{ __('supply_chain_trans.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">
                                {{ __('supply_chain_trans.no_suppliers_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
 @endif

    {{-- مودال تأكيد الحذف --}}
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('call_center_trans.cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" id="confirm-delete-btn">
                            {{ __('call_center_trans.yes_delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- سكربت Select2 والأحداث --}}
    <script>
    document.addEventListener('livewire:load', function () {
        function initSelect2() {
            $('#suppliersSelect, #conditionsSelect').select2({
                placeholder: "{{ __('supply_chain_trans.choose') }}",
                allowClear: true,
                width: '100%'
            }).on('change', function () {
                const prop = $(this).attr('id') === 'suppliersSelect'
                    ? 'selectedSupplierIds'
                    : 'selectedStorageConditionIds';
                @this.set(prop, $(this).val());
            });
        }

        initSelect2();
        Livewire.hook('message.processed', initSelect2);

        window.addEventListener('open-delete-modal', () => {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });

        window.addEventListener('close-delete-modal', () => {
            const m = document.getElementById('deleteModal');
            bootstrap.Modal.getInstance(m)?.hide();
        });

        document.getElementById('confirm-delete-btn').addEventListener('click', () => {
            Livewire.emit('deleteConfirmed');
        });

        window.addEventListener('reset-select2', initSelect2);
    });
    </script>


</div>
