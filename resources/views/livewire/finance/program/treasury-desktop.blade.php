{{-- File: resources/views/livewire/finance/program/treasury-desktop.blade.php --}}
<div class="card shadow-lg border-0" style="border-radius: 16px; overflow: hidden; background-color: #f8fafc;" dir="rtl">
    <!-- Card Header with Glass Morphism Effect -->
    <div class="card-header text-white d-flex justify-content-between align-items-center py-3" style="
        background: linear-gradient(135deg, rgba(58, 123, 213, 0.9) 0%, rgb(19, 115, 195) 100%);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    ">
        <div class="d-flex align-items-center">
            <div class="bg-white bg-opacity-20 p-2 rounded-circle me-3">
                <i class="ri-bar-chart-fill fs-4" style="color: #299cdb;"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-semibold" style="color: #fff;">{{ __('lockers_trans.treasury_dashboard') }}</h5>
                <small class="opacity-80">{{ __('lockers_trans.Real_time_financial_overview') }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Date Filter -->
            <div class="input-group input-group-sm" style="width: 180px;">
                <span class="input-group-text bg-white bg-opacity-20 border-0"><i class="ri-calendar-2-line"></i></span>
                <input type="date" wire:model="selected_date" class="form-control bg-white bg-opacity-20 border-0" style="backdrop-filter: blur(5px);">
            </div>
            <!-- Status Filter -->
            <div class="input-group input-group-sm" style="width: 200px;">
                <span class="input-group-text bg-white bg-opacity-20 border-0"><i class="ri-filter-3-line"></i></span>
                <select wire:model="statusFilter" class="form-select bg-white bg-opacity-20 border-0" style="backdrop-filter: blur(5px);">
                    <option value="">{{ __('lockers_trans.all_statuses') }}</option>
                    <option value="paid">{{ __('lockers_trans.status_paid') }}</option>
                    <option value="unpaid">{{ __('lockers_trans.status_unpaid') }}</option>
                    <option value="pending">{{ __('lockers_trans.status_pending') }}</option>
                    <option value="partial">{{ __('lockers_trans.partial') }}</option>
                    <option value="canceled">{{ __('lockers_trans.status_canceled') }}</option>
                    <option value="pending_discount">{{ __('lockers_trans.status_pending_discount') }}</option>
                    <option value="pending_cancellation">{{ __('lockers_trans.status_pending_cancellation') }}</option>
                </select>
            </div>
            <!-- Search -->
            <div class="input-group input-group-sm" style="width: 240px;">
                <span class="input-group-text bg-white bg-opacity-20 border-0"><i class="ri-search-line"></i></span>
                <input type="text" wire:model.debounce.500ms="search" class="form-control bg-white bg-opacity-20 border-0" placeholder="{{ __('lockers_trans.search_placeholder') }}" style="backdrop-filter: blur(5px);">
            </div>
            <!-- Refresh Button -->
            <button wire:click="refreshData" class="btn btn-sm btn-light bg-white bg-opacity-20 border-0" style="backdrop-filter: blur(5px);" title="{{ __('lockers_trans.refresh') }}">
                <i class="ri-refresh-line"></i>
            </button>
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body p-4">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm">
                <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm">
                <i class="mdi mdi-alert-circle-outline me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
            <!-- Treasury Card -->
            <div class="col">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6a11cb;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">{{ __('lockers_trans.treasury') }}</h6>
                                <h2 class="mb-0 fw-bold">{{ $treasury ?? '-' }}</h2>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-light rounded-circle text-primary fs-4">
                                    <i class="ri-safe-2-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light text-success fs-11">
                                <i class="ri-arrow-up-line align-middle"></i>{{ __('lockers_trans.Current_session') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Paid Card -->
            <div class="col">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">{{ __('lockers_trans.total_paid') }}</h6>
                                <h2 class="mb-0 fw-bold text-success">{{ number_format($total_paid,2) }} <small class="fs-6">{{ $currency }}</small></h2>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success bg-opacity-10 rounded-circle text-success fs-4">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light text-muted fs-11">
                                <i class="ri-arrow-up-line text-success align-middle"></i> {{ __('lockers_trans.Today_s_collections') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Residual Card -->
            <div class="col">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #fd7e14;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">{{ __('lockers_trans.total_residual') }}</h6>
                                <h2 class="mb-0 fw-bold text-warning">{{ number_format($total_residual,2) }} <small class="fs-6">{{ $currency }}</small></h2>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning bg-opacity-10 rounded-circle text-warning fs-4">
                                    <i class="ri-time-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light text-muted fs-11">
                                <i class="ri-information-line text-warning align-middle"></i>{{ __('lockers_trans.Pending_payments') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Discount Card -->
            <div class="col">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #17a2b8;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">{{ __('lockers_trans.total_discount') }}</h6>
                                <h2 class="mb-0 fw-bold text-info">{{ number_format($total_discount,2) }} <small class="fs-6">{{ $currency }}</small></h2>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info bg-opacity-10 rounded-circle text-info fs-4">
                                    <i class="ri-coupon-2-line"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-light text-muted fs-11">
                                <i class="ri-arrow-down-line text-danger align-middle"></i>{{ __('lockers_trans.Discountsapplied') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Metrics - Horizontal Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-primary bg-opacity-10 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger bg-opacity-10 rounded-circle fs-4" style="background-color: #1e78c7!important; margin:-8px;">
                                    <i class="ri-file-list-2-line text-primary fs-4" style="color:#fff!important;"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">{{ __('lockers_trans.invoice_count') }}</h6>
                                <h4 class="mb-0 text-primary">{{ $total_invoices }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-danger bg-opacity-10 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger bg-opacity-10 rounded-circle fs-4" style="background-color:#f05f5f!important;margin:-8px;">
                                    <i class="ri-close-circle-line text-danger" style="color:#fff!important;"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">{{ __('lockers_trans.cancelled_receipts') }}</h6>
                                <h4 class="mb-0 text-danger">{{ $cancelled_receipts_count }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-purple bg-opacity-10 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar-sm bg-purple bg-opacity-20 rounded-circle p-2">
                                    <i class="ri-percent-line text-purple fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">{{ __('lockers_trans.pending_discounts') }}</h6>
                                <h4 class="mb-0 text-purple">{{ $pending_discount_requests }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-orange bg-opacity-10 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <span class="avatar-sm bg-orange bg-opacity-20 rounded-circle p-2">
                                    <i class="ri-forbid-line text-orange fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-muted">{{ __('lockers_trans.pending_cancellations') }}</h6>
                                <h4 class="mb-0 text-orange">{{ $pending_cancellation_requests }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table with Advanced UI -->
        <div class="card border-0 shadow-sm mt-2">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center mb-2 mb-sm-0">
                        <i class="ri-file-list-3-line fs-5 text-primary me-2"></i>
                        <h6 class="mb-0 fw-semibold">{{ __('lockers_trans.daily_invoices') }}</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                            {{ $dailyInvoices->total() }} {{ __('lockers_trans.records') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>{{ __('lockers_trans.actions') }}</th>
                                <th>{{ __('lockers_trans.status') }}</th>
                                <th>{{ __('lockers_trans.patient') }}</th>
                                <th>{{ __('lockers_trans.contract') }}</th>
                                <th class="text-end">{{ __('lockers_trans.patient_contribution') }}</th>
                                <th class="text-end">{{ __('lockers_trans.contract_contribution') }}</th>
                                <th class="text-end">{{ __('lockers_trans.total') }}</th>
                                <th class="text-end">{{ __('lockers_trans.paid') }}</th>
                                <th class="text-end">{{ __('lockers_trans.residual') }}</th>
                                <th>{{ __('lockers_trans.date') }}</th>
                                <th class="pe-4">{{ __('lockers_trans.created_by') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dailyInvoices as $invoice)
                                <tr>
                                    <td class="ps-4 fw-semibold">
                                        <a href="#" class="text-info">#{{ $invoice->number }}</a>
                                    </td>
                                    <td>
                                        @php
                                            // إظهار زر الدفع حتى لو الرصيد = 0 بشرط عدم وجود إيصالات سابقة
                                            // وإخفاؤه فقط لو الفاتورة ملغاة أو تحت طلب إلغاء
                                            $notBlockedStatuses = !in_array($invoice->status, ['canceled', 'pending_cancellation']);
                                            $noReceiptsYet      = (int)($invoice->receipts_count ?? 0) === 0;
                                            $hasResidual        = (float)$invoice->residual > 0;
                                            $canPay             = $notBlockedStatuses && ($hasResidual || $noReceiptsYet ||$invoice->status=="unpaid");
                                        @endphp

                                        <div class="d-flex gap-1">
                                            @if($canPay)
                                                <a href="{{ route('finance.invoicepayment', $invoice->id) }}"
                                                   class="btn btn-sm btn-light rounded-circle" title="{{ __('lockers_trans.pay') }}" data-bs-toggle="tooltip">
                                                    <i class="ri-wallet-3-line text-success"></i>
                                                </a>
                                            @endif

                                            <a href="{{ route('finance.discountrequest',$invoice->id) }}"
                                               class="btn btn-sm btn-light rounded-circle" title="{{ __('lockers_trans.discount_request') }}" data-bs-toggle="tooltip">
                                                <i class="ri-percent-line text-warning"></i>
                                            </a>

                                            <a href="{{ route('finance.cancellation-request',$invoice->id) }}"
                                               class="btn btn-sm btn-light rounded-circle" title="{{ __('lockers_trans.cancel_invoice') }}" data-bs-toggle="tooltip">
                                                <i class="ri-close-circle-line text-danger"></i>
                                            </a>

                                            <a href="{{ route('finance.invoicedetails',$invoice->id) }}"
                                               class="btn btn-sm btn-light rounded-circle" title="{{ __('lockers_trans.details') }}" data-bs-toggle="tooltip">
                                                <i class="ri-eye-line text-info"></i>
                                            </a>
                                        </div>
                                    </td>

                                    <td>
                                        @switch($invoice->status)
                                            @case('paid')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                                    <i class="ri-checkbox-circle-line me-1"></i> {{ __('lockers_trans.status_paid') }}
                                                </span>
                                                @break
                                            @case('unpaid')
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                                    <i class="ri-time-line me-1"></i> {{ __('lockers_trans.status_unpaid') }}
                                                </span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">
                                                    <i class="ri-time-line me-1"></i> {{ __('lockers_trans.status_pending') }}
                                                </span>
                                                @break
                                            @case('canceled')
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">
                                                    <i class="ri-close-circle-line me-1"></i> {{ __('lockers_trans.status_canceled') }}
                                                </span>
                                                @break
                                            @case('pending_discount')
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill">
                                                    <i class="ri-percent-line me-1"></i> {{ __('lockers_trans.status_pending_discount') }}
                                                </span>
                                                @break
                                            @case('pending_cancellation')
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">
                                                    <i class="ri-error-warning-line me-1"></i> {{ __('lockers_trans.status_pending_cancellation') }}
                                                </span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-warning bg-opacity-10 text-danger rounded-pill">
                                                    <i class="ri-error-warning-line me-1"></i> {{ __('lockers_trans.partial') }}
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark rounded-pill">
                                                    <i class="ri-question-line me-1"></i> {{ __('lockers_trans.status_unknown') }}
                                                </span>
                                        @endswitch
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title bg-light rounded-circle" style="background-color:#1e78c7!important;">
                                                    {{ App\Helpers\TextHelper::getFirstChar($invoice->patient->full_name ?? '-') }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $invoice->patient->full_name ?? '-' }}</h6>
                                                <small class="text-muted">#{{ $invoice->patient->id ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width:160px;" title="{{ $this->getcontractname($invoice->reservations->contract ?? null) }}" data-bs-toggle="tooltip">
                                            {{ $this->getcontractname($invoice->reservations->contract ?? null) }}
                                        </span>
                                    </td>

                                    <td class="text-end fw-semibold">{{ number_format($invoice->patient_contribution,2) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($invoice->contract_contribution,2) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($invoice->total,2) }}</td>
                                    <td class="text-end text-success">{{ number_format($invoice->paid,2) }}</td>
                                    <td class="text-end text-warning">{{ number_format($invoice->residual,2) }}</td>

                                    {{-- التاريخ بصيغة يوم/شهر/سنة --}}
                                    <td><small class="text-muted">{{ $invoice->created_at?->format('d M Y') }}</small></td>

                                    <td class="pe-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title bg-light rounded-circle" style="background-color:#1e78c7!important;">
                                                    {{ substr($invoice->user->name ?? 'U',0,1) }}
                                                </span>
                                            </div>
                                            <span>{{ $invoice->user->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="ri-search-line fs-3 text-muted"></i>
                                            <h5 class="mt-2">{{ __('lockers_trans.no_invoices_found') }}</h5>
                                            <p class="text-muted mb-0">{{ __('lockers_trans.try_adjusting_filters') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted mb-2 mb-sm-0">
                        {{ __('lockers_trans.showing') }}
                        <span class="fw-semibold">{{ $dailyInvoices->firstItem() }}</span>
                        {{ __('lockers_trans.to') }}
                        <span class="fw-semibold">{{ $dailyInvoices->lastItem() }}</span>
                        {{ __('lockers_trans.of') }}
                        <span class="fw-semibold">{{ $dailyInvoices->total() }}</span>
                        {{ __('lockers_trans.entries') }}
                    </div>
                    <div>
                        {{ $dailyInvoices->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tooltips init (Bootstrap) --}}
<script>
    document.addEventListener('livewire:navigated', initTooltips);
    document.addEventListener('DOMContentLoaded', initTooltips);
    function initTooltips() {
        if (window.bootstrap) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });
        }
    }
</script>
