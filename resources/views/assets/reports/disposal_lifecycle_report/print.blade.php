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
    td.num{text-align:right}
    @media print{ .no-print{display:none!important} @page{size:A4 portrait;margin:8mm 10mm} }
  </style>
  <style id="print-orientation">@page{ size: A4 portrait; margin: 8mm 10mm; }</style>
</head>
<body>
  <div class="no-print" style="text-align: {{ app()->getLocale()==='ar'?'left':'right' }}; margin:10px 15px;">
    <label style="font-size:12px"><input type="checkbox" id="landscapeToggle"> {{ trans('assets.landscape_mode') }}</label>
    <button onclick="window.print()" style="background:#1a5490;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600">
      {{ trans('assets.print') }}
    </button>
  </div>

  <div class="container">
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></div>
      <div class="header-center">
        <div class="org-name">{{ trans('assets.org_name', [], 'ar') ?? 'مؤسستك' }}</div>
        <div class="report-title">
          {{ $meta['title'] ?? '' }}
          — {{
            $tab==='executions' ? trans('assets.tab_executions') :
            ($tab==='approvals' ? trans('assets.tab_approvals') :
            ($tab==='by_reason' ? trans('assets.tab_by_reason') : trans('assets.tab_requests')))
          }}
        </div>
      </div>
      <div class="header-right">
        <div>{{ trans('assets.generated_at') }}: {{ $meta['generated_at'] ?? '' }}</div>
        <div>{{ trans('assets.items_count') }}: {{ $meta['count'] ?? 0 }}</div>
      </div>
    </div>

    @if(!empty($meta['date_from']) || !empty($meta['date_to']) || !empty($meta['status']) || !empty($meta['methods']) || !empty($meta['reasons']) || !empty($meta['category_ids']) || !empty($meta['location_ids']))
      <div class="filters">
        <div class="filters-title">{{ trans('assets.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['date_from'])) <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_date_from') }}:</span><span class="filter-value">{{ $meta['date_from'] }}</span></div>@endif
          @if(!empty($meta['date_to']))   <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_date_to') }}:</span><span class="filter-value">{{ $meta['date_to'] }}</span></div>@endif
          @if(!empty($meta['status']))    <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_status') }}:</span><span class="filter-value">{{ is_array($meta['status']) ? implode(', ', $meta['status']) : $meta['status'] }}</span></div>@endif
          @if(!empty($meta['methods']))   <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_method') }}:</span><span class="filter-value">{{ is_array($meta['methods']) ? implode(', ', $meta['methods']) : $meta['methods'] }}</span></div>@endif
          @if(!empty($meta['reasons']))   <div class="filter-item"><span class="filter-label">{{ trans('assets.filter_reason') }}:</span><span class="filter-value">{{ is_array($meta['reasons']) ? implode(', ', $meta['reasons']) : $meta['reasons'] }}</span></div>@endif
          @if(!empty($meta['category_ids'])) <div class="filter-item"><span class="filter-label">{{ trans('assets.category') }}:</span><span class="filter-value">{{ is_array($meta['category_ids']) ? implode(', ', $meta['category_ids']) : $meta['category_ids'] }}</span></div>@endif
          @if(!empty($meta['location_ids'])) <div class="filter-item"><span class="filter-label">{{ trans('assets.location') }}:</span><span class="filter-value">{{ is_array($meta['location_ids']) ? implode(', ', $meta['location_ids']) : $meta['location_ids'] }}</span></div>@endif
        </div>
      </div>
    @endif

    <table>
      <thead>
        <tr>
          @foreach($headers as $h) <th>{{ $h }}</th> @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            @foreach($r as $k=>$val)
              @php $isNum = is_numeric($val); @endphp
              <td class="{{ $isNum ? 'num':'' }}">{{ $isNum ? number_format($val,2) : $val }}</td>
            @endforeach
          </tr>
        @empty
          <tr><td colspan="{{ count($headers) }}" style="text-align:center;color:#999;padding:20px">{{ trans('assets.no_results') }}</td></tr>
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
