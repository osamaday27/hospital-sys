<div class="no-print mb-4 text-center">
    <button onclick="window.print()" class="btn btn-lg btn-primary px-5 shadow">
        <i class="ri-printer-line me-2"></i> طباعة التقرير
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
                <div><i class="ri-time-line me-1"></i> تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</div>
                <div class="barcode">*{{ $request->request_number}}*</div>
            </div>
        </div>

        <div class="report-title">{{ $title }}</div>
    </div>


    <table class="header-table">
        <tr>
            <td><strong>رقم الطلب:</strong> {{ $request->request_number }}</td>
            <td><strong>تاريخ الطلب:</strong> {{ $request->request_date }}</td>
            <td><strong>الموظف:</strong> {{ $request->employee?->full_name }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>المخزن:</strong> {{ $request->store?->name_ar }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>سبب الطلب:</strong> {{ $request->reason ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>م</th>
                <th>كود الصنف</th>
                <th>اسم الصنف</th>
                <th>الوحدة</th>
                <th>الكمية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->details as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product?->product_code ?? '-' }}</td>
                    <td class="text-right">{{ $item->product?->name_ar ?? '-' }}</td>
                    <td>{{ $item->unit?->name_ar ?? '-' }}</td>
                    <td>{{ number_format($item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>مدير القسم<div class="signature-line"></div><div>الاسم: ...............</div></td>
                <td>الموظف المستلم<div class="signature-line"></div><div>الاسم: ...............</div></td>
                <td>أمين المخزن<div class="signature-line"></div><div>الاسم: ...............</div></td>
            </tr>
        </table>
    </div>

    <div class="footer-notes">
        <p><strong>ملاحظات:</strong></p>
        <p>يرجى مراجعة الأصناف المستلمة والتأكد من مطابقتها للطلب.</p>
    </div>
</div>

@include('components.print.styles')
