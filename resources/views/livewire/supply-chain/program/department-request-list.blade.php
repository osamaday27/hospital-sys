{{-- resources/views/livewire/supply-chain/program/department-request-list.blade.php --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold" style="color: #fff">
            <i class="ri-list-check-2 me-2"></i> {{ __('supply_chain_trans.previous_requests') }}
        </h5>
        <a href="{{ route('create_request_department') }}" class="btn btn-light btn-sm text-primary fw-bold">
            <i class="ri-add-circle-line me-1"></i> {{ __('supply_chain_trans.new_request') }}
        </a>
    </div>


    <div class="card-body p-4">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('supply_chain_trans.request_number') }}</th>
                        <th>{{ __('supply_chain_trans.request_date') }}</th>
                        <th>{{ __('supply_chain_trans.store') }}</th>
                        <th>{{ __('supply_chain_trans.status') }}</th>
                        <th>{{ __('supply_chain_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td>{{ $request->request_number }}</td>
                            <td>{{ $request->request_date }}</td>
                            <td>{{ $request->store?->name_ar }}</td>
                            <td>
                                @if ($request->status === 'new')
                                    <span class="badge bg-warning text-dark">{{ __('supply_chain_trans.new') }}</span>
                                @elseif ($request->status === 'closed')
                                    <span class="badge bg-secondary">{{ __('supply_chain_trans.closed') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($request->status === 'new')
                                    <button wire:click="editRequest({{ $request->id }})" class="btn btn-sm btn-info me-1">
                                        <i class="ri-edit-line"></i> {{ __('supply_chain_trans.edit') }}
                                    </button>
                                    <button wire:click="closeRequest({{ $request->id }})" class="btn btn-sm btn-danger me-1">
                                        <i class="ri-close-line"></i> {{ __('supply_chain_trans.close') }}
                                    </button>
                                @endif
                                <a href="/show_request_department/{{ $request->id }}/department_requestshow" class="btn btn-sm btn-outline-secondary">
                                    <i class="ri-eye-line"></i> {{ __('supply_chain_trans.view') }}
                                </a>
                                <a href="/print_request_department/{{ $request->id }}/print" class="btn btn-sm btn-outline-warning">
                                    <i class="ri-printer-line"></i>
                                    {{ __('supply_chain_trans.print') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('supply_chain_trans.no_requests_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
