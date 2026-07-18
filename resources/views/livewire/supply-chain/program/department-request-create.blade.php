<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0" style="color: #fff">{{ __('supply_chain_trans.title') }}</h5>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ __('supply_chain_trans.success') }}
            </div>
        @endif

        <form wire:submit.prevent="save">
            <!-- بيانات أساسية -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.department') }}</label>
                    <select wire:model="department_id" class="form-control">
                        <option value="">{{ __('supply_chain_trans.select_department') }}</option>
                        @foreach($departments as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                    <select wire:model="store_id" class="form-control">
                        <option value="">{{ __('supply_chain_trans.select_store') }}</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('store_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
                    <input type="date" wire:model="request_date" class="form-control">
                    @error('request_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('supply_chain_trans.reason') }}</label>
                <textarea wire:model="reason" class="form-control" rows="2"></textarea>
            </div>

            <hr>
            <!-- الأصناف -->
            <h6>{{ __('supply_chain_trans.items_details') }}</h6>
            @foreach($items as $index => $item)
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('supply_chain_trans.product') }}</label>
                        <select wire:model="items.{{ $index }}.product_id" class="form-control">
                            <option value="">{{ __('supply_chain_trans.select_product') }}</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->product_code }} - {{ $prod->name_ar }}</option>
                            @endforeach
                        </select>
                        @error("items.$index.product_id") <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">{{ __('supply_chain_trans.quantity') }}</label>
                        <input type="number" step="0.01" wire:model="items.{{ $index }}.quantity" class="form-control">
                        @error("items.$index.quantity") <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('supply_chain_trans.unit') }}</label>
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
                        
                        @error("items.$index.unit_id") <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger" wire:click="removeItem({{ $index }})">
                            {{ __('supply_chain_trans.delete') }}
                        </button>
                    </div>
                </div>
            @endforeach

            <div class="mb-3">
                <button type="button" wire:click="addItem" class="btn btn-secondary">
                    {{ __('supply_chain_trans.add_item') }}
                </button>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="ri-check-double-line"></i> {{ __('supply_chain_trans.save_request') }}
                </button>
            </div>
        </form>
    </div>
</div>
