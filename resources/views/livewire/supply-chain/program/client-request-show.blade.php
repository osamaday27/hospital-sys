<div class="card shadow-lg border-0">
    <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="ri-eye-line me-2"></i> {{ __('supply_chain_trans.client_request_details') }}
        </h5>
        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm text-primary fw-bold">
            <i class="ri-arrow-go-back-line me-1"></i> {{ __('رجوع') }}
        </a>
    </div>

    <div class="card-body">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.client') }}</label>
                <div class="form-control bg-light">
                    {{ $request->client->full_name ?? '-' }} ({{ $request->client->medical_number ?? '' }})
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.store') }}</label>
                <div class="form-control bg-light">
                    {{ $request->store->name_ar ?? '-' }} ({{ $request->store->manual_code ?? '' }})
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('supply_chain_trans.request_date') }}</label>
                <div class="form-control bg-light">
                    {{ $request->request_date }}
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">{{ __('supply_chain_trans.reason') }}</label>
            <div class="form-control bg-light">
                {{ $request->reason ?: '-' }}
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold mb-3">
                <i class="ri-list-check-2 me-2"></i> {{ __('supply_chain_trans.request_items') }}
            </h6>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('supply_chain_trans.product') }}</th>
                            <th>{{ __('supply_chain_trans.unit') }}</th>
                            <th>{{ __('supply_chain_trans.quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($request->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $item->product->name_ar ?? '-' }} ({{ $item->product->product_code ?? '' }})
                                </td>
                                <td>
                                    {{ $item->unit->name_ar ?? '-' }}
                                </td>
                                <td>
                                    {{ $item->quantity }}
                                </td>
                            </tr>
                        @endforeach
                        @if($request->items->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    {{ __('supply_chain_trans.no_items_found') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 0.75rem; }
    .card-header { padding: 1rem 1.5rem; }
    .form-control.bg-light { border: 1px solid #e0e0e0; }
</style>
