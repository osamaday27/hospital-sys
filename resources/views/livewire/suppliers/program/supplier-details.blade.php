<div class="container-fluid py-4" dir="rtl">
    <!-- عنوان -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">
            <i class="mdi mdi-account-circle-outline text-secondary"></i>
            {{ __('suppliers.supplier_details') }}
        </h3>
        <a href="{{ route('suppliers_accounts.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="mdi mdi-arrow-left"></i> {{ __('suppliers.Back') }}
        </a>
    </div>

    @if($supplier)
        <!-- بيانات المورد -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4"><strong>{{ __('suppliers.name') }}:</strong> {{ $supplier->name_ar }}</div>
                    <div class="col-md-4"><strong>{{ __('suppliers.group') }}:</strong> {{ $supplier->group->name_ar ?? '-' }}</div>
                    <div class="col-md-4"><strong>{{ __('suppliers.branch') }}:</strong> {{ $supplier->branch->name_ar ?? '-' }}</div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4"><strong>{{ __('suppliers.code') }}:</strong> {{ $supplier->code }}</div>
                    <div class="col-md-4"><strong>{{ __('suppliers.status') }}:</strong> {{ $supplier->status }}</div>
                </div>
            </div>
        </div>

        <!-- الملخص المالي -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card bg-light shadow-sm text-center p-3">
                    <i class="mdi mdi-file-document text-secondary fs-2"></i>
                    <h6>{{ __('suppliers.total_invoices') }}</h6>
                    <span class="fw-bold">{{ number_format($totalInvoices, 2) }}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light shadow-sm text-center p-3">
                    <i class="mdi mdi-cash text-success fs-2"></i>
                    <h6>{{ __('suppliers.total_payments') }}</h6>
                    <span class="fw-bold text-success">{{ number_format($totalPayments, 2) }}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light shadow-sm text-center p-3">
                    <i class="mdi mdi-bank text-info fs-2"></i>
                    <h6>{{ __('suppliers.opening_balance') }}</h6>
                    <span class="fw-bold text-info">{{ number_format($totalOpening, 2) }}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light shadow-sm text-center p-3">
                    <i class="mdi mdi-plus-circle text-secondary fs-2"></i>
                    <h6>{{ __('suppliers.additions') }}</h6>
                    <span class="fw-bold text-secondary">{{ number_format($totalAdditions, 2) }}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light shadow-sm text-center p-3">
                    <i class="mdi mdi-minus-circle text-danger fs-2"></i>
                    <h6>{{ __('suppliers.discounts') }}</h6>
                    <span class="fw-bold text-danger">{{ number_format($totalDiscounts, 2) }}</span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-light shadow-sm text-center p-3">
                    <i class="mdi mdi-scale-balance fs-2 {{ $finalBalance > 0 ? 'text-danger' : 'text-success' }}"></i>
                    <h6>{{ __('suppliers.final_balance') }}</h6>
                    <span class="fw-bold {{ $finalBalance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($finalBalance, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- تبويبات -->
        <ul class="nav nav-tabs mb-3" id="supplierTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabInvoices">{{ __('suppliers.invoices') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPayments">{{ __('suppliers.payments') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabOpening">{{ __('suppliers.opening_balances') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabAlerts">{{ __('suppliers.alerts') }}</a></li>
        </ul>

        <div class="tab-content">
            <!-- فواتير -->
            <div class="tab-pane fade show active" id="tabInvoices">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex mb-3 gap-2">
                            <input type="text" wire:model="invoiceSearch" placeholder="{{ __('suppliers.search_invoice') }}" class="form-control form-control-sm w-25">
                            <input type="date" wire:model="invoiceDateFrom" class="form-control form-control-sm">
                            <input type="date" wire:model="invoiceDateTo" class="form-control form-control-sm">
                        </div>
                        <table class="table table-hover table-bordered text-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('suppliers.invoice_no') }}</th>
                                    <th>{{ __('suppliers.invoice_date') }}</th>
                                    <th>{{ __('suppliers.due_date') }}</th>
                                    <th>{{ __('suppliers.total_amount') }}</th>
                                    <th>{{ __('suppliers.paid_amount') }}</th>
                                    <th>{{ __('suppliers.remaining_amount') }}</th>
                                    <th>{{ __('suppliers.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $inv)
                                    <tr>
                                        <td>{{ $inv->id }}</td>
                                        <td>{{ $inv->invoice_no }}</td>
                                        <td>{{ $inv->invoice_date }}</td>
                                        <td>{{ $inv->due_date }}</td>
                                        <td>{{ number_format($inv->total_amount, 2) }}</td>
                                        <td>{{ number_format($inv->paid_amount, 2) }}</td>
                                        <td>{{ number_format($inv->remaining_amount, 2) }}</td>
                                        <td><span class="badge bg-{{ $inv->status == 'paid' ? 'success' : 'warning' }}">{{ $inv->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted">{{ __('suppliers.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- مدفوعات -->
            <div class="tab-pane fade" id="tabPayments">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex mb-3 gap-2">
                            <input type="text" wire:model="paymentSearch" placeholder="{{ __('suppliers.search_payment') }}" class="form-control form-control-sm w-25">
                            <input type="date" wire:model="paymentDateFrom" class="form-control form-control-sm">
                            <input type="date" wire:model="paymentDateTo" class="form-control form-control-sm">
                        </div>
                        <table class="table table-hover table-striped text-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('suppliers.amount') }}</th>
                                    <th>{{ __('suppliers.method') }}</th>
                                    <th>{{ __('suppliers.reference_no') }}</th>
                                    <th>{{ __('suppliers.bank_name') }}</th>
                                    <th>{{ __('suppliers.notes') }}</th>
                                    <th>{{ __('suppliers.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $pay)
                                    <tr>
                                        <td>{{ $pay->id }}</td>
                                        <td class="text-success">{{ number_format($pay->amount, 2) }}</td>
                                        <td>{{ $pay->payment_method }}</td>
                                        <td>{{ $pay->reference_no }}</td>
                                        <td>{{ $pay->bank_name }}</td>
                                        <td>{{ $pay->notes }}</td>
                                        <td>{{ $pay->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">{{ __('suppliers.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- الأرصدة -->
            <div class="tab-pane fade" id="tabOpening">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <table class="table table-bordered text-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('suppliers.year') }}</th>
                                    <th>{{ __('suppliers.debit_balance') }}</th>
                                    <th>{{ __('suppliers.credit_balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($openingBalances as $ob)
                                    <tr>
                                        <td>{{ $ob->id }}</td>
                                        <td>{{ $ob->year }}</td>
                                        <td class="text-success">{{ number_format($ob->debit_balance, 2) }}</td>
                                        <td class="text-danger">{{ number_format($ob->credit_balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">{{ __('suppliers.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- إشعارات -->
            <div class="tab-pane fade" id="tabAlerts">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex mb-3 gap-2">
                            <input type="text" wire:model="alertSearch" placeholder="{{ __('suppliers.search_alert') }}" class="form-control form-control-sm w-25">
                            <input type="date" wire:model="alertDateFrom" class="form-control form-control-sm">
                            <input type="date" wire:model="alertDateTo" class="form-control form-control-sm">
                        </div>
                        <table class="table table-hover table-bordered text-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('suppliers.type') }}</th>
                                    <th>{{ __('suppliers.value') }}</th>
                                    <th>{{ __('suppliers.notify_date') }}</th>
                                    <th>{{ __('suppliers.notes') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alerts as $alert)
                                    <tr>
                                        <td>{{ $alert->id }}</td>
                                        <td>{{ $alert->type }}</td>
                                        <td class="{{ $alert->type == 'discount' ? 'text-danger' : 'text-secondary' }}">
                                            {{ number_format($alert->value, 2) }}
                                        </td>
                                        <td>{{ $alert->notify_date }}</td>
                                        <td>{{ $alert->notes }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">{{ __('suppliers.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
