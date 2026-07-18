@extends('layouts.master')

@section('content')

@if(request()->get('type') === 'thermal')
    @include('finance.lockers.program.receipt_thermal', ['receipt' => $receipt, 'title' => $title])
@else
    <!-- أدوات الطباعة -->
    <div class="no-print mb-4 text-center" dir="rtl">
        <a href="{{ route('finance.invoice_payment_printthermal', ['invoice_id' => $receipt->invoices_id]) }}" target="_blank" class="btn btn-outline-secondary px-4 me-3">
            <i class="ri-printer-line me-1"></i> {{ __('lockers_trans.print_thermal_receipt') }}
        </a>
        <button onclick="window.print()" class="btn btn-success btn-lg shadow">
            <i class="ri-printer-fill me-2"></i> {{ __('lockers_trans.print_normal_receipt') }}
        </button>
    </div>

    @php
        /* ===== ترجمة ببديل افتراضي عند فقدان المفتاح ===== */
        $t = function(string $key, string $fallback) {
            $v = __($key);
            return ($v === $key) ? $fallback : $v;
        };

        /* ===== مساعدات قراءة أرقام من أكثر من مفتاح ===== */
        $firstNum = function($model, array $keys) {
            foreach ($keys as $k) {
                $val = data_get($model, $k);
                if ($val !== null && $val !== '' && is_numeric($val)) {
                    return (float)$val;
                }
            }
            return null;
        };

        $inv      = $receipt->invoice;
        $currency = $inv->currency ?? 'EGP';

        /* ===== التقاط الخصم ===== */
        $discountValue = (float)($firstNum($inv, ['discount_value','total_discount','discount','discountamount','discount_amt'])
                        ?? $firstNum($receipt, ['discount_value','total_discount','discount','discountamount','discount_amt'])
                        ?? 0);

        $discountPercent = $firstNum($inv, ['discount_percent','discount_percentage','discount_rate'])
                        ?? $firstNum($receipt, ['discount_percent','discount_percentage','discount_rate']);

        /* ===== إجماليات الهيدر (إن وُجدت) ===== */
        $headerSubtotal = $firstNum($inv, [
            'subtotal','total_before_discount','gross_total','gross','total_amount',
            'amount_before_discount','total_pricing','price_before_discount','total_services',
            'total_invoice','invoice_total','total_before_disc','total_without_discount'
        ]) ?? $firstNum($receipt, [
            'subtotal','total_before_discount','gross_total','gross','total_amount',
            'amount_before_discount','total_pricing','price_before_discount','total_services',
            'total_invoice','invoice_total','total_before_disc','total_without_discount'
        ]);

        $headerNet = $firstNum($inv, [
            'net_after_discount','net_total','total_after_discount','grand_total','net','total_price','amount_net'
        ]) ?? $firstNum($receipt, [
            'net_after_discount','net_total','total_after_discount','grand_total','net','total_price','amount_net'
        ]);

        /* ===== إجمالي البنود (لو موجودة) ===== */
        $servicesSubtotal = 0.0;
        if(isset($inv->services) && $inv->services && $inv->services->count() > 0){
            foreach ($inv->services as $r) {
                $price     = (float)($r->total_pricing ?? $r->price ?? $r->unit_price ?? 0);
                $qty       = (float)($r->quantity ?? 1);
                $lineTotal = (float)($r->line_total ?? $r->total ?? $price * $qty);
                $servicesSubtotal += $lineTotal;
            }
        }

        /* ===== تحديد Subtotal & Net فعليين ===== */
        $subtotal = 0.0;
        $netAfterDiscount = 0.0;

        if(!is_null($headerSubtotal) && !is_null($headerNet)){
            $subtotal = (float)$headerSubtotal;
            $netAfterDiscount = (float)$headerNet;
            if($discountValue <= 0 && $subtotal > $netAfterDiscount){
                $discountValue = $subtotal - $netAfterDiscount;
            }
        } elseif(!is_null($headerNet) && is_null($headerSubtotal)) {
            if($discountValue > 0){
                $netAfterDiscount = (float)$headerNet;
                $subtotal = $netAfterDiscount + $discountValue;
            } elseif(!is_null($discountPercent) && $discountPercent > 0 && $discountPercent < 100){
                $netAfterDiscount = (float)$headerNet;
                $subtotal = $netAfterDiscount / (1 - ($discountPercent/100));
                $discountValue = $subtotal - $netAfterDiscount;
            } else {
                $netAfterDiscount = (float)$headerNet;
                $subtotal = $netAfterDiscount;
            }
        } elseif(is_null($headerNet) && !is_null($headerSubtotal)) {
            $subtotal = (float)$headerSubtotal;
            if($discountValue > 0){
                $netAfterDiscount = $subtotal - $discountValue;
            } elseif(!is_null($discountPercent) && $discountPercent > 0 && $discountPercent < 100){
                $discountValue = $subtotal * ($discountPercent/100);
                $netAfterDiscount = $subtotal - $discountValue;
            } else {
                $netAfterDiscount = $subtotal;
            }
        } else {
            if($servicesSubtotal > 0){
                $subtotal = $servicesSubtotal;
                if($discountValue <= 0 && !is_null($discountPercent) && $discountPercent > 0 && $discountPercent < 100){
                    $discountValue = $subtotal * ($discountPercent/100);
                }
                $netAfterDiscount = max(0, $subtotal - $discountValue);
            }
        }

        /* ===== في حالة لا توجد بنود ولا هيدر: نقدّر من المدفوع + المتبقي ===== */
        $paidAmountDB   = (float)($receipt->total_paid ?? 0);
        $remainderDB    = (float)($receipt->total_remainder ?? 0);

        if($netAfterDiscount <= 0 && ($paidAmountDB > 0 || $remainderDB > 0)){
            $netAfterDiscount = $paidAmountDB + $remainderDB;
        }
        if($subtotal <= 0 && $netAfterDiscount >= 0){
            $subtotal = $netAfterDiscount + max(0,$discountValue);
        }

        $subtotal         = max(0, (float)$subtotal);
        $discountValue    = max(0, (float)$discountValue);
        $netAfterDiscount = max(0, (float)$netAfterDiscount);

        /* ===== المتبقي المحسوب ===== */
        $paidAmount = $paidAmountDB;
        $computedRemainder = max(0, $netAfterDiscount - $paidAmount);

        /* ===== تنسيق تاريخ الحجز ليكون تاريخ فقط ===== */
        $bookingDateOnly = null;
        if(optional($inv->reservations)->date_reservations){
            try {
                $bookingDateOnly = \Carbon\Carbon::parse($inv->reservations->date_reservations)->format('Y-m-d');
            } catch (\Throwable $e) {
                $bookingDateOnly = $inv->reservations->date_reservations; // احتياط
            }
        }
    @endphp

    <div class="report-container" dir="rtl"
         style="max-width: 980px; margin: 0 auto; background: #fff; padding: 28px; border-radius: 14px; box-shadow: 0 14px 38px rgba(0,0,0,0.08);">

        <!-- شريط علوي -->
        <div style="height:6px; border-radius: 8px; background: linear-gradient(90deg,#0aa2c0,#0d6efd); margin-bottom:18px;"></div>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4"
             style="border-bottom: 3px solid #0d6efd; padding-bottom: 16px;">
            <img src="{{ asset('assets/images/his_logo.png') }}" alt="{{ $t('lockers_trans.hospital_logo_alt','شعار المستشفى') }}" style="height: 78px;">
            <div style="text-align: center; flex-grow: 1;">
                <div style="font-size: 28px; font-weight: 800; color: #0d6efd; letter-spacing:.2px">{{ $t('lockers_trans.hospital_name','مستشفى دنيا العيون') }}</div>
                <div style="font-size: 14px; color: #6c757d; margin-top:3px">{{ $t('lockers_trans.financial_department','الإدارة المالية') }}</div>
                <div style="margin-top: 10px; font-size: 20px; font-weight: 800; color: #111;">{{ $title }}</div>
            </div>
            <div class="text-end" style="font-size: 13px; line-height: 1.9;">
                <div><i class="ri-time-line me-1"></i> {{ $t('lockers_trans.print_date','تاريخ الطباعة') }}: <span class="num">{{ now()->format('Y-m-d H:i') }}</span></div>
                <div style="font-weight: 700; color: #0d6efd;">
                    {{ $t('lockers_trans.receipt_number','رقم الإيصال') }}: <span class="num">{{ $receipt->id }}</span>
                </div>
            </div>
        </div>

        <!-- Patient & Invoice -->
        <div class="mb-4">
            <table class="table table-bordered text-sm align-middle m-0 pretty-table">
                <tbody>
                    <tr>
                        <td class="key"><strong>{{ $t('lockers_trans.patient_name','اسم المريض') }}</strong></td>
                        <td class="val">{{ $inv->patient?->full_name }}</td>
                        <td class="key"><strong>{{ $t('lockers_trans.invoice_number','رقم الفاتورة') }}</strong></td>
                        <td class="val"><span class="num">{{ $inv?->number }}</span></td>
                    </tr>
                    <tr>
                        <td class="key"><strong>{{ $t('lockers_trans.payment_type','طريقة الدفع') }}</strong></td>
                        <td class="val">{{ __('lockers_trans.payment_methods2.' . $receipt->pay_type) }}</td>
                        <td class="key"><strong>{{ $t('lockers_trans.locker','الخزينة') }}</strong></td>
                        <td class="val">{{ $receipt->locker?->name_ar }}</td>
                    </tr>
                    <tr>
                        <td class="key"><strong>{{ $t('lockers_trans.file_number','الرقم الطبي') }}</strong></td>
                        <td class="val"><span class="num">{{ $inv->patient?->medical_number ?? '-' }}</span></td>
                        <td class="key"><strong>{{ $t('lockers_trans.createby','تم الإنشاء بواسطة') }}</strong></td>
                        <td class="val">{{ $inv->user?->name ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Booking Info -->
        @if($inv->reservations)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header fw-bold brand-gradient">
                {{ $t('lockers_trans.booking_details','تفاصيل الحجز') }}
            </div>
            <div class="card-body row text-sm g-2">
                <div class="col-md-3"><strong>{{ $t('lockers_trans.doctor','الطبيب') }}:</strong> {{ $inv->reservations?->doctor?->full_name }}</div>
                <div class="col-md-3"><strong>{{ $t('lockers_trans.clinic','العيادة') }}:</strong> {{ $inv->reservations?->clinic?->name_ar }}</div>
                <div class="col-md-3"><strong>{{ $t('lockers_trans.reservation_service','نوع الحجز') }}:</strong> {{ $inv->reservations?->service?->name ?? '-' }}</div>
                <!-- التاريخ (تاريخ فقط) -->
                <div class="col-md-3"><strong>{{ $t('lockers_trans.booking_date','تاريخ الحجز') }}:</strong> <span class="num">{{ $bookingDateOnly }}</span></div>
            </div>
        </div>
        @endif

        <!-- ====== إعادة تصميم: "ملخص الفاتورة" + "ملخص الدفع" جنب بعض ====== -->
        <div class="row g-3 mb-4">
            <!-- ملخص الفاتورة -->
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header fw-bold" style="background:#0d6efd; color:#fff; border-radius: 8px 8px 0 0;">
                        {{ $t('lockers_trans.invoice_summary','ملخص الفاتورة') }}
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered m-0 text-center align-middle pretty-table">
                            <tbody>
                                <tr>
                                    <td class="key fw-bold">{{ $t('lockers_trans.subtotal_before_discount','المجموع قبل الخصم') }}</td>
                                    <td class="val num">{{ number_format($subtotal, 2) }} {{ $currency }}</td>
                                </tr>
                                @if( $discountValue > 0 || !is_null($discountPercent) )
                                    <tr>
                                        <td class="key fw-bold">
                                            {{ $t('lockers_trans.discount','الخصم') }}
                                            @if(!is_null($discountPercent))
                                                <span class="text-muted">({{ rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') }}%)</span>
                                            @endif
                                        </td>
                                        <td class="val num text-danger fw-bold">- {{ number_format($discountValue, 2) }} {{ $currency }}</td>
                                    </tr>
                                @endif
                                <tr class="table-light big-row">
                                    <td class="key fw-bold">{{ $t('lockers_trans.net_after_discount','الصافي بعد الخصم') }}</td>
                                    <td class="val num fw-bold" style="font-size:20px">{{ number_format($netAfterDiscount, 2) }} {{ $currency }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ملخص الدفع -->
            <div class="col-12 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header fw-bold" style="background:#e9f7ef; color:#198754; border-radius: 8px 8px 0 0;">
                        {{ $t('lockers_trans.payment_summary','ملخص الدفع') }}
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered text-center align-middle pretty-table m-0">
                            <thead>
                                <tr class="table-success">
                                    <th style="width:50%">{{ $t('lockers_trans.paid_amount','المبلغ المدفوع') }}</th>
                                    <th style="width:50%">{{ $t('lockers_trans.remaining_amount','المبلغ المتبقي') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="big-row">
                                    <td class="text-success fw-bold num" style="font-size:22px">{{ number_format($paidAmount, 2) }} {{ $currency }}</td>
                                    <td class="text-danger fw-bold num" style="font-size:22px">{{ number_format($computedRemainder, 2) }} {{ $currency }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- ====== نهاية إعادة التصميم ====== -->

        <!-- Notes -->
        <div class="alert alert-warning m-0" style="border-radius:10px">
            <strong>{{ $t('lockers_trans.notes','ملاحظات') }}:</strong>
            {{ $receipt->notes ?? $t('lockers_trans.no_notes','لا توجد ملاحظات') }}
        </div>

        <!-- Footer -->
        <div class="footer mt-4 pt-3 border-top text-center text-muted" style="font-size: 12px;">
            <div>{{ $t('lockers_trans.thanks_message','شكراً لثقتكم بنا') }}</div>
            <div>{{ $t('lockers_trans.legal_document_note','هذه الوثيقة صالحة للاستخدام القانوني.') }}</div>
        </div>
    </div>

    <style>
        :root{ --brand:#0d6efd; --brand2:#0aa2c0; }
        .brand-gradient{ background: linear-gradient(90deg,#0aa2c0,#0d6efd); color:#fff; border-radius:8px 8px 0 0; }
        [dir="rtl"] .table th, [dir="rtl"] .table td{ text-align: right; }
        .pretty-table thead th{ background:#f8f9fa; border-bottom:2px solid #e9ecef; font-weight:700; }
        .pretty-table tbody tr:nth-child(even) td{ background:#fcfdff; }
        .table td.key{ width: 230px; background:#f6f9ff; color:#0d6efd; }
        .table td.val{ background:#fff; }
        .big-row td{ padding-top:12px !important; padding-bottom:12px !important; }
        .num{ direction:ltr; unicode-bidi: plaintext; font-variant-numeric: tabular-nums; letter-spacing:.2px; }
        .card{ border-radius: 12px; overflow:hidden; }
        .card-header{ border-bottom:none; font-size:16px; }
        .alert-warning{ background-color:#fff3cd !important; color:#856404; border-color:#ffeeba; }
        /* طباعة */
        @page{ margin: 14mm 12mm; }
        @media print{
            body{ background:#fff !important; color:#000 !important; }
            .no-print{ display:none !important; }
            a[href]:after{ content:""; }
            .report-container{ box-shadow:none !important; border:none !important; padding:0 !important; }
            .card-header, .table-success, .table-light, .brand-gradient{
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .footer{ border-top:1px solid #e5e7eb !important; }
        }
    </style>
@endif

@endsection
