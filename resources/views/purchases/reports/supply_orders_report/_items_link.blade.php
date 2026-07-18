@php
  $count = (int)($count ?? 0);
@endphp
@if($count > 0)
  <a href="{{ route('supply_orders_report.show', $id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
    {{ trans('purchases.view_items') }} ({{ $count }})
  </a>
@else
  <span class="text-muted">—</span>
@endif
