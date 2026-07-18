<div class="card shadow-lg border-0">
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="ri-edit-line me-2"></i> {{ __('supply_chain_trans.edit_client_request') }}
            </h5>
        </div>
    </div>

    <div class="card-body">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-check-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="save" class="needs-validation" novalidate>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.client') }}</label>
                    <input type="text" wire:model.debounce.500ms="client_search" class="form-control form-control-sm mb-2" placeholder="{{ __('بحث عن عميل...') }}">
                    <select wire:model="client_id" class="form-select" required>
                        <option value="">{{ __('supply_chain_trans.select_client') }}</option>
                        @foreach($clients as $client)
                            <option value="{{ $client['id'] }}">
                                {{ $client['full_name'] }} ({{ $client['medical_number'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                    <input type="text" wire:model.debounce.500ms="store_search" class="form-control form-control-sm mb-2" placeholder="{{ __('بحث عن مخزن...') }}">
                    <select wire:model="store_id" class="form-select" required>
                        <option value="">{{ __('supply_chain_trans.select_store') }}</option>
                        @foreach($stores as $store)
                            <option value="{{ $store['id'] }}">
                                {{ $store['name_ar'] }} ({{ $store['manual_code'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('store_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri-calendar-line"></i></span>
                        <input type="date" wire:model="request_date" max="{{ now()->format('Y-m-d') }}" class="form-control" required>
                    </div>
                    @error('request_date') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">{{ __('supply_chain_trans.reason') }}</label>
                <textarea wire:model="reason" class="form-control" rows="2" placeholder="{{ __('supply_chain_trans.enter_reason') }}"></textarea>
                @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        <i class="ri-list-check-2 me-2"></i> {{ __('supply_chain_trans.request_items') }}
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" wire:click="addItem">
                        <i class="ri-add-line me-1"></i> {{ __('supply_chain_trans.add_item') }}
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('supply_chain_trans.product') }}</th>
                                <th>{{ __('supply_chain_trans.quantity') }}</th>
                                <th>{{ __('supply_chain_trans.unit') }}</th>
                                <th class="text-center">{{ __('supply_chain_trans.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr>
                                    <td>
                                        <input type="text" wire:model.debounce.500ms="product_search" class="form-control form-control-sm mb-1" placeholder="{{ __('بحث عن صنف...') }}">
                                        <select wire:model="items.{{ $index }}.product_id" class="form-select" required>
                                            <option value="">{{ __('supply_chain_trans.select_product') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product['id'] }}">
                                                    {{ $product['name_ar'] }} ({{ $product['product_code'] }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("items.$index.product_id") <small class="text-danger">{{ $message }}</small> @enderror
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" wire:model="items.{{ $index }}.quantity" class="form-control" required>
                                        @error("items.$index.quantity") <small class="text-danger">{{ $message }}</small> @enderror
                                    </td>
                                    <td>
                                        <select wire:model="items.{{ $index }}.unit_id" class="form-select" required>
                                            <option value="">{{ __('supply_chain_trans.select_unit') }}</option>
                                            @foreach($item['related_units'] ?? [] as $unit)
                                                <option value="{{ $unit['id'] }}">
                                                    {{ app()->getLocale() == 'ar' ? $unit['name_ar'] : $unit['name_en'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("items.$index.unit_id") <small class="text-danger">{{ $message }}</small> @enderror
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="removeItem({{ $index }})" @if(count($items) <= 1) disabled @endif>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between border-top pt-3">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-go-back-line me-1"></i> {{ __('رجوع') }}
                </a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="ri-save-line me-1"></i> {{ __('supply_chain_trans.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .card { border-radius: 0.75rem; }
    .card-header { padding: 1rem 1.5rem; }
    .table th { font-weight: 600; font-size: 0.8rem; text-transform: uppercase; color: #6c757d; }
    .form-select, .form-control { border-radius: 0.5rem; }
    .input-group-text { background-color: #f8f9fa; }
</style>
