<div class="container-fluid py-4">
    @section('title') {{ trans('pricing_list.title') }} @stop

    <script>
        window.addEventListener('show-success', event => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: event.detail.message,
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>


    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card card-hover border-0 shadow-sm rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded me-3">
                            <i class="ri-newspaper-fill fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ trans('pricing_list.title') }}</h6>
                            <h4 class="mb-0">{{ $price_list->getTranslation('name', app()->getLocale()) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-hover border-0 shadow-sm rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-success p-3 rounded me-3">
                            <i class="ri-money-dollar-circle-line fs-2 text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ trans('pricing_list.currency') }}</h6>
                            <h4 class="mb-0">{{ $price_list->getcurrency->name ?? '-' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-hover border-0 shadow-sm rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-info p-3 rounded me-3">
                            <i class="ri-calendar-todo-line fs-2 text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">{{ trans('pricing_list.fiscal_year') }}</h6>
                            <h4 class="mb-0">{{ $price_list->fiscal_year }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card card-hover border-0 shadow-sm rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning p-3 rounded me-3">
                            <i class="ri-timer-2-line fs-2 text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">{{ trans('pricing_list.start_date') }}</h6>
                            <h4 class="mb-0">{{ $price_list->start_date }}</h4>
                        </div>
                        <div class="badge bg-soft-warning text-warning">
                            <i class="ri-arrow-right-line me-1"></i> Active
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-hover border-0 shadow-sm rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-danger p-3 rounded me-3">
                            <i class="ri-timer-2-fill fs-2 text-danger"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">{{ trans('pricing_list.end_date') }}</h6>
                            <h4 class="mb-0">{{ $price_list->end_date }}</h4>
                        </div>
                        <div class="badge bg-soft-secondary text-secondary">
                            <i class="ri-calendar-line me-1"></i>
                            {{ \Carbon\Carbon::parse($price_list->end_date)->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="row g-0 text-center">
                <div class="col-md-4 border-end py-4">
                    <h2 class=" mb-1" style="color: #1977c5 !important;">{{ $counts['total'] }}</h2>
                    <p class="text-muted mb-0">{{ trans('pricing_list.total_services') }}</p>
                </div>
                <div class="col-md-4 border-end py-4">
                    <h2 class="text-success mb-1">{{ $counts['priced'] }}</h2>
                    <p class="text-muted mb-0">{{ trans('pricing_list.priced_services') }}</p>
                </div>
                <div class="col-md-4 py-4">
                    <h2 class="text-warning mb-1">{{ $counts['remaining'] }}</h2>
                    <p class="text-muted mb-0">{{ trans('pricing_list.remaining_services') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" wire:model.debounce.500ms="search"
                            placeholder="{{ trans('datatables.search') }}">
                    </div>
                </div>

                <!-- Filter by Sublevel -->
                <div class="col-md-3">
                    <select wire:model="filter_sublevel_id" class="form-select">
                        <option value="">{{ trans('pricing_list.filter_by_sublevel') }}</option>
                        @foreach ($sublevels as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter by Pricing Status -->
                <div class="col-md-3">
                    <select wire:model="filter_priced" class="form-select">
                        <option value="">{{ trans('pricing_list.filter_by_price_status') }}</option>
                        <option value="priced">{{ trans('pricing_list.only_priced') }}</option>
                        <option value="unpriced">{{ trans('pricing_list.only_unpriced') }}</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="col-md-2">
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10 {{ trans('datatables.entries') }}</option>
                        <option value="25">25 {{ trans('datatables.entries') }}</option>
                        <option value="50">50 {{ trans('datatables.entries') }}</option>
                        <option value="100">100 {{ trans('datatables.entries') }}</option>
                        <option value="500">500 {{ trans('datatables.entries') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    <!-- Services Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>{{ trans('pricing_list.hospital_code') }}</th>
                            <th>{{ trans('pricing_list.service_name') }}</th>
                            <th>{{ trans('pricing_list.sublevel') }}</th>
                            <th class="text-end">{{ trans('pricing_list.hospital_price') }}</th>
                            <th class="text-end">{{ trans('pricing_list.special_price') }}</th>
                            <th>{{ trans('pricing_list.status') }}</th>
                            <th class="pe-4">{{ trans('pricing_list.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            @php
                                $pricing = $service->pricelistservices->first(); // السعر الخاص بهذه اللائحة فقط
                            @endphp
                            <tr>
                                <td class="ps-4">{{ $service->id }}</td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary">
                                        {{ $service->hospital_code ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ri-service-line me-2 text-info"></i>
                                        {{ $service->getTranslation('name', app()->getLocale()) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-secondary" style="color: #000;">
                                        {{ $service->sublevel->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent">
                                            <i class="ri-hospital-line text-danger"></i>
                                        </span>
                                        <input type="number" class="form-control text-end border-start-0"
                                            wire:change="$emit('storeOrUpdatePrice', {{ $service->id }}, 'hospital_price', $event.target.value)"
                                            value="{{ $pricing->hospital_price ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-transparent">
                                            <i class="ri-star-line text-warning"></i>
                                        </span>
                                        <input type="number" class="form-control text-end border-start-0"
                                            wire:change="$emit('storeOrUpdatePrice', {{ $service->id }}, 'special_price', $event.target.value)"
                                            value="{{ $pricing->special_price ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm"
                                        wire:change="$emit('storeOrUpdatePrice', {{ $service->id }}, 'status', $event.target.value)">
                                        <option value="active"
                                            {{ ($pricing->status ?? '') == 'active' ? 'selected' : '' }}>
                                            {{ trans('status.active') }}
                                        </option>
                                        <option value="inactive"
                                            {{ ($pricing->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                            {{ trans('status.inactive') }}
                                        </option>
                                    </select>
                                </td>
                                <td class="pe-4">
                                    <small class="text-muted">
                                        <i class="ri-calendar-line me-1"></i>
                                        {{ $pricing?->created_at?->format('d M Y') ?? '-' }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach




                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($services->hasPages())
                <div class="card-footer border-top bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            {{ trans('datatables.showing') }} {{ $services->firstItem() }} -
                            {{ $services->lastItem() }}
                            {{ trans('datatables.of') }} {{ $services->total() }}
                        </div>
                        <div>
                            {{ $services->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal (placeholder) -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-filter-line me-2"></i> Filter Services</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Filter options would go here</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Apply Filters</button>
            </div>
        </div>
    </div>
</div>
