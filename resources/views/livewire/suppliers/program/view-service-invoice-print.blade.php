<div>
    <!-- رأس الفاتورة -->
    <div class="invoice-header">
        <h4>{{ __('suppliers.view_invoice') }}</h4>
        <h5>#{{ $invoice->invoice_no }}</h5>
        <p>{{ $invoice->invoice_date }} | {{ $invoice->supplier->name_ar ?? '' }}</p>
    </div>

    <!-- بيانات الفاتورة -->
    <table class="table table-bordered mb-4 text-sm">
        <tr>
            <th>{{ __('suppliers.supplier') }}</th>
            <td>{{ $invoice->supplier->name_ar ?? '-' }}</td>
            <th>{{ __('suppliers.branch') }}</th>
            <td>{{ $invoice->branch->name_ar ?? '-' }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.invoice_no') }}</th>
            <td>{{ $invoice->invoice_no }}</td>
            <th>{{ __('suppliers.invoice_date') }}</th>
            <td>{{ $invoice->invoice_date }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.due_date') }}</th>
            <td>{{ $invoice->due_date }}</td>
            <th>{{ __('suppliers.status') }}</th>
            <td>{{ __('suppliers.' . $invoice->status) }}</td>
        </tr>
    </table>

    <!-- جدول الخدمات -->
    <h5 class="mb-2">{{ __('suppliers.services') }}</h5>
    <table class="table table-bordered text-center text-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>{{ __('suppliers.service') }}</th>
                <th>{{ __('suppliers.description') }}</th>
                <th>{{ __('suppliers.qty') }}</th>
                <th>{{ __('suppliers.unit_price') }}</th>
                <th>{{ __('suppliers.discount') }}</th>
                <th>{{ __('suppliers.tax') }}</th>
                <th>{{ __('suppliers.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->service->name_ar ?? '-' }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->discount, 2) }}</td>
                    <td>{{ $item->tax_type == 'percent' ? $item->tax.' %' : number_format($item->tax, 2) }}</td>
                    <td>{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ملخص الحساب -->
    <table class="table table-bordered w-50 ms-auto text-sm">
        <tr>
            <th>{{ __('suppliers.subtotal') }}</th>
            <td>{{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.discount') }}</th>
            <td>{{ number_format($invoice->discount, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.tax') }}</th>
            <td>{{ number_format($invoice->tax, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.total_amount') }}</th>
            <td>{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.paid_amount') }}</th>
            <td>{{ number_format($invoice->paid_amount, 2) }}</td>
        </tr>
        <tr>
            <th>{{ __('suppliers.remaining_amount') }}</th>
            <td>{{ number_format($invoice->remaining_amount, 2) }}</td>
        </tr>
    </table>

    <!-- جدول الدفعات -->
    <h5 class="mb-2">{{ __('suppliers.payments') }}</h5>
    <table class="table table-bordered text-center text-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>{{ __('suppliers.locker') }}</th>
                <th>{{ __('suppliers.payment_method') }}</th>
                <th>{{ __('suppliers.amount') }}</th>
                <th>{{ __('suppliers.reference_no') }}</th>
                <th>{{ __('suppliers.bank_name') }}</th>
                <th>{{ __('suppliers.bank_details') }}</th>
                <th>{{ __('suppliers.notes') }}</th>
                <th>{{ __('suppliers.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $pay)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $pay->locker->name ?? '-' }}</td>
                    <td>{{ __('suppliers.' . $pay->payment_method) }}</td>
                    <td>{{ number_format($pay->amount, 2) }}</td>
                    <td>{{ $pay->reference_no ?? '-' }}</td>
                    <td>{{ $pay->bank_name ?? '-' }}</td>
                    <td>{{ $pay->bank_details ?? '-' }}</td>
                    <td>{{ $pay->notes ?? '-' }}</td>
                    <td>{{ $pay->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-muted">{{ __('suppliers.no_payments') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- توقيعات -->
    <div class="mt-5">
        <table class="table text-center border-0">
            <tr>
                <td>
                    <strong>{{ __('lockers_trans.collector_name') }}</strong>
                    <div class="signature-line"></div>
                    {{ __('lockers_trans.signature') }}
                </td>
                <td>
                    <strong>{{ __('lockers_trans.financial_manager') }}</strong>
                    <div class="signature-line"></div>
                </td>
                <td>
                    <strong>{{ __('lockers_trans.receiver') }}</strong>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
