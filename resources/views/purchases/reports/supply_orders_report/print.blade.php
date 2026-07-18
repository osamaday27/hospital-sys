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
    .org-subtitle{ font-size:14px; color:#666; margin-bottom:3px; }
    .header-right{
      flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
      font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px;
      background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490;
    }
    .header-right-label{ font-weight:600; color:#1a5490; font-size:12px; margin-bottom:6px; }

    /* ===== عنوان التقرير ===== */
    .report-header{
      display:flex; justify-content:space-between; align-items:center; gap:30px;
      margin-bottom:15px; padding:10px 0; break-inside: avoid;
    }
    .report-title-section{ flex:1; text-align:center; }
    .report-title{ font-size:22px; font-weight:700; color:#1a5490; margin-bottom:8px; }
    .report-meta{ font-size:12px; color:#666; line-height:1.5; }
    .report-meta-item{ display:flex; justify-content:center; gap:20px; flex-wrap:wrap; }
    .report-meta-row{ display:inline-block; }
    .meta-label{ font-weight:600; color:#1a5490; display:inline; }
    .meta-value{ color:#555; display:inline; font-family:'Courier New', monospace; }
    .count-box{
      background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff;
      padding:15px 20px; border-radius:6px; text-align:center; min-width:150px; flex-shrink:0;
    }
    .count-label{ font-size:11px; opacity:.9; letter-spacing:.5px; margin-bottom:5px; }
    .count-value{ font-size:28px; font-weight:700; font-family:'Courier New', monospace; }

    /* ===== الفلاتر ===== */
    .filters{
      background:#f0f4f8; border:1px solid #d0dce6; border-radius:6px; padding:12px 15px; margin-bottom:15px;
      break-inside: avoid;
    }
    .filters-title{ font-weight:700; color:#1a5490; font-size:12px; text-transform:uppercase; margin-bottom:10px; letter-spacing:.5px; }
    .filter-row{ display:flex; flex-wrap:wrap; gap:15px; line-height:1.5; }
    .filter-item{ font-size:11px; flex:0 1 auto; }
    .filter-label{ font-weight:600; color:#1a5490; display:inline-block; margin-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:6px; }
    .filter-value{
      color:#333; background:#fff; padding:2px 8px; border-radius:3px;
      border-{{ app()->getLocale() === 'ar' ? 'right':'left' }}:2px solid #1a5490;
      padding-{{ app()->getLocale() === 'ar' ? 'right':'left' }}:6px; display:inline-block;
    }

    /* ===== الجدول ===== */
    table{ width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; margin-bottom:10px; }
    thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
    th{
      border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }};
      font-weight:600; font-size:11px; white-space:nowrap; letter-spacing:.3px;
    }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    tbody tr:hover{ background:#f0f4f8; }
    td:first-child{ text-align:center; background:#f8f9fa; font-weight:500; color:#1a5490; min-width:35px; width:35px; }

    .no-results{ text-align:center; padding:30px !important; color:#999; font-style:italic; }

    /* ===== الطباعة ===== */
    @media print {
      .no-print{ display:none !important; }
      body{ margin:0; padding:0; background:#fff; }
      .container{ padding:8px; max-width:100%; }

      .main-header, .report-header, .filters{
        page-break-after: auto;
        break-after: auto;
      }

      table{
        page-break-inside: auto !important;
        break-inside: auto !important;
      }
      thead{ display: table-header-group; }
      tfoot{ display: table-footer-group; }
      tr{ page-break-inside: avoid; break-inside: avoid; }
    }

    /* أزرار التحكم */
    .print-btn-container{ text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; margin-bottom:15px; }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; transition:background .3s; }
    .print-btn:hover{ background:#0d3a5c; }

    .form-check{ display:inline-flex; align-items:center; gap:6px; font-size:13px; color:#333; }
  </style>

  <!-- الاتجاه الافتراضي: رأسي -->
  <style id="print-orientation">
    @page{ size: A4 portrait; margin: 8mm 10mm; }
  </style>
</head>
<body>
  <!-- عناصر تحكم الطباعة -->
  <div class="print-btn-container no-print">
    <label class="form-check">
      <input type="checkbox" id="landscapeToggle">
      <span>{{ trans('purchases.supply_requests_report_landscape_mode') }}</span>
    </label>
    &nbsp;&nbsp;
    <button class="print-btn" onclick="window.print()">{{ trans('purchases.print') }}</button>
  </div>

  <div class="container">
    <!-- الهيدر الرئيسي للمؤسسة -->
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="شعار المؤسسة"></div>
      <div class="header-center">
        <div class="org-name">{{ trans('purchases.org_name', [], 'ar') ?? 'مؤسستك' }}</div>
        <div class="org-subtitle">{{ trans('purchases.supply_orders_report_title') }}</div>
      </div>
      <div class="header-right">
        <div class="header-right-label">{{ trans('purchases.print_info') }}</div>
        <div>{{ trans('purchases.generated_at') }}: {{ $meta['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>
      </div>
    </div>

    <!-- عنوان التقرير + المدى الزمني وعدّاد -->
    <div class="report-header">
      <div class="report-title-section">
        <div class="report-title">{{ $meta['title'] ?? '' }}</div>
        <div class="report-meta">
          <div class="report-meta-item">
            @if(!empty($meta['date_from']))
              <div class="report-meta-row">
                <span class="meta-label">{{ trans('purchases.filter_date_from') }}:</span>
                <span class="meta-value">{{ $meta['date_from'] }}</span>
              </div>
            @endif
            @if(!empty($meta['date_to']))
              <div class="report-meta-row">
                <span class="meta-label">{{ trans('purchases.filter_date_to') }}:</span>
                <span class="meta-value">{{ $meta['date_to'] }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>


    </div>

    <!-- الفلاتر المطبقة -->
    @if(
      !empty($meta['supplier_names']) ||
      !empty($meta['store_names']) ||
      !empty($meta['product_names']) ||
      !empty($meta['category_names']) ||
      !empty($meta['department_names']) ||
      !empty($meta['statuses'])
    )
      <div class="filters">
        <div class="filters-title">{{ trans('purchases.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['supplier_names']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('purchases.filter_suppliers') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['supplier_names']) }}</span>
            </div>
          @endif

          @if(!empty($meta['store_names']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('purchases.filter_store') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['store_names']) }}</span>
            </div>
          @endif

          @if(!empty($meta['product_names']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('purchases.filter_products') }}:</span>
              <span class="filter-value">
                {{ implode(', ', array_slice($meta['product_names'], 0, 5)) }}
                @if(count($meta['product_names']) > 5)
                  <strong>{{ ' +' . (count($meta['product_names']) - 5) }}</strong>
                @endif
              </span>
            </div>
          @endif

          @if(!empty($meta['category_names']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('purchases.filter_categories') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['category_names']) }}</span>
            </div>
          @endif

          @if(!empty($meta['department_names']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('purchases.filter_department') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['department_names']) }}</span>
            </div>
          @endif

          @if(!empty($meta['statuses']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('purchases.filter_status') }}:</span>
              <span class="filter-value">{{ implode(', ', array_map(fn($s)=> trans('purchases.supply_orders_report_status_'.$s) ?? $s, $meta['statuses'])) }}</span>
            </div>
          @endif
        </div>
      </div>
    @endif

    <!-- جدول النتائج -->
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ trans('purchases.supply_orders_report_th_order_no') }}</th>
          <th>{{ trans('purchases.supply_orders_report_th_date') }}</th>
          <th>{{ trans('purchases.supply_orders_report_th_supplier') }}</th>
          <th>{{ trans('purchases.supply_orders_report_th_status') }}</th>
          <th>{{ trans('purchases.supply_orders_report_th_items') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['order_number'] }}</td>
            <td>{{ $r['order_date'] }}</td>
            <td>{{ $r['supplier'] }}</td>
            <td>{{ $r['status'] }}</td>
            <td>{{ $r['items_count'] }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="no-results">{{ trans('purchases.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>

    <!-- فوتر بسيط -->
    <div style="margin-top:18px; border-top:2px solid #1a5490; padding-top:10px; text-align:center; color:#666; font-size:10px;">
      <div>{{ trans('purchases.supply_orders_report_title') }}</div>
      <div>{{ $meta['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>
    </div>
  </div>

  <script>
    // تبديل اتجاه الطباعة
    const cb = document.getElementById('landscapeToggle');
    const styleBlock = document.getElementById('print-orientation');
    if (cb) {
      cb.addEventListener('change', function(){
        styleBlock.textContent = this.checked
          ? '@page{ size: A4 landscape; margin: 8mm 10mm; }'
          : '@page{ size: A4 portrait;  margin: 8mm 10mm; }';
      });
    }

    // طباعة تلقائية إن رغبت: أضف #auto-print للعنوان
    document.addEventListener('DOMContentLoaded', function () {
      if (window.location.hash === '#auto-print') {
        setTimeout(() => window.print(), 400);
      }
    });
  </script>
</body>
</html>
