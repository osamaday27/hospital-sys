@php $count = (int)($count ?? 0); @endphp
<a href="{{ route('late_delivery_items.show', $id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
  {{ trans('purchases.view_items') }} ({{ $count }})
</a>
