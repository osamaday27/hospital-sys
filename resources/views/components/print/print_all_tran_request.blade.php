<div class="no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow">
        <i class="ri-printer-line me-2"></i> {{ __('supply_chain_trans.print_report') }}
    </button>
</div>

<div class="report-container">
    <div class="report-header">
        <div class="hospital-info" style="justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="شعار المستشفى" class="hospital-logo" style="height: 70px;">
            <div class="hospital-text" style="flex-grow: 1; text-align: center;">
                <div class="hospital-name" style="font-size: 20px;">مستشفي دنيا العيون</div>
                <div class="hospital-department" style="font-size: 14px;">إدارة المخازن والصيدلة</div>
            </div>
            <div class="text-left" style="font-size: 13px; color: #555;">
                <div><i class="ri-time-line me-1"></i> {{ __('supply_chain_trans.print_date') }}: {{ now()->format('Y-m-d H:i') }}</div>
                <div class="barcode">*{{ $selectedRequest->request_number }}*</div>
            </div>
        </div>
        <div class="report-title">{{ __('supply_chain_trans.request_details') }}</div>
    </div>

    <table class="header-table">
        <tr>
            <td><strong>{{ __('supply_chain_trans.request_number') }}:</strong> {{ $selectedRequest->request_number }}</td>
            <td><strong>{{ __('supply_chain_trans.request_date') }}:</strong> {{ $selectedRequest->request_date }}</td>
            <td><strong>{{ __('supply_chain_trans.status') }}:</strong> {{ __('supply_chain_trans.status_' . $selectedRequest->status) }}</td>
        </tr>
        <tr>
            <td colspan="3">
                @if($selectedType === 'client')
                    <strong>{{ __('supply_chain_trans.client') }}:</strong> {{ $selectedRequest->client->full_name ?? '-' }}
                @elseif($selectedType === 'employee')
                    <strong>{{ __('supply_chain_trans.employee') }}:</strong> {{ $selectedRequest->employee->full_name ?? '-' }}
                @elseif($selectedType === 'department')
                    <strong>{{ __('supply_chain_trans.department') }}:</strong> {{ $selectedRequest->department->name_ar ?? '-' }}
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3"><strong>{{ __('supply_chain_trans.store') }}:</strong> {{ $selectedRequest->store->name_ar ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>{{ __('supply_chain_trans.reason') }}:</strong> {{ $selectedRequest->reason ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('supply_chain_trans.item_code') }}</th>
                <th>{{ __('supply_chain_trans.item_name') }}</th>
                <th>{{ __('supply_chain_trans.unit') }}</th>
                <th>{{ __('supply_chain_trans.quantity_requested') }}</th>
                <th>{{ __('supply_chain_trans.quantity_issued') }}</th>
                <th>{{ __('supply_chain_trans.issued_unit') }}</th>
                <th>{{ __('supply_chain_trans.issuer') }}</th>
                <th>{{ __('supply_chain_trans.issue_date') }}</th>
                <th>{{ __('supply_chain_trans.store') }}</th>
                <th>{{ __('supply_chain_trans.batch_no') }}</th>
                <th>{{ __('supply_chain_trans.expiry_date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($selectedRequest->items ?? $selectedRequest->details) as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->product_code ?? '-' }}</td>
                    <td>{{ $item->product->name_ar ?? '-' }}</td>
                    <td>{{ $item->unit->name_ar ?? '-' }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                    <td>{{ number_format($item->issued_quantity ?? 0, 2) }}</td>
                    <td>{{ $item->issued_unit_name ?? '-' }}</td>
                    <td>{{ $item->issuer_name ?? '-' }}</td>
                    <td>{{ $item->movement_date ?? '-' }}</td>
                    <td>{{ $item->store_name ?? '-' }}</td>
                    <td>{{ $item->batch_no ?? '-' }}</td>
                    <td>{{ $item->expiry_date ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>{{ __('supply_chain_trans.department_manager') }}<div class="signature-line"></div><div>{{ __('supply_chain_trans.name') }}: ...............</div></td>
                <td>{{ __('supply_chain_trans.receiver_employee') }}<div class="signature-line"></div><div>{{ __('supply_chain_trans.name') }}: ...............</div></td>
                <td>{{ __('supply_chain_trans.store_keeper') }}<div class="signature-line"></div><div>{{ __('supply_chain_trans.name') }}: ...............</div></td>
            </tr>
        </table>
    </div>

    <div class="footer-notes">
        <p><strong>{{ __('supply_chain_trans.notes') }}:</strong></p>
        <p>{{ __('supply_chain_trans.review_items_notice') }}</p>
    </div>
</div>

@include('components.print.styles')
