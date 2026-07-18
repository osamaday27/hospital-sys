<div class="no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow print-btn">
        <i class="ri-printer-line me-2"></i> طباعة التقرير
    </button>
</div>

<style>
    @media print {
        @page { size: A4 landscape; margin: 10mm 15mm; }
        body { font-family: 'Arial', sans-serif; direction: rtl; color: #000; line-height: 1.5; background-color: #fff !important;}
        .no-print { display: none !important; }
        .page-break { page-break-after: always; }
        .print-btn { display: none !important; }
    }

    .report-container { max-width: 100%; margin: 0 auto; background-color: #fff; padding: 20px; box-shadow: 0 0 15px rgba(0,0,0,0.08); border-radius: 8px; }
    .report-header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px double #444; }
    .hospital-info { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    .hospital-logo { height: 80px; width: auto; object-fit: contain; }
    .hospital-text { text-align: center; flex-grow: 1; }
    .hospital-name { font-size: 22px; font-weight: bold; color: #003366; margin-bottom: 5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);}
    .hospital-department { font-size: 16px; color: #555; font-weight: 500;}
    .report-title { font-size: 24px; font-weight: bold; color: #003366; margin: 20px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #003366; display: inline-block;}
    .header-table, .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f8f9fa; box-shadow: 0 2px 5px rgba(0,0,0,0.05);}
    .header-table { background-color: #f1f7ff;}
    .header-table td, .items-table td, .items-table th { border: 1px solid #dee2e6; padding: 12px; font-size: 14px; text-align: center;}
    .header-table td { font-weight: 500; }
    .items-table th { background-color: #003366; color: white; font-weight: bold; font-size: 15px; padding: 12px;}
    .items-table tr:nth-child(even) { background-color: #f8f9fa; }
    .items-table tr:hover { background-color: #e9ecef; }
    .total-row { font-weight: bold; background-color: #e7f1ff !important;}
    .total-row td { font-size: 15px;}
    .signature-section { margin-top: 40px;}
    .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px;}
    .signature-table td { border: 1px solid #dee2e6; padding: 50px 10px 15px; text-align: center; position: relative; background-color: #f8f9fa;}
    .signature-line { position: absolute; bottom: 30px; left: 10%; width: 80%; height: 1px; background-color: #666;}
    .signature-table td div { font-size: 14px; color: #555;}
    .footer-notes { margin-top: 30px; padding: 15px; border-top: 1px dashed #999; font-size: 13px; color: #666; text-align: right; background-color: #f8f9fa; border-radius: 5px;}
    .footer-notes p { margin-bottom: 8px; }
    .footer-notes strong { color: #003366; }
    .barcode { font-family: 'Libre Barcode 128', cursive; font-size: 30px; margin-top: 10px; letter-spacing: 2px;}
    .print-info { display: flex; flex-direction: column; align-items: flex-start; font-size: 13px; color: #555;}
    .print-info i { margin-left: 5px;}
    .section-title { font-size: 18px; font-weight: bold; color: #003366; margin: 25px 0 15px; text-align: center; position: relative;}
    .section-title:after { content: ""; display: block; width: 100px; height: 2px; background: #003366; margin: 10px auto;}
    .print-btn { transition: all 0.3s; border-radius: 30px;}
    .print-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
</style>

<div class="report-container">
    <div class="report-header">
        <div class="hospital-info">
            <img src="{{ asset('assets/images/logo.png') }}" alt="شعار المستشفى" class="hospital-logo">
            <div class="hospital-text">
                <div class="hospital-name">مستشفي دنيا العيون</div>
                <div class="hospital-department">إدارة المخازن والصيدلة</div>
            </div>
            <div class="print-info">
                <div><i class="ri-time-line"></i> تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                <div class="barcode">*{{ $movement_summary->movement_number ?? '-' }}*</div>
            </div>
        </div>
        <div class="report-title">{{ $title ?? 'طلب شراء' }}</div>
    </div>

    {{-- بيانات الطلب --}}
    <table class="header-table">
        <tr>
            <td width="33%"><strong>رقم الطلب:</strong> {{ $movement_summary->movement_number ?? '-' }}</td>
            <td width="33%"><strong>تاريخ الطلب:</strong> {{ $movement_summary->movement_date ?? '-' }}</td>
            <td width="34%"><strong>المخزن:</strong> {{ $movement_summary->store->name_ar ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>القسم:</strong> {{ $movement_summary->department->name_ar ?? '-' }}</td>
            <td><strong>المورد المقترح:</strong> {{ $movement_summary->supplier->name_ar ?? '-' }}</td>
            <td><strong>سبب التوريد:</strong> {{ $movement_summary->notes ?? '-' }}</td>
        </tr>
    </table>

    {{-- تفاصيل الطلب --}}
    <h5 class="section-title">تفاصيل الطلب</h5>
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">م</th>
                <th width="10%">كود الصنف</th>
                <th width="20%">اسم الصنف</th>
                <th width="10%">الكمية المطلوبة</th>
                <th width="10%">الوحدة</th>
                <th width="10%">سعر الوحدة</th>
                <th width="15%">الإجمالي</th>
                <th width="20%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; $totalItems = 0; @endphp
            @foreach($movement_items as $index => $item)
                @php
                    $product = $item->product;
                    // جلب الوحدة مع العلاقتين
                    $unitRow = \App\Models\supply_chain\productunit::with(['smallUnit:id,name_ar,name_en', 'largeUnit:id,name_ar,name_en'])->find($item->unit_id);
                    $unitName = '-';
                    if($unitRow){
                        $unitName = $unitRow->smallUnit
                            ? ($unitRow->smallUnit->name_ar . ' / ' . $unitRow->smallUnit->name_en)
                            : ($unitRow->largeUnit
                                ? ($unitRow->largeUnit->name_ar . ' / ' . $unitRow->largeUnit->name_en)
                                : '-');
                    }
                    $price = $item->last_price ?? 0;
                    $subtotal = $price * $item->requested_qty;
                    $total += $subtotal;
                    $totalItems++;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->system_code ?? '-' }}</td>
                    <td>{{ $product->name_ar ?? '-' }}{{ $product->name_en ? ' / ' . $product->name_en : '' }}</td>
                    <td>{{ number_format($item->requested_qty) }}</td>
                    <td>{{ $unitName }}</td>
                    <td>{{ number_format($price, 2) }}</td>
                    <td>{{ number_format($subtotal, 2) }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="3" class="text-end"><strong>إجمالي عدد الأصناف:</strong></td>
                <td colspan="1"><strong>{{ $totalItems }}</strong></td>
                <td colspan="2" class="text-end"><strong>الإجمالي:</strong></td>
                <td colspan="2">                    <strong>{{ number_format($total, 2) }} جنيه</strong><br>
                    <small class="text-muted">
                        {{ \App\Helpers\NumberHelper::spellOutArabic($total) }} جنيه مصري

                    </small></td>
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
        <p>1. يرجى مراجعة الطلب قبل التنفيذ.</p>
        <p>2. جميع الكميات المطلوبة يجب أن تتطابق مع التفاصيل أعلاه.</p>
        <p>3. في حالة وجود أي اختلاف، يرجى التواصل مع إدارة المخازن.</p>
    </div>
</div>
