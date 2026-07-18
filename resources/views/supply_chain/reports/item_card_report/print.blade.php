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

    .report-header{
      display:flex; justify-content:space-between; align-items:center; gap:30px;
      margin-bottom:15px; padding:10px 0;
      break-inside: avoid;
    }
    .report-title-section{ flex:1; text-align:center; }
    .report-title{ font-size:22px; font-weight:700; color:#1a5490; margin-bottom:8px; }
    .report-meta{ font-size:12px; color:#666; line-height:1.5; }
    .report-meta-item{ display:flex; justify-content:center; gap:20px; flex-wrap:wrap; }
    .report-meta-row{ display:inline-block; }
    .meta-label{ font-weight:600; color:#1a5490; display:inline; }
    .meta-value{ color:#555; display:inline; font-family:'Courier New', monospace; }

    .section{ margin-bottom:14px; break-inside: avoid; }
    .section-title{ font-weight:700; color:#1a5490; margin-bottom:8px; border-bottom:1px solid #d0dce6; padding-bottom:6px; }

    .grid{ display:grid; gap:10px; }
    .grid-2{ grid-template-columns: 1fr 1fr; }
    .grid-3{ grid-template-columns: 1fr 1fr 1fr; }
    .card{ border:1px solid #d0dce6; border-radius:6px; padding:10px; background:#fff; }
    .muted{ color:#666; }
    .kpi{ display:flex; flex-direction:column; gap:4px; }
    .kpi .label{ font-size:12px; color:#6c757d; }
    .kpi .value{ font-weight:700; font-size:16px; }

    table{ width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; margin-bottom:10px; }
    thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
    th{
      border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }};
      font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.3px; white-space:nowrap;
    }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    tbody tr:hover{ background:#f0f4f8; }
    td:first-child{ text-align:center; background:#f8f9fa; font-weight:500; color:#1a5490; min-width:35px; width:35px; }
    .numeric{ font-family:'Courier New', monospace; font-weight:500; text-align:left; }
    .no-results{ text-align:center; padding:30px !important; color:#999; font-style:italic; }

    .footer{ margin-top:30px; border-top:2px solid #1a5490; padding-top:15px; text-align:center; color:#666; font-size:10px; break-inside: avoid; }
    .footer-info{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; margin-bottom:10px; }

    .sign-row{ display:flex; gap:12px; margin-top:18px; }
    .sign-box{ flex:1; border:1px dashed #bbb; border-radius:6px; padding:10px; min-height:80px; }
    .sign-title{ font-size:12px; color:#6c757d; margin-bottom:6px; }

    .badge{ display:inline-block; padding:2px 6px; border-radius:4px; font-size:11px; }
    .badge-info{ background:#e7f1ff; color:#225ea8; border:1px solid #bcd7ff; }
    .badge-warn{ background:#fff3cd; color:#8a6d3b; border:1px solid #ffe49c; }
    .badge-danger{ background:#f8d7da; color:#842029; border:1px solid #f1aeb5; }

    @media print {
      .no-print{ display:none !important; }
      body{ margin:0; padding:0; background:#fff; }
      .container{ padding:8px; max-width:100%; }
      .main-header, .report-header, .filters, .section{
        page-break-after: auto;
        break-after: auto;
      }
      table{ page-break-inside: auto !important; break-inside: auto !important; }
      thead{ display: table-header-group; }
      tfoot{ display: table-footer-group; }
      tr{ page-break-inside: avoid; break-inside: avoid; }
      @page{ size: A4 portrait; margin: 8mm 10mm; }
    }

    .print-btn-container{
      display:flex; align-items:center; gap:12px;
      justify-content: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
      margin-bottom:15px;
    }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; transition:background .3s; }
    .print-btn:hover{ background:#0d3a5c; }
    .checkbox-label{ font-size:13px; color:#333; user-select:none; display:flex; align-items:center; gap:6px; }
    .checkbox-label input{ transform: scale(1.15); }
  </style>
</head>
<body>
  <div class="print-btn-container no-print">
    <label class="checkbox-label" title="الطباعة باتجاه أفقي (Landscape)">
      <input type="checkbox" id="landscapeToggle"> طباعة أفقي (A4 Landscape)
    </label>
    <button class="print-btn" onclick="printNow()">{{ trans('supply_chain_trans.print') }}</button>
  </div>

  <div class="container">

    {{-- رأس الصفحة --}}
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

    {{-- عنوان وتواريخ --}}
    <div class="report-header">
      <div class="report-title-section">
        <div class="report-title">{{ $meta['title'] ?? '' }}</div>
        <div class="report-meta">
          <div class="report-meta-item">
            @if(!empty($meta['date_from']))
              <div class="report-meta-row"><span class="meta-label">{{ trans('supply_chain_trans.filter_date_from') }}:</span> <span class="meta-value">{{ $meta['date_from'] }}</span></div>
            @endif
            @if(!empty($meta['date_to']))
              <div class="report-meta-row"><span class="meta-label">{{ trans('supply_chain_trans.filter_date_to') }}:</span> <span class="meta-value">{{ $meta['date_to'] }}</span></div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- فلاتر مختصرة --}}
    @if(!empty($meta['store_names']) || !empty($meta['product_names']))
      <div class="filters">
        <div class="filters-title">{{ trans('supply_chain_trans.filters_title') }}</div>
        <div class="filter-row">
          @if(!empty($meta['store_names']))
            <div class="filter-item"><span class="filter-label">{{ trans('supply_chain_trans.filter_store') }}:</span> <span class="filter-value">{{ implode(', ', $meta['store_names']) }}</span></div>
          @endif
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
        </div>
      </div>
    @endif

    {{-- ========== نظرة عامة محسوبة من $rows ==========
         كل الحسابات التالية تتم محليًا في الفيو لضمان عملها دون تعديل الكنترولر --}}
    @php
      $countRows = is_array($rows) ? count($rows) : (is_countable($rows) ? count($rows) : 0);

      // المنتجات المميزة (بالكود والاسم)
      $prodMap = [];
      foreach ($rows as $r) {
        $code = $r['product_code'] ?? '—';
        $name = $r['product_name'] ?? '—';
        $prodMap[$code.'|'.$name] = ['code'=>$code,'name'=>$name];
      }
      $products = array_values($prodMap);

      // تواريخ الحركة الأولى/الأخيرة + أول إضافة
      $firstDate = null; $lastDate = null; $firstAddition = null;
      foreach ($rows as $r){
        $d = $r['movement_date'] ?? null;
        if($d){ $firstDate = $firstDate ? min($firstDate,$d) : $d; $lastDate = $lastDate ? max($lastDate,$d) : $d; }
        $fa = $r['first_addition_at'] ?? null;
        if(!empty($fa) && $fa !== '—'){ $firstAddition = $firstAddition ? min($firstAddition,$fa) : $fa; }
      }

      // ملخص الباتشات (حسب رقم الباتش)
      $batchMap = [];
      foreach ($rows as $r){
        $bn = $r['batch_no'] ?? '—';
        if(!isset($batchMap[$bn])) $batchMap[$bn] = ['expiry'=>$r['expiry_date'] ?? '—', 'count'=>0, 'first'=>null, 'last'=>null];
        $batchMap[$bn]['count']++;
        $md = $r['movement_date'] ?? null;
        if($md){
          $batchMap[$bn]['first'] = $batchMap[$bn]['first'] ? min($batchMap[$bn]['first'],$md) : $md;
          $batchMap[$bn]['last']  = $batchMap[$bn]['last']  ? max($batchMap[$bn]['last'],$md) : $md;
        }
      }
      // أقرب صلاحية
      $nearestExp = null;
      foreach ($batchMap as $b){
        $ex = $b['expiry'] ?? null;
        if(!empty($ex) && $ex !== '—'){ $nearestExp = $nearestExp ? min($nearestExp,$ex) : $ex; }
      }

      // الجهات/المخازن المستفيدة الأكثر تكرارًا
      $benefCounts = [];
      foreach ($rows as $r){
        $b = $r['beneficiary_store'] ?? '—';
        $benefCounts[$b] = ($benefCounts[$b] ?? 0) + 1;
      }
      arsort($benefCounts);
      $topBenef = array_slice($benefCounts, 0, 5, true);

      // آخر 10 حركات
$last10 = ($rows instanceof \Illuminate\Support\Collection ? $rows : collect($rows))
  ->sortByDesc(function($r){ return $r['movement_date'] ?? null; })
  ->take(10)
  ->values()
  ->all();

      // عدد المستخدمين المميزين
      $userSet = [];
      foreach ($rows as $r){ $u = $r['user_name'] ?? null; if($u){ $userSet[$u]=true; } }
      $distinctUsers = count($userSet);
    @endphp

    <div class="section">
      <div class="section-title">{{ __('نظرة عامة') }}</div>

      <div class="grid grid-2">
        <div class="card">
          <div class="grid grid-2">
            <div class="kpi">
              <div class="label">{{ __('عدد الحركات') }}</div>
              <div class="value">{{ number_format($countRows) }}</div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('عدد الباتشات') }}</div>
              <div class="value">{{ max(0, count($batchMap)) }}</div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('أول إضافة') }}</div>
              <div class="value">{{ $firstAddition ?? '—' }}</div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('أقرب صلاحية') }}</div>
              <div class="value">
                @if($nearestExp)
                  <span class="badge {{ (strtotime($nearestExp) < time()) ? 'badge-danger' : 'badge-info' }}">{{ $nearestExp }}</span>
                @else
                  —
                @endif
              </div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('أول حركة') }}</div>
              <div class="value">{{ $firstDate ?? '—' }}</div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('آخر حركة') }}</div>
              <div class="value">{{ $lastDate ?? '—' }}</div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('عدد المستخدمين') }}</div>
              <div class="value">{{ number_format($distinctUsers) }}</div>
            </div>
            <div class="kpi">
              <div class="label">{{ __('عدد الأصناف المختارة') }}</div>
              <div class="value">{{ number_format(max(0, count($products))) }}</div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="muted" style="margin-bottom:6px; font-weight:600;">{{ __('تفاصيل الصنف/الأصناف') }}</div>
          @if(count($products) === 1)
            <div><strong>{{ __('الكود') }}:</strong> {{ $products[0]['code'] ?? '—' }}</div>
            <div><strong>{{ __('الاسم') }}:</strong> {{ $products[0]['name'] ?? '—' }}</div>
          @elseif(count($products) > 1)
            <div class="muted">{{ __('أكثر من صنف محدد. فيما يلي أول 5:') }}</div>
            <ol style="margin-top:6px; padding-{{ app()->getLocale()==='ar' ? 'right' : 'left' }}:16px;">
              @foreach(array_slice($products,0,5) as $p)
                <li><span class="badge badge-info">{{ $p['code'] ?? '—' }}</span> — {{ $p['name'] ?? '—' }}</li>
              @endforeach
            </ol>
          @else
            <div class="muted">—</div>
          @endif

          @if(!empty($topBenef))
            <div style="margin-top:8px;">
              <div class="muted" style="margin-bottom:6px; font-weight:600;">{{ __('أكثر جهات/مخازن مستفيدة ظهورًا (Top 5)') }}</div>
              <ul style="margin:0; padding-{{ app()->getLocale()==='ar' ? 'right' : 'left' }}:16px;">
                @foreach($topBenef as $name=>$cnt)
                  <li>{{ $name ?: '—' }} <span class="badge badge-info">{{ $cnt }}</span></li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ملخص الباتشات --}}
    <div class="section">
      <div class="section-title">{{ __('ملخص الباتشات') }}</div>
      <table>
        <thead>
          <tr>
            <th style="width:40px">#</th>
            <th>{{ __('الباتش') }}</th>
            <th>{{ __('تاريخ الصلاحية') }}</th>
            <th>{{ __('أول حركة') }}</th>
            <th>{{ __('آخر حركة') }}</th>
            <th class="numeric">{{ __('عدد الحركات') }}</th>
            <th>{{ __('الحالة') }}</th>
          </tr>
        </thead>
        <tbody>
          @php $i=1; @endphp
          @forelse($batchMap as $bn=>$b)
            @php
              $exp = $b['expiry'] ?? null;
              $status = 'ok';
              if(!empty($exp) && $exp !== '—'){
                  $isExpired = strtotime($exp) < strtotime(date('Y-m-d'));
                  $isNear = !$isExpired && (strtotime($exp) - time()) <= 30*24*60*60;
                  $status = $isExpired ? 'expired' : ($isNear ? 'near' : 'ok');
              }
            @endphp
            <tr>
              <td>{{ $i++ }}</td>
              <td>{{ $bn ?: '—' }}</td>
              <td>{{ $b['expiry'] ?? '—' }}</td>
              <td>{{ $b['first'] ?? '—' }}</td>
              <td>{{ $b['last'] ?? '—' }}</td>
              <td class="numeric">{{ number_format($b['count'] ?? 0) }}</td>
              <td>
                @if($status==='expired')
                  <span class="badge badge-danger">{{ __('منتهي') }}</span>
                @elseif($status==='near')
                  <span class="badge badge-warn">{{ __('قريب الانتهاء') }}</span>
                @else
                  <span class="badge badge-info">{{ __('سليم') }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="no-results">{{ trans('supply_chain_trans.no_results') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- آخر 10 حركات (مختصر) --}}
    <div class="section">
      <div class="section-title">{{ __('آخر 10 حركات') }}</div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>{{ trans('supply_chain_trans.table_movement_date') }}</th>
            <th>{{ trans('supply_chain_trans.table_movement_type') }}</th>
            <th>{{ trans('supply_chain_trans.table_beneficiary_code') }}</th>
            <th>{{ trans('supply_chain_trans.table_beneficiary_store') }}</th>
            <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
            <th>{{ trans('supply_chain_trans.table_expiry_date') }}</th>
            <th class="numeric">{{ trans('supply_chain_trans.table_cost_price') }}</th>
            <th>{{ trans('supply_chain_trans.table_user') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($last10 as $j=>$r)
            <tr>
              <td>{{ $j+1 }}</td>
              <td>{{ $r['movement_date'] ?? '—' }}</td>
              <td>{{ $r['movement_type'] ?? '—' }}</td>
              <td>{{ $r['beneficiary_code'] ?? '—' }}</td>
              <td>{{ $r['beneficiary_store'] ?? '—' }}</td>
              <td>{{ $r['batch_no'] ?? '—' }}</td>
              <td>{{ $r['expiry_date'] ?? '—' }}</td>
              <td class="numeric">{{ $r['cost_price'] ?? '—' }}</td>
              <td>{{ $r['user_name'] ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="9" class="no-results">{{ trans('supply_chain_trans.no_results') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- دفتر الحركات الكامل --}}
    <div class="section">
      <div class="section-title">{{ __('دفتر الحركات التفصيلي') }}</div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>{{ trans('supply_chain_trans.table_product_code') }}</th>
            <th>{{ trans('supply_chain_trans.table_product_name') }}</th>
            <th>{{ trans('supply_chain_trans.table_beneficiary_code') }}</th>
            <th>{{ trans('supply_chain_trans.table_beneficiary_store') }}</th>
            <th>{{ trans('supply_chain_trans.table_movement_type') }}</th>
            <th>{{ trans('supply_chain_trans.table_movement_date') }}</th>
            <th>{{ trans('supply_chain_trans.table_sale_price') }}</th>
            <th>{{ trans('supply_chain_trans.table_cost_price') }}</th>
            <th>{{ trans('supply_chain_trans.table_unit') }}</th>
            <th>{{ trans('supply_chain_trans.table_ledger_balance') }}</th>
            <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
            <th>{{ trans('supply_chain_trans.table_first_addition_at') }}</th>
            <th>{{ trans('supply_chain_trans.table_expiry_date') }}</th>
            <th>{{ trans('supply_chain_trans.table_user') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $i=>$r)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $r['product_code'] }}</td>
              <td>{{ $r['product_name'] }}</td>
              <td>{{ $r['beneficiary_code'] }}</td>
              <td>{{ $r['beneficiary_store'] }}</td>
              <td>{{ $r['movement_type'] }}</td>
              <td>{{ $r['movement_date'] }}</td>
              <td class="numeric">{{ $r['sale_price'] }}</td>
              <td class="numeric">{{ $r['cost_price'] }}</td>
              <td>{{ $r['unit_name'] }}</td>
              <td class="numeric">{{ $r['current_balance'] }}</td>
              <td>{{ $r['batch_no'] }}</td>
              <td>{{ $r['first_addition_at'] }}</td>
              <td>{{ $r['expiry_date'] }}</td>
              <td>{{ $r['user_name'] }}</td>
            </tr>
          @empty
            <tr><td colspan="15" class="no-results">{{ trans('supply_chain_trans.no_results') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>



    {{-- تذييل --}}
    <div class="footer">
      <div class="footer-info">
        <div>{{ $meta['title'] ?? '' }}</div>
        <div>{{ $meta['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>
      </div>
    </div>

  </div><!-- /container -->

  <script>
    function applyPrintOrientation(isLandscape) {
      let style = document.getElementById('print-orientation-style');
      if (!style) {
        style = document.createElement('style');
        style.id = 'print-orientation-style';
        document.head.appendChild(style);
      }
      style.textContent = isLandscape
        ? '@media print { @page { size: A4 landscape; margin: 8mm 10mm; } }'
        : '@media print { @page { size: A4 portrait;  margin: 8mm 10mm; } }';
    }

    function getQueryParam(name) {
      const m = new RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
      return m ? decodeURIComponent(m[1].replace(/\+/g, ' ')) : null;
    }

    function printNow() {
      const isLandscape = document.getElementById('landscapeToggle').checked;
      applyPrintOrientation(isLandscape);
      setTimeout(() => window.print(), 50);
    }

    document.addEventListener('DOMContentLoaded', function () {
      const toggle = document.getElementById('landscapeToggle');
      const orient = (getQueryParam('orient') || '').toLowerCase();
      if (orient === 'landscape') toggle.checked = true;
      applyPrintOrientation(toggle.checked);
      toggle.addEventListener('change', function(){ applyPrintOrientation(this.checked); });
      if (window.location.hash === '#auto-print') {
        setTimeout(() => printNow(), 400);
      }
    });
  </script>
</body>
</html>
