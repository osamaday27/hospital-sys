<div class="container-fluid py-4" dir="rtl">
    <div class="card shadow-lg border-0 rounded-3">
        <div
            class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="mdi mdi-file-document-outline me-2 fs-4"></i>
                {{ __('suppliers.view_invoice') }}
                <span class="ms-2 text-warning">#{{ $invoice->invoice_no }}</span>
            </h5>
            <a href="{{ route('serviceinvoicesindex') }}" class="btn btn-light btn-sm">
                <i class="mdi mdi-arrow-left"></i> {{ __('suppliers.back') }}
            </a>
        </div>

        <div class="card-body">
            <!-- بيانات الفاتورة -->
            <h6 class="mb-3 border-bottom pb-2 text-secondary">
                <i class="mdi mdi-information-outline"></i> {{ __('suppliers.invoice_details') }}
            </h6>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <i class="mdi mdi-account-tie text-secondary me-1"></i>
                        <strong>{{ __('suppliers.supplier') }}:</strong>
                        <span class="d-block text-muted">{{ $invoice->supplier->name_ar ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <i class="mdi mdi-store text-success me-1"></i>
                        <strong>{{ __('suppliers.branch') }}:</strong>
                        <span class="d-block text-muted">{{ $invoice->branch->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <i class="mdi mdi-numeric text-warning me-1"></i>
                        <strong>{{ __('suppliers.invoice_no') }}:</strong>
                        <span class="d-block text-muted">{{ $invoice->invoice_no }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <i class="mdi mdi-calendar text-info me-1"></i>
                        <strong>{{ __('suppliers.invoice_date') }}:</strong>
                        <span class="d-block text-muted">{{ $invoice->invoice_date }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <i class="mdi mdi-calendar-clock text-danger me-1"></i>
                        <strong>{{ __('suppliers.due_date') }}:</strong>
                        <span class="d-block text-muted">{{ $invoice->due_date }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <i class="mdi mdi-check-circle text-success me-1"></i>
                        <strong>{{ __('suppliers.status') }}:</strong>
                        <span class="badge bg-info ms-2">{{ __('suppliers.' . $invoice->status) }}</span>
                    </div>
                </div>
            </div>

            <!-- جدول الأصناف -->
            <h6 class="mt-5 mb-3 border-bottom pb-2 text-secondary">
                <i class="mdi mdi-cart-outline"></i> {{ __('suppliers.services') }}
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>#</th>
                            <th><i class="mdi mdi-wrench"></i> {{ __('suppliers.service') }}</th>
                            <th><i class="mdi mdi-text-box"></i> {{ __('suppliers.description') }}</th>
                            <th><i class="mdi mdi-counter"></i> {{ __('suppliers.qty') }}</th>
                            <th><i class="mdi mdi-cash"></i> {{ __('suppliers.unit_price') }}</th>
                            <th><i class="mdi mdi-tag-minus"></i> {{ __('suppliers.discount') }}</th>
                            <th><i class="mdi mdi-percent"></i> {{ __('suppliers.tax') }}</th>
                            <th><i class="mdi mdi-cash-multiple"></i> {{ __('suppliers.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->service->name_ar ?? '-' }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->qty }}</td>
                                <td class="text-success fw-bold">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-danger">{{ number_format($item->discount, 2) }}</td>
                                <td>{{ $item->tax_type == 'percent' ? $item->tax . ' %' : number_format($item->tax, 2) }}
                                </td>
                                <td class="fw-bold">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="fw-bold">
                        <tr>
                            <td colspan="3">{{ __('suppliers.subtotal') }}</td>
                            <td colspan="5">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr class="text-danger">
                            <td colspan="3">{{ __('suppliers.discount') }}</td>
                            <td colspan="5">{{ number_format($invoice->discount, 2) }}</td>
                        </tr>
                        <tr class="text-secondary">
                            <td colspan="3">{{ __('suppliers.tax') }}</td>
                            <td colspan="5">{{ number_format($invoice->tax, 2) }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td colspan="3">{{ __('suppliers.total_amount') }}</td>
                            <td colspan="5" class="text-secondary">{{ number_format($invoice->total_amount, 2) }}
                            </td>
                        </tr>
                        <tr class="text-success">
                            <td colspan="3">{{ __('suppliers.paid_amount') }}</td>
                            <td colspan="5">{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="text-danger">
                            <td colspan="3">{{ __('suppliers.remaining_amount') }}</td>
                            <td colspan="5">{{ number_format($invoice->remaining_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- جدول الدفعات -->
            <h6 class="mt-5 mb-3 border-bottom pb-2 text-secondary">
                <i class="mdi mdi-cash-check"></i> {{ __('suppliers.payments') }}
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th><i class="mdi mdi-credit-card-outline"></i> {{ __('suppliers.method') }}</th>
                            <th><i class="mdi mdi-cash"></i> {{ __('suppliers.amount') }}</th>
                            <th><i class="mdi mdi-barcode"></i> {{ __('suppliers.reference_no') }}</th>
                            <th><i class="mdi mdi-safe"></i> {{ __('suppliers.locker') }}</th>
                            <th><i class="mdi mdi-bank"></i> {{ __('suppliers.bank') }}</th>
                            <th><i class="mdi mdi-bank-transfer"></i> {{ __('suppliers.bank_account') }}</th>
                            <th><i class="mdi mdi-file-document-edit-outline"></i> {{ __('suppliers.cheque_book') }}
                            </th>
                            <th><i class="mdi mdi-file-document-outline"></i> {{ __('suppliers.cheque_leaf') }}</th>
                            <th><i class="mdi mdi-calendar"></i> {{ __('suppliers.due_date') }}</th>
                            <th><i class="mdi mdi-calendar-check"></i> {{ __('suppliers.paid_date') }}</th>
                            <th><i class="mdi mdi-check-circle"></i> {{ __('suppliers.status') }}</th>
                            <th><i class="mdi mdi-note-text-outline"></i> {{ __('suppliers.notes') }}</th>
                            <th><i class="mdi mdi-calendar-clock"></i> {{ __('suppliers.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $pay)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($pay->method == 'cash')
                                        <span class="badge bg-success">{{ __('suppliers.cash') }}</span>
                                    @elseif($pay->method == 'cheque')
                                        <span class="badge bg-secondary">{{ __('suppliers.cheque') }}</span>
                                    @elseif($pay->method == 'bank_transfer')
                                        <span
                                            class="badge bg-info text-dark">{{ __('suppliers.bank_transfer') }}</span>
                                    @elseif($pay->method == 'visa')
                                        <span class="badge bg-primary">{{ __('suppliers.visa') }}</span>
                                    @else
                                        <span class="badge bg-dark">{{ $pay->method }}</span>
                                    @endif
                                </td>
                                <td class="text-success fw-bold">{{ number_format($pay->amount, 2) }}</td>
                                <td>{{ $pay->reference_no ?? '-' }}</td>
                                <td>{{ $pay->locker?->name_ar ?? '-' }}</td>
                                <td>{{ $pay->bank?->name_ar ?? '-' }}</td>
                                <td>{{ $pay->bankAccount?->account_number ?? '-' }}</td>
                                <td>{{ $pay->chequeBook?->name ?? '-' }}</td>
                                <td>{{ $pay->chequeLeaf?->cheque_number ?? '-' }}</td>
                                <td>{{ $pay->due_date ?? '-' }}</td>
                                <td>{{ $pay->paid_date ?? '-' }}</td>
                                <td>
                                    @if ($pay->status == 'pending')
                                        <span class="badge bg-warning text-dark">{{ __('suppliers.pending') }}</span>
                                    @elseif($pay->status == 'paid')
                                        <span class="badge bg-success">{{ __('suppliers.paid') }}</span>
                                    @elseif($pay->status == 'bounced')
                                        <span class="badge bg-danger">{{ __('suppliers.bounced') }}</span>
                                    @endif
                                </td>
                                <td>{{ $pay->notes ?? '-' }}</td>
                                <td>{{ $pay->created_at ? $pay->created_at->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-muted text-center">
                                    <i class="mdi mdi-information-outline"></i> {{ __('suppliers.no_payments') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
    </div>
</div>
