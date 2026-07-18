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
    .header-logo img{ max-height:90px; max-width:120px; object-fit:contain; }
    .header-center{ flex:1; text-align:center; }
    .org-name{ font-size:26px; font-weight:700; color:#1a5490; margin-bottom:5px; }
    .org-subtitle{ font-size:14px; color:#666; margin-bottom:3px; }
    .header-right{
      flex:0 0 auto; text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
      font-size:11px; color:#666; line-height:1.6; min-width:180px; padding:10px;
      background:#f8f9fa; border-radius:4px; border-{{ app()->getLocale() === 'ar' ? 'left':'right' }}:3px solid #1a5490;
    }
    .header-right-label{ font-weight:600; color:#1a5490; font-size:12px; margin-bottom:6px; }

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

    table{ width:100%; border-collapse:collapse; font-size:11px; margin-top:10px; margin-bottom:10px; }
    thead{ background:linear-gradient(135deg,#1a5490 0%,#2d7ab8 100%); color:#fff; }
    th{
      border:1px solid #1a5490; padding:10px 6px; text-align:{{ app()->getLocale()==='ar' ? 'right':'left' }};
      font-weight:600; font-size:11px; white-space:nowrap;
    }
    td{ border:1px solid #ddd; padding:8px 6px; color:#555; font-size:11px; }
    tbody tr:nth-child(even){ background:#f9f9f9; }
    .no-results{ text-align:center; padding:30px !important; color:#999; font-style:italic; }

    @media print {
      .no-print{ display:none !important; }
      body{ margin:0; padding:0; background:#fff; }
      .container{ padding:8px; max-width:100%; }
      thead{ display: table-header-group; }
      tfoot{ display: table-footer-group; }
      tr{ page-break-inside: avoid; break-inside: avoid; }
      @page{ size: A4 portrait; margin: 8mm 10mm; }
    }

    .print-btn-container{ text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; margin-bottom:15px; }
    .print-btn{ background:#1a5490; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; font-size:14px; font-weight:600; }
  </style>

  <style id="print-orientation">
    @page{ size: A4 portrait; margin: 8mm 10mm; }
  </style>
</head>
<body>
  <div class="print-btn-container no-print">
    <label class="form-check">
      <input type="checkbox" id="landscapeToggle">
      <span>{{ trans('suppliers.landscape_mode') }}</span>
    </label>
    &nbsp;&nbsp;
    <button class="print-btn" onclick="window.print()">{{ trans('suppliers.print') }}</button>
  </div>

  <div class="container">
    <div class="main-header">
      <div class="header-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></div>
      <div class="header-center">
        <div class="org-name">{{ trans('suppliers.org_name', [], 'ar') ?? 'مؤسستك' }}</div>
        <div class="org-subtitle">{{ trans('suppliers.supplier_payment_report_title') }}</div>
      </div>
      <div class="header-right">
        <div class="header-right-label">{{ trans('suppliers.print_info') }}</div>
        <div>{{ trans('suppliers.generated_at') }}: {{ $meta['generated_at'] ?? '' }}</div>
      </div>
    </div>

    <div class="report-header">
      <div class="report-title-section">
        <div class="report-title">{{ $meta['title'] ?? '' }}</div>
        <div class="report-meta">
          <div class="report-meta-item">
            @if(!empty($meta['date_from']))
              <div class="report-meta-row">
                <span class="meta-label">{{ trans('suppliers.filter_date_from') }}:</span>
                <span class="meta-value">{{ $meta['date_from'] }}</span>
              </div>
            @endif
            @if(!empty($meta['date_to']))
              <div class="report-meta-row">
                <span class="meta-label">{{ trans('suppliers.filter_date_to') }}:</span>
                <span class="meta-value">{{ $meta['date_to'] }}</span>
              </div>
            @endif
            @if(!empty($meta['receipt_no']))
              <div class="report-meta-row">
                <span class="meta-label">{{ trans('suppliers.filter_receipt_no') }}:</span>
                <span class="meta-value">{{ $meta['receipt_no'] }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="count-box">
        <div class="count-label">{{ trans('suppliers.records_count') }}</div>
        <div class="count-value">{{ $meta['count'] ?? 0 }}</div>
      </div>
    </div>

    @if(!empty($meta['supplier_names']))
      <div class="filters">
        <div class="filters-title">{{ trans('suppliers.filters_title') }}</div>
        <div class="filter-row">
          <div class="filter-item">
            <span class="filter-label">{{ trans('suppliers.filter_suppliers') }}:</span>
            <span class="filter-value">{{ implode(', ', $meta['supplier_names']) }}</span>
          </div>
        </div>
      </div>
    @endif

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_receipt') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_date') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_supplier') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_cheque_amount') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_cheque_no') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_cash_amount') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_deduction') }}</th>
          <th>{{ trans('suppliers.supplier_payment_report_th_credit_settle') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $i=>$r)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['receipt_no'] }}</td>
            <td>{{ $r['paid_date'] }}</td>
            <td>{{ $r['supplier'] }}</td>
            <td style="text-align:right">{{ $r['cheque_amount'] }}</td>
            <td>{{ $r['cheque_no'] }}</td>
            <td style="text-align:right">{{ $r['cash_amount'] }}</td>
            <td style="text-align:right">{{ $r['deduction'] }}</td>
            <td style="text-align:right">{{ $r['credit_note'] }}</td>
          </tr>
        @empty
          <tr><td colspan="9" class="no-results">{{ trans('suppliers.no_results') }}</td></tr>
        @endforelse
      </tbody>
    </table>

    <div style="margin-top:18px; border-top:2px solid #1a5490; padding-top:10px; text-align:center; color:#666; font-size:10px;">
      <div>{{ trans('suppliers.supplier_payment_report_title') }}</div>
      <div>{{ $meta['generated_at'] ?? now()->format('Y-m-d H:i') }}</div>
    </div>
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
    document.addEventListener('DOMContentLoaded', function () {
      if (window.location.hash === '#auto-print') {
        setTimeout(() => window.print(), 400);
      }
    });
  </script>
</body>
</html>
