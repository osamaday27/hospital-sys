<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>طباعة الجرد #{{ $audit->code }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="{{ asset('assets/css/bootstrap-rtl.min.css') }}" rel="stylesheet"> {{-- أو رابط Bootstrap لديك --}}
  <style>
    @media print { .no-print { display:none !important; } .table th, .table td { page-break-inside: avoid; } }
    body { padding: 16px; }
  </style>
</head>
<body>
  <div class="no-print mb-3 d-flex gap-2">
    <button class="btn btn-primary" onclick="window.print()"><i class="ri-printer-line"></i> @lang('assets.print')</button>
    <button class="btn btn-light" onclick="window.close()">@lang('assets.close')</button>
  </div>

  <h5 class="mb-2">@lang('assets.audit.index_title')</h5>
  <div class="mb-3">
    <div><strong>@lang('assets.code'):</strong> {{ $audit->code }}</div>
    <div><strong>@lang('assets.title'):</strong> {{ $audit->title }}</div>
    <div><strong>@lang('assets.status'):</strong> {{ $audit->status_label }}</div>
    <div class="mt-2 d-flex gap-3 flex-wrap">
      <span><strong>@lang('assets.audit.expected'):</strong> {{ $audit->expected_count }}</span>
      <span><strong>@lang('assets.audit.scanned'):</strong> {{ $audit->scanned_count }}</span>
      <span><strong class="text-success">@lang('assets.audit.matched'):</strong> {{ $audit->matched_count }}</span>
      <span><strong class="text-warning">@lang('assets.audit.mismatch'):</strong> {{ $audit->mismatch_count }}</span>
      <span><strong class="text-danger">@lang('assets.audit.missing'):</strong> {{ $audit->missing_count }}</span>
      <span><strong class="text-info">@lang('assets.audit.extra'):</strong> {{ $audit->extra_count }}</span>
    </div>
  </div>

  <table class="table table-bordered table-sm align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>@lang('assets.depreciation.asset')</th>
        <th>@lang('assets.audit.result')</th>
        <th>@lang('assets.location')</th>
        <th>@lang('assets.store')</th>
        <th>@lang('assets.status')</th>
        <th>@lang('assets.qty')</th>
        <th>@lang('assets.audit.scans')</th>
        <th>@lang('assets.updated_at')</th>
      </tr>
    </thead>
    <tbody>
      @foreach($audit->lines as $i => $ln)
        @php
          $expQty   = optional($ln->asset)->qty ?? 1;
          $foundQty = (int)($ln->scan_count ?? 0);
          $expLoc = $ln->expected_location_id ? ($locMap[$ln->expected_location_id] ?? $ln->expected_location_id) : '-';
          $foundLoc = $ln->found_location_id ? ($locMap[$ln->found_location_id] ?? $ln->found_location_id) : '-';
          $expStore = $ln->expected_store_id ? ($storeMap[$ln->expected_store_id] ?? $ln->expected_store_id) : '-';
          $foundStore = $ln->found_store_id ? ($storeMap[$ln->found_store_id] ?? $ln->found_store_id) : '-';
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td>[{{ $ln->asset_id ?? '-' }}] {{ $ln->asset_code }} — {{ $ln->asset->name_ar ?? $ln->asset->name_en }}</td>
          <td>{{ trans('assets.audit.result_'.$ln->result) }}</td>
          <td>{{ $expLoc }} ➜ {{ $foundLoc }}</td>
          <td>{{ $expStore }} ➜ {{ $foundStore }}</td>
          <td>{{ $ln->expected_status ? trans('assets.status_'.$ln->expected_status) : '-' }} ➜ {{ $ln->found_status ? trans('assets.status_'.$ln->found_status) : '-' }}</td>
          <td>{{ $expQty }} ➜ {{ $foundQty }}</td>
          <td>{{ (int)$ln->scan_count }}</td>
          <td>{{ optional($ln->last_scanned_at)->toDateTimeString() }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <script>/* يمكن تفعيل الطباعة تلقائياً لو رغبت */ /* window.print(); */</script>
</body>
</html>
