@php($row = $row ?? ($x ?? null))
@csrf
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">@lang('assets.code')</label>
    <input type="text" name="code" value="{{ old('code', $row->code ?? '') }}" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">@lang('assets.name')</label>
    <input type="text" name="name" value="{{ old('name', $row->name ?? '') }}" class="form-control" required>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $row->is_active ?? 1))>
      <label class="form-check-label" for="is_active">@lang('assets.active')</label>
    </div>
  </div>
</div>
