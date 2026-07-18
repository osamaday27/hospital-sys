<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? '' }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', DejaVu Sans, Arial, Tahoma;
      line-height: 1.4; color: #333; background: #fff;
      direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    }

    .container { max-width: 100%; margin: 0 auto; padding: 15px; }

    /* ===== الهيدر الرئيسي ===== */
    .main-header{
      display:flex; justify-content:space-between; align-items:center; gap:20px;
      margin-bottom:20px; padding-bottom:20px; border-bottom:3px solid #1a5490;
      break-inside: avoid;
    }
    .header-logo{ flex:0 0 auto; }
    .header-logo img{ max-height:90px; max-width:120px; object-fit:contain; }
    .header-center{ flex:1; text-align:center; }
    .org-name{ font-size:26px; font-weight:700; color:#1a5490; margin-bottom:5px; letter-spacing:.5px; }
    .header-right{
      flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
      font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px;
      background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490;
    }

    /* ===== عنوان التقرير ===== */
    .report-title{ font-size:22px; font-weight:700; color:#1a5490; margin:12px 0 6px; text-align:center; }

    /* ===== الجدول ===== */
    table{ width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; margin-bottom:10px; }
    thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
    th{
      border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }};
      font-weight:600; font-size:11px; white-space:nowrap;
    }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    .text-center{ text-align:center; }
    .numeric{ font-family:'Courier New', monospace; text-align:left; }

    /* عناصر الطباعة */
    .controls{ display:flex; gap:16px; align-items:center; justify-content: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; margin-bottom:12px; }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; }
    @media print { .no-print{ display:none !important; } }
  </style>
  <style id="print-orientation">
    @page{ size: {{ request('landscape') ? 'A4 landscape' : 'A4 portrait' }}; margin: 8mm 10mm; }
  </style>
</head>
<body>
  <div class="controls no-print">
    <label><input type="checkbox" id="landscapeToggle" {{ request('landscape') ? 'checked' : '' }}> {{ trans('purchases.supply_requests_report_landscape_mode') }}</label>
    <button class="print-btn" onclick="window.print()">{{ trans('purchases.print') }}</button>
  </div>

  <div class="container">
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></div>
      <div class="header-center">
        <div class="org-name">{{ trans('purchases.org_name', [], 'ar') ?? 'مؤسستك' }}</div>
        <div class="report-title">{{ $meta['title'] ?? '' }}</div>
      </div>
      <div class="header-right">
        <div>{{ trans('purchases.generated_at') }}: {{ $meta['generated_at'] ?? '' }}</div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>{{ trans('purchases.supply_requests_report_th_no') }}</th>
          <th>{{ trans('purchases.supply_requests_report_th_date') }}</th>
          <th>{{ trans('purchases.supply_requests_report_th_status') }}</th>
          <th>{{ trans('purchases.supply_requests_report_th_reason') }}</th>
          <th>{{ trans('purchases.supply_requests_report_th_creator') }}</th>
          <th>{{ trans('purchases.supply_requests_report_th_items') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r['requisition_number'] }}</td>
            <td>{{ $r['requisition_date'] }}</td>
            <td>{{ trans('purchases.supply_requests_report_status_'.$r['status']) ?? $r['status'] }}</td>
            <td>{{ $r['reason'] }}</td>
            <td>{{ $r['creator'] }}</td>
            <td class="numeric">{{ $r['items_count'] }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center">{{ trans('purchases.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <script>
    // تبديل الاتجاه
    const cb = document.getElementById('landscapeToggle');
    const styleBlock = document.getElementById('print-orientation');
    if (cb) {
      cb.addEventListener('change', function(){
        styleBlock.textContent = this.checked
          ? '@page{ size: A4 landscape; margin: 8mm 10mm; }'
          : '@page{ size: A4 portrait;  margin: 8mm 10mm; }';
      });
    }
  </script>
</body>
</html>
