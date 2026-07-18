<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>{{ __('assets.depreciation.run_print_title') }} #{{ $run->id }}</title>
  <style>
    body { font-family: Tahoma, Arial, sans-serif; }
    h3 { margin: 0 0 8px; }
    .meta { font-size:12px; color:#555; margin-bottom:6px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #999; padding:4px 6px; font-size:12px; }
    thead { background:#eee; }
    tfoot td { font-weight: bold; background:#fafafa; }
    .text-end { text-align: right; }
  </style>
</head>
<body>
  @php
    $periodYmd   = optional($run->period)->toDateString();
    $statusLabel = __('assets.depreciation.status_' . $run->status);
  @endphp

  <h3>{{ __('assets.depreciation.run_print_title') }} #{{ $run->id }}</h3>

  <div class="meta">
    {{ __('assets.depreciation.period') }}: {{ $periodYmd }}
    &nbsp;|&nbsp;
    {{ __('assets.depreciation.status') }}: {{ $statusLabel }}
    @if($run->notes)
      &nbsp;|&nbsp;
      {{ __('assets.notes') }}: {{ $run->notes }}
    @endif
  </div>

  <hr>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>{{ __('assets.depreciation.asset') }}</th>
        <th>{{ __('assets.depreciation.method') }}</th>
        <th>{{ __('assets.depreciation.amount') }}</th>
        <th>{{ __('assets.depreciation.accum_before') }}</th>
        <th>{{ __('assets.depreciation.accum_after') }}</th>
        <th>{{ __('assets.depreciation.nbv_before') }}</th>
        <th>{{ __('assets.depreciation.nbv_after') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($run->lines as $i=>$ln)
        @php
          $methodLabel = $ln->dep_method ? __('assets.dep_method_' . $ln->dep_method) : '-';
        @endphp
        <tr>
          <td>{{ $i+1 }}</td>
          <td>[{{ $ln->asset_id }}] {{ $ln->asset?->code }} - {{ $ln->asset?->name_ar ?? $ln->asset?->name_en }}</td>
          <td>{{ $methodLabel }}</td>
          <td class="text-end">{{ number_format($ln->amount,2) }}</td>
          <td class="text-end">{{ number_format($ln->accum_before,2) }}</td>
          <td class="text-end">{{ number_format($ln->accum_after,2) }}</td>
          <td class="text-end">{{ number_format($ln->nbv_before,2) }}</td>
          <td class="text-end">{{ number_format($ln->nbv_after,2) }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">{{ __('assets.depreciation.total_amount') }}</td>
        <td class="text-end">
          {{ number_format($run->lines->sum('amount'), 2) }}
        </td>
        <td colspan="4"></td>
      </tr>
    </tfoot>
  </table>
</body>
</html>
