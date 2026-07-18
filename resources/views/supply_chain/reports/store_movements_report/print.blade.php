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
      /* page-break-after: avoid;   <-- كان يمنع الكسر بعد الهيدر */
      break-inside: avoid; /* FIX: امنع تقطيع الهيدر داخلياً فقط */
    }

    .header-logo{ flex:0 0 auto; }
    .header-logo img{ max-height:90px; max-width:120px; object-fit:contain; }
    .header-center{ flex:1; text-align:center; }
    .org-name{ font-size:26px; font-weight:700; color:#1a5490; margin-bottom:5px; letter-spacing:.5px; }
    .org-subtitle{ font-size:14px; color:#666; margin-bottom:3px; }
    .org-details{ font-size:12px; color:#888; line-height:1.4; }
    .header-right{
      flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
      font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px;
      background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490;
    }
    .header-right-label{ font-weight:600; color:#1a5490; font-size:12px; margin-bottom:6px; }

    /* ===== هيدر التقرير ===== */
    .report-header{
      display:flex; justify-content:space-between; align-items:center; gap:30px;
      margin-bottom:15px; padding:10px 0;
      /* page-break-after: avoid; */  /* FIX */
      break-inside: avoid;            /* FIX */
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
    .count-label{ font-size:11px; opacity:.9; text-transform:uppercase; letter-spacing:.5px; margin-bottom:5px; }
    .count-value{ font-size:28px; font-weight:700; font-family:'Courier New', monospace; }

    /* ===== الفلاتر ===== */
    .filters{
      background:#f0f4f8; border:1px solid #d0dce6; border-radius:6px; padding:12px 15px; margin-bottom:15px;
      /* page-break-after: avoid; */  /* FIX */
      break-inside: avoid;            /* FIX */
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
      font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
    }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    tbody tr:hover{ background:#f0f4f8; }
    .text-right{ text-align:right; }
    .text-center{ text-align:center; }
    td:first-child{ text-align:center; background:#f8f9fa; font-weight:500; color:#1a5490; min-width:35px; width:35px; }
    .numeric{ font-family:'Courier New', monospace; font-weight:500; text-align:left; }
    .no-results{ text-align:center; padding:30px !important; color:#999; font-style:italic; }

    /* ===== الفوتر ===== */
    .footer{ margin-top:30px; border-top:2px solid #1a5490; padding-top:15px; text-align:center; color:#666; font-size:10px; break-inside: avoid; }
    .footer-info{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; margin-bottom:10px; }
    .signature-line{ display:inline-block; border-top:1px solid #333; width:150px; text-align:center; padding-top:5px; }

    /* ===== الطباعة ===== */
    @media print {
      .no-print{ display:none !important; }
      body{ margin:0; padding:0; background:#fff; }
      .container{ padding:8px; max-width:100%; }

      /* اسمح بكسر الصفحة بعد الأقسام العلوية عند الحاجة */
      .main-header, .report-header, .filters{
        page-break-after: auto;   /* FIX: بدلاً من avoid */
        break-after: auto;        /* FIX */
      }

      /* اسمح للجدول أن ينقسم عبر الصفحات */
      table{
        page-break-inside: auto !important;   /* FIX */
        break-inside: auto !important;        /* FIX */
      }
      thead{ display: table-header-group; }   /* تكرار الترويسة */
      tfoot{ display: table-footer-group; }
      tr{ page-break-inside: avoid; break-inside: avoid; } /* لا تقسّم الصف الواحد */

      @page{ size: A4 portrait; margin: 8mm 10mm; }
    }

    /* زر الطباعة */
    .print-btn-container{ text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; margin-bottom:15px; }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; transition:background .3s; }
    .print-btn:hover{ background:#0d3a5c; }
  </style>
</head>
<body>
  <div class="print-btn-container no-print">
    <button class="print-btn" onclick="window.print()">{{ trans('supply_chain_trans.print') }}</button>
  </div>

  <div class="container">
    <!-- الهيدر الرئيسي للمؤسسة -->
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="شعار المؤسسة"></div>
      <div class="header-center">
        <div class="org-name">مستشفي دنيا العيون</div>
        <div class="org-subtitle">الادارة المالية</div>
      </div>
      <div class="header-right">
        <div class="header-right-label">{{ trans('supply_chain_trans.print_info') }}</div>
        <div>{{ trans('supply_chain_trans.generated_at') }}: {{ $meta['generated_at'] ?? '' }}</div>
      </div>
    </div>

    <!-- هيدر التقرير -->
    <div class="report-header">
      <div class="report-title-section">
        <div class="report-title">{{ $meta['title'] ?? '' }}</div>
        <div class="report-meta">
          <div class="report-meta-item">
            @if(!empty($meta['date_from']) || !empty($meta['date_to']))
              @if(!empty($meta['date_from']))
                <div class="report-meta-row"><span class="meta-label">{{ trans('supply_chain_trans.filter_date_from') }}:</span> <span class="meta-value">{{ $meta['date_from'] }}</span></div>
              @endif
              @if(!empty($meta['date_to']))
                <div class="report-meta-row"><span class="meta-label">{{ trans('supply_chain_trans.filter_date_to') }}:</span> <span class="meta-value">{{ $meta['date_to'] }}</span></div>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- الفلاتر المطبقة -->
    @if(!empty($meta['store_names']) || !empty($meta['type_names']) || !empty($meta['user_names']) || !empty($meta['product_names']))
      <div class="filters">
        <div class="filters-title">{{ trans('supply_chain_trans.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['store_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_store') }}:</span> <span class="filter-value">{{ implode(', ', $meta['store_names']) }}</span></div>
          @endif
          @if(!empty($meta['type_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_movement_type') }}:</span> <span class="filter-value">{{ implode(', ', $meta['type_names']) }}</span></div>
          @endif
          @if(!empty($meta['user_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_users') }}:</span> <span class="filter-value">{{ implode(', ', $meta['user_names']) }}</span></div>
          @endif
          @if(!empty($meta['product_names']))
            <div class="filter-item">
              <span class="filter-label">{{ trans('supply_chain_trans.filter_products') }}:</span>
              <span class="filter-value">
                {{ implode(', ', array_slice($meta['product_names'], 0, 5)) }}
                @if(count($meta['product_names']) > 5)
                  <strong>{{ ' +' . (count($meta['product_names']) - 5) }}</strong>
                @endif
              </span>
            </div>
          @endif
        </div>
      </div>
    @endif

    <!-- الجدول -->
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ trans('supply_chain_trans.table_store_name') }}</th>
          <th>{{ trans('supply_chain_trans.table_movement_code') }}</th>
          <th>{{ trans('supply_chain_trans.table_movement_type') }}</th>
          <th>{{ trans('supply_chain_trans.table_product_name') }}</th>
          <th>{{ trans('supply_chain_trans.table_movement_date') }}</th>
          <th>{{ trans('supply_chain_trans.table_user') }}</th>
          <th>{{ trans('supply_chain_trans.table_quantity') }}</th>
          <th>{{ trans('supply_chain_trans.table_ledger_balance') }}</th>
          <th>{{ trans('supply_chain_trans.table_unit') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['store_name'] }}</td>
            <td>{{ $r['movement_code'] }}</td>
            <td>{{ $r['movement_type_label'] }}</td>
            <td>{{ $r['product_name'] }}</td>
            <td>{{ $r['movement_date'] }}</td>
            <td>{{ $r['user_name'] }}</td>
            <td class="numeric text-right">{{ $r['quantity'] }}</td>
            <td class="numeric text-right">{{ $r['product_store_balance'] }}</td>
            <td>{{ $r['unit_name'] }}</td>
          </tr>
        @empty
          <tr><td colspan="10" class="no-results">{{ trans('supply_chain_trans.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>

    <!-- الفوتر -->
    <div class="footer">
      <div class="footer-info">
        <div>{{ trans('supply_chain_trans.store_movements_report_title') }}</div>
        <div>{{ $meta['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.location.hash === '#auto-print') {
        setTimeout(() => window.print(), 500);
      }
    });
  </script>
</body>
</html>
