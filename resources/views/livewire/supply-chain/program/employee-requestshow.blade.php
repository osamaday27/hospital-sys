<div class="card shadow-sm border-0">
    <div class="card-header bg-info text-white py-3">
        <h5 class="mb-0 fw-bold" style="color: #fff">
            <i class="ri-eye-line me-2"></i> {{ __('supply_chain_trans.request_details') }}
        </h5>
    </div>

    <div class="card-body p-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>{{ __('supply_chain_trans.request_number') }}:</strong> {{ $request->request_number }}</p>
                <p><strong>{{ __('supply_chain_trans.request_date') }}:</strong> {{ $request->request_date }}</p>
                <p><strong>{{ __('supply_chain_trans.employee') }}:</strong> {{ $request->employee->full_name }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>{{ __('supply_chain_trans.store') }}:</strong> {{ $request->store->name_ar }}</p>
                <p><strong>{{ __('supply_chain_trans.reason') }}:</strong> {{ $request->reason ?? __('supply_chain_trans.not_specified') }}</p>
                <p><strong>{{ __('supply_chain_trans.status') }}:</strong>
                    <span class="badge bg-{{ $request->status == 'new' ? 'warning text-dark' : 'secondary' }}">
                        {{ __('supply_chain_trans.' . $request->status) }}
                    </span>
                </p>
            </div>
        </div>

        <hr>

        <h6 class="fw-bold mb-3">{{ __('supply_chain_trans.request_items') }}</h6>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('supply_chain_trans.product') }}</th>
                        <th>{{ __('supply_chain_trans.unit') }}</th>
                        <th>{{ __('supply_chain_trans.quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($request->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name_ar ?? '-' }}</td>
                            <td>{{ $detail->unit->name_ar ?? '-' }}</td>
                            <td>{{ $detail->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
