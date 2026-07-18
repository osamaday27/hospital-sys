<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <title>{{ $meta['title'] ?? '' }}</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Segoe UI',DejaVu Sans,Arial,Tahoma;line-height:1.4;color:#333;background:#fff;direction:{{ app()->getLocale()==='ar'?'rtl':'ltr' }}}
    .container{max-width:100%;margin:0 auto;padding:15px}
    .main-header{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:20px;padding-bottom:20px;border-bottom:3px solid #1a5490}
    .header-logo img{max-height:90px;max-width:120px;object-fit:contain}
    .header-center{flex:1;text-align:center}
    .org-name{font-size:26px;font-weight:700;color:#1a5490;margin-bottom:5px}
    .header-right{flex:0 0 auto;text-align:{{ app()->getLocale()==='ar'?'left':'right' }};font-size:11px;color:#666;line-height:1.6;min-width:180px;padding:10px;background:#f8f9fa;border-radius:4px;border-{{ app()->getLocale()==='ar'?'left':'right' }}:3px solid #1a5490}
    .report-title{font-size:22px;font-weight:700;color:#1a5490;margin:12px 0 6px;text-align:center}
    .filters{background:#f0f4f8;border:1px solid #d0dce6;border-radius:6px;padding:12px 15px;margin-bottom:15px}
    .filters-title{font-weight:700;color:#1a5490;font-size:12px;text-transform:uppercase;margin-bottom:10px}
    .filter-row{display:flex;flex-wrap:wrap;gap:15px;line-height:1.5}
    .filter-item{font-size:11px}
    .filter-label{font-weight:600;color:#1a5490;display:inline-block;margin-{{ app()->getLocale()==='ar'?'left':'right' }}:6px}
    .filter-value{color:#333;background:#fff;padding:2px 8px;border-radius:3px;border-{{ app()->getLocale()==='ar'?'right':'left' }}:2px solid #1a5490;padding-{{ app()->getLocale()==='ar'?'right':'left' }}:6px;display:inline-block}

    table{width:100%;border-collapse:collapse;font-size:11px;margin-top:10px;margin-bottom:10px}
    thead{background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%);color:#fff}
    th{border:1px solid #1a5490;padding:10px 6px;text-align:{{ app()->getLocale()==='ar'?'right':'left' }};font-weight:600;font-size:11px;white-space:nowrap}
    td{border:1px solid #ddd;padding:8px 6px;color:#555;font-size:11px}
    tbody tr:nth-child(even){background:#f9f9f9}
    td:first-child{text-align:center;background:#f8f9fa;font-weight:500;color:#1a5490;min-width:35px;width:35px}
    .text-end{text-align:right}

    @media print{
      .no-print{display:none!important}
      @page{size:A4 portrait;margin:8mm 10mm}
    }
  </style>
  <style id="print-orientation">
    @page{ size: A4 portrait; margin: 8mm 10mm; }
  </style>
</head>
<body>
  <div class="no-print" style="text-align: {{ app()->getLocale()==='ar'?'left':'right' }}; margin:10px 15px;">
    <label style="font-size:12px">
      <input type="checkbox" id="landscapeToggle"> {{ trans('purchases.landscape_mode') }}
    </label>
    <button onclick="window.print()" style="background:#1a5490;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600">
      {{ trans('purchases.print') }}
    </button>
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

    @if(!empty($meta['date_from']) || !empty($meta['date_to']) || !empty($meta['supplier_names']) || !empty($meta['store_names']) || !empty($meta['product_names']) || !empty($meta['category_names']) || !empty($meta['department_names']))
      <div class="filters">
        <div class="filters-title">{{ trans('purchases.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['date_from']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_date_from') }}:</span><span class="filter-value">{{ $meta['date_from'] }}</span></div>
          @endif
          @if(!empty($meta['date_to']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_date_to') }}:</span><span class="filter-value">{{ $meta['date_to'] }}</span></div>
          @endif
          @if(!empty($meta['supplier_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_suppliers') }}:</span><span class="filter-value">{{ implode(', ', $meta['supplier_names']) }}</span></div>
          @endif
          @if(!empty($meta['store_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_store') }}:</span><span class="filter-value">{{ implode(', ', $meta['store_names']) }}</span></div>
          @endif
          @if(!empty($meta['product_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_products') }}:</span><span class="filter-value">{{ implode(', ', array_slice($meta['product_names'],0,5)) }}@if(count($meta['product_names'])>5) <strong>{{ ' +' . (count($meta['product_names'])-5) }}</strong>@endif</span></div>
          @endif
          @if(!empty($meta['category_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_categories') }}:</span><span class="filter-value">{{ implode(', ', $meta['category_names']) }}</span></div>
          @endif
          @if(!empty($meta['department_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('purchases.filter_department') }}:</span><span class="filter-value">{{ implode(', ', $meta['department_names']) }}</span></div>
          @endif
        </div>
      </div>
    @endif

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ trans('purchases.most_purchased_items_th_code') }}</th>
          <th>{{ trans('purchases.most_purchased_items_th_name') }}</th>
          <th>{{ trans('purchases.most_purchased_items_th_unit') }}</th>
          <th>{{ trans('purchases.most_purchased_items_th_qty') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['product_code'] }}</td>
            <td>{{ $r['product_name'] }}</td>
            <td>{{ $r['unit_name'] }}</td>
            <td class="text-end">{{ $r['qty'] }}</td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:#999;padding:20px">{{ trans('purchases.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <script>
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
