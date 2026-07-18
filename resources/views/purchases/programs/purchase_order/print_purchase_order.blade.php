@extends('layouts.master')

@section('title', $title ?? 'طباعة أمر الشراء')

@section('content')
    @php
        $totalBeforeTax = 0;
        $totalTax       = 0;
        $totalAfterTax  = 0;
    @endphp

    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow print-btn">
            <i class="ri-printer-line me-2"></i> طباعة التقرير
        </button>
    </div>

    <style>
        @media print {
            @page { size: A4 landscape; margin: 10mm 15mm; }
            body { font-family: 'Arial', sans-serif; direction: rtl; color: #000; line-height: 1.5; background-color: #fff !important; }
            .no-print { display: none !important; }
        }
        .report-container {
            max-width:100%;
            margin:0 auto;
            padding:20px;
            background:#fff;
            box-shadow:0 0 15px rgba(0,0,0,0.08);
            border-radius:8px;
        }
        .report-header {
            margin-bottom:25px;
            border-bottom:3px double #444;
            padding-bottom:15px;
        }
        .hospital-info {
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-wrap:wrap;
            gap:10px;
            margin-bottom:15px;
        }
        .hospital-logo { height:80px; }
        .hospital-text { text-align:center; flex-grow:1; }
        .hospital-name { font-size:22px; font-weight:bold; color:#003366; margin-bottom:5px; }
        .hospital-department { font-size:16px; color:#555; }
        .print-info { font-size:13px; color:#555; display:flex; flex-direction:column; }
        .print-info i { margin-left:5px; }
        .barcode { font-family:'Libre Barcode 128', cursive; font-size:30px; letter-spacing:2px; }
        .report-title {
            font-size:24px;
            font-weight:bold;
            color:#003366;
            margin: 0 auto 15px;
            display:block;
            text-align:center;
        }
        .header-table, .items-table {
            width:100%;
            border-collapse:collapse;
            margin:20px 0;
            background:#f8f9fa;
        }
        .header-table { background:#f1f7ff; }
        .header-table td, .items-table th, .items-table td {
            border:1px solid #dee2e6;
            padding:12px;
            text-align:center;
            font-size:14px;
        }
        .header-table td { font-weight:500; }
        .items-table th { background:#003366; color:#fff; }
        .items-table tr:nth-child(even) { background:#f8f9fa; }
        .total-row { font-weight:bold; background:#e7f1ff; }
        .section-title {
            font-size:18px;
            font-weight:bold;
            color:#003366;
            margin:25px 0 15px;
            text-align:center;
            position:relative;
        }
        .section-title:after {
            content:"";
            display:block;
            width:100px;
            height:2px;
            background:#003366;
            margin:10px auto;
        }
        .signature-section {
            margin-top:40px;
            text-align:center;
        }
        .signature-table {
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
        }
        .signature-table td {
            border:1px solid #dee2e6;
            padding:50px 10px 15px;
            vertical-align:bottom;
            background:#f8f9fa;
        }
        .signature-line {
            width:80%;
            height:1px;
            background:#666;
            margin:0 auto 10px;
        }
    </style>

    <div class="report-container">

        <div class="report-header">
            <div class="hospital-info">
                <img src="{{ asset('assets/images/logo.png') }}" alt="شعار" class="hospital-logo">
                <div class="hospital-text">
                    <div class="hospital-name">مستشفي دنيا العيون</div>
                    <div class="hospital-department">إدارة المشتريات</div>
                </div>
                <div class="print-info">
                    <div><i class="ri-time-line"></i> تاريخ ووقت الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                    <div class="barcode">*{{ $order_summary->order_number }}*</div>
                </div>
            </div>
            <span class="report-title">{{ $title }}</span>
        </div>

        {{-- ملخص الأمر --}}
        <table class="header-table">
            <tr>
                <td><strong>رقم الطلب:</strong> {{ $order_summary->order_number }}</td>
                <td><strong>تاريخ الطلب:</strong> {{ $order_summary->order_date }}</td>
                <td><strong>المورد:</strong>
                    {{ optional($order_summary->supplier)->name_ar ?? '-' }}
                    @if(optional($order_summary->supplier)->name_en)
                        / {{ optional($order_summary->supplier)->name_en }}
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>مكان التسليم:</strong> {{ $order_summary->delivery_location ?? '-' }}</td>
                <td><strong>طريقة الدفع:</strong>
                {{ $order_summary->payment_method === 'cash'
                    ? __('purchases.payment_cash')
                    : (
                        $order_summary->payment_method === 'bank_transfer'
                            ? __('purchases.payment_bank_transfer')
                            : __('purchases.payment_credit')
                    )
                }}

                </td>
                <td><strong>الأولوية:</strong> {{ __("purchases.priority_{$order_summary->priority}") }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>شروط الدفع:</strong> {{ $order_summary->payment_terms ?? '-' }}</td>
                <td><strong>ملاحظات:</strong> {{ $order_summary->notes ?? '-' }}</td>
            </tr>
        </table>

        {{-- تفاصيل البنود --}}
        <h5 class="section-title">تفاصيل البنود</h5>
        <table class="items-table">
            <thead>
                <tr>
                    <th>م</th>
                    <th>الصنف</th>
                    <th>الوحدة</th>
                    <th>الكمية</th>
                    <th>سعر الوحدة</th>
                    <th>قيمة الضريبة</th>
                    <th>الإجمالي قبل الضريبة</th>
                    <th>الإجمالي بعد الضريبة</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_items as $i => $item)
                    @php
                        $prod     = $item->product;
                        $name     = $prod
                                  ? $prod->name_ar . ($prod->name_en ? ' / ' . $prod->name_en : '')
                                  : '-';
                        $qty      = $item->requested_qty;
                        $price    = $item->last_price;
                        $before   = $qty * $price;
                        $vatAmt   = $item->tax_amount;
                        $after    = $before + $vatAmt;
                        $totalBeforeTax += $before;
                        $totalTax       += $vatAmt;
                        $totalAfterTax  += $after;
                        $unitRow  = $item->unit;
                        $unitName = '-';
                        if ($unitRow) {
                            if ($unitRow->smallUnit) {
                                $unitName = $unitRow->smallUnit->name_ar;
                            } elseif ($unitRow->largeUnit) {
                                $unitName = $unitRow->largeUnit->name_ar;
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $name }}</td>
                        <td>{{ $unitName }}</td>
                        <td>{{ number_format($qty,2) }}</td>
                        <td>{{ number_format($price,2) }}</td>
                        <td>{{ number_format($vatAmt,2) }}</td>
                        <td>{{ number_format($before,2) }}</td>
                        <td>{{ number_format($after,2) }}</td>
                        <td>{{ $item->notes ?? '-' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="text-end">الإجماليات:</td>
                    <td>{{ number_format($totalTax,2) }}</td>
                    <td>{{ number_format($totalBeforeTax,2) }}</td>
                    <td>{{ number_format($totalAfterTax,2) }}</td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td colspan="6" class="text-end">المبلغ كتابيًا:</td>
                    <td colspan="3">
                        {{ \App\Helpers\NumberHelper::spellOutArabic($totalAfterTax) }} جنيه مصري
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- لجنة الفحص والاستلام --}}
        <h5 class="section-title">لجنة الفحص والاستلام</h5>
        <table class="items-table">
            <thead>
                <tr>
                    <th>م</th>
                    <th>اسم الموظف</th>
                    <th>القسم</th>
                    <th>المنصب الوظيفي</th>
                    <th>رئيس اللجنة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_committees as $j => $cm)
                    @php
                        $empName  = $cm->employee ? $cm->employee->full_name : '-';
                        $deptName = $cm->department ? $cm->department->name_ar : '-';
                        $isChair  = $cm->is_chair ? 'نعم' : 'لا';
                    @endphp
                    <tr>
                        <td>{{ $j+1 }}</td>
                        <td>{{ $empName }}</td>
                        <td>{{ $deptName }}</td>
                        <td>{{ $cm->job_position ?? '-' }}</td>
                        <td>{{ $isChair }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- توقيعات --}}
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>مقدم الطلب<div class="signature-line"></div></td>
                    <td>مسؤول المالية<div class="signature-line"></div></td>
                    <td>المدير العام<div class="signature-line"></div></td>
                </tr>
            </table>
        </div>
    </div>
@endsection
