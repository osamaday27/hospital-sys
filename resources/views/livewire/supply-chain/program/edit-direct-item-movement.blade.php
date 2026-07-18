<div class="card modern-form">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0 font-weight-bold">
            <i class="ri-edit-2-line me-2"></i>{{ __('supply_chain_trans.edit_direct_addition_title') }}
        </h5>
    </div>
    <div class="card-body">
        {{-- ✅ رسائل النجاح --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="update" class="needs-validation" novalidate>
            <!-- Basic Info -->
            <div class="form-section-header mb-4">
                <i class="ri-information-line me-2"></i>
                <h6>{{ __('supply_chain_trans.basic_information') }}</h6>
            </div>

            <div class="row g-3 mb-4">
                <!-- المورد -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ri-truck-line me-1"></i>{{ __('supply_chain_trans.supplier') }}
                    </label>
                    <select wire:model="supplier_id" class="form-select" required>
                        <option value="">{{ __('supply_chain_trans.choose_supplier') }}</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ app()->getLocale() == 'ar' ? $supplier->name_ar : $supplier->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- المخزن -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ri-store-2-line me-1"></i>{{ __('supply_chain_trans.store') }}
                    </label>
                    <select wire:model="store_id" class="form-select" required>
                        <option value="">{{ __('supply_chain_trans.choose_store') }}</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- تاريخ الحركة -->
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ri-calendar-line me-1"></i>{{ __('supply_chain_trans.movement_date') }}
                    </label>
                    <input type="date" wire:model="movement_date"
                           value="{{ old('movement_date', $movement_date) }}"
                           class="form-control flatpickr" required>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <!-- رقم المرجع -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="ri-file-text-line me-1"></i>{{ __('supply_chain_trans.reference_no') }}
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-hashtag"></i></span>
                        <input type="text" wire:model="reference_no"
                               value="{{ old('reference_no', $reference_no) }}"
                               class="form-control" placeholder="REF-001">
                    </div>
                </div>

                <!-- رقم الفاتورة -->
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="ri-file-list-2-line me-1"></i>{{ __('supply_chain_trans.invoice_no') }}
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-file-copy-line"></i></span>
                        <input type="text" wire:model="invoice_no"
                               value="{{ old('invoice_no', $invoice_no) }}"
                               class="form-control" placeholder="INV-001">
                    </div>
                </div>
            </div>

            <!-- ملاحظات -->
            <div class="mb-4">
                <label class="form-label">
                    <i class="ri-file-list-line me-1"></i>{{ __('supply_chain_trans.notes') }}
                </label>
                <textarea wire:model="notes" class="form-control" rows="2"
                          placeholder="{{ __('supply_chain_trans.optional_notes') }}">{{ old('notes', $notes) }}</textarea>
            </div>

            <!-- Items Section -->
            <div class="form-section-header mb-4">
                <i class="ri-list-check-2 me-2"></i>
                <h6>{{ __('supply_chain_trans.items_details') }}</h6>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('supply_chain_trans.product') }}</th>
                            <th>{{ __('supply_chain_trans.unit') }}</th>
                            <th>{{ __('supply_chain_trans.quantity') }}</th>
                            <th>{{ __('supply_chain_trans.expiry_date') }}</th>
                            <th>{{ __('supply_chain_trans.batch_no') }}</th>
                            <th>{{ __('supply_chain_trans.cost_price') }}</th>
                            <th>{{ __('supply_chain_trans.sale_price') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr class="item-row">
                                <!-- المنتج -->
                                <td>
                                    <select wire:model="items.{{ $index }}.product_id"
                                            class="form-select select2"
                                            data-index="{{ $index }}">
                                        <option value="">{{ __('supply_chain_trans.choose_product') }}</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name_ar }} ({{ $product->product_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block">
                                        {{ __('supply_chain_trans.code') }}: {{ $item['code'] ?? '-' }}<br>
                                        {{ __('supply_chain_trans.current_balance') }}: {{ $item['current_balance'] ?? 0 }}
                                    </small>
                                </td>

                                <!-- الوحدة -->
                                <td>
                                    <select wire:model="items.{{ $index }}.unit_id" class="form-select" required>
                                        <option value="">{{ __('supply_chain_trans.choose_unit') }}</option>
                                        @foreach ($item['related_units'] ?? [] as $unit)
                                            <option value="{{ is_array($unit) ? $unit['id'] : $unit->id }}">
                                                {{ app()->getLocale() == 'ar'
                                                    ? (is_array($unit) ? $unit['name_ar'] : $unit->name_ar)
                                                    : (is_array($unit) ? $unit['name_en'] : $unit->name_en) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- الكمية -->
                                <td>
                                    <input type="number" wire:model="items.{{ $index }}.quantity"
                                           class="form-control" min="1" required>
                                </td>

                                <!-- تاريخ الصلاحية -->
                                <td>
                                    <input type="date" class="form-control"
                                           wire:model="items.{{ $index }}.expiry_date"
                                           value="{{ $item['expiry_date'] ?? '' }}">
                                </td>

                                <!-- رقم الباتش -->
                                <td>
                                    <input type="text" wire:model="items.{{ $index }}.batch_no"
                                           class="form-control" placeholder="BATCH-001">
                                </td>

                                <!-- سعر التكلفة -->
                                <td>
                                    <input type="text" class="form-control"
                                           wire:model="items.{{ $index }}.cost_price" readonly>
                                </td>

                                <!-- سعر البيع -->
                                <td>
                                    <input type="text" class="form-control"
                                           wire:model="items.{{ $index }}.sale_price" readonly>
                                </td>

                                <!-- زر الحذف -->
                                <td class="text-center">
                                    <button type="button" wire:click="removeItemRow({{ $index }})"
                                            class="btn btn-sm btn-outline-danger rounded-circle">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- زر إضافة صف -->
            <button type="button" wire:click="addItemRow" class="btn btn-outline-primary btn-sm mb-4">
                <i class="ri-add-line me-1"></i>{{ __('supply_chain_trans.add_item_row') }}
            </button>

            <!-- أزرار الحفظ -->
            <div class="d-flex justify-content-between border-top pt-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-go-back-line me-1"></i>{{ __('supply_chain_trans.back') }}
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>{{ __('supply_chain_trans.update') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .modern-form { border: none; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .card-header { border-radius: 10px 10px 0 0 !important; padding: 1.25rem 1.5rem; }
    .form-section-header { display: flex; align-items: center; color: #4a6cf7; padding-bottom: .5rem; border-bottom: 1px solid #e0e0e0; }
    .form-section-header h6 { margin: 0; font-weight: 600; }
    .table th { font-weight: 600; text-transform: uppercase; font-size: .75rem; letter-spacing: .5px; white-space: nowrap; }
    .item-row:hover { background-color: #f8f9fa; }
    .select2 { width: 100% !important; }
    .flatpickr-input { background-color: white; }
    .input-group-text { background-color: #f8f9fa; }
    .form-label i { color: #6c757d; }
</style>

<script>
document.addEventListener('livewire:load', function () {
    initSelect2();
    Livewire.hook('message.processed', () => { initSelect2(); });
    function initSelect2() {
        $('.select2').select2({
            placeholder: "{{ __('supply_chain_trans.search_product') }}",
            allowClear: true,
            width: '100%'
        });
        $('.select2').on('change', function () {
            let index = $(this).data('index');
            let value = $(this).val();
            @this.set('items.' + index + '.product_id', value);
        });
    }
});
</script>
