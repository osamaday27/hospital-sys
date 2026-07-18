<div class="row g-3">
  <div class="col-md-4">
    <div class="border p-2 rounded">
      <div class="small text-muted">@lang('assets.code')</div>
      <div class="fw-semibold">{{ $x->code }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="border p-2 rounded">
      <div class="small text-muted">@lang('assets.name')</div>
      <div class="fw-semibold">{{ $x->name }}</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="border p-2 rounded">
      <div class="small text-muted">@lang('assets.status')</div>
<div class="fw-semibold">
  {!! $x->is_active
      ? '<span class="badge bg-success">'.__('assets.active').'</span>'
      : '<span class="badge bg-secondary">'.__('assets.inactive').'</span>' !!}
</div>

    </div>
  </div>
</div>
