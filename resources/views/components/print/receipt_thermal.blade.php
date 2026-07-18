<div style="font-family: 'Arial', sans-serif; width: 320px; margin: auto; border: 1px solid #e0e0e0; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 15px; background: linear-gradient(to bottom, #f9f9f9, #fff); border-radius: 8px;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 15px; border-bottom: 2px dashed #0078d7; padding-bottom: 10px;">
        <img src="{{ asset('assets/images/logo.png') }}" style="height: 60px; margin-bottom: 5px; filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.2));">
        <h2 style="margin: 5px 0; color: #0078d7; font-size: 18px;">مستشفى دنيا العيون</h2>
        <p style="margin: 0; font-size: 14px; color: #555;">إيصال دفع نقدي</p>
        <div style="background: #0078d7; height: 3px; width: 50px; margin: 5px auto;"></div>
    </div>

    <!-- Receipt Details -->
    <div style="margin-bottom: 15px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #ddd; padding-bottom: 5px;">
            <span style="font-weight: bold; color: #333;">رقم الإيصال:</span>
            <span style="color: #0078d7;">{{ $receipt->id }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #ddd; padding-bottom: 5px;">
            <span style="font-weight: bold; color: #333;">تاريخ الطباعة:</span>
            <span>{{ now()->format('Y-m-d H:i') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #ddd; padding-bottom: 5px;">
            <span style="font-weight: bold; color: #333;">اسم المريض:</span>
            <span style="color: #0078d7;">{{ $receipt->invoice->patient?->full_name }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #ddd; padding-bottom: 5px;">
            <span style="font-weight: bold; color: #333;">رقم الفاتورة:</span>
            <span>{{ $receipt->invoice?->number }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #ddd; padding-bottom: 5px;">
            <span style="font-weight: bold; color: #333;">نوع الدفع:</span>
            <span>{{ __('lockers_trans.payment_methods2.' . $receipt->pay_type) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dotted #ddd; padding-bottom: 5px;">
            <span style="font-weight: bold; color: #333;">الخزنة:</span>
            <span>{{ $receipt->locker?->name_ar }}</span>
        </div>
    </div>

    <!-- Payment Summary -->
    <div style="background: #f5f9ff; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px dashed #0078d7;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: bold; color: #333;">المبلغ المدفوع:</span>
            <span style="font-weight: bold; color: #28a745; font-size: 14px;">{{ number_format($receipt->total_paid, 2) }} {{ $receipt->invoice->currency }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="font-weight: bold; color: #333;">المتبقي:</span>
            <span style="font-weight: bold; color: #dc3545; font-size: 14px;">{{ number_format($receipt->total_remainder, 2) }} {{ $receipt->invoice->currency }}</span>
        </div>
    </div>

    <!-- Notes -->
    <div style="margin-bottom: 15px; background: #fff8e6; padding: 8px; border-radius: 5px; border-left: 3px solid #ffc107;">
        <div style="font-weight: bold; margin-bottom: 5px; color: #333;">ملاحظات:</div>
        <div style="color: #666;">{{ $receipt->notes ?? 'لا توجد ملاحظات' }}</div>
    </div>

    <!-- Footer -->
    <div style="text-align: center; border-top: 2px dashed #0078d7; padding-top: 10px;">
        <div style="display: flex; justify-content: space-around; margin: 15px 0;">
            <div>
                <p style="margin: 5px 0; font-size: 12px;">توقيع المحصل</p>
                <div style="border-top: 1px solid #333; width: 100px; margin: 0 auto;"></div>
            </div>
            <div>
                <p style="margin: 5px 0; font-size: 12px;">توقيع المستلم</p>
                <div style="border-top: 1px solid #333; width: 100px; margin: 0 auto;"></div>
            </div>
        </div>
        <p style="margin: 5px 0; color: #0078d7; font-size: 12px;">شكراً لثقتكم بمستشفى دنيا العيون</p>
        <p style="margin: 0; font-size: 10px; color: #888;">هذا الإيصال صالح كسند قانوني</p>
    </div>
    
    <!-- Watermark -->
    <div style="position: absolute; opacity: 0.1; font-size: 80px; transform: rotate(-30deg); top: 40%; left: 10%; z-index: -1; color: #0078d7;">
        دنيا العيون
    </div>
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

    </style>
<script>
    window.print();
</script>