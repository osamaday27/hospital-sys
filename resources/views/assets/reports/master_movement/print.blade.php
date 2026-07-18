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
      <input type="checkbox" id="landscapeToggle"> {{ trans('assets.landscape_mode') }}
    </label>
    <button onclick="window.print()" style="background:#1a5490;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600">
      {{ trans('assets.print') }}
    </button>
  </div>

  <div class="container">
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></div>
      <div class="header-center">
        <div class="org-name">{{ trans('assets.org_name', [], 'ar') ?? 'مؤسستك' }}</div>
        <div class="report-title">{{ $meta['title'] ?? '' }}</div>
      </div>
      <div class="header-right">
        <div>{{ trans('assets.generated_at') }}: {{ $meta['generated_at'] ?? '' }}</div>
        <div>{{ trans('assets.items_count') }}: {{ $meta['count'] ?? 0 }}</div>
      </div>
    </div>

    {{-- ملخص الفلاتر --}}
    @if(
      !empty($meta['purchase_from']) || !empty($meta['purchase_to']) ||
      !empty($meta['category_names']) || !empty($meta['brand_names']) ||
      !empty($meta['model_names']) || !empty($meta['location_names']) ||
      !empty($meta['store_names']) || !empty($meta['supplier_names']) ||
      !empty($meta['status_labels']) || !empty($meta['addition_source']) ||
      !empty($meta['cost_min']) || !empty($meta['cost_max']) ||
      !empty($meta['nbv_min'])  || !empty($meta['nbv_max']) ||
      !empty($meta['qty_min'])  || !empty($meta['qty_max'])
    )
      <div class="filters">
        <div class="filters-title">{{ trans('assets.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['purchase_from']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_purchase_from') }}:</span><span class="filter-value">{{ $meta['purchase_from'] }}</span></div>
          @endif
          @if(!empty($meta['purchase_to']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_purchase_to') }}:</span><span class="filter-value">{{ $meta['purchase_to'] }}</span></div>
          @endif

          @if(!empty($meta['category_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.category') }}:</span><span class="filter-value">{{ implode(', ', $meta['category_names']) }}</span></div>
          @endif
          @if(!empty($meta['brand_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.brand') }}:</span><span class="filter-value">{{ implode(', ', $meta['brand_names']) }}</span></div>
          @endif
          @if(!empty($meta['model_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.model') }}:</span><span class="filter-value">{{ implode(', ', $meta['model_names']) }}</span></div>
          @endif
          @if(!empty($meta['location_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.location') }}:</span><span class="filter-value">{{ implode(', ', $meta['location_names']) }}</span></div>
          @endif

          @if(!empty($meta['store_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.store') }}:</span><span class="filter-value">{{ implode(', ', $meta['store_names']) }}</span></div>
          @endif
          @if(!empty($meta['supplier_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.supplier') }}:</span><span class="filter-value">{{ implode(', ', $meta['supplier_names']) }}</span></div>
          @endif

          @if(!empty($meta['status_labels']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.status') }}:</span>
              <span class="filter-value">{{ implode(', ', $meta['status_labels']) }}</span>
            </div>
          @endif
          @if(!empty($meta['addition_source']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.addition_source') }}:</span>
              <span class="filter-value">{{ $meta['addition_source'] }}</span>
            </div>
          @endif

          @if(!empty($meta['cost_min']) || !empty($meta['cost_max']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.cost_range') }}:</span>
              <span class="filter-value">{{ $meta['cost_min'] ?? '—' }} — {{ $meta['cost_max'] ?? '—' }}</span>
            </div>
          @endif
          @if(!empty($meta['nbv_min']) || !empty($meta['nbv_max']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.nbv_range') }}:</span>
              <span class="filter-value">{{ $meta['nbv_min'] ?? '—' }} — {{ $meta['nbv_max'] ?? '—' }}</span>
            </div>
          @endif
          @if(!empty($meta['qty_min']) || !empty($meta['qty_max']))
            <div class="filter-item"><span class="filter-label">{{ trans('assets.qty_range') }}:</span>
              <span class="filter-value">{{ $meta['qty_min'] ?? '—' }} — {{ $meta['qty_max'] ?? '—' }}</span>
            </div>
          @endif
        </div>
      </div>
    @endif

    <table>
      <thead>
        <tr>
          <th>{{ trans('assets.col_code') }}</th>
          <th>{{ trans('assets.col_name') }}</th>
          <th>{{ trans('assets.col_category') }}</th>
          {{-- <th>{{ trans('assets.col_brand') }}</th> --}}
          {{-- <th>{{ trans('assets.col_model') }}</th> --}}
          <th>{{ trans('assets.col_serial') }}</th>
          <th>{{ trans('assets.col_location') }}</th>
          {{-- <th>{{ trans('assets.col_custodian') }}</th> --}}
          <th>{{ trans('assets.col_store') }}</th>
          <th>{{ trans('assets.col_supplier') }}</th>
          <th>{{ trans('assets.col_purchase_date') }}</th>
          <th>{{ trans('assets.col_cost') }}</th>
          <th>{{ trans('assets.col_status') }}</th>
          <th>{{ trans('assets.col_addition_source') }}</th>
          <th>{{ trans('assets.col_last_movement') }}</th>
          <th>{{ trans('assets.col_qty_total') }}</th>
          <th>{{ trans('assets.col_qty_assigned') }}</th>
          <th>{{ trans('assets.col_qty_available') }}</th>
          <th>{{ trans('assets.col_nbv') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ $r['code'] }}</td>
            <td>{{ $r['name'] }}</td>
            <td>{{ $r['category'] }}</td>
            {{-- <td>{{ $r['brand'] }}</td> --}}
            {{-- <td>{{ $r['model'] }}</td> --}}
            <td>{{ $r['serial'] }}</td>
            <td>{{ $r['location'] }}</td>
            {{-- <td>{{ $r['custodian'] }}</td> --}}
            <td>{{ $r['store'] }}</td>
            <td>{{ $r['supplier'] }}</td>
            <td>{{ $r['purchase_date'] }}</td>
            <td class="text-end">{{ number_format((float)$r['cost'],2) }}</td>
            <td>{{ $r['status'] }}</td>
            <td>{{ $r['addition_source'] }}</td>
            <td>{{ $r['last_movement'] }}</td>
            <td class="text-end">{{ $r['qty_total'] }}</td>
            <td class="text-end">{{ $r['qty_assigned'] }}</td>
            <td class="text-end">{{ $r['qty_available'] }}</td>
            <td class="text-end">{{ number_format((float)$r['nbv'],2) }}</td>
          </tr>
        @empty
          <tr><td colspan="19" style="text-align:center;color:#999;padding:20px">{{ trans('assets.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="small" style="color:#666">{{ trans('assets.note_report_generated_now') }}</div>
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
