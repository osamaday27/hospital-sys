<div class="container-fluid py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="ri-file-text-line fs-4 me-3"></i>
                <div>
                    <h5 class="mb-0 fw-bold">{{ __('lockers_trans.invoice_details') }}</h5>
                    <small class="opacity-80">{{ __('lockers_trans.invoice_number') }}: {{ $invoice->number }}</small>
                </div>
            </div>
            <div class="text-end">
                <div class="badge bg-white text-dark fs-6 fw-normal px-3 py-2 rounded-3">
                    <i class="ri-calendar-line me-2"></i>{{ now()->format('Y-m-d H:i') }}
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Invoice Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-start border-3 border-primary h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">{{ __('lockers_trans.total') }}</h6>
                            <h4 class="fw-bold">{{ number_format($invoice->total, 2) }}</h4>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card border-start border-3 border-success h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">{{ __('lockers_trans.paid') }}</h6>
                            <h4 class="fw-bold text-success">{{ number_format($invoice->paid, 2) }}</h4>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card border-start border-3 border-warning h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">{{ __('lockers_trans.residual') }}</h6>
                            <h4 class="fw-bold text-warning">{{ number_format($invoice->residual, 2) }}</h4>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card border-start border-3 border-info h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-2">{{ __('lockers_trans.status') }}</h6>
                            <div class="mt-2">
                                @switch($invoice->status)
                                    @case('paid')
                                        <span class="badge bg-success bg-opacity-20 fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-checkbox-circle-line me-1"></i> {{ __('lockers_trans.status_paid') }}
                                        </span>
                                        @break
                                
                                    @case('unpaid')
                                        <span class="badge bg-secondary bg-opacity-20 fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-time-line me-1"></i> {{ __('lockers_trans.status_unpaid') }}
                                        </span>
                                        @break
                                
                                    @case('pending')
                                        <span class="badge bg-warning bg-opacity-20  fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-time-line me-1"></i> {{ __('lockers_trans.status_pending') }}
                                        </span>
                                        @break
                                
                                    @case('canceled')
                                        <span class="badge bg-danger bg-opacity-20  fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-close-circle-line me-1"></i> {{ __('lockers_trans.status_canceled') }}
                                        </span>
                                        @break
                                
                                    @case('pending_discount')
                                        <span class="badge bg-info bg-opacity-20  fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-percent-line me-1"></i> {{ __('lockers_trans.status_pending_discount') }}
                                        </span>
                                        @break
                                
                                    @case('pending_cancellation')
                                        <span class="badge bg-danger bg-opacity-20  fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-error-warning-line me-1"></i> {{ __('lockers_trans.status_pending_cancellation') }}
                                        </span>
                                        @break
                                        
                                    @case('partial')
                                        <span class="badge bg-warning bg-opacity-20  fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-error-warning-line me-1"></i> {{ __('lockers_trans.partial') }}
                                        </span>
                                        @break   
                                        
                                    @default
                                        <span class="badge bg-light  fs-6 rounded-pill px-3 py-1">
                                            <i class="ri-question-line me-1"></i> {{ __('lockers_trans.status_unknown') }}
                                        </span>
                                @endswitch
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient and Reservation Info -->
            <div class="row g-4 mb-4">
                <!-- Patient Info -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">
                                <i class="ri-user-3-line me-2"></i> {{ __('lockers_trans.patient_info') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-md bg-primary bg-opacity-10  rounded-circle" style="text-align: center;padding-top: 12px;">
                                        <i class="ri-user-3-line fs-4" style="font-size: 32px !important;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0">{{ $invoice->patient->full_name ?? '-' }}</h5>
                                    <small class="text-muted">{{ __('lockers_trans.patient') }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>{{ __('lockers_trans.patient_contribution') }}:</strong></p>
                                    <h5 class="">{{ number_format($invoice->patient_contribution, 2) }}</h5>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>{{ __('lockers_trans.contract_contribution') }}:</strong></p>
                                    <h5 class="text-success">{{ number_format($invoice->contract_contribution, 2) }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservation Info -->
                @if($invoice->reservations)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">
                                <i class="ri-calendar-event-line me-2"></i> {{ __('lockers_trans.reservation_info') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ri-hospital-line  me-2"></i>
                                    <strong>{{ __('lockers_trans.clinic') }}:</strong>
                                    <span class="ms-2">{{ $invoice->reservations->clinic->name_ar ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ri-user-star-line  me-2"></i>
                                    <strong>{{ __('lockers_trans.doctor') }}:</strong>
                                    <span class="ms-2">{{ $invoice->reservations->doctor->full_name ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="ri-service-line  me-2"></i>
                                    <strong>{{ __('lockers_trans.service') }}:</strong>
                                    <span class="ms-2">{{ $invoice->reservations->service->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @if($supplies->count())
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold"><i class="ri-archive-line me-2"></i> {{ __('lockers_trans.visit_supplies') }}</h6>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>#</th>
                        <th>{{ __('lockers_trans.product') }}</th>
                        <th>{{ __('lockers_trans.unit') }}</th>
                        <th class="text-end">{{ __('lockers_trans.quantity') }}</th>
                        <th class="text-end">{{ __('lockers_trans.price') }}</th>
                        <th class="text-end">{{ __('lockers_trans.total') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($supplies as $i => $item)
                      <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ optional($item->product)->name_ar ?? '-' }}</td>
                        <td>{{ optional($item->unit)->name_ar ?? '-' }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->price,2) }}</td>
                        <td class="text-end">{{ number_format($item->quantity * $item->price,2) }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          @endif
          
            <!-- Services Table -->
            <div class="card mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-list-check-2 me-2"></i> {{ __('lockers_trans.invoice_services') }}
                    </h6>
                    <span class="badge bg-primary rounded-pill">{{ $services->count() }} {{ __('lockers_trans.services') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>{{ __('lockers_trans.service_name') }}</th>
                                    <th class="text-end">{{ __('lockers_trans.quantity') }}</th>
                                    <th class="text-end">{{ __('lockers_trans.price') }}</th>
                                    <th class="text-end">{{ __('lockers_trans.patient_contribution') }}</th>
                                    <th class="text-end">{{ __('lockers_trans.contract_contribution') }}</th>
                                    <th class="text-end pe-4">{{ __('lockers_trans.discount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $index => $srv)
                                    <tr>
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs bg-primary bg-opacity-10  rounded me-2">
                                                    <i class="ri-service-line"></i>
                                                </div>
                                                {{ $srv->service->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="text-end">{{ $srv->quantity }}</td>
                                        <td class="text-end">{{ number_format($srv->total_pricing, 2) }}</td>
                                        <td class="text-end">{{ number_format($srv->patient_contribution, 2) }}</td>
                                        <td class="text-end">{{ number_format($srv->contract_contribution, 2) }}</td>
                                        <td class="text-end pe-4">{{ number_format($srv->discount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="ps-4">{{ __('lockers_trans.total') }}</th>
                                    <th class="text-end">{{ number_format($services->sum('total_pricing'), 2) }}</th>
                                    <th class="text-end">{{ number_format($services->sum('patient_contribution'), 2) }}</th>
                                    <th class="text-end">{{ number_format($services->sum('contract_contribution'), 2) }}</th>
                                    <th class="text-end pe-4">{{ number_format($services->sum('discount'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Discounts and Cancellation Requests -->
            <div class="row g-4 mb-4">
                @if($discount_requests->count())
                <div class="col-md-6">
                    <div class="card border border-success border-opacity-20 h-100">
                        <div class="card-header bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="ri-percent-line me-2"></i> {{ __('lockers_trans.approved_discounts') }}
                            </h6>
                            <span class="badge bg-success rounded-pill">{{ $discount_requests->count() }}</span>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($discount_requests as $d)
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $d->reason }}</h6>
                                            <small class="text-muted">{{ $d->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                            {{ number_format($d->amount, 2) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($cancellation_request)
                <div class="col-md-6">
                    <div class="card border border-warning border-opacity-20 h-100">
                        <div class="card-header bg-warning bg-opacity-10 ">
                            <h6 class="mb-0 fw-bold text-warning">
                                <i class="ri-close-circle-line me-2"></i> {{ __('lockers_trans.pending_cancellation_request') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="mb-1">{{ __('lockers_trans.reason') }}</h6>
                                <p class="mb-0">{{ $cancellation_request->reason->name_ar ?? '-' }}</p>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ __('lockers_trans.notes') }}</h6>
                                <p class="mb-0 text-muted">{{ $cancellation_request->cancellation_notes ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Receipts Table -->
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-money-dollar-circle-line me-2"></i> {{ __('lockers_trans.receipts_list') }}
                    </h6>
                    <span class="badge bg-primary rounded-pill">{{ $receipts->count() }} {{ __('lockers_trans.receipts') }}</span>
                </div>
                <div class="card-body p-0">
                    @if($receipts->count())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>{{ __('lockers_trans.amount') }}</th>
                                    <th>{{ __('lockers_trans.payment_method') }}</th>
                                    <th>{{ __('lockers_trans.date') }}</th>
                                    <th class="pe-4">{{ __('lockers_trans.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receipts as $i => $receipt)
                                    <tr>
                                        <td class="ps-4">{{ $i + 1 }}</td>
                                        <td class="fw-bold text-success">{{ number_format($receipt->total_paid, 2) }}</td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10  rounded-pill px-3 py-1">
                                                {{ $receipt->pay_type }}
                                            </span>
                                        </td>
                                        <td>{{ $receipt->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="pe-4 text-truncate" style="max-width: 200px;" title="{{ $receipt->notes ?? '' }}">
                                            {{ $receipt->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="1" class="ps-4">{{ __('lockers_trans.total') }}</th>
                                    <th class="fw-bold">{{ number_format($receipts->sum('total_paid'), 2) }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl bg-light text-muted rounded-circle mb-3" style="text-align: center;padding-top: 25px;">
                            <i class="ri-wallet-3-line fs-3" style="font-size: 42px !important;"></i>
                        </div>
                        <h5 class="text-muted">{{ __('lockers_trans.no_receipts') }}</h5>
                        <p class="text-muted mb-0">{{ __('lockers_trans.no_payments_made_yet') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>