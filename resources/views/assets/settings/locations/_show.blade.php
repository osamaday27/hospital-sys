<div class="row g-3">
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.code')</div>
      <div class="fw-semibold">{{ $x->code }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.name')</div>
      <div class="fw-semibold">{{ $x->name_ar ?? $x->name_en }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.type')</div>
      <div class="fw-semibold">{{ $x->type }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.parent')</div>
      <div class="fw-semibold">{{ optional($x->parent)->name_ar ?? optional($x->parent)->name_en ?? '-' }}</div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.status')</div>
      <div class="fw-semibold">
        {!! $x->is_active
            ? '<span class="badge bg-success">'.__('assets.active').'</span>'
            : '<span class="badge bg-secondary">'.__('assets.inactive').'</span>' !!}
      </div>
    </div>
  </div>
</div>

