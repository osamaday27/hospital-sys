<div class="card shadow-sm border-0 rounded-lg overflow-hidden">
    <!-- Header Section -->
    <div class="card-header bg-gradient-primary text-white p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <i class="ri-file-list-line fs-4 me-3"></i>
                <h4 class="mb-0 fw-semibold">{{ __('lockers_trans.manage_cancellation_requests') }}</h4>
            </div>
            <div class="d-flex align-items-center mt-3 mt-md-0">
                <input type="text" wire:model.debounce.500ms="search" class="form-control form-control-sm me-2"
                       placeholder="{{ __('lockers_trans.search_by_invoice_or_patient') }}" style="width: 220px;">
            </div>
            
            <div class="d-flex align-items-center">
                <label class="me-2 text-white-50 mb-0">{{ __('lockers_trans.filter_by_status') }}</label>
                <select wire:model.live="filter_status" class="form-select form-select-sm bg-white bg-opacity-10 border-0 " style="width: 120px;">
                    <option value="pending">{{ __('lockers_trans.pending') }}</option>
                    <option value="approved">{{ __('lockers_trans.approved') }}</option>
                    <option value="rejected">{{ __('lockers_trans.rejected') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Body Section -->
    <div class="card-body p-0">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Loading State -->
        @if($loading)
            <div class="d-flex justify-content-center align-items-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @else
            <!-- Requests List -->
            <div class="list-group list-group-flush">
                @forelse ($requests as $req)
                    <div class="list-group-item p-4 hover-bg">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center">
                            <!-- Request Info -->
                            <div class="mb-3 mb-lg-0 flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-{{ $req->status == 'approved' ? 'success' : ($req->status == 'rejected' ? 'danger' : 'warning') }} me-2">
                                        {{ __('lockers_trans.' . $req->status) }}
                                    </span>
                                    <h6 class="mb-0 text-primary">{{ __('lockers_trans.invoice_number') }}: {{ $req->invoice->number }}</h6>
                                </div>
                                
                                <div class="d-flex flex-wrap text-muted">
                                    <div class="me-3 mb-1">
                                        <i class="ri-user-line me-1"></i>
                                        <span>{{ __('lockers_trans.patient_name') }}: {{ $req->invoice->patient->full_name ?? '-' }}</span>
                                    </div>
                                    <div class="me-3 mb-1">
                                        <i class="ri-calendar-line me-1"></i>
                                        <span>{{ $req->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    <div class="d-flex mb-1">
                                        <i class="ri-alert-line me-2 text-warning"></i>
                                        <strong>{{ __('lockers_trans.reason') }}:</strong>
                                        <span class="ms-1">{{ $req->reason->name_ar ?? '-' }}</span>
                                    </div>
                                    
                                    @if($req->cancellation_notes)
                                    <div class="d-flex">
                                        <i class="ri-chat-4-line me-2 text-info"></i>
                                        <strong>{{ __('lockers_trans.notes') }}:</strong>
                                        <span class="ms-1">{{ $req->cancellation_notes }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            @if ($filter_status == 'pending')
                                <div class="d-flex flex-column flex-sm-row">
                                    <button wire:click="approve({{ $req->id }})" 
                                            wire:loading.attr="disabled"
                                            class="btn btn-success btn-sm mb-2 mb-sm-0 me-sm-2">
                                        <i class="ri-check-line me-1"></i>
                                        <span wire:loading.remove>{{ __('lockers_trans.approve') }}</span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </span>
                                    </button>
                                    <button wire:click="reject({{ $req->id }})" 
                                            wire:loading.attr="disabled"
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="ri-close-line me-1"></i>
                                        <span wire:loading.remove>{{ __('lockers_trans.reject') }}</span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </span>
                                    </button>
                                </div>
                            @else
                                <div class="text-muted small">
                                    {{ __('lockers_trans.processed_on') }} {{ $req->updated_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="ri-inbox-line fs-1 text-muted"></i>
                        </div>
                        <h5 class="text-muted">{{ __('lockers_trans.no_requests_found') }}</h5>
                        <p class="text-muted small">{{ __('lockers_trans.change_filter_or_check_later') }}</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    @if($requests->hasPages())
        <div class="card-footer bg-transparent border-top">
            {{ $requests->links() }}
        </div>
    @endif
</div>

<style>
    .hover-bg:hover {
        background-color: rgba(0, 123, 255, 0.03);
        transition: background-color 0.2s ease;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
    }
</style>