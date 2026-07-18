<div class="no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow">
        <i class="ri-printer-line me-2"></i> طباعة التقرير
    </button>
</div>

<style>
    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 15mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            color: #000;
            line-height: 1.5;
        }
        .no-print {
            display: none !important;
        }
        .page-break {
            page-break-after: always;
        }
    }

    .report-container {
        max-width: 100%;
        margin: 0 auto;
    }

    .report-header {
        text-align: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px double #444;
    }

    .hospital-info {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-bottom: 10px;
    }

    .hospital-logo {
        height: 90px;
        width: auto;
    }

    .hospital-text {
        text-align: center;
    }

    .hospital-name {
        font-size: 22px;
        font-weight: bold;
        color: #003366;
        margin-bottom: 5px;
    }

    .hospital-department {
        font-size: 16px;
        color: #555;
    }

    .report-title {
        font-size: 24px;
        font-weight: bold;
        color: #003366;
        margin: 15px 0 10px;
        text-decoration: underline;
        text-underline-offset: 8px;
    }

    .print-info {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        color: #666;
        margin-top: 10px;
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background-color: #f8f9fa;
    }

    .header-table td {
        border: 1px solid #dee2e6;
        padding: 10px;
        font-size: 14px;
    }

    .header-table td strong {
        color: #003366;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
    }

    .items-table th {
        background-color: #003366;
        color: white;
        padding: 12px;
        border: 1px solid #ddd;
        font-weight: bold;
        text-align: center;
    }

    .items-table td {
        border: 1px solid #dee2e6;
        padding: 10px;
        text-align: center;
    }

    .items-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .expired {
        color: #dc3545;
        font-weight: bold;
    }

    .warning {
        color: #ffc107;
        font-weight: bold;
    }

    .total-row {
        font-weight: bold;
        background-color: #e7f1ff !important;
    }

    .signature-section {
        margin-top: 40px;
    }

    .signature-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .signature-table td {
        border: 1px solid #dee2e6;
        padding: 40px 10px 10px;
        text-align: center;
        position: relative;
    }

    .signature-line {
        position: absolute;
        bottom: 25px;
        left: 10%;
        width: 80%;
        height: 1px;
        background-color: #666;
    }

    .footer-notes {
        margin-top: 30px;
        padding-top: 15px;
        border-top: 1px dashed #999;
        font-size: 13px;
        color: #666;
        text-align: center;
    }

    .text-right {
        text-align: right !important;
    }

    .text-left {
        text-align: left !important;
    }

    .barcode {
        font-family: 'Libre Barcode 128', cursive;
        font-size: 30px;
        margin-top: 10px;
    }
</style>

<div class="report-container">
    <div class="report-header">
        <div class="hospital-info" style="justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <img src="{{ asset('assets/images/logo.png') }}" alt="شعار المستشفى" class="hospital-logo" style="height: 70px;">
            
            <div class="hospital-text" style="flex-grow: 1; text-align: center;">
                <div class="hospital-name" style="font-size: 20px;">مستشفي دنيا العيون</div>
                <div class="hospital-department" style="font-size: 14px;">إدارة المخازن والصيدلة</div>
            </div>

            <div class="text-left" style="font-size: 13px; color: #555;">
                <div><i class="ri-time-line me-1"></i> تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                <div class="barcode">*{{ $movement_summary->movement_number }}*</div>
            </div>
        </div>

        <div class="report-title">{{ $title }}</div>
    </div>

    <table class="header-table">
        <tr>
            <td width="33%"><strong>رقم الحركة:</strong> {{ $movement_summary->movement_number }}</td>
            <td width="33%"><strong>تاريخ الحركة:</strong> {{ $movement_summary->movement_date }}</td>
            <td width="34%"><strong>المخزن:</strong> {{ $movement_summary->store->name_ar ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>رقم الطلب:</strong> {{ $movement_summary->reference_no ?? '-' }}</td>
            <td><strong> المورد:</strong> {{ $movement_summary->supplier->name_ar ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>ملاحظات:</strong> {{ $movement_summary->notes ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="4%">م</th>
                <th width="10%">كود الصنف</th>
                <th width="22%">اسم الصنف</th>
                <th width="8%">الوحدة</th>
                <th width="8%">الكمية</th>
                <th width="10%">سعر الوحدة</th>
                <th width="12%">الإجمالي</th>
                <th width="12%">رقم التشغيلة</th>
                <th width="14%">تاريخ الانتهاء</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($movement_items as $index => $item)
                @php
                    $price = $item->cost_price ?? $item->unitData->cost_price ?? 0;
                    $subtotal = $price * $item->quantity;
                    $total += $subtotal;
                    
                    $expiryClass = '';
                    if ($item->expiry_date) {
                        $daysDiff = now()->diffInDays($item->expiry_date, false);
                        if ($daysDiff < 0) {
                            $expiryClass = 'expired';
                        } elseif ($daysDiff < 30) {
                            $expiryClass = 'warning';
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->product_code ?? '-' }}</td>
                    <td class="text-right">{{ $item->product->name_ar ?? '-' }}</td>
                    <td>{{ $item->unit->name_ar ?? '-' }}</td>
                    <td>{{ number_format($item->quantity) }}</td>
                    <td>{{ number_format($price, 2) }}</td>
                    <td>{{ number_format($subtotal, 2) }}</td>
                    <td>{{ $item->batch_no ?? '-' }}</td>
                    <td class="{{ $expiryClass }}">
                        {{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d') : '-' }}
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-end"><strong>الإجمالي فقط وقدره:</strong></td>
                <td colspan="4">
                    <strong>{{ number_format($total, 2) }} جنيه</strong><br>
                    <small class="text-muted">
                        {{ \App\Helpers\NumberHelper::spellOutArabic($total) }} جنيه مصري

                    </small>
                    
                    
                </td>
            </tr>
            
        </tbody>
    </table>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td width="33%">
                      مدير القسم
                    <div class="signature-line"></div>
                    <div style="margin-top: 25px;">الاسم: ........................</div>
                    <div>التاريخ: ........................</div>
                </td>
                <td width="33%">
                    المستلم
                    <div class="signature-line"></div>
                    <div style="margin-top: 25px;">الاسم: ........................</div>
                    <div>التاريخ: ........................</div>
                </td>
                <td width="34%">
                    أمين المخزن
                    <div class="signature-line"></div>
                    <div style="margin-top: 25px;">الاسم: ........................</div>
                    <div>التاريخ: ........................</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-notes">
        <p><strong>ملاحظات:</strong></p>
        <p>2. يرجى مراجعة تواريخ الصلاحية قبل الاستخدام</p>
        <p>3. جميع الكميات المستلمة يجب أن تتطابق مع الكميات المذكورة أعلاه</p>
    </div>
</div>