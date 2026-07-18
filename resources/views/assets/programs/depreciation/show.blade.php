@extends('layouts.master_table')

@section('title') {{ __('assets.depreciation.show_title') }} @stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  // ألوان الشارات حسب الحالة
  $badgeMap = [
    'draft'  => 'badge bg-secondary',
    'posted' => 'badge bg-success',
    'void'   => 'badge bg-danger',
  ];
  $badgeClass = $badgeMap[$run->status] ?? 'badge bg-light text-dark';

  // ترجمة الحالة (يفضَّل استخدام Accessor من الموديل إن وُجد)
  $statusLabel = method_exists($run, 'getStatusLabelAttribute')
    ? $run->status_label
    : __('assets.depreciation.status_' . $run->status);

  // تاريخ الفترة بصيغة YYYY-MM-DD
  $periodYmd = optional($run->period)->toDateString();
@endphp

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('assets.depreciation.show_title') }} #{{ $run->id }}</h4>
    <div class="btn-group">
      @if($run->status === 'draft')
        <form method="post" action="{{ route('assets.programs.depreciation.post', $run->id) }}">@csrf
          <button class="btn btn-success">
            <i class="ri-check-line"></i> {{ __('assets.depreciation.post') }}
          </button>
        </form>
      @elseif($run->status === 'posted')
        <form method="post" action="{{ route('assets.programs.depreciation.unpost', $run->id) }}"
              onsubmit="return confirm('{{ __('assets.depreciation.unpost_confirm') }}')">@csrf
          <button class="btn btn-warning">
            <i class="ri-arrow-go-back-line"></i> {{ __('assets.depreciation.unpost') }}
          </button>
        </form>
      @endif
      <a href="{{ route('assets.programs.depreciation.export.csv', $run->id) }}" class="btn btn-outline-primary">
        <i class="ri-file-excel-2-line"></i> {{ __('assets.depreciation.export_csv') }}
      </a>
      <a href="{{ route('assets.programs.depreciation.export.print', $run->id) }}" class="btn btn-outline-secondary" target="_blank">
        <i class="ri-printer-line"></i> {{ __('assets.depreciation.print') }}
      </a>
      <a href="{{ route('assets.programs.depreciation.index') }}" class="btn btn-light">{{ __('assets.back') }}</a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-3">
        <b>{{ __('assets.depreciation.period') }}</b>:
        {{ $periodYmd }}
      </div>
      <div class="col-md-3">
        <b>{{ __('assets.depreciation.status') }}</b>:
        <span class="{{ $badgeClass }}">{{ $statusLabel }}</span>
      </div>
      <div class="col-md-6">
        <b>{{ __('assets.notes') }}</b>: {{ $run->notes }}
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
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
              <td class="text-end">{{ number_format($ln->amount, 2) }}</td>
              <td class="text-end">{{ number_format($ln->accum_before, 2) }}</td>
              <td class="text-end">{{ number_format($ln->accum_after, 2) }}</td>
              <td class="text-end">{{ number_format($ln->nbv_before, 2) }}</td>
              <td class="text-end">{{ number_format($ln->nbv_after, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</div>
@endsection
