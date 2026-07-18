<div class="card modern-form">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0 font-weight-bold">
            <i class="ri-archive-line me-2"></i>{{ __('supply_chain_trans.opening_balance_title') }}
        </h5>
    </div>

    <div class="card-body">
        {{-- رسائل التنبيه --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <!-- Header Section -->
            <div class="form-section-header mb-4">
                <i class="ri-information-line me-2"></i>
                <h6>{{ __('supply_chain_trans.basic_information') }}</h6>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label"><i class="ri-truck-line me-1"></i>{{ __('supply_chain_trans.supplier') }}</label>
                    <select wire:model="supplier_id" class="form-select js-example-basic-single" required>
                        <option value="">{{ __('supply_chain_trans.choose_supplier') }}</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">
                                {{ app()->getLocale() == 'ar' ? $supplier->name_ar : $supplier->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><i class="ri-store-2-line me-1"></i>{{ __('supply_chain_trans.store') }}</label>
                    <select wire:model="store_id" class="form-select js-example-basic-single" required>
                        <option value="">{{ __('supply_chain_trans.choose_store') }}</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><i class="ri-calendar-line me-1"></i>{{ __('supply_chain_trans.movement_date') }}</label>
                    <input type="date" wire:model="movement_date" class="form-control flatpickr" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="ri-file-list-line me-1"></i>{{ __('supply_chain_trans.notes') }}</label>
                <textarea wire:model="notes" class="form-control" rows="2" placeholder="{{ __('supply_chain_trans.optional_notes') }}"></textarea>
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
                            <th width="20%"><i class="ri-box-line me-1"></i>{{ __('supply_chain_trans.product') }}</th>
                            <th width="15%"><i class="ri-ruler-line me-1"></i>{{ __('supply_chain_trans.unit') }}</th>
                            <th width="10%"><i class="ri-number-1 me-1"></i>{{ __('supply_chain_trans.quantity') }}</th>
                            <th width="15%"><i class="ri-calendar-event-line me-1"></i>{{ __('supply_chain_trans.expiry_date') }}</th>
                            <th width="15%"><i class="ri-barcode-line me-1"></i>{{ __('supply_chain_trans.batch_no') }}</th>
                            <th width="12%"><i class="ri-money-dollar-circle-line me-1"></i>{{ __('supply_chain_trans.cost_price') }}</th>
                            <th width="12%"><i class="ri-price-tag-3-line me-1"></i>{{ __('supply_chain_trans.sale_price') }}</th>
                            <th width="3%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                        <tr class="item-row">
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                                    <select wire:model="items.{{ $index }}.product_id" class="form-select product-select" required>
                                        <option value="">{{ __('supply_chain_trans.choose_product') }}</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="ri-barcode-line me-1"></i>{{ __('supply_chain_trans.code') }}: {{ $item['code'] ?? '-' }} |
                                    <i class="ri-scales-line me-1"></i>{{ __('supply_chain_trans.current_balance') }}: {{ $item['current_balance'] ?? '0' }}
                                </small>
                            </td>
                            <td>
                                <select wire:model="items.{{ $index }}.unit_id" class="form-select" required>
                                    <option value="">{{ __('supply_chain_trans.choose_unit') }}</option>
                                    @foreach ($item['related_units'] ?? [] as $unit)
                                        <option value="{{ is_array($unit) ? $unit['id'] : $unit->id }}">
                                            {{ app()->getLocale() == 'ar' ? (is_array($unit) ? $unit['name_ar'] : $unit->name_ar) : (is_array($unit) ? $unit['name_en'] : $unit->name_en) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" wire:model="items.{{ $index }}.quantity" class="form-control" min="1" required></td>
                            <td><input type="date" wire:model="items.{{ $index }}.expiry_date" class="form-control"></td>
                            <td><input type="text" wire:model="items.{{ $index }}.batch_no" class="form-control" placeholder="BATCH-001"></td>
                            <td><input type="text" wire:model="items.{{ $index }}.cost_price" class="form-control" readonly></td>
                            <td><input type="text" wire:model="items.{{ $index }}.sale_price" class="form-control" readonly></td>
                            <td class="text-center">
                                <button type="button" wire:click="removeItemRow({{ $index }})" class="btn btn-sm btn-outline-danger rounded-circle">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- عرض الخطأ إذا وُجد لهذا الصف --}}
                        @if (isset($rowErrors[$index]))
                        <tr>
                            <td colspan="8">
                                <div class="alert alert-warning p-2 mb-2">
                                    <i class="ri-alert-line me-1"></i> {{ $rowErrors[$index] }}
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="button" wire:click="addItemRow" class="btn btn-outline-primary btn-sm mb-4">
                <i class="ri-add-line me-1"></i> {{ __('supply_chain_trans.add_item_row') }}
            </button>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between border-top pt-3">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="ri-restart-line me-1"></i> {{ __('supply_chain_trans.reset') }}
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i> {{ __('supply_chain_trans.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .modern-form {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    .form-section-header {
        display: flex;
        align-items: center;
        color: #4a6cf7;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e0e0e0;
    }
    .form-section-header h6 {
        margin: 0;
        font-weight: 600;
    }
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .item-row:hover {
        background-color: #f8f9fa;
    }
</style>
