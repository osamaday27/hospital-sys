<div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark py-3">
        <h5 class="mb-0 fw-bold">
            <i class="ri-edit-line me-2"></i> {{ __('supply_chain_trans.edit_request') }}
        </h5>
    </div>

    <div class="card-body p-4">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="update">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
                    <input type="date" wire:model="request_date" class="form-control">
                    @error('request_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                    <select wire:model="store_id" class="form-control">
                        <option value="">{{ __('supply_chain_trans.select_store') }}</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('store_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.reason') }}</label>
                    <input type="text" wire:model="reason" class="form-control">
                </div>
            </div>

            <hr>
            <h6 class="fw-bold mb-3">{{ __('supply_chain_trans.request_items') }}</h6>

            @foreach ($items as $index => $item)
                <div class="row g-3 mb-2 align-items-end border-bottom pb-2">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('supply_chain_trans.product') }}</label>
                        <select class="form-control" wire:model="items.{{ $index }}.product_id">
                            <option value="">{{ __('supply_chain_trans.select_product') }}</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name_ar }}</option>
                            @endforeach
                        </select>
                        @error("items.$index.product_id") <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('supply_chain_trans.unit') }}</label>
                        <select class="form-control" wire:model="items.{{ $index }}.unit_id">
                            <option value="">{{ __('supply_chain_trans.select_unit') }}</option>
                            @foreach ($item['related_units'] as $unit)
                                <option value="{{ $unit['id'] }}">{{ $unit['name'] }}</option>
                            @endforeach
                        </select>
                        @error("items.$index.unit_id") <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">{{ __('supply_chain_trans.quantity') }}</label>
                        <input type="number" min="0.01" step="0.01" class="form-control" wire:model="items.{{ $index }}.quantity">
                        @error("items.$index.quantity") <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm mt-4" wire:click="removeItem({{ $index }})">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            @endforeach

            <button type="button" class="btn btn-outline-secondary btn-sm mt-3" wire:click="addItem">
                <i class="ri-add-line me-1"></i> {{ __('supply_chain_trans.add_item') }}
            </button>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success">
                    <i class="ri-save-line me-1"></i> {{ __('supply_chain_trans.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
