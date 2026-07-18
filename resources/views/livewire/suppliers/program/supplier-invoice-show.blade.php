<div class="container-fluid py-5" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 fw-bold">
                            <i class="mdi mdi-file-document me-2"></i>
                            {{ __('suppliers.invoice_details') }}
                        </h4>
                        <a href="{{ route('serviceinvoicesindex') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-right me-1"></i> {{ __('suppliers.back_to_invoices') }}
                        </a>
                    </div>
                </div>

                <div class="card-body p-5">
                    <!-- معلومات الفاتورة -->
                    <div class="invoice-info-section mb-5">
                        <h5 class="section-title mb-4 text-primary">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            {{ __('suppliers.invoice_info') }}
                        </h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="info-card bg-light p-3 rounded-3">
                                    <small class="text-muted">{{ __('suppliers.invoice_no') }}</small>
                                    <p class="mb-0 fw-bold fs-5">{{ $invoice->invoice_no }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-card bg-light p-3 rounded-3">
                                    <small class="text-muted">{{ __('suppliers.supplier') }}</small>
                                    <p class="mb-0 fw-bold fs-5">{{ $invoice->supplier->name_ar ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-card bg-light p-3 rounded-3">
                                    <small class="text-muted">{{ __('suppliers.status') }}</small>
                                    <span class="badge 
                                        @if ($invoice->status == 'paid') bg-success
                                        @elseif($invoice->status == 'partial') bg-warning
                                        @else bg-danger @endif">
                                        {{ __('suppliers.' . $invoice->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-card bg-light p-3 rounded-3 text-center">
                                    <small class="text-muted">{{ __('suppliers.total_amount') }}</small>
                                    <p class="mb-0 fw-bold fs-5 text-success">
                                        {{ number_format($invoice->total_amount, 2) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-card bg-light p-3 rounded-3 text-center">
                                    <small class="text-muted">{{ __('suppliers.paid_amount') }}</small>
                                    <p class="mb-0 fw-bold fs-5 text-info">
                                        {{ number_format($invoice->paid_amount, 2) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="info-card bg-light p-3 rounded-3 text-center">
                                    <small class="text-muted">{{ __('suppliers.remaining_amount') }}</small>
                                    <p class="mb-0 fw-bold fs-5 text-danger">
                                        {{ number_format($invoice->remaining_amount, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- جدول الدفعات -->
                    <div class="payment-history-section">
                        <h5 class="section-title mb-4 text-primary">
                            <i class="fas fa-history me-2"></i>
                            {{ __('suppliers.previous_payments') }}
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('suppliers.payment_date') }}</th>
                                        <th>{{ __('suppliers.method') }}</th>
                                        <th>{{ __('suppliers.amount') }}</th>
                                        <th>{{ __('suppliers.reference_no') }}</th>
                                        <th>{{ __('suppliers.locker') }}</th>
                                        <th>{{ __('suppliers.bank') }}</th>
                                        <th>{{ __('suppliers.bank_account') }}</th>
                                        <th>{{ __('suppliers.notes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $index => $pay)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $pay->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if ($pay->method == 'cash')
                                                    <span class="badge bg-success">{{ __('suppliers.cash') }}</span>
                                                @elseif($pay->method == 'cheque')
                                                    <span class="badge bg-secondary">{{ __('suppliers.cheque') }}</span>
                                                @elseif($pay->method == 'bank_transfer')
                                                    <span class="badge bg-info text-dark">{{ __('suppliers.bank_transfer') }}</span>
                                                @elseif($pay->method == 'visa')
                                                    <span class="badge bg-primary">{{ __('suppliers.visa') }}</span>
                                                @else
                                                    <span class="badge bg-dark">{{ $pay->method }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold text-success">{{ number_format($pay->amount, 2) }}</td>
                                            <td>{{ $pay->reference_no ?? '-' }}</td>
                                            <td>{{ $pay->locker_id ?? '-' }}</td>
                                            <td>{{ $pay->bank_id ?? '-' }}</td>
                                            <td>{{ $pay->bank_account_id ?? '-' }}</td>
                                            <td>{{ $pay->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-muted">{{ __('suppliers.no_payments') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
