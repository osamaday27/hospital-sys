@extends('layouts.master')

@section('title', $title ?? 'طباعة عرض السعر')

@section('content')
    @php
        // المتغيرات لجمع الإجماليات:
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
            .print-btn { display: none !important; }
        }
    .hospital-name { font-size: 22px; font-weight: bold; color: #003366; margin-bottom: 5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);}

        .report-container {
            max-width: 100%; margin: 0 auto; background-color: #fff; padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08); border-radius: 8px;
        }
        .report-header {
            text-align: center; margin-bottom: 25px; padding-bottom: 15px;
            border-bottom: 3px double #444;
        }
        .company-info {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px; margin-bottom: 15px;
        }
        .company-logo {
            height: 80px; width: auto; object-fit: contain;
        }
        .company-text {
            text-align: center; flex-grow: 1;
        }
        .company-name {
            font-size: 22px; font-weight: bold; color: #003366;
            margin-bottom: 5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .report-title {
            font-size: 24px; font-weight: bold; color: #003366;
            margin: 20px 0 15px; padding-bottom: 10px;
            border-bottom: 2px solid #003366; display: inline-block;
        }
        .header-table, .items-table {
            width: 100%; border-collapse: collapse; margin: 20px 0;
            background-color: #f8f9fa; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .header-table {
            background-color: #f1f7ff;
        }
        .header-table td, .items-table td, .items-table th {
            border: 1px solid #dee2e6; padding: 12px; font-size: 14px; text-align: center;
        }
        .header-table td {
            font-weight: 500;
        }
        .items-table th {
            background-color: #003366; color: white; font-weight: bold; font-size: 15px; padding: 12px;
        }
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .items-table tr:hover {
            background-color: #e9ecef;
        }
        .total-row {
            font-weight: bold; background-color: #e7f1ff !important;
        }
        .total-row td {
            font-size: 15px;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-table {
            width: 100%; border-collapse: collapse; margin-top: 30px;
        }
        .signature-table td {
            border: 1px solid #dee2e6; padding: 50px 10px 15px; text-align: center;
            position: relative; background-color: #f8f9fa;
        }
        .signature-line {
            position: absolute; bottom: 30px; left: 10%; width: 80%;
            height: 1px; background-color: #666;
        }
        .signature-table td div {
            font-size: 14px; color: #555;
        }
        .footer-notes {
            margin-top: 30px; padding: 15px; border-top: 1px dashed #999;
            font-size: 13px; color: #666; text-align: right; background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer-notes p {
            margin-bottom: 8px;
        }
        .footer-notes strong {
            color: #003366;
        }
        .barcode {
            font-family: 'Libre Barcode 128', cursive; font-size: 30px; margin-top: 10px; letter-spacing: 2px;
        }
        .print-info {
            display: flex; flex-direction: column; align-items: flex-start; font-size: 13px; color: #555;
        }
        .print-info i {
            margin-left: 5px;
        }
        .section-title {
            font-size: 18px; font-weight: bold; color: #003366; margin: 25px 0 15px;
            text-align: center; position: relative;
        }
        .section-title:after {
            content: ""; display: block; width: 100px; height: 2px; background: #003366; margin: 10px auto;
        }
        .print-btn {
            transition: all 0.3s; border-radius: 30px;
        }
        .print-btn:hover {
            transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>

    <div class="report-container">
        <div class="report-header">
            <div class="company-info">
                <img src="{{ asset('assets/images/logo.png') }}" alt="شعار المؤسسة" class="company-logo">
                <div class="company-text">
                <div class="hospital-name">مستشفي دنيا العيون</div>
                    <div class="company-department">إدارة المشتريات</div>
                </div>
                <div class="print-info">
                    <div><i class="ri-time-line"></i> تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                    <div class="barcode">*{{ $movement_summary->quotation_number ?? '-' }}*</div>
                </div>
            </div>
            <div class="report-title">{{ $title ?? 'عرض السعر' }}</div>
        </div>

        {{-- بيانات ملخص عرض السعر --}}
        <table class="header-table">
            <tr>
                <td width="33%"><strong>رقم عرض السعر:</strong> {{ $movement_summary->quotation_number ?? '-' }}</td>
                <td width="33%"><strong>تاريخ البداية:</strong> {{ $movement_summary->start_date ?? '-' }}</td>
                <td width="34%"><strong>تاريخ النهاية:</strong> {{ $movement_summary->end_date ?? '-' }}</td>
            </tr>
            <tr>
                <td>
                    <strong>المورد:</strong>
                    {{ optional($movement_summary->supplier)->name_ar ?? '-' }}
                    @if(optional($movement_summary->supplier)->name_en)
                        / {{ optional($movement_summary->supplier)->name_en }}
                    @endif
                </td>
                <td><strong>مكان التسليم:</strong> {{ $movement_summary->delivery_location ?? '-' }}</td>
                <td><strong>طريقة الدفع:</strong>
{{ $movement_summary->payment_method === 'cash'
    ? __('purchases.payment_cash')
    : (
        $movement_summary->payment_method === 'bank_transfer'
            ? __('purchases.payment_bank_transfer')
            : __('purchases.payment_credit')
      )
}}
                </td>
            </tr>
            <tr>
                <td colspan="2"><strong>شروط الدفع:</strong> {{ $movement_summary->payment_terms ?? '-' }}</td>
                <td><strong>ملاحظات:</strong> {{ $movement_summary->notes ?? '-' }}</td>
            </tr>
        </table>

        {{-- تفاصيل البنود --}}
        <h5 class="section-title">بنود عرض السعر</h5>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">م</th>
                    <th width="20%">اسم الصنف</th>
                    <th width="10%">الوحدة</th>
                    <th width="10%">الكمية</th>
                    <th width="10%">سعر الوحدة</th>
                    <th width="10%">قيمة الضريبة</th>
                    <th width="15%">الإجمالي قبل الضريبة</th>
                    <th width="15%">الإجمالي بعد الضريبة</th>
                    <th width="5%">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movement_items as $index => $item)
                    @php
                        $product = $item->product;

                        // نجلُب "نسبة الضريبة" من المنتج نفسه (vat و vat_value)
                        $vatRate = ($product->vat == 1) ? ($product->vat_value ?? 0) : 0;

                        $qty    = $item->quantity;
                        $price  = $item->price;
                        // نحسب "الإجمالي قبل الضريبة" و"قيمة الضريبة" و"الإجمالي بعد الضريبة" هنا:
                        $before    = $qty * $price;
                        $taxAmount = $before * ($vatRate / 100);
                        $after     = $before + $taxAmount;

                        // نجمعها في المتغيرات الإجمالية
                        $totalBeforeTax += $before;
                        $totalTax       += $taxAmount;
                        $totalAfterTax  += $after;

                        // نحصل على اسم الوحدة (بناءً على العلاقة الموجودة في Item)
                        $unitRow = $item->unit;
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
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $product->name_ar ?? '-' }}
                            @if($product->name_en)
                                / {{ $product->name_en }}
                            @endif
                        </td>
                        <td>{{ $unitName }}</td>
                        <td>{{ number_format($qty, 2) }}</td>
                        <td>{{ number_format($price, 2) }}</td>
                        <td>{{ number_format($taxAmount, 2) }}</td>
                        <td>{{ number_format($before, 2) }}</td>
                        <td>{{ number_format($after, 2) }}</td>
                        <td>{{ $item->notes ?? '-' }}</td>
                    </tr>
                @endforeach

                {{-- صف الإجماليات: --}}
                <tr class="total-row">
                    {{-- الصفوف 1 إلى 5 مختفية (تركت أعمدة الـ 1–5 فارغة لاستخدام colspan) --}}
                    <td colspan="5" class="text-end"><strong>الإجماليات:</strong></td>
                    {{-- عمود 6: قيمة الضريبة الكلية --}}
                    <td><strong>{{ number_format($totalTax, 2) }}</strong></td>
                    {{-- عمود 7: الإجمالي قبل الضريبة --}}
                    <td><strong>{{ number_format($totalBeforeTax, 2) }}</strong></td>
                    {{-- عمود 8: الإجمالي بعد الضريبة --}}
                    <td><strong>{{ number_format($totalAfterTax, 2) }}</strong></td>
                    {{-- عمود 9 (ملاحظات) يبقى فارغًا --}}
                    <td></td>
                </tr>

                {{-- صف المبلغ كتابيًا --}}
                <tr class="total-row">
                    <td colspan="6" class="text-end"><strong>المبلغ كتابيًا:</strong></td>
                    <td colspan="3" class="text-start">
                        <strong>
                            {{ \App\Helpers\NumberHelper::spellOutArabic($totalAfterTax) }} جنيه مصري
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- قسم التوقيعات --}}
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="33%">
                        المقدم:
                        <div class="signature-line"></div>
                        <div style="margin-top: 25px;">الاسم: .........................</div>
                        <div>التاريخ: .........................</div>
                    </td>
                    <td width="33%">
                        المسؤول المالي:
                        <div class="signature-line"></div>
                        <div style="margin-top: 25px;">الاسم: .........................</div>
                        <div>التاريخ: .........................</div>
                    </td>
                    <td width="34%">
                        المدير:
                        <div class="signature-line"></div>
                        <div style="margin-top: 25px;">الاسم: .........................</div>
                        <div>التاريخ: .........................</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ملاحظات إضافية --}}
        <div class="footer-notes">
            <p><strong>ملاحظات مهمة:</strong></p>
            <p>1. يرجى التحقق من صحة البيانات قبل اعتماد العرض.</p>
            <p>2. مدة سريان العرض من تاريخ البداية حتى تاريخ النهاية المذكورين أعلاه.</p>
            <p>3. في حالة وجود أي استفسار، يرجى التواصل مع الإدارة.</p>
        </div>
    </div>
@endsection
