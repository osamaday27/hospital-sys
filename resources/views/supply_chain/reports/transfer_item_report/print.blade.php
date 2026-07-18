<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? '' }}</title>
  <style id="dyn-orient"></style>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', DejaVu Sans, Arial, Tahoma; line-height: 1.4; color: #333; background: #fff; direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}; }
    .container { max-width: 100%; margin: 0 auto; padding: 15px; }

    .main-header{ display:flex; justify-content:space-between; align-items:center; gap:20px; margin-bottom:20px; padding-bottom:20px; border-bottom:3px solid #1a5490; break-inside: avoid; }
    .header-logo img{ max-height:90px; max-width:120px; object-fit:contain; }
    .header-center{ flex:1; text-align:center; }
    .org-name{ font-size:26px; font-weight:700; color:#1a5490; margin-bottom:5px; letter-spacing:.5px; }
    .org-subtitle{ font-size:14px; color:#666; margin-bottom:3px; }
    .header-right{ flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px; background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490; }
    .header-right-label{ font-weight:600; color:#1a5490; font-size:12px; margin-bottom:6px; }

    .report-header{ display:flex; justify-content:space-between; align-items:center; gap:30px; margin-bottom:15px; padding:10px 0; break-inside: avoid; }
    .report-title{ font-size:22px; font-weight:700; color:#1a5490; margin-bottom:8px; text-align:center; }

    .filters{ background:#f0f4f8; border:1px solid #d0dce6; border-radius:6px; padding:12px 15px; margin-bottom:15px; break-inside: avoid; }
    .filters-title{ font-weight:700; color:#1a5490; font-size:12px; text-transform:uppercase; margin-bottom:10px; letter-spacing:.5px; }
    .filter-row{ display:flex; flex-wrap:wrap; gap:15px; line-height:1.5; }
    .filter-item{ font-size:11px; flex:0 1 auto; }
    .filter-label{ font-weight:600; color:#1a5490; display:inline-block; margin-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:6px; }
    .filter-value{ color:#333; background:#fff; padding:2px 8px; border-radius:3px; border-{{ app()->getLocale() === 'ar' ? 'right':'left' }}:2px solid #1a5490; padding-{{ app()->getLocale() === 'ar' ? 'right':'left' }}:6px; display:inline-block; }

    table{ width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; margin-bottom:10px; }
    thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
    th{ border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }}; font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    tbody tr:hover{ background:#f0f4f8; }
    td:first-child{ text-align:center; background:#f8f9fa; font-weight:500; color:#1a5490; min-width:35px; width:35px; }
    .numeric{ font-family:'Courier New', monospace; font-weight:500; text-align:left; }
    .no-results{ text-align:center; padding:30px !important; color:#999; font-style:italic; }

    body.landscape th, body.landscape td{ padding:6px 5px; font-size:10px; }
    body.landscape table{ font-size:10px; }

    .footer{ margin-top:30px; border-top:2px solid #1a5490; padding-top:15px; text-align:center; color:#666; font-size:10px; break-inside: avoid; }
    .footer-info{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; margin-bottom:10px; }

    @media print {
      .no-print{ display:none !important; }
      body{ margin:0; padding:0; background:#fff; }
      .container{ padding:8px; max-width:100%; }
      .report-header, .filters{ page-break-after: auto; break-after: auto; }
      table{ page-break-inside: auto !important; break-inside: auto !important; }
      thead{ display: table-header-group; } tfoot{ display: table-footer-group; }
      tr{ page-break-inside: avoid; break-inside: avoid; }
    }

    .print-btn-container{ text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; margin-bottom:15px; display:flex; gap:12px; align-items:center; }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; transition:background .3s; }
    .print-btn:hover{ background:#0d3a5c; }
    .switch{ display:inline-flex; gap:8px; align-items:center; font-size:13px; color:#333; background:#f5f6f7; border:1px solid #ddd; padding:6px 10px; border-radius:6px; }
  </style>
</head>
<body>
  <div class="print-btn-container no-print">
    <label class="switch">
      <input type="checkbox" id="toggle-orientation">
      <span>{{ trans('supply_chain_trans.print_landscape') ?? 'طباعة أفقية' }}</span>
    </label>
    <button class="print-btn" id="btn-print">{{ trans('supply_chain_trans.print') }}</button>
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
      <div class="report-title">{{ $meta['title'] ?? '' }}</div>
    </div>

    @if(!empty($meta['product_names']) || !empty($meta['category_names']) || !empty($meta['from_names']) || !empty($meta['to_names']) || !empty($meta['date_from']) || !empty($meta['date_to']))
      <div class="filters">
        <div class="filters-title">{{ trans('supply_chain_trans.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['product_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_products') }}:</span>
              <span class="filter-value">
                {{ implode(', ', array_slice($meta['product_names'], 0, 5)) }}
                @if(count($meta['product_names']) > 5)
                  <strong>{{ ' +' . (count($meta['product_names']) - 5) }}</strong>
                @endif
              </span>
            </div>
          @endif
          @if(!empty($meta['category_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_categories') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['category_names']) }}</span>
            </div>
          @endif
          @if(!empty($meta['from_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_store_from') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['from_names']) }}</span>
            </div>
          @endif
          @if(!empty($meta['to_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_store_to') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['to_names']) }}</span>
            </div>
          @endif
          @if(!empty($meta['date_from']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_date_from') }}:</span>
              <span class="filter-value">{{ $meta['date_from'] }}</span>
            </div>
          @endif
          @if(!empty($meta['date_to']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_date_to') }}:</span>
              <span class="filter-value">{{ $meta['date_to'] }}</span>
            </div>
          @endif
        </div>
      </div>
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
          <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
          <th>{{ trans('supply_chain_trans.table_unit_small') }}</th>
          <th>{{ trans('supply_chain_trans.table_quantity') }}</th>
          <th>{{ trans('supply_chain_trans.table_store_from') }}</th>
          <th>{{ trans('supply_chain_trans.table_store_to') }}</th>
          <th>{{ trans('supply_chain_trans.table_transfer_date') }}</th>
          <th>{{ trans('supply_chain_trans.table_user') }}</th>
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
            <td>{{ $r['batch_no'] }}</td>
            <td>{{ $r['unit_name'] }}</td>
            <td class="numeric">{{ $r['quantity'] }}</td>
            <td>{{ $r['store_from'] }}</td>
            <td>{{ $r['store_to'] }}</td>
            <td>{{ $r['transfer_date'] }}</td>
            <td>{{ $r['user_name'] }}</td>
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
      const styleTag = document.getElementById('dyn-orient');
      const chk = document.getElementById('toggle-orientation');
      const btn = document.getElementById('btn-print');

      try { if (localStorage.getItem('transfer_report_landscape') === '1') chk.checked = true; } catch(e){}

      function applyOrientation(){
        const landscape = chk.checked;
        document.body.classList.toggle('landscape', landscape);
        styleTag.textContent = landscape
          ? '@media print { @page { size: A4 landscape; margin: 8mm 10mm; } }'
          : '@media print { @page { size: A4 portrait;  margin: 8mm 10mm; } }';
        try { localStorage.setItem('transfer_report_landscape', landscape ? '1' : '0'); } catch(e){}
      }

      chk.addEventListener('change', applyOrientation);
      btn.addEventListener('click', function(){
        applyOrientation();
        setTimeout(()=>window.print(), 120);
      });

      // تهيئة
      applyOrientation();

      document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#auto-print') {
          setTimeout(() => { applyOrientation(); window.print(); }, 400);
        }
      });
    })();
  </script>
</body>
</html>
