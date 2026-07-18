<div class="row g-3">
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.code')</div>
      <div class="fw-semibold">{{ $r->code }}</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.name')</div>
      <div class="fw-semibold">{{ $r->name_ar ?? $r->name_en }}</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.parent')</div>
      <div class="fw-semibold">{{ optional($r->parent)->name_ar ?? optional($r->parent)->name_en ?? '-' }}</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="border p-2 rounded mb-3">
      <div class="small text-muted">@lang('assets.status')</div>
      <div class="fw-semibold">
        {!! $r->is_active
            ? '<span class="badge bg-success">'.__('assets.active').'</span>'
            : '<span class="badge bg-secondary">'.__('assets.inactive').'</span>' !!}
      </div>
    </div>
  </div>

  @if($r->useful_life_months || $r->default_dep_method || $r->default_dep_rate)
    <div class="col-12">
      <div class="border p-3 rounded">
        <div class="small text-muted mb-1">@lang('assets.default_settings')</div>
        <div>
          @lang('assets.life_months'): {{ $r->useful_life_months ?? '-' }}
          — @lang('assets.dep_method'): {{ $r->default_dep_method ?? '-' }}
          — @lang('assets.dep_rate'): {{ $r->default_dep_rate ?? '-' }}
        </div>
      </div>
    </div>
  @endif
</div>
