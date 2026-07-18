<div class="container-fluid py-4">
    <div class="card shadow-lg border-0">
        <!-- Card Header with Search and Filters -->
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center">
                    <i class="ri-discount-percent-line fs-4 me-3"></i>
                    <h5 class="mb-0 fw-bold" style="width: 229px;">{{ __('lockers_trans.manage_discount_requests') }}</h5>
                </div>
                
                <div class="d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
                    <div class="position-relative flex-grow-1">
                        <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-3  opacity-75"></i>
                        <input type="text" wire:model.debounce.500ms="search" 
                               class="form-control bg-white bg-opacity-10  ps-5 border-0" 
                               placeholder="{{ __('lockers_trans.search_invoice') }}">
                    </div>
                    <select wire:model="statusFilter" class="form-select" style="width: 203px;">
                        <option value="">{{ __('lockers_trans.all_statuses') }}</option>
                        <option value="pending">{{ __('lockers_trans.pending') }}</option>
                        <option value="approved">{{ __('lockers_trans.approved') }}</option>
                        <option value="rejected">{{ __('lockers_trans.rejected') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-0">
            <!-- Status Messages -->
            @if (session()->has('success'))
                <div class="alert alert-success border-0 rounded-0 alert-dismissible fade show mb-0">
                    <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger border-0 rounded-0 alert-dismissible fade show mb-0">
                    <i class="ri-alert-line me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Requests Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>{{ __('lockers_trans.invoice_number') }}</th>
                            <th>{{ __('lockers_trans.patient_name') }}</th>
                            <th class="text-end">{{ __('lockers_trans.amount') }}</th>
                            <th>{{ __('lockers_trans.reason') }}</th>
                            <th>{{ __('lockers_trans.status') }}</th>
                            <th class="pe-4">{{ __('lockers_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $index => $request)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs bg-primary bg-opacity-10  rounded me-2" style="text-align: center;padding-top: 10px;">
                                            <i class="ri-file-text-line"></i>
                                        </div>
                                        {{ $request->invoice->number ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs bg-info bg-opacity-10  rounded me-2" style="text-align: center;padding-top: 10px;">
                                            <i class="ri-user-3-line"></i>
                                        </div>
                                        {{ $request->invoice->patient->full_name ?? '-' }}
                                    </div>
                                </td>
                                <td class="text-end fw-bold">{{ number_format($request->amount, 2) }}</td>
                                <td class="text-truncate" style="max-width: 200px;" title="{{ $request->reason }}">
                                    {{ $request->reason }}
                                </td>
                                <td>
                                    @if ($request->status === 'pending')
                                        <span class="badge bg-warning bg-opacity-15 rounded-pill px-3 py-1">
                                            <i class="ri-time-line me-1"></i> {{ __('lockers_trans.pending') }}
                                        </span>
                                    @elseif ($request->status === 'approved')
                                        <span class="badge bg-success bg-opacity-15  rounded-pill px-3 py-1">
                                            <i class="ri-check-line me-1"></i> {{ __('lockers_trans.approved') }}
                                        </span>
                                    @elseif ($request->status === 'rejected')
                                        <span class="badge bg-danger bg-opacity-15 rounded-pill px-3 py-1">
                                            <i class="ri-close-line me-1"></i> {{ __('lockers_trans.rejected') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex gap-2">
                                        @if ($request->status === 'pending')
                                            <button wire:click="approve({{ $request->id }})" 
                                                    class="btn btn-sm btn-success rounded-pill px-3 d-flex align-items-center"
                                                    data-bs-toggle="tooltip" title="{{ __('lockers_trans.approve') }}">
                                                <i class="ri-check-line me-1"></i>
                                                <span class="d-none d-md-inline">{{ __('lockers_trans.approve') }}</span>
                                            </button>
                                            <button wire:click="reject({{ $request->id }})" 
                                                    class="btn btn-sm btn-danger rounded-pill px-3 d-flex align-items-center"
                                                    data-bs-toggle="tooltip" title="{{ __('lockers_trans.reject') }}">
                                                <i class="ri-close-line me-1"></i>
                                                <span class="d-none d-md-inline">{{ __('lockers_trans.reject') }}</span>
                                            </button>
                                        @else
                                            <span class="text-muted small d-flex align-items-center">
                                                <i class="ri-information-line me-1"></i> {{ __('lockers_trans.no_actions') }}
                                            </span>
                                        @endif
                                        <a href="{{ route('finance.invoicedetails', $request->invoice_id) }}" 
                                           class="btn btn-sm btn-info rounded-pill px-3 d-flex align-items-center"
                                           data-bs-toggle="tooltip" title="{{ __('lockers_trans.view_details') }}">
                                            <i class="ri-eye-line me-1"></i>
                                            <span class="d-none d-md-inline">{{ __('lockers_trans.view_details') }}</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="avatar avatar-xl bg-light text-muted rounded-circle mb-3">
                                        <i class="ri-discount-percent-line fs-3"></i>
                                    </div>
                                    <h5 class="text-muted">{{ __('lockers_trans.no_discount_requests_found') }}</h5>
                                    <p class="text-muted small">{{ __('lockers_trans.try_adjusting_search') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($requests->hasPages())
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    {{ __('lockers_trans.showing') }} {{ $requests->firstItem() }} - {{ $requests->lastItem() }} {{ __('lockers_trans.of') }} {{ $requests->total() }}
                </div>
                <div>
                    {{ $requests->links('pagination::bootstrap-5') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>