{{-- resources/views/livewire/supply-chain/program/store-request-process.blade.php --}}

<div class="card shadow-sm border-0">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">
            <i class="ri-share-forward-line me-2"></i> {{ __('supply_chain_trans.store_request_issuance') }}
        </h5>
        <a href="/receive_withdraw_requests" class="btn btn-light btn-sm">
            <i class="ri-arrow-go-back-line me-1"></i> {{ __('supply_chain_trans.back_to_requests') }}
        </a>
    </div>

    <div class="card-body p-4">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri-check-double-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="mb-4">
            <h6 class="fw-semibold mb-2">{{ __('supply_chain_trans.request_info') }}</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">{{ __('supply_chain_trans.request_number') }}</label>
                    <div class="form-control bg-light">{{ $request->request_number }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">{{ __('supply_chain_trans.source_store') }}</label>
                    <div class="form-control bg-light">{{ $request->store?->name_ar ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">{{ __('supply_chain_trans.destination_store') }}</label>
                    <div class="form-control bg-light">{{ $request->store_to?->name_ar ?? '-' }}</div>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="process">
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('supply_chain_trans.item') }}</th>
                            <th>{{ __('supply_chain_trans.unit') }}</th>
                            <th>{{ __('supply_chain_trans.quantity_requested') }}</th>
                            <th>{{ __('supply_chain_trans.quantity_issued') }}</th>
                            <th>{{ __('supply_chain_trans.source_store') }}</th>
                            <th>{{ __('supply_chain_trans.current_balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['product_name'] }}</td>
                                <td>
                                    <select wire:model="items.{{ $index }}.unit_id"
                                        class="form-select form-select-sm">
                                        <option value="">{{ __('supply_chain_trans.choose') }}</option>
                                        @foreach ($item['related_units'] as $unit)
                                            <option value="{{ $unit['id'] }}">{{ $unit['name_ar'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $item['quantity_requested'] }}</td>
                                <td>
                                    <input type="number" min="0" step="0.01"
                                        wire:model="items.{{ $index }}.quantity_issued"
                                        class="form-control form-control-sm">
                                    @if (isset($item['current_balance']) && $item['quantity_issued'] > $item['current_balance'])
                                        <small
                                            class="text-danger">{{ __('supply_chain_trans.qty_gt_balance') }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{-- السحب يكون دائمًا من مخزن المصدر الخاص بالطلب، لكن لو سمحت بالتعديل خلي السطر التالي --}}
                                    <select wire:model="items.{{ $index }}.source_store_id"
                                        class="form-select form-select-sm">
                                        <option value="">{{ __('supply_chain_trans.choose') }}</option>
                                        @foreach ($item['available_stores'] as $store)
                                            <option value="{{ $store['id'] }}">
                                                {{ $store['name_ar'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <small class="text-muted d-block">
                                        {{ __('supply_chain_trans.balance') }}:
                                        {{ $item['current_balance'] ?? 0 }}
                                        {{ optional(collect($item['related_units'])->firstWhere('id', $item['unit_id'] ?? null))['name_ar'] ?? '' }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                @if ($request->status !== 'processed')
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5 shadow">
                            <i class="ri-send-plane-line me-1"></i> {{ __('supply_chain_trans.complete_issuance') }}
                        </button>
                    </div>
                @endif

            </div>
        </form>
    </div>
</div>
