<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>{{ __('محضر تنفيذ عملية التخلص/الشطب') }} — {{ $row->request_no }}</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
      font-size: 14px;
      color: #1a1a1a;
      line-height: 1.6;
      background: #f5f5f5;
      padding: 20px;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      background: white;
      padding: 40px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
    }

    .page-header {
      border-bottom: 3px solid #28a745;
      padding-bottom: 20px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .page-header h1 {
      font-size: 24px;
      color: #28a745;
      font-weight: 600;
      flex: 1;
    }

    .request-number {
      background: #28a745;
      color: white;
      padding: 8px 20px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 16px;
    }

    .alert {
      background: #fff3cd;
      border: 1px solid #ffc107;
      border-right: 4px solid #ffc107;
      padding: 20px;
      border-radius: 6px;
      margin: 20px 0;
      color: #856404;
    }

    .alert-icon {
      font-size: 20px;
      margin-left: 10px;
    }

    .info-section {
      margin-bottom: 30px;
    }

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #28a745;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid #e0e0e0;
      display: flex;
      align-items: center;
    }

    .section-icon {
      margin-left: 10px;
      font-size: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
      background: white;
    }

    .info-table th {
      background: #f8f9fa;
      color: #333;
      font-weight: 600;
      width: 220px;
      padding: 12px 15px;
      border: 1px solid #dee2e6;
      text-align: right;
    }

    .info-table td {
      padding: 12px 15px;
      border: 1px solid #dee2e6;
      color: #495057;
    }

    .data-table {
      margin-top: 15px;
    }

    .data-table thead {
      background: #28a745;
      color: white;
    }

    .data-table th {
      padding: 12px 15px;
      font-weight: 600;
      text-align: center;
      border: 1px solid #1e7e34;
    }

    .data-table td {
      padding: 10px 15px;
      border: 1px solid #dee2e6;
      text-align: center;
    }

    .data-table tbody tr:nth-child(even) {
      background: #f8f9fa;
    }

    .data-table tbody tr:hover {
      background: #e9ecef;
    }

    .highlight-value {
      background: #d4edda;
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: 600;
      color: #155724;
    }

    .currency-value {
      font-weight: 600;
      color: #28a745;
    }

    .footer-section {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 2px solid #e0e0e0;
      text-align: center;
    }

    .print-button {
      background: #28a745;
      color: white;
      border: none;
      padding: 12px 30px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .print-button:hover {
      background: #1e7e34;
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .execution-badge {
      display: inline-flex;
      align-items: center;
      background: #d4edda;
      color: #155724;
      padding: 6px 12px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 13px;
    }

    .execution-badge::before {
      content: '✓';
      margin-left: 8px;
      font-size: 16px;
    }

    .method-badge {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 13px;
    }

    .method-sale {
      background: #cfe2ff;
      color: #084298;
    }

    .method-donation {
      background: #e7d4f8;
      color: #6f42c1;
    }

    .method-destruction {
      background: #f8d7da;
      color: #842029;
    }

    .empty-value {
      color: #999;
      font-style: italic;
    }

    .total-row {
      background: #e9f7ef !important;
      font-weight: 600;
    }

    .total-row td {
      border-top: 2px solid #28a745;
      color: #155724;
    }

    @media print {
      body {
        background: white;
        padding: 0;
      }

      .container {
        box-shadow: none;
        padding: 20px;
        border-radius: 0;
      }

      .no-print {
        display: none !important;
      }

      .data-table tbody tr:hover {
        background: inherit;
      }

      table {
        page-break-inside: avoid;
      }

      .info-section {
        page-break-inside: avoid;
      }

      .page-header {
        page-break-after: avoid;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- رأس الصفحة -->
  <div class="page-header">
    <h1>📋 {{ __('محضر تنفيذ عملية التخلص/الشطب') }}</h1>
    <div class="request-number">
      {{ $row->request_no }}
    </div>
  </div>

  @if(!$row->disposal)
    <!-- رسالة عدم التنفيذ -->
    <div class="alert">
      <span class="alert-icon">⚠️</span>
      <strong>{{ __('تنبيه:') }}</strong> {{ __('لم يتم تنفيذ عملية التخلص/الشطب بعد.') }}
    </div>
  @else
    <!-- معلومات التنفيذ الأساسية -->
    <div class="info-section">
      <h3 class="section-title">
        <span class="section-icon">📝</span>
        {{ __('بيانات التنفيذ') }}
      </h3>
      <table class="info-table">
        <tr>
          <th>{{ __('حالة التنفيذ') }}</th>
          <td><span class="execution-badge">{{ __('تم التنفيذ') }}</span></td>
        </tr>
        <tr>
          <th>{{ __('طريقة التخلّص') }}</th>
          <td>
            <span class="method-badge
              @if($row->method === 'sale') method-sale
              @elseif($row->method === 'donation') method-donation
              @else method-destruction
              @endif">
              {{ __('assets.disposal.method_'.$row->method) }}
            </span>
          </td>
        </tr>
        <tr>
          <th>{{ __('تاريخ التنفيذ') }}</th>
          <td><strong>{{ $row->disposal->executed_at?->format('Y-m-d H:i') ?? '-' }}</strong></td>
        </tr>

        @if($row->method === 'sale')
          <tr>
            <th>{{ __('اسم المشتري') }}</th>
            <td>{{ $row->disposal->buyer_name ?? '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('رقم فاتورة البيع') }}</th>
            <td>{{ $row->disposal->sale_invoice_no ?? '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('قيمة البيع') }}</th>
            <td><span class="currency-value">{{ number_format($row->disposal->sale_amount ?? 0, 2) }} {{ __('ج.م') }}</span></td>
          </tr>
        @elseif($row->method === 'donation')
          <tr>
            <th>{{ __('الجهة المتبرع لها') }}</th>
            <td>{{ $row->disposal->donee_name ?? '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('رقم مستند التبرع') }}</th>
            <td>{{ $row->disposal->donation_doc_no ?? '-' }}</td>
          </tr>
        @else
          <tr>
            <th>{{ __('طريقة الإتلاف') }}</th>
            <td>{{ $row->disposal->destruction_method ?? '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('رقم مستند الإتلاف') }}</th>
            <td>{{ $row->disposal->destruction_doc_no ?? '-' }}</td>
          </tr>
          <tr>
            <th>{{ __('الشهود على الإتلاف') }}</th>
            <td>{{ $row->disposal->witnessed_by ?? '-' }}</td>
          </tr>
        @endif
      </table>
    </div>

    <!-- جدول بنود التنفيذ -->
    <div class="info-section">
      <h3 class="section-title">
        <span class="section-icon">📦</span>
        {{ __('الأصول المنفذة') }} ({{ $row->disposal->items->count() }})
      </h3>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 60px">#</th>
            <th style="width: 120px">{{ __('كود الأصل') }}</th>
            <th>{{ __('اسم الأصل') }}</th>
            <th style="width: 100px">{{ __('الكمية المنفذة') }}</th>
            <th style="width: 150px">{{ __('صافي القيمة الدفترية') }}</th>
          </tr>
        </thead>
        <tbody>
          @php $totalNBV = 0; @endphp
          @forelse($row->disposal->items as $i => $it)
            @php $totalNBV += ($it->exec_snapshot_nbv ?? 0) * ($it->qty_executed ?? 0); @endphp
            <tr>
              <td><strong>{{ $i+1 }}</strong></td>
              <td>{{ $it->asset?->code ?? '-' }}</td>
              <td style="text-align: right">{{ $it->asset?->name_ar ?? $it->asset?->name_en ?? '-' }}</td>
              <td><span class="highlight-value">{{ $it->qty_executed ?? 0 }}</span></td>
              <td><span class="currency-value">{{ number_format($it->exec_snapshot_nbv ?? 0, 2) }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="empty-value">{{ __('لا توجد بنود منفذة') }}</td>
            </tr>
          @endforelse

          @if($row->disposal->items->count() > 0)
            <tr class="total-row">
              <td colspan="3" style="text-align: left"><strong>{{ __('الإجمالي') }}</strong></td>
              <td><strong>{{ $row->disposal->items->sum('qty_executed') }}</strong></td>
              <td><strong>{{ number_format($totalNBV, 2) }} {{ __('ج.م') }}</strong></td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    <!-- معلومات إضافية -->
    @if($row->disposal->notes)
      <div class="info-section">
        <h3 class="section-title">
          <span class="section-icon">💬</span>
          {{ __('ملاحظات إضافية') }}
        </h3>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-right: 4px solid #28a745;">
          {{ $row->disposal->notes }}
        </div>
      </div>
    @endif
  @endif

  <!-- زر الطباعة -->
  <div class="footer-section no-print">
    <button class="print-button" onclick="window.print()">
      🖨️ {{ __('طباعة المحضر') }}
    </button>
  </div>
</div>
</body>
</html>
