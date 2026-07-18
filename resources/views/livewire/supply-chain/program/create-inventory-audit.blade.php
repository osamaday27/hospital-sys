{{-- resources/views/livewire/supply-chain/inventory/create-inventory-audit.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="ri-file-list-3-line me-2"></i>
            {{ __('supply_chain_trans.create_inventory_audit') }}
        </h5>
        <a href="inventory_audits" class="btn btn-light btn-sm">
            <i class="ri-arrow-go-back-line me-1"></i> {{ __('supply_chain_trans.back') }}
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

        <form wire:submit.prevent="save">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                    <select class="form-select" wire:model="store_id">
                        <option value="">اختر المخزن</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name_ar }}</option>
                        @endforeach
                    </select>
                    @error('store_id')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('supply_chain_trans.audit_date') }}</label>
                    <input type="date" class="form-control" wire:model="audit_date">
                    @error('audit_date')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('supply_chain_trans.notes') }}</label>
                    <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('supply_chain_trans.product') }}</th>
                            <th>{{ __('supply_chain_trans.unit') }}</th>
                            <th>{{ __('supply_chain_trans.system_quantity') }}</th>
                            <th>{{ __('supply_chain_trans.actual_quantity') }}</th>
                            <th>{{ __('supply_chain_trans.difference') }}</th>
                            <th>{{ __('supply_chain_trans.reason') }}</th>
                            <th>{{ __('supply_chain_trans.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($storeProducts as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product['product_name'] }}</td>
                                <td>{{ $product['unit_name'] }}</td>
                                <td>{{ $product['system_quantity'] }}</td>
                                <td>
                                    <input type="number" step="0.0001" class="form-control" wire:model="storeProducts.{{ $index }}.actual_quantity">
                                </td>
                                <td>
                                    @php
                                        $diff = $product['actual_quantity'] - $product['system_quantity'];
                                        $type = $diff > 0 ? 'زيادة' : ($diff < 0 ? 'عجز' : '-');
                                    @endphp
                                    <span class="badge {{ $diff > 0 ? 'bg-success' : ($diff < 0 ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ number_format($diff, 2) }} ({{ $type }})
                                    </span>
                                </td>
                                <td>
                                    <input type="text" class="form-control" wire:model="storeProducts.{{ $index }}.reason">
                                </td>
                                <td>
                                    <input type="text" class="form-control" wire:model="storeProducts.{{ $index }}.notes">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('supply_chain_trans.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="ri-save-line me-1"></i> {{ __('supply_chain_trans.save') }}
                </button>
            </div>
        </form>
    </div>
</div>