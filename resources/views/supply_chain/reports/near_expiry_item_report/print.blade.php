<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? '' }}</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Segoe UI', DejaVu Sans, Arial, Tahoma;
      line-height:1.4; color:#333; background:#fff;
      direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    }
    .container{ max-width:100%; margin:0 auto; padding:15px; }

    .main-header{ display:flex; justify-content:space-between; align-items:center; gap:20px;
      margin-bottom:20px; padding-bottom:20px; border-bottom:3px solid #1a5490; break-inside:avoid; }
    .header-logo img{ max-height:90px; max-width:120px; object-fit:contain; }
    .header-center{ flex:1; text-align:center; }
    .org-name{ font-size:26px; font-weight:700; color:#1a5490; margin-bottom:5px; }
    .org-subtitle{ font-size:14px; color:#666; margin-bottom:3px; }
    .header-right{
      flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left':'right' }};
      font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px;
      background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490;
    }
    .header-right-label{ font-weight:600; color:#1a5490; font-size:12px; margin-bottom:6px; }

    .report-header{ display:flex; justify-content:space-between; align-items:center; gap:30px;
      margin-bottom:15px; padding:10px 0; break-inside:avoid; }
    .report-title{ font-size:22px; font-weight:700; color:#1a5490; margin-bottom:8px; text-align:center; }

    .filters{
      background:#f0f4f8; border:1px solid #d0dce6; border-radius:6px; padding:12px 15px; margin-bottom:15px; break-inside:avoid;
    }
    .filters-title{ font-weight:700; color:#1a5490; font-size:12px; text-transform:uppercase; margin-bottom:10px; }
    .filter-row{ display:flex; flex-wrap:wrap; gap:15px; line-height:1.5; }
    .filter-item{ font-size:11px; }
    .filter-label{ font-weight:600; color:#1a5490; margin-{{ app()->getLocale()==='ar'?'left':'right' }}:6px; }
    .filter-value{
      color:#333; background:#fff; padding:2px 8px; border-radius:3px;
      border-{{ app()->getLocale()==='ar'?'right':'left' }}:2px solid #1a5490;
      padding-{{ app()->getLocale()==='ar'?'right':'left' }}:6px; display:inline-block;
    }

    table{ width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; margin-bottom:10px; }
    thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
    th{
      border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }};
      font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
    }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    td:first-child{ text-align:center; background:#f8f9fa; font-weight:500; color:#1a5490; min-width:35px; width:35px; }
    .numeric{ font-family:'Courier New', monospace; font-weight:500; text-align:left; }
    .no-results{ text-align:center; padding:30px !important; color:#999; font-style:italic; }

    .footer{ margin-top:30px; border-top:2px solid #1a5490; padding-top:15px; text-align:center; color:#666; font-size:10px; break-inside:avoid; }
    .footer-info{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; margin-bottom:10px; }

    /* افتراض الطباعة Portrait هنا فقط لعناصر التخطيط، أما @page سنحقنها ديناميكياً */
    @media print {
      .no-print{ display:none !important; }
      body{ margin:0; padding:0; background:#fff; }
      .container{ padding:8px; max-width:100%; }
      .main-header, .report-header, .filters{ page-break-after:auto; break-after:auto; }
      table{ page-break-inside:auto !important; break-inside:auto !important; }
      thead{ display:table-header-group; } tfoot{ display:table-footer-group; }
      tr{ page-break-inside:avoid; break-inside:avoid; }
    }

    .print-btn-container{ display:flex; gap:10px; justify-content:{{ app()->getLocale()==='ar' ? 'left':'right' }}; margin-bottom:15px; align-items:center; }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; transition:background .3s; }
    .print-btn:hover{ background:#0d3a5c; }
    .landscape-toggle{ display:flex; align-items:center; gap:6px; font-size:13px; }
  </style>

  <!-- سنحقن هنا قاعدة @page حسب الاختيار -->
  <style id="print-orientation"></style>
</head>
<body>
  <div class="print-btn-container no-print">
    <label class="landscape-toggle">
      <input type="checkbox" id="chkLandscape">
      <span>{{ trans('supply_chain_trans.print_landscape') }}</span>
    </label>
    <button class="print-btn" onclick="window.print()">{{ trans('supply_chain_trans.print') }}</button>
  </div>

  <div class="container">
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo"></div>
      <div class="header-center">
        <div class="org-name">مستشفي دنيا العيون</div>
        <div class="org-subtitle">الإدارة المالية</div>
      </div>
      <div class="header-right">
        <div class="header-right-label">{{ trans('supply_chain_trans.print_info') }}</div>
        <div>{{ trans('supply_chain_trans.generated_at') }}: {{ $meta['generated_at'] ?? '' }}</div>
      </div>
    </div>

    <div class="report-header">
      <div class="report-title" style="flex:1">{{ $meta['title'] ?? '' }}</div>
    </div>

    @if(!empty($meta['filters_block']))
      {!! $meta['filters_block'] !!}
    @endif

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ trans('supply_chain_trans.table_product_code') }}</th>
          <th>{{ trans('supply_chain_trans.table_product_name') }}</th>
          <th>{{ trans('supply_chain_trans.table_category_name') }}</th>
          <th>{{ trans('supply_chain_trans.table_sale_price') }}</th>
          <th>{{ trans('supply_chain_trans.table_cost_price') }}</th>
          <th>{{ trans('supply_chain_trans.table_unit_small') }}</th>
          <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
          <th>{{ trans('supply_chain_trans.table_quantity') }}</th>
          <th>{{ trans('supply_chain_trans.table_store_name') }}</th>
          <th>{{ trans('supply_chain_trans.table_created_at') }}</th>
          <th>{{ trans('supply_chain_trans.table_expiry_date') }}</th>
          <th>{{ trans('supply_chain_trans.table_created_by') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['product_code'] }}</td>
            <td>{{ $r['product_name'] }}</td>
            <td>{{ $r['category_name'] }}</td>
            <td class="numeric">{{ $r['sale_price'] }}</td>
            <td class="numeric">{{ $r['cost_price'] }}</td>
            <td>{{ $r['unit_name'] }}</td>
            <td>{{ $r['batch_no'] }}</td>
            <td class="numeric">{{ $r['quantity'] }}</td>
            <td>{{ $r['store_name'] }}</td>
            <td>{{ $r['created_at'] }}</td>
            <td>{{ $r['expiry_date'] }}</td>
            <td>{{ $r['created_by'] }}</td>
          </tr>
        @empty
          <tr><td colspan="13" class="no-results">{{ trans('supply_chain_trans.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="footer">
      <div class="footer-info">
        <div>{{ $meta['title'] ?? '' }}</div>
        <div>{{ $meta['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const chk = document.getElementById('chkLandscape');
      const dyn = document.getElementById('print-orientation');

      // يسمح بتمرير ?orientation=landscape لفتح الصفحة جاهزة أفقياً
      const params = new URLSearchParams(window.location.search);
      if (params.get('orientation') === 'landscape' && chk) chk.checked = true;

      function updatePrintOrientation(){
        if (!dyn) return;
        if (chk && chk.checked) {
          // Landscape (مع fallback لبعض المتصفحات)
          dyn.textContent = '@media print { @page { size: A4 landscape; margin: 8mm 10mm; } } @media print { @page { size: landscape; } }';
        } else {
          // Portrait
          dyn.textContent = '@media print { @page { size: A4 portrait; margin: 8mm 10mm; } }';
        }
      }

      function apply(){
        updatePrintOrientation();
        document.body.classList.toggle('landscape', !!(chk && chk.checked));
      }

      if (chk) {
        chk.addEventListener('change', apply);
      }
      apply();

      if (window.location.hash === '#auto-print') {
        // تأكد أن حقن @page تم قبل أمر الطباعة
        setTimeout(()=>window.print(), 400);
      }
    })();
  </script>
</body>
</html>
