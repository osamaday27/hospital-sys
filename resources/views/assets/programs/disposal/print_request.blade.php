<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>{{ __('نموذج طلب تخلّص/شطب') }} — {{ $row->request_no }}</title>
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
      border-bottom: 3px solid #2c5aa0;
      padding-bottom: 20px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .page-header h1 {
      font-size: 24px;
      color: #2c5aa0;
      font-weight: 600;
    }

    .print-date {
      font-size: 12px;
      color: #666;
      text-align: left;
    }

    .info-section {
      margin-bottom: 30px;
    }

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #2c5aa0;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid #e0e0e0;
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
      width: 200px;
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
      background: #2c5aa0;
      color: white;
    }

    .data-table th {
      padding: 12px 15px;
      font-weight: 600;
      text-align: center;
      border: 1px solid #1e4180;
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

    .footer-section {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 2px solid #e0e0e0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .created-by {
      font-size: 14px;
      color: #495057;
    }

    .created-by strong {
      color: #2c5aa0;
    }

    .print-button {
      background: #2c5aa0;
      color: white;
      border: none;
      padding: 10px 25px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .print-button:hover {
      background: #1e4180;
    }

    .badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: 600;
    }

    .badge-status {
      background: #d1ecf1;
      color: #0c5460;
    }

    .attachment-link {
      color: #2c5aa0;
      text-decoration: none;
      font-weight: 500;
    }

    .attachment-link:hover {
      text-decoration: underline;
    }

    .empty-value {
      color: #999;
      font-style: italic;
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

      .print-only {
        display: inline !important;
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
    }

    .print-only {
      display: none;
    }
  </style>
</head>
<body>
<div class="container">
  <!-- رأس الصفحة -->
  <div class="page-header">
    <h1>{{ __('طلب تخلّص/شطب أصل') }}</h1>
    <div class="print-date">
      <div>تاريخ الطباعة</div>
      <div><strong>{{ now()->format('Y-m-d H:i') }}</strong></div>
    </div>
  </div>

  <!-- معلومات الطلب الأساسية -->
  <div class="info-section">
    <h3 class="section-title">{{ __('بيانات الطلب') }}</h3>
    <table class="info-table">
      <tr>
        <th>{{ __('رقم الطلب') }}</th>
        <td><strong>{{ $row->request_no }}</strong></td>
      </tr>
      <tr>
        <th>{{ __('طريقة التخلّص') }}</th>
        <td>{{ __('assets.disposal.method_'.$row->method) }}</td>
      </tr>
      <tr>
        <th>{{ __('الحالة الحالية') }}</th>
        <td>
          <span class="badge badge-status">{{ __('assets.disposal.status_'.$row->status) }}</span>
        </td>
      </tr>
      <tr>
        <th>{{ __('سبب الطلب') }}</th>
        <td>{{ $row->reason ?: '-' }}</td>
      </tr>
      <tr>
        <th>{{ __('ملاحظات') }}</th>
        <td>{{ $row->notes ?: '-' }}</td>
      </tr>
    </table>
  </div>

  <!-- جدول الأصول -->
  <div class="info-section">
    <h3 class="section-title">{{ __('الأصول المطلوب التخلص منها') }}</h3>
    <table class="data-table">
      <thead>
        <tr>
          <th style="width: 60px">#</th>
          <th>{{ __('كود الأصل') }}</th>
          <th>{{ __('اسم الأصل') }}</th>
          <th style="width: 120px">{{ __('الكمية المطلوبة') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($row->items as $i => $it)
          <tr>
            <td><strong>{{ $i+1 }}</strong></td>
            <td>{{ $it->asset?->code ?? '-' }}</td>
            <td style="text-align: right">{{ $it->asset?->name_ar ?? $it->asset?->name_en ?? '-' }}</td>
            <td><strong>{{ $it->qty_requested }}</strong></td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="empty-value">{{ __('لا توجد أصول مسجلة') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- جدول المرفقات -->
  @if($row->attachments->count())
    <div class="info-section">
      <h3 class="section-title">{{ __('المرفقات') }} ({{ $row->attachments->count() }})</h3>
      <table class="data-table">
        <thead>
          <tr>
            <th style="width: 60px">#</th>
            <th>{{ __('اسم الملف') }}</th>
            <th>{{ __('ملاحظات') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($row->attachments as $i => $att)
            <tr>
              <td><strong>{{ $i+1 }}</strong></td>
              <td style="text-align: right">
                <a class="no-print attachment-link"
                   href="{{ url('attachments/' . ltrim($att->file_path, '/')) }}"
                   target="_blank">
                  📎 {{ $att->original_name ?? basename($att->file_path) }}
                </a>
                <span class="print-only">{{ $att->original_name ?? basename($att->file_path) }}</span>
              </td>
              <td style="text-align: right">{{ $att->notes ?: '-' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <!-- تذييل الصفحة -->
  <div class="footer-section">
    <div class="created-by">
      <strong>{{ __('مُنشأ الطلب') }}:</strong>
      {{ optional($row->requested_by ? \App\Models\User::find($row->requested_by) : null)?->name ?? '-' }}
    </div>
    <button class="print-button no-print" onclick="window.print()">
      🖨️ {{ __('طباعة') }}
    </button>
  </div>
</div>
</body>
</html>
