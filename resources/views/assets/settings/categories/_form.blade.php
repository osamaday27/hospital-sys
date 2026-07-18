@csrf
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">@lang('assets.code')</label>
    <input type="text"
           name="code"
           value="{{ old('code', $row->code ?? '') }}"
           class="form-control @error('code') is-invalid @enderror" required>
    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4">
    <label class="form-label">@lang('assets.name_ar')</label>
    <input type="text"
           name="name_ar"
           value="{{ old('name_ar', $row->name_ar ?? '') }}"
           class="form-control @error('name_ar') is-invalid @enderror" required>
    @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-4">
    <label class="form-label">@lang('assets.name_en')</label>
    <input type="text"
           name="name_en"
           value="{{ old('name_en', $row->name_en ?? '') }}"
           class="form-control @error('name_en') is-invalid @enderror">
    @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">@lang('assets.parent')</label>
    <select name="parent_id"
            class="form-select select2 @error('parent_id') is-invalid @enderror">
      <option value="">@lang('assets.none')</option>
      @foreach(($parents ?? []) as $p)
        <option value="{{ $p->id }}"
                @selected(old('parent_id', $row->parent_id ?? null)==$p->id)>
          {{ $p->name_ar ?? $p->name_en ?? $p->code }}
        </option>
      @endforeach
    </select>
    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-3">
    <label class="form-label">@lang('assets.life_months')</label>
    <input type="number"
           name="useful_life_months"
           value="{{ old('useful_life_months', $row->useful_life_months ?? '') }}"
           class="form-control @error('useful_life_months') is-invalid @enderror">
    @error('useful_life_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check">
      <input type="checkbox"
             class="form-check-input @error('is_active') is-invalid @enderror"
             id="is_active"
             name="is_active"
             value="1"
             @checked(old('is_active', $row->is_active ?? 1))>
      <label class="form-check-label" for="is_active">@lang('assets.active')</label>
      @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

<script>
  $('.select2').select2({width:'100%'});
</script>
