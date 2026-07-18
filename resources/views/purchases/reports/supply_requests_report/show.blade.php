@extends('layouts.master_table')

@section('title')
  {{ trans('purchases.supply_requests_report_details_title') }} - {{ $req->requisition_number }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  * { box-sizing: border-box; }
  .print-btn{ background:#1a5490; color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; }
  .controls{ display:flex; gap:16px; align-items:center; justify-content: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; margin-bottom:12px; }
  @media print { .no-print{ display:none !important; } }

  .main-header{
    display:flex; justify-content:space-between; align-items:center; gap:20px;
    margin-bottom:20px; padding-bottom:20px; border-bottom:3px solid #1a5490;
  }
  .header-logo img{ max-height:70px; max-width:120px; object-fit:contain; }
  .header-center{ flex:1; text-align:center; }
  .org-name{ font-size:24px; font-weight:700; color:#1a5490; margin-bottom:4px; }
  .header-right{
    flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
    font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px;
    background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490;
  }
  .report-title{ font-size:20px; font-weight:700; color:#1a5490; margin:12px 0 6px; text-align:center; }

  .note-box{
    background:#fff3cd; border:1px solid #ffe69c; color:#7a5d00; padding:10px 12px; border-radius:6px;
    margin-bottom:12px; font-size:12px;
  }

  table{ width:100%; border-collapse:collapse; font-size:12px; margin-top:10px; margin-bottom:10px; }
  thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
  th{
    border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }};
    font-weight:600; font-size:12px; white-space:nowrap;
  }
  td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:12px; }
  tbody tr:nth-child(even){ background:#f9f9f9; }
  .text-end{ text-align: right; }
</style>
<style id="print-orientation">
  @page{ size: A4 portrait; margin: 8mm 10mm; }
</style>

<div class="controls no-print">
  <label class="form-check">
    <input type="checkbox" class="form-check-input" id="landscapeToggle">
    <span class="form-check-label">{{ trans('purchases.supply_requests_report_landscape_mode') }}</span>
  </label>
  <button class="print-btn" onclick="window.print()">{{ trans('purchases.print') }}</button>
</div>

<div class="card">
  <div class="card-body">
    <!-- هيدر مطابق لستايل الطباعة -->
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></div>
      <div class="header-center">
        <div class="org-name">{{ trans('purchases.org_name', [], 'ar') ?? 'مؤسستك' }}</div>
        <div class="report-title">{{ trans('purchases.supply_requests_report_details_title') }}</div>
      </div>
      <div class="header-right">
        <div>{{ trans('purchases.generated_at') }}: {{ $generatedAt }}</div>
      </div>
    </div>

    <!-- بيانات عامة عن الطلب -->
    <div class="row g-3 mb-2">
      <div class="col-md-3"><strong>{{ trans('purchases.supply_requests_report_th_no') }}:</strong> {{ $req->requisition_number }}</div>
      <div class="col-md-3"><strong>{{ trans('purchases.supply_requests_report_th_date') }}:</strong> {{ optional($req->requisition_date)->format('Y-m-d') ?? $req->requisition_date }}</div>
      <div class="col-md-3"><strong>{{ trans('purchases.supply_requests_report_th_status') }}:</strong> {{ trans('purchases.supply_requests_report_status_'.$req->status) ?? $req->status }}</div>
      <div class="col-md-3"><strong>{{ trans('purchases.supply_requests_report_th_creator') }}:</strong> {{ $req->creator?->name ?? '—' }}</div>

      <div class="col-md-4"><strong>{{ trans('purchases.supply_requests_report_store') }}:</strong> {{ $req->warehouse?->name_ar ?? $req->warehouse?->name_en ?? '—' }}</div>
      <div class="col-md-4"><strong>{{ trans('purchases.supply_requests_report_department') }}:</strong> {{ $req->department?->name_ar ?? $req->department?->name_ne ?? '—' }}</div>
      <div class="col-md-4"><strong>{{ trans('purchases.supply_requests_report_supplier') }}:</strong> {{ $req->supplier?->name_ar ?? $req->supplier?->name_en ?? '—' }}</div>

      <div class="col-md-12"><strong>{{ trans('purchases.supply_requests_report_reason') }}:</strong> {{ $req->reason }}</div>
    </div>

    <!-- ملاحظة الرصيد -->
    <div class="note-box">
      {{ trans('purchases.supply_requests_report_balance_note') }} {{ $generatedAt }}
    </div>

    <!-- جدول الأصناف -->
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ trans('purchases.supply_requests_report_item_code') }}</th>
            <th>{{ trans('purchases.supply_requests_report_item_name') }}</th>
            <th>{{ trans('purchases.supply_requests_report_item_unit') }}</th>
            <th class="text-end">{{ trans('purchases.supply_requests_report_item_qty') }}</th>
            <th class="text-end">{{ trans('purchases.supply_requests_report_item_price') }}</th>
            <th>{{ trans('purchases.supply_requests_report_item_supplier') }}</th>
            <th class="text-end">{{ trans('purchases.supply_requests_report_item_balance') }}</th>
            <th class="text-end">{{ trans('purchases.supply_requests_report_item_balance_all') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $i=>$it)
            <tr>
              <td>{{ $i+1 }}</td>
              <td>{{ $it['code'] }}</td>
              <td>{{ $it['name'] }}</td>
              <td>{{ $it['unit'] }}</td>
              <td class="text-end">{{ number_format($it['qty'], 2) }}</td>
              <td class="text-end">{{ $it['price'] !== null ? number_format($it['price'], 2) : '—' }}</td>
              <td>{{ $it['supplier'] }}</td>
              <td class="text-end">{{ number_format($it['balance'], 2) }}</td>
              <td class="text-end">{{ number_format($it['balance_all'], 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted">{{ trans('purchases.no_results') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
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
@endsection
