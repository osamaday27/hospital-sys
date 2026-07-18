@extends('layouts.master_table')

@section('title') {{ __('assets.maintenance.show.title') }} @stop

@section('content')
@php
  // خرائط ألوان الـ Badges (Bootstrap)
  $typeColors = [
    'corrective' => 'bg-danger',
    'preventive' => 'bg-success',
    'inspection' => 'bg-info',
  ];
  $statusColors = [
    'open'        => 'bg-secondary',
    'in_progress' => 'bg-warning',
    'done'        => 'bg-success',
    'cancelled'   => 'bg-dark',
  ];
  $priorityColors = [
    'low'    => 'bg-secondary',
    'medium' => 'bg-info',
    'high'   => 'bg-danger',
  ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">{{ __('assets.maintenance.show.heading', ['code' => $row->code]) }}</h4>
  <a href="{{ route('assets.programs.maintenance.index') }}" class="btn btn-light">
    <i class="ri-arrow-go-back-line"></i> {{ __('assets.maintenance.buttons.back') }}
  </a>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.asset') }}</div>
      <div class="fw-semibold">
        {{ $row->asset?->code }} - {{ $row->asset?->name_ar ?? $row->asset?->name_en }}
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.type') }}</div>
      <div class="fw-semibold">
        <span class="badge {{ $typeColors[$row->maintenance_type] ?? 'bg-secondary' }}">
          {{ __('assets.maintenance.types.' . $row->maintenance_type) }}
        </span>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.status') }}</div>
      <div class="fw-semibold">
        <span class="badge {{ $statusColors[$row->status] ?? 'bg-secondary' }}">
          {{ __('assets.maintenance.statuses.' . $row->status) }}
        </span>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.priority') }}</div>
      <div class="fw-semibold">
        <span class="badge {{ $priorityColors[$row->priority] ?? 'bg-secondary' }}">
          {{ __('assets.maintenance.priorities.' . $row->priority) }}
        </span>
      </div>
    </div>
  </div>

  <div class="col-md-2">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.total') }}</div>
      <div class="fw-semibold">{{ number_format($row->cost_total,2) }}</div>
    </div>
  </div>

  <div class="col-12">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.summary') }}</div>
      <div class="fw-semibold">{{ $row->summary }}</div>
    </div>
  </div>

  @if($row->description)
  <div class="col-12">
    <div class="border p-2">
      <div class="text-muted small">{{ __('assets.maintenance.show.labels.description') }}</div>
      <div class="fw-semibold">{{ $row->description }}</div>
    </div>
  </div>
  @endif
</div>

<div class="card mt-3">
  <div class="card-header"><strong>{{ __('assets.maintenance.tasks.card_title') }}</strong></div>
  <div class="card-body table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>{{ __('assets.maintenance.tasks.table.index') }}</th>
          <th>{{ __('assets.maintenance.tasks.table.description') }}</th>
          <th>{{ __('assets.maintenance.tasks.table.performed_at') }}</th>
          <th>{{ __('assets.maintenance.tasks.table.hours') }}</th>
          <th>{{ __('assets.maintenance.tasks.table.cost') }}</th>
          <th>{{ __('assets.maintenance.tasks.table.technician') }}</th>
          <th>{{ __('assets.maintenance.tasks.table.notes') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($row->tasks as $i=>$t)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $t->description }}</td>
            <td>{{ optional($t->performed_at)->format('Y-m-d') }}</td>
            <td>{{ $t->hours }}</td>
            <td>{{ number_format($t->cost,2) }}</td>
            <td>{{ $t->technician_name }}</td>
            <td>{{ $t->notes }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted">
              {{ __('assets.maintenance.tasks.empty') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
