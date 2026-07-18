@php
  $url = route('supply_requests_report.show', $id);
@endphp
<a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">
  <i class="ri-file-list-3-line"></i>
  {{ trans('purchases.supply_requests_report_view_items') }}
  <span class="badge bg-secondary">{{ (int)$count }}</span>
</a>
