{{-- resources/views/livewire/supply-chain/program/damaged-requests-index.blade.php --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 fw-bold">
            <i class="ri-delete-bin-line me-2"></i>
            {{ __('supply_chain_trans.damaged_requests_list') }}
        </h5>
        <a href="{{ route('damaged_request.create') }}" class="btn btn-light btn-sm text-danger fw-bold">
            <i class="ri-add-line me-1"></i> {{ __('supply_chain_trans.create_damaged_request') }}
        </a>
    </div>

    <div class="card-body">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <input type="text" class="form-control w-25" wire:model.debounce.500ms="search" placeholder="{{ __('supply_chain_trans.search_by_number') }}">
            <select class="form-select w-25" wire:model="perPage">
                <option value="10">10 {{ __('supply_chain_trans.records') }}</option>
                <option value="25">25 {{ __('supply_chain_trans.records') }}</option>
                <option value="50">50 {{ __('supply_chain_trans.records') }}</option>
            </select>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('supply_chain_trans.request_number') }}</th>
                        <th>{{ __('supply_chain_trans.date') }}</th>
                        <th>{{ __('supply_chain_trans.store') }}</th>
                        <th>{{ __('supply_chain_trans.status') }}</th>
                        <th>{{ __('supply_chain_trans.created_by') }}</th>
                        <th>{{ __('supply_chain_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $index => $request)
                        <tr>
                            <td>{{ $requests->firstItem() + $index }}</td>
                            <td>{{ $request->request_number }}</td>
                            <td>{{ $request->request_date }}</td>
                            <td>{{ $request->store->name_ar ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'secondary') }}">
                                    {{ __('supply_chain_trans.status_' . $request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('damaged_request.show', $request->id) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                {{ __('supply_chain_trans.no_requests_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
