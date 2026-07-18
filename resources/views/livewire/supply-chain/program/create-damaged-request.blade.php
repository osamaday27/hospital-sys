{{-- resources/views/livewire/supply-chain/program/create-damaged-request.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 fw-bold">
            <i class="ri-delete-bin-5-line me-2"></i>
            {{ __('supply_chain_trans.create_damaged_request') }}
        </h5>
        <a href="write_off_items" class="btn btn-light btn-sm text-danger fw-bold">
            <i class="ri-arrow-go-back-line me-1"></i> {{ __('supply_chain_trans.back_to_requests') }}
        </a>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success">
                <i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">
                <i class="ri-close-circle-line me-1"></i> {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                    <select wire:model="store_id" class="form-select">
                        <option value="">{{ __('supply_chain_trans.choose') }}</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('store_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
                    <input type="date" wire:model="request_date" class="form-control">
                    @error('request_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.reason') }}</label>
                    <input type="text" wire:model="reason" class="form-control">
                    @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('supply_chain_trans.product') }}</th>
                            <th>{{ __('supply_chain_trans.unit') }}</th>
                            <th>{{ __('supply_chain_trans.quantity') }}</th>
                            <th>{{ __('supply_chain_trans.current_balance') }}</th>
                            <th>{{ __('supply_chain_trans.notes') }}</th>
                            <th>{{ __('supply_chain_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <select wire:model="items.{{ $index }}.product_id" class="form-select">
                                        <option value="">{{ __('supply_chain_trans.choose') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name_ar }}</option>
                                        @endforeach
                                    </select>
                                    @error("items.$index.product_id") <small class="text-danger">{{ $message }}</small> @enderror
                                </td>

                                <td>
                                    <select wire:model="items.{{ $index }}.unit_id" class="form-select">
                                        <option value="">{{ __('supply_chain_trans.choose') }}</option>
                                        @foreach($item['related_units'] ?? [] as $unit)
                                            <option value="{{ $unit['id'] }}">{{ $unit['name_ar'] }}</option>
                                        @endforeach
                                    </select>
                                    @error("items.$index.unit_id") <small class="text-danger">{{ $message }}</small> @enderror
                                </td>

                                <td>
                                    <input type="number" step="0.01" wire:model="items.{{ $index }}.quantity" class="form-control">
                                    @error("items.$index.quantity") <small class="text-danger">{{ $message }}</small> @enderror
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $item['current_balance'] ?? 0 }}
                                    </span>
                                </td>

                                <td>
                                    <input type="text" wire:model="items.{{ $index }}.notes" class="form-control">
                                </td>

                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="removeItem({{ $index }})">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" wire:click="addItem" class="btn btn-outline-danger">
                    <i class="ri-add-line"></i> {{ __('supply_chain_trans.add_row') }}
                </button>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-danger px-5 fw-bold">
                    <i class="ri-save-line me-1"></i> {{ __('supply_chain_trans.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
