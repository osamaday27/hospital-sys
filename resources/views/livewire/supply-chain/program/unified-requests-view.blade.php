<div class="card border-0 shadow-lg overflow-hidden">
    {{-- Gradient Header with Advanced Controls --}}
    <div class="card-header bg-gradient-primary text-white p-3" style="background-color: #1373c3 !important;">
        <div class="d-flex flex-column">
            <!-- Title and Description Row -->
            <div class="d-flex align-items-center mb-3">
                <div class="icon-wrapper bg-white bg-opacity-20 p-2 rounded me-3" style="color: #333;">
                    <i class="ri-database-2-line fs-4"></i>
                </div>
                <div>
                    <h2 class="h5 mb-1 fw-semibold" style="color: #fff;">{{ __('supply_chain_trans.requests_dashboard') }}</h2>
                    <p class="small opacity-85 mb-0">{{ __('supply_chain_trans.unified_requests_management') }}</p>
                </div>
            </div>
            
            <!-- Controls Row -->
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <!-- Tabs -->
                <div class="btn-group btn-group-sm segmented-control shadow-sm flex-grow-1 flex-md-grow-0">
                    <button wire:click="$set('tab', 'open')"  style="color: bisque;"
                            class="btn btn-outline-secondary waves-effect waves-light shadow-none {{ $tab === 'open' ? 'active' : '' }}">
                        <i class="ri-inbox-line me-1"></i>
                        {{ __('supply_chain_trans.open_requests') }}
                        @if($openCount > 0)
                            <span class="badge bg-primary ms-1" style="background-color: #008608 !important;font-size: inherit;">{{ $openCount }}</span>
                        @endif
                    </button>
                    <button wire:click="$set('tab', 'closed')"  style="color: bisque;"
                            class="btn btn-outline-secondary waves-effect waves-light shadow-none {{ $tab === 'closed' ? 'active' : '' }}">
                        <i class="ri-archive-line me-1"></i>
                        {{ __('supply_chain_trans.closed_requests') }}
                    </button>
                </div>
                
                <!-- Search Box -->
                <div class="flex-grow-1" style="max-width: 300px;">
                    <div class="input-group input-group-sm shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0" 
                               wire:model.debounce.500ms="search"
                               placeholder="{{ __('supply_chain_trans.search_requests') }}">
                        @if($search)
                            <button class="btn btn-outline-secondary" 
                                    wire:click="$set('search', '')"
                                    type="button">
                                <i class="ri-close-line"></i>
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Filter Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle shadow-sm" type="button" 
                            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-filter-2-line me-1"></i>
                        {{ __('supply_chain_trans.filters') }}
                        @if($hasActiveFilters)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Active filters</span>
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 280px;">
                        <h6 class="dropdown-header mb-2">
                            <i class="ri-filter-line me-1"></i>
                            {{ __('supply_chain_trans.filter_options') }}
                        </h6>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">{{ __('supply_chain_trans.date_range') }}</label>
                            <div class="input-group input-group-sm">
                                <input type="date" wire:model="from_date" class="form-control">
                                <span class="input-group-text">to</span>
                                <input type="date" wire:model="to_date" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">{{ __('supply_chain_trans.request_type') }}</label>
                            <select wire:model="filter_type" class="form-select form-select-sm">
                                <option value="">{{ __('supply_chain_trans.all_types') }}</option>
                                <option value="client">{{ __('supply_chain_trans.client') }}</option>
                                <option value="employee">{{ __('supply_chain_trans.employee') }}</option>
                                <option value="department">{{ __('supply_chain_trans.department') }}</option>
                                <option value="store">{{ __('supply_chain_trans.replenishment') }}</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button wire:click="resetFilters" 
                                    class="btn btn-sm btn-outline-secondary">
                                {{ __('supply_chain_trans.reset') }}
                            </button>
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-dismiss="dropdown">
                                {{ __('supply_chain_trans.apply') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      

    {{-- Enhanced Table with Status Visualization --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="180" class="ps-4">{{ __('supply_chain_trans.request_number') }}</th>
                        <th width="120">{{ __('supply_chain_trans.date') }}</th>
                        <th width="150">{{ __('supply_chain_trans.type') }}</th>
                        <th>{{ __('supply_chain_trans.entity') }}</th>
                        <th width="150">{{ __('supply_chain_trans.store') }}</th>
                        <th width="140">{{ __('supply_chain_trans.status') }}</th>
                        <th width="160" class="text-end pe-4">{{ __('supply_chain_trans.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr class="border-bottom border-light hover-highlight">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="request-icon me-2">
                                        @if($request['type'] === 'client')
                                            <i class="ri-user-line text-primary"></i>
                                        @elseif($request['type'] === 'employee')
                                            <i class="ri-user-3-line text-info"></i>
                                        @elseif($request['type'] === 'department')
                                            <i class="ri-building-line text-warning"></i>
                                        @else
                                            <i class="ri-store-2-line text-success"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-primary">{{ $request['number'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small text-muted">{{ $request['date'] }}</div>
                            </td>
                            <td>
                                <span class="badge bg-opacity-10 
                                    @if($request['key'] === 'client') bg-primary text-primary
                                    @elseif($request['key'] === 'employee') bg-info text-info
                                    @elseif($request['key'] === 'department') bg-warning text-warning
                                    @else bg-success text-success @endif">
                                    {{ $request['type'] }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs me-2 flex-shrink-0" style="background-color: #1373c3 !important;">
                                        <span class="avatar-title rounded-circle 
                                            @if($request['key'] === 'client') bg-soft-primary
                                            @elseif($request['key'] === 'employee') bg-soft-info
                                            @elseif($request['key'] === 'department') bg-soft-warning
                                            @else bg-soft-success @endif">
                                            {{ App\Helpers\TextHelper::getFirstChar($request['name']) }}
                                        </span>
                                    </div>
                                    <div class="text-truncate" style="max-width: 200px;" 
                                         data-bs-toggle="tooltip" title="{{ $request['name'] }}">
                                        {{ $request['name'] }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 150px;" 
                                     data-bs-toggle="tooltip" title="{{ $request['store'] }}">
                                    {{ $request['store'] }}
                                </div>
                            </td>
                            <td>
                                @if($request['status'] === 'new')
                                    <div class="status-badge status-new">
                                        <div class="pulse-dot"></div>
                                        <span>{{ __('supply_chain_trans.status_new') }}</span>
                                    </div>
                                @elseif($request['status'] === 'partial')
                                    <div class="status-badge status-partial">
                                        <span>{{ __('supply_chain_trans.status_partial') }}</span>
                                    </div>
                                @elseif($request['status'] === 'processed')
                                    <div class="status-badge status-processed">
                                        <span>{{ __('supply_chain_trans.status_processed') }}</span>
                                    <i class="ri-check-line ms-1"></i>
                                    </div>
                                @elseif($request['status'] === 'closed')
                                    <div class="status-badge status-closed">
                                        <span>{{ __('supply_chain_trans.status_closed') }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($request['status'] === 'new' || $request['status'] === 'partial')
                                    <?php
                                        $processRoute = '#';
                                        if ($request['key'] === 'client') {
                                            $processRoute = route('client_request.process', ['id' => $request['id']]);
                                        } elseif ($request['key'] === 'employee') {
                                            $processRoute = route('employee_request.process', ['id' => $request['id']]);
                                        } elseif ($request['key'] === 'department') {
                                            $processRoute = route('department_request.process', ['id' => $request['id']]);
                                        } elseif ($request['key'] === 'store') {
                                            $processRoute = route('store_request.process', ['id' => $request['id']]);
                                        }
                                  ?>
                                
                                    <a href="{{ $processRoute }}"
                                       class="btn btn-sm btn-icon btn-primary rounded-circle"
                                       data-bs-toggle="tooltip" 
                                       title="{{ __('supply_chain_trans.process_request') }}">
                                        <i class="ri-play-line"></i>
                                    </a>
                                @endif
                                
                                

                                    <a href="{{route('view_request', ['id' => $request['id']])}}" type="button" 
                                            class="btn btn-sm btn-icon btn-outline-secondary rounded-circle"
                                            wire:click="viewDetails({{ $request['id'] }}, '{{ $request['type'] }}')"
                                            data-bs-toggle="tooltip" 
                                            title="{{ __('supply_chain_trans.view_details') }}">
                                        <i class="ri-eye-line"></i>
                                    </a>

                                    <a href="{{ route('print_client_request', ['id' => $request['id'], 'type' => $request['key']]) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-icon btn-outline-secondary rounded-circle"
                                        data-bs-toggle="tooltip"
                                        title="{{ __('supply_chain_trans.print_request') }}">
                                         <i class="ri-printer-line"></i>
                                     </a>
                                     

                                    @if($request['status'] !== 'processed')
                                        <button type="button" 
                                                class="btn btn-sm btn-icon btn-outline-danger rounded-circle"
                                                wire:click="closeRequest({{ $request['id'] }}, '{{ $request['type'] }}')"
                                                data-bs-toggle="tooltip" 
                                                title="{{ __('supply_chain_trans.close_request') }}">
                                            <i class="ri-lock-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="ri-database-2-line"></i>
                                    </div>
                                    <h5>{{ __('supply_chain_trans.no_requests_found') }}</h5>
                                    <p class="text-muted small mb-4">{{ __('supply_chain_trans.try_changing_filters') }}</p>
                                    <button wire:click="resetFilters" 
                                            class="btn btn-sm btn-primary">
                                        {{ __('supply_chain_trans.reset_filters') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Enhanced Pagination Footer --}}
    @if($requests->hasPages())
    <div class="card-footer bg-transparent">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="small text-muted mb-2 mb-md-0">
                {{ __('supply_chain_trans.showing') }} 
                <span class="fw-semibold">{{ $requests->firstItem() }}</span> 
                {{ __('supply_chain_trans.to') }} 
                <span class="fw-semibold">{{ $requests->lastItem() }}</span> 
                {{ __('supply_chain_trans.of') }} 
                <span class="fw-semibold">{{ $requests->total() }}</span> 
                {{ __('supply_chain_trans.entries') }}
            </div>
            <div>
                {{ $requests->links('components.pagination-livewire') }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.document-container {
    font-family: 'Tajawal', sans-serif;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    direction: rtl;
}

.document-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.document-title {
    color: #2c3e50;
    font-size: 24px;
    margin: 0;
}

.document-actions .btn {
    margin-right: 10px;
    padding: 8px 16px;
    font-size: 14px;
}

.document-filters {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: 600;
    color: #555;
    white-space: nowrap;
}

.form-select, .form-control {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
}

.date-range {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-input {
    position: relative;
}

.search-input input {
    padding-right: 35px;
    width: 200px;
}

.search-input i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #777;
}

.document-table-container {
    overflow-x: auto;
}

.document-table {
    width: 100%;
    border-collapse: collapse;
}

.document-table th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 12px 15px;
    text-align: right;
    border-bottom: 2px solid #dee2e6;
}

.document-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    color: #555;
}

.document-table tr:hover {
    background-color: #f8f9fa;
}

.btn-icon {
    background: transparent;
    border: none;
    color: #6c757d;
    font-size: 16px;
    padding: 5px;
    cursor: pointer;
    transition: color 0.2s;
}

.btn-icon:hover {
    color: #0d6efd;
}

.document-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    margin-top: 20px;
}

.pagination-btn {
    border: 1px solid #ddd;
    background: #fff;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.pagination-btn:hover {
    background: #f8f9fa;
}

.pagination-btn.active {
    background: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .document-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .document-filters {
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-group {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .search-input input {
        width: 100%;
    }
}
</style>
<script>
    document.addEventListener('livewire:load', function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Initialize floating labels
        const floatingInputs = document.querySelectorAll('.form-floating input');
        floatingInputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentNode.classList.add('focused');
            });
            input.addEventListener('blur', () => {
                if (!input.value) {
                    input.parentNode.classList.remove('focused');
                }
            });
            // Initialize state
            if (input.value) {
                input.parentNode.classList.add('focused');
            }
        });
    });

    Livewire.hook('message.processed', () => {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
