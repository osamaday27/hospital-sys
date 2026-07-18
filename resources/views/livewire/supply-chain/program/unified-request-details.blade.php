<div>
    @if($selectedRequest)
        <div class="mb-3">
            <a href="/receive_withdraw_requests" class="btn btn-sm btn-secondary">
                <i class="ri-arrow-go-back-line me-1"></i> {{ __('supply_chain_trans.back_to_requests') }}
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ri-file-info-line me-2"></i>
                    {{ __('supply_chain_trans.request_details') }}
                </h5>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('supply_chain_trans.request_number') }}</label>
                        <div class="form-control bg-light">{{ $selectedRequest->request_number ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('supply_chain_trans.request_date') }}</label>
                        <div class="form-control bg-light">{{ $selectedRequest->request_date ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('supply_chain_trans.status') }}</label>
                        <div class="form-control bg-light">{{ __('supply_chain_trans.status_' . $selectedRequest->status) }}</div>
                    </div>
                </div>

                <div class="row mb-3">
                    @if($selectedType === 'client')
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('supply_chain_trans.client') }}</label>
                            <div class="form-control bg-light">{{ $selectedRequest->client->full_name ?? '-' }}</div>
                        </div>
                    @elseif($selectedType === 'employee')
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('supply_chain_trans.employee') }}</label>
                            <div class="form-control bg-light">{{ $selectedRequest->employee->full_name ?? '-' }}</div>
                        </div>
                        @elseif($selectedType === 'department')
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('supply_chain_trans.department') }}</label>
                            <div class="form-control bg-light">{{ $selectedRequest->department->name_ar ?? '-' }}</div>
                        </div>

                    @elseif($selectedType === 'store')
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('supply_chain_trans.destination_store') }}</label>
                            <div class="form-control bg-light">{{ $selectedRequest->storeTo->name_ar ?? '-' }}</div>
                        </div>
                    @endif

                    <div class="col-md-4">
                        <label class="form-label fw-bold">{{ __('supply_chain_trans.store') }}</label>
                        <div class="form-control bg-light">{{ $selectedRequest->store->name_ar ?? '-' }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('supply_chain_trans.item_name') }}</th>
                                <th>{{ __('supply_chain_trans.unit') }}</th>
                                <th>{{ __('supply_chain_trans.quantity_requested') }}</th>
                                <th>{{ __('supply_chain_trans.quantity_issued') }}</th>
                                <th>{{ __('supply_chain_trans.unit_issued') }}</th>
                                <th>{{ __('supply_chain_trans.issue_status') }}</th>
                                <th>{{ __('supply_chain_trans.issued_by') }}</th>
                                <th>{{ __('supply_chain_trans.store') }}</th>
                                <th>{{ __('supply_chain_trans.batch_no') }}</th>
                                <th>{{ __('supply_chain_trans.expiry_date') }}</th>
                                <th>{{ __('supply_chain_trans.movement_date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($selectedRequest->items ?? $selectedRequest->details) as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name_ar ?? '-' }}</td>
                                    <td>{{ $item->unit->name_ar ?? '-' }}</td>
                                    <td>{{ $item->quantity ?? '-' }}</td>
                                    <td>{{ $item->issued_quantity ?? '-' }}</td>
                                    <td>{{ $item->issued_unit_name ?? '-' }}</td>
                                    <td>
                                        @if(isset($item->issued_quantity) && isset($item->quantity))
                                            @if($item->issued_quantity >= $item->quantity)
                                                <span class="badge bg-success">{{ __('supply_chain_trans.full_issued') }}</span>
                                            @elseif($item->issued_quantity > 0)
                                                <span class="badge bg-warning">{{ __('supply_chain_trans.partial_issued') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('supply_chain_trans.not_issued') }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->issuer_name ?? '-' }}</td>
                                    <td>{{ $item->store_name ?? '-' }}</td>
                                    <td>{{ $item->batch_no ?? '-' }}</td>
                                    <td>{{ $item->expiry_date ?? '-' }}</td>
                                    <td>{{ $item->movement_date ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    @else
        <div class="alert alert-warning text-center">
            <i class="ri-error-warning-line me-1"></i> {{ __('supply_chain_trans.request_not_found') }}
        </div>
    @endif
</div>