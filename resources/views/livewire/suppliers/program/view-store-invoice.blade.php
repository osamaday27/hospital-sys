<div class="container-fluid py-4" dir="rtl">
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="mdi mdi-file-document-outline me-2 fs-4"></i>
                {{ __('suppliers.view_store_invoice') }}
                <span class="ms-2 text-warning">#{{ $invoice->invoice_no }}</span>
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('warehouse_invoices.index') }}" class="btn btn-light btn-sm">
                    <i class="mdi mdi-arrow-left"></i> {{ __('suppliers.back') }}
                </a>
                <a href="{{ route('warehouse_invoices.print', $invoice->id) }}" target="_blank" class="btn btn-success btn-sm">
                    <i class="mdi mdi-printer"></i> {{ __('suppliers.print') }}
                </a>
                <a href="" class="btn btn-danger btn-sm">
                    <i class="mdi mdi-file-pdf-box"></i> {{ __('suppliers.download_pdf') }}
                </a>
                <a href="" class="btn btn-success btn-sm">
                    <i class="mdi mdi-file-excel-box"></i> {{ __('suppliers.download_excel') }}
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- 🧾 بيانات الفاتورة -->
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
                        <i class="mdi mdi-warehouse text-success me-1"></i>
                        <strong>{{ __('suppliers.store') }}:</strong>
                        <span class="d-block text-muted">{{ $invoice->store->name_ar ?? '-' }}</span>
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

            <!-- 📦 جدول الأصناف -->
            <h6 class="mt-5 mb-3 border-bottom pb-2 text-secondary">
                <i class="mdi mdi-package-variant"></i> {{ __('suppliers.products') }}
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead class="table-primary text-dark">
                        <tr>
                            <th>#</th>
                            <th><i class="mdi mdi-package-variant-closed"></i> {{ __('suppliers.product') }}</th>
                            <th><i class="mdi mdi-ruler"></i> {{ __('suppliers.unit') }}</th>
                            <th><i class="mdi mdi-text-box"></i> {{ __('suppliers.description') }}</th>
                            <th><i class="mdi mdi-counter"></i> {{ __('suppliers.qty') }}</th>
                            <th><i class="mdi mdi-cash"></i> {{ __('suppliers.unit_price') }}</th>
                            <th><i class="mdi mdi-tag-minus"></i> {{ __('suppliers.discount') }}</th>
                            <th><i class="mdi mdi-percent"></i> {{ __('suppliers.tax') }}</th>
                            <th><i class="mdi mdi-cash-multiple"></i> {{ __('suppliers.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name_ar ?? '-' }}</td>
                                <td>{{ $item->productUnit->name_ar ?? '-' }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->qty }}</td>
                                <td class="text-success fw-bold">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-danger">{{ number_format($item->discount, 2) }}</td>
                                <td>{{ number_format($item->tax, 2) }}</td>
                                <td class="fw-bold">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="fw-bold">
                        <tr>
                            <td colspan="4">{{ __('suppliers.subtotal') }}</td>
                            <td colspan="5">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr class="text-danger">
                            <td colspan="4">{{ __('suppliers.discount') }}</td>
                            <td colspan="5">{{ number_format($invoice->discount, 2) }}</td>
                        </tr>
                        <tr class="text-secondary">
                            <td colspan="4">{{ __('suppliers.tax') }}</td>
                            <td colspan="5">{{ number_format($invoice->tax, 2) }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td colspan="4">{{ __('suppliers.total_amount') }}</td>
                            <td colspan="5" class="text-secondary">{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr class="text-success">
                            <td colspan="4">{{ __('suppliers.paid_amount') }}</td>
                            <td colspan="5">{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="text-danger">
                            <td colspan="4">{{ __('suppliers.remaining_amount') }}</td>
                            <td colspan="5">{{ number_format($invoice->remaining_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- 💵 جدول الدفعات -->
            <h6 class="mt-5 mb-3 border-bottom pb-2 text-secondary">
                <i class="mdi mdi-cash-check"></i> {{ __('suppliers.payments') }}
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th><i class="mdi mdi-safe"></i> {{ __('suppliers.locker') }}</th>
                            <th><i class="mdi mdi-credit-card-outline"></i> {{ __('suppliers.payment_method') }}</th>
                            <th><i class="mdi mdi-cash"></i> {{ __('suppliers.amount') }}</th>
                            <th><i class="mdi mdi-barcode"></i> {{ __('suppliers.reference_no') }}</th>
                            <th><i class="mdi mdi-bank"></i> {{ __('suppliers.bank_name') }}</th>
                            <th><i class="mdi mdi-file-document-edit-outline"></i> {{ __('suppliers.bank_details') }}</th>
                            <th><i class="mdi mdi-note-text-outline"></i> {{ __('suppliers.notes') }}</th>
                            <th><i class="mdi mdi-calendar-clock"></i> {{ __('suppliers.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $pay)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $pay->locker->name_ar ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ __('suppliers.'.$pay->payment_method) }}</span></td>
                                <td class="text-success fw-bold">{{ number_format($pay->amount, 2) }}</td>
                                <td>{{ $pay->reference_no ?? '-' }}</td>
                                <td>{{ $pay->bank_name ?? '-' }}</td>
                                <td>{{ $pay->bank_details ?? '-' }}</td>
                                <td>{{ $pay->notes ?? '-' }}</td>
                                <td>{{ $pay->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-muted">
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
