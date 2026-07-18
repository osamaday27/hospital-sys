<div class="card modern-form">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0 font-weight-bold">
            <i class="ri-exchange-line me-2"></i>{{ __('supply_chain_trans.direct_store_transfer') }}
        </h5>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-close-circle-line me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <div class="form-section-header mb-4">
                <i class="ri-information-line me-2"></i>
                <h6>{{ __('supply_chain_trans.basic_information') }}</h6>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ri-store-3-line me-1"></i>
                        {{ __('supply_chain_trans.store_from') }}
                    </label>
                    <select wire:model="store_from_id" class="form-select" required>
                        <option value="">{{ __('supply_chain_trans.choose_store') }}</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('store_from_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ri-store-2-line me-1"></i>
                        {{ __('supply_chain_trans.store_to') }}
                    </label>
                    <select wire:model="store_to_id" class="form-select" required>
                        <option value="">{{ __('supply_chain_trans.choose_store') }}</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}" @if ($store->id == $store_from_id) disabled @endif>
                                {{ $store->name_ar }}
                            </option>
                        @endforeach
                    </select>
                    @error('store_to_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">
                        <i class="ri-calendar-line me-1"></i>
                        {{ __('supply_chain_trans.transfer_date') }}
                    </label>
                    <input type="date" wire:model="transfer_date" class="form-control" required>
                    @error('transfer_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="ri-file-list-line me-1"></i>
                    {{ __('supply_chain_trans.notes') }}
                </label>
                <textarea wire:model="notes" class="form-control" rows="2"></textarea>
            </div>

            <div class="form-section-header mb-4">
                <i class="ri-list-check-2 me-2"></i>
                <h6>{{ __('supply_chain_trans.items_details') }}</h6>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="25%">{{ __('supply_chain_trans.product') }}</th>
                            <th width="15%">{{ __('supply_chain_trans.unit') }}</th>
                            <th width="20%">{{ __('supply_chain_trans.available_qty') }}</th>
                            <th width="10%">{{ __('supply_chain_trans.transfer_qty') }}</th>
                            <th width="15%">{{ __('supply_chain_trans.batch_no') }}</th>
                            <th width="10%">{{ __('supply_chain_trans.expiry_date') }}</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr>
                                <td>
                                    <select wire:model="items.{{ $index }}.product_id" class="form-select"
                                        required>
                                        <option value="">{{ __('supply_chain_trans.choose_product') }}</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    @error('items.' . $index . '.product_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>

                                <td>
                                    <select wire:model="items.{{ $index }}.unit_id" class="form-select" required
                                            @empty($item['related_units']) disabled @endempty>
                                        <option value="">{{ __('supply_chain_trans.choose_unit') }}</option>
                                        @foreach ($item['related_units'] ?? [] as $unit)
                                            <option value="{{ $unit['id'] }}">
                                                {{ app()->getLocale() === 'ar' ? $unit['name_ar'] : $unit['name_en'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.unit_id') 
                                        <small class="text-danger">{{ $message }}</small> 
                                    @enderror
                                </td>
                                <td>
                                    <small class="text-muted d-block mt-2">
                                        <i class="ri-barcode-line me-1"></i>{{ __('supply_chain_trans.code') }}: {{ $item['code'] ?? '-' }}<br>
                                        <i class="ri-scales-line me-1"></i>{{ __('supply_chain_trans.current_balance') }}:
                                        {{ $item['current_balance'] ?? '0' }} 
                                        {{ optional(collect($item['related_units'])->firstWhere('id', $item['unit_id'] ?? null))->name_ar ?? __('supply_chain_trans.unit') }}
                                    </small>
                                    
                                    
                                </td>

                                <td>
                                    <input type="number" wire:model="items.{{ $index }}.quantity"
                                        class="form-control" min="0.01" step="0.01" required
                                        @empty($item['unit_id']) disabled @endempty>
                                    @error('items.' . $index . '.quantity')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>

                                <td>
                                    <input type="text" wire:model="items.{{ $index }}.batch_no"
                                        class="form-control">
                                </td>

                                <td>
                                    <input type="date" wire:model="items.{{ $index }}.expiry_date"
                                        class="form-control">
                                </td>

                                <td class="text-center">
                                    @if (count($items) > 1)
                                        <button type="button" wire:click="removeItemRow({{ $index }})"
                                            class="btn btn-sm btn-outline-danger rounded-circle">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mb-4">
                <button type="button" wire:click="addItemRow" class="btn btn-outline-primary">
                    <i class="ri-add-line me-1"></i>
                    {{ __('supply_chain_trans.add_item_row') }}
                </button>
            </div>

            <div class="d-flex justify-content-between border-top pt-3">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="ri-restart-line me-1"></i>
                    {{ __('supply_chain_trans.reset') }}
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-1"></i>
                    {{ __('supply_chain_trans.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
    <style>
        .modern-form .form-section-header {
            background-color: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .modern-form .table th {
            white-space: nowrap;
        }

        .disabled-option {
            color: #6c757d;
            background-color: #f8f9fa;
        }
    </style>
@endpush
