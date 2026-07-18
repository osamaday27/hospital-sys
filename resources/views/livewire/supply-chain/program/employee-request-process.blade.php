{{-- resources/views/livewire/supply-chain/program/employee-request-process.blade.php --}}
<div class="card border-0 shadow-sm">

    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 fw-bold">
            <i class="ri-user-settings-line me-2"></i>
            {{ __('supply_chain_trans.process_employee_request') }}
        </h5>
        <a href="/receive_withdraw_requests" class="btn btn-light btn-sm text-primary fw-bold">
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
                <i class="ri-error-warning-line me-1"></i> {{ session('error') }}
            </div>
        @endif
        {{-- Request Summary --}}
        <div class="mt-5">
            <h6 class="mb-3 fw-bold text-primary">
                <i class="ri-information-line me-1"></i>
                {{ __('supply_chain_trans.request_details') }}
            </h6>

            <div class="row gy-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('supply_chain_trans.request_number') }}:</label>
                    <div class="form-control bg-light">{{ $request->request_number ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('supply_chain_trans.request_date') }}:</label>
                    <div class="form-control bg-light">{{ $request->request_date ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('supply_chain_trans.status') }}:</label>
                    <div class="form-control bg-light">
                        @php
                            $statusMap = [
                                'new' => __('supply_chain_trans.status_new'),
                                'partial' => __('supply_chain_trans.status_partial'),
                                'processed' => __('supply_chain_trans.status_processed'),
                                'closed' => __('supply_chain_trans.status_closed'),
                            ];
                        @endphp
                        {{ $statusMap[$request->status] ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('supply_chain_trans.employee') }}:</label>
                    <div class="form-control bg-light">{{ $request->employee->full_name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('supply_chain_trans.store') }}:</label>
                    <div class="form-control bg-light">{{ $request->store->name_ar ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">{{ __('supply_chain_trans.reason') }}:</label>
                    <div class="form-control bg-light">{{ $request->reason ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="table-responsive" style="margin-top: 30px;">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('supply_chain_trans.item_name') }}</th>
                        <th>{{ __('supply_chain_trans.unit') }}</th>
                        <th>{{ __('supply_chain_trans.quantity_requested') }}</th>
                        <th>{{ __('supply_chain_trans.quantity_issued') }}</th>
                        <th>{{ __('supply_chain_trans.store') }}</th>
                        <th>{{ __('supply_chain_trans.current_balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td>
                                {{ $item['product_name'] }}
                            </td>
                            <td>
                                <select class="form-select form-select-sm"
                                    wire:model="items.{{ $index }}.unit_id">
                                    <option value="">{{ __('supply_chain_trans.choose') }}</option>
                                    @foreach ($item['related_units'] as $unit)
                                        <option value="{{ $unit['id'] }}">{{ $unit['name_ar'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" readonly
                                    value="{{ $item['quantity_requested'] }}">
                            </td>
                            <td>
                                <input type="number" min="0" class="form-control form-control-sm"
                                    wire:model.defer="items.{{ $index }}.quantity_issued">
                            </td>
                            <td>
                                <select class="form-select form-select-sm"
                                    wire:model="items.{{ $index }}.store_id">
                                    <option value="">{{ __('supply_chain_trans.choose') }}</option>
                                    @foreach ($item['available_stores'] as $store)
                                        <option value="{{ $store['id'] }}">{{ $store['name_ar'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-dark">
                                    {{ $item['current_balance'] ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="text-end mt-4">
            <button class="btn btn-success" wire:click="process">
                <i class="ri-checkbox-circle-line me-1"></i> {{ __('supply_chain_trans.confirm_issue') }}
            </button>
        </div>
    </div>
</div>
