<div class="container-fluid py-4" dir="rtl">
    <div class="card shadow-lg border-0 rounded-3">
        <div
            class="card-header bg-gradient-primary text-white rounded-top d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="mdi mdi-bell-ring-outline me-2"></i>
                {{ __('suppliers.payment_alerts') }}
            </h5>
        </div>

        <div class="card-body">
            {{-- 🔍 فلاتر البحث --}}
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('suppliers.supplier') }}</label>
                    <input type="text" wire:model.debounce.500ms="supplier_name" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('suppliers.invoice_status') }}</label>
                    <select wire:model="invoice_status" class="form-select">
                        <option value="">{{ __('suppliers.all') }}</option>
                        <option value="paid">{{ __('suppliers.paid') }}</option>
                        <option value="partial">{{ __('suppliers.partial') }}</option>
                        <option value="unpaid">{{ __('suppliers.unpaid') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('suppliers.payment_status') }}</label>
                    <select wire:model="payment_status" class="form-select">
                        <option value="">{{ __('suppliers.all') }}</option>
                        <option value="pending">{{ __('suppliers.pending') }}</option>
                        <option value="paid">{{ __('suppliers.paid') }}</option>
                        <option value="bounced">{{ __('suppliers.bounced') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('suppliers.review_status') }}</label>
                    <select wire:model="review_status" class="form-select">
                        <option value="">{{ __('suppliers.all') }}</option>
                        <option value="reviewed">{{ __('suppliers.reviewed') }}</option>
                        <option value="not_reviewed">{{ __('suppliers.not_reviewed') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('suppliers.date_range') }}</label>
                    <div class="d-flex">
                        <input type="date" wire:model="from_date" class="form-control me-2">
                        <input type="date" wire:model="to_date" class="form-control">
                    </div>
                </div>
            </div>

            {{-- 📋 جدول التنبيهات --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('suppliers.supplier') }}</th>
                            <th>{{ __('suppliers.invoice_no') }}</th>
                            <th>{{ __('suppliers.invoice_status') }}</th>
                            <th>{{ __('suppliers.invoice_total') }}</th>
                            <th>{{ __('suppliers.paid_amount') }}</th>
                            <th>{{ __('suppliers.remaining_amount') }}</th>
                            <th>{{ __('suppliers.installment_amount') }}</th>
                            <th>{{ __('suppliers.due_date') }}</th>
                            <th>{{ __('suppliers.payment_status') }}</th>
                            <th>{{ __('suppliers.payment_method') }}</th>
                            <th>{{ __('suppliers.review') }}</th>
                            <th>{{ __('suppliers.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alerts as $index => $alert)
                            <tr>
                                <td>{{ $alerts->firstItem() + $index }}</td>
                                <td>{{ $alert->invoice->supplier->name_ar ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('supplier_invoices.show', $alert->invoice->id) }}"
                                        class="text-info fw-bold">
                                        {{ $alert->invoice->invoice_no }}
                                    </a>
                                </td>
                                <td>
                                    <span
                                        class="badge 
                                    @if ($alert->invoice->status == 'paid') bg-success 
                                    @elseif($alert->invoice->status == 'partial') bg-warning 
                                    @else bg-danger @endif">
                                        {{ __('suppliers.' . $alert->invoice->status) }}
                                    </span>
                                </td>
                                <td class="fw-bold text-primary">{{ number_format($alert->invoice->total_amount, 2) }}
                                </td>
                                <td class="fw-bold text-success">{{ number_format($alert->invoice->paid_amount, 2) }}
                                </td>
                                <td class="fw-bold text-danger">
                                    {{ number_format($alert->invoice->remaining_amount, 2) }}</td>
                                <td class="fw-bold">{{ number_format($alert->amount, 2) }}</td>
                                <td class="text-danger">{{ $alert->due_date }}</td>
                                <td>
                                    <span
                                        class="badge 
                                    @if ($alert->status == 'paid') bg-success 
                                    @elseif($alert->status == 'pending') bg-warning 
                                    @else bg-danger @endif">
                                        {{ __('suppliers.' . $alert->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($alert->method == 'cash')
                                        <span class="badge bg-success">
                                            {{ __('suppliers.cash') }}
                                        </span>
                                        <br>
                                        <small>{{ $alert->locker->name ?? __('suppliers.no_locker') }}</small>
                                    @elseif($alert->method == 'bank_transfer')
                                        <span class="badge bg-primary">
                                            {{ __('suppliers.bank_transfer') }}
                                        </span>
                                        <br>
                                        <small>{{ $alert->bank->name_ar ?? '' }} -
                                            {{ $alert->bankAccount->account_number ?? '' }}</small>
                                    @elseif($alert->method == 'visa')
                                        <span class="badge bg-info">
                                            {{ __('suppliers.visa') }}
                                        </span>
                                        <br>
                                        <small>{{ $alert->bank->name_ar ?? '' }} - {{ $alert->reference_no }}</small>
                                    @elseif($alert->method == 'cheque')
                                        <span class="badge bg-warning text-dark">
                                            {{ __('suppliers.cheque') }}
                                        </span>
                                        <br>
                                        <small>{{ $alert->chequeLeaf->cheque_number ?? __('suppliers.no_cheque') }}</small>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ __('suppliers.other') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <input type="checkbox" wire:click="toggleReviewed({{ $alert->id }})"
                                        @checked($alert->reviewed)>
                                </td>
                                <td>
                                    <a href="{{ route('supplier_invoices.show', $alert->invoice->id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i> {{ __('suppliers.view_invoice') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-muted">
                                    <i class="mdi mdi-information-outline"></i> {{ __('suppliers.no_alerts') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ الباجينشن --}}
            <div class="mt-3">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>
</div>
