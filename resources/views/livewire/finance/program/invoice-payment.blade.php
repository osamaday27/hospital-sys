<div class="container-fluid py-3">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold  mb-0">
                <i class="ri-wallet-3-line me-2"></i> {{ __('lockers_trans.invoice_payment') }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="#">{{ __('lockers_trans.financial') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('lockers_trans.payments') }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end">
            <span class="badge bg-light text-dark fs-6">
                <i class="ri-time-line me-1"></i> {{ now()->format('Y-m-d H:i') }}
            </span>
        </div>
    </div>

    <!-- Alert Notifications -->
    <div class="row mb-4">
        <div class="col-12">
            @if (session()->has('success'))
                <div class="alert alert-success d-flex align-items-center">
                    <i class="ri-checkbox-circle-fill me-2 fs-4"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($cancellation_request)
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="ri-alarm-warning-line me-2 fs-4"></i>
                    <div>
                        <strong>!</strong> {{ __('lockers_trans.invoice_has_cancel_request') }}
                    </div>
                </div>
            @endif

            @if ($discount_requests->count() && count($selected_discounts) === 0)
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="ri-error-warning-line me-2 fs-4"></i>
                    <div>{{ __('lockers_trans.discount_warning') }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Patient and Invoice Summary -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light-primary d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="ri-user-3-line me-2"></i> {{ __('lockers_trans.patient_info') }}
            </h5>
            <span class="badge bg-primary">فاتورة #{{ $invoice->number }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Patient Info -->
                <div class="col-md-6 border-end-md">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg bg-light-primary rounded-circle me-3">
                            <i class="ri-user-line fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $invoice->patient->full_name ?? '-' }}</h5>
                            <div class="text-muted">رقم المريض: {{ $invoice->patient->id ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="contact-info">
                        <div class="d-flex mb-2">
                            <i class="ri-phone-line me-2 text-muted"></i>
                            <div>
                                {{ $invoice->patient->phone_number ?? '-' }}<br>
                                {{ $invoice->patient->phone_number2 ?? '-' }}<br>
                                {{ $invoice->patient->whatsapp ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="col-md-6 mt-3 mt-md-0">
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-label text-muted">{{ __('lockers_trans.invoice_total') }}</div>
                                <div class="stat-value ">{{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card">
                                <div class="stat-label text-muted">{{ __('lockers_trans.patient_share') }}</div>
                                <div class="stat-value text-success">{{ number_format($invoice->patient_contribution, 2) }} {{ $invoice->currency }}</div>
                            </div>
                        </div>
                    </div>

                    @if($invoice->reservations)
                    <hr class="my-3">
                    <div class="row">
                        <div class="col-4">
                            <small class="text-muted d-block">{{ __('lockers_trans.doctor_name') }}</small>
                            <strong>{{ $invoice->reservations->doctor->full_name ?? '-' }}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">{{ __('lockers_trans.clinic_name') }}</small>
                            <strong>{{ $invoice->reservations->clinic->name_ar ?? '-' }}</strong>
                        </div>
                        <div class="col-4">
                            <small class="text-muted d-block">{{ __('lockers_trans.reservation_type') }}</small>
                            <strong>{{ $invoice->reservations->service->name ?? '-' }}</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Discounts Section -->
    @if ($discount_requests->count())
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light-warning">
            <h5 class="mb-0 fw-bold">
                <i class="ri-coupon-3-line me-2"></i> {{ __('lockers_trans.approved_discount_requests') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($discount_requests as $discount)
                <div class="col-md-6">
                    <div class="discount-card p-3 border rounded d-flex justify-content-between align-items-center 
                                {{ in_array($discount->id, $selected_discounts) ? 'border-success bg-light-success' : '' }}">
                        <div class="form-check form-check-lg">
                            <input class="form-check-input" type="checkbox"
                                   wire:model="selected_discounts"
                                   value="{{ $discount->id }}"
                                   id="discount_{{ $discount->id }}">
                            <label class="form-check-label" for="discount_{{ $discount->id }}">
                                <div class="fw-bold">{{ number_format($discount->amount, 2) }} {{ $invoice->currency }}</div>
                                <small class="text-muted">{{ $discount->reason }}</small>
                            </label>
                        </div>
                        <span class="badge bg-light text-dark">
                            {{ $discount->created_at->format('Y-m-d') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($supplies->count())
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light">
        <h5 class="mb-0 fw-bold">
          <i class="ri-archive-line me-2"></i> {{ __('lockers_trans.visit_supplies') }}
        </h5>
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
                <td>{{ $i + 1 }}</td>
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
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-bold">
                <i class="ri-list-check-2 me-2"></i> {{ __('lockers_trans.invoice_items') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>{{ __('lockers_trans.service_name') }}</th>
                            <th class="text-end">{{ __('lockers_trans.quantity') }}</th>
                            <th class="text-end">{{ __('lockers_trans.price') }}</th>
                            <th class="text-end">{{ __('lockers_trans.patient_contribution') }}</th>
                            <th class="text-end">{{ __('lockers_trans.contract_contribution') }}</th>
                            <th class="text-end">{{ __('lockers_trans.discount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $index => $srv)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="ri-service-line me-2 "></i>
                                    {{ $srv->service->name ?? '-' }}
                                </div>
                            </td>
                            <td class="text-end">{{ $srv->quantity }}</td>
                            <td class="text-end">{{ number_format($srv->total_pricing, 2) }}</td>
                            <td class="text-end">{{ number_format($srv->patient_contribution, 2) }}</td>
                            <td class="text-end">{{ number_format($srv->contract_contribution, 2) }}</td>
                            <td class="text-end">{{ number_format($srv->discount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light-primary">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="ri-bank-card-line me-2"></i> {{ __('lockers_trans.payment_methods') }}
                </h5>
                <button class="btn btn-sm btn-primary" wire:click="addPayment">
                    <i class="ri-add-line me-1"></i> {{ __('lockers_trans.add_payment_method') }}
                </button>
            </div>
        </div>
        <div class="card-body">
            @foreach($payments as $index => $payment)
            <div class="payment-card mb-4 border rounded-3 p-3 
                      {{ $payment['method'] === 'cash' ? 'border-primary' : '' }}
                      {{ $payment['method'] === 'visa' ? 'border-success' : '' }}
                      {{ $payment['method'] === 'bank' ? 'border-info' : '' }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-{{ $payment['method'] === 'cash' ? 'money-dollar-circle-line' : ($payment['method'] === 'visa' ? 'visa-line' : 'bank-card-line') }} me-2"></i>
                        {{ __('lockers_trans.payment') }} #{{ $index + 1 }}
                    </h6>
                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger" wire:click="removePayment({{ $index }})">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('lockers_trans.payment_type') }}</label>
                        <select class="form-select" wire:model="payments.{{ $index }}.method">
                            <option value="">{{ __('lockers_trans.choose') }}</option>
                            <option value="cash" selected>{{ __('lockers_trans.cash') }}</option>
                            <option value="visa">{{ __('lockers_trans.visa') }}</option>
                            <option value="bank">{{ __('lockers_trans.bank_transfer') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('lockers_trans.amount') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" wire:model="payments.{{ $index }}.amount">
                            <span class="input-group-text">{{ $invoice->currency }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('lockers_trans.select_locker') }}</label>
                        <select class="form-select" wire:model="payments.{{ $index }}.locker_id">
                            <option value="">{{ __('lockers_trans.choose') }}</option>
                            @foreach($lockers as $locker)
                                <option value="{{ $locker->id }}">{{ $locker->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('lockers_trans.partial_payment_reason') }}</label>
                        <select class="form-select" wire:model="payments.{{ $index }}.partial_reason">
                            <option value="">{{ __('lockers_trans.choose') }}</option>
                            @foreach($reasons as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- تفاصيل إضافية حسب الطريقة --}}
                @if(($payment['method'] ?? '') === 'bank')
                    <div class="row mt-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('lockers_trans.bank_name') }}</label>
                            <select class="form-select" wire:model="payments.{{ $index }}.bank_id">
                                <option value="">{{ __('lockers_trans.choose') }}</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id }}">{{ $b->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('lockers_trans.transaction_number') }}</label>
                            <input type="text" class="form-control" wire:model="payments.{{ $index }}.transaction_number" placeholder="رقم التحويل البنكي">
                        </div>
                    </div>
                @endif

                @if(($payment['method'] ?? '') === 'visa')
                    <div class="row mt-3 g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('lockers_trans.bank_machine') }}</label>
                            <select class="form-select" wire:model="payments.{{ $index }}.bank_machine_id">
                                <option value="">{{ __('lockers_trans.choose') }}</option>
                                @foreach($machines as $m)
                                    <option value="{{ $m->id }}">{{ $m->machine_code }} - {{ optional($m->bank)->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('lockers_trans.card_holder_name') }}</label>
                            <input type="text" class="form-control" wire:model="payments.{{ $index }}.card_info.holder_name" placeholder="اسم حامل البطاقة">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('lockers_trans.card_number') }}</label>
                            <input type="text" class="form-control" wire:model="payments.{{ $index }}.card_info.card_number" placeholder="xxxx-xxxx-xxxx-xxxx">
                        </div>
                        
                    </div>
                @endif
            </div>
            @endforeach

            <!-- Payment Summary -->
            <div class="payment-summary p-4 bg-light rounded-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="summary-item">
                            <span class="label">{{ __('lockers_trans.patient_share') }}</span>
                            <span class="value">{{ number_format($invoice->patient_contribution, 2) }} {{ $invoice->currency }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-item">
                            <span class="label">{{ __('lockers_trans.approved_discount') }}</span>
                            <span class="value text-danger">- {{ number_format($approved_discount_total, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="summary-item">
                            <span class="label">{{ __('lockers_trans.total_paid') }}</span>
                            <span class="value text-success">{{ number_format($total_paid, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="summary-total">
                            <span class="label">{{ __('lockers_trans.total_remain') }}</span>
                            <span class="value fw-bold {{ $total_remain > 0 ? 'text-warning' : 'text-success' }}">
                                {{ number_format($total_remain, 2) }} {{ $invoice->currency }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary px-4" wire:click="resetForm">
                    <i class="ri-close-line me-1"></i> {{ __('lockers_trans.cancel') }}
                </button>
                <button wire:click="submit" class="btn btn-success px-5" {{ $total_remain > 0 ? 'disabled' : '' }}>
                    <i class="ri-check-double-line me-1"></i> {{ __('lockers_trans.confirm_payment') }}
                </button>
            </div>
        </div>
    </div>
</div>
