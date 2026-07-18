@php($row = $row ?? ($x ?? null))
@csrf
<div class="row g-3">
  <div class="col-md-3">
    <label class="form-label">@lang('assets.code')</label>
    <input type="text" name="code" value="{{ old('code', $row->code ?? '') }}" class="form-control" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">@lang('assets.name_ar')</label>
    <input type="text" name="name_ar" value="{{ old('name_ar', $row->name_ar ?? '') }}" class="form-control" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">@lang('assets.name_en')</label>
    <input type="text" name="name_en" value="{{ old('name_en', $row->name_en ?? '') }}" class="form-control">
  </div>
  <div class="col-md-3">
    <label class="form-label">النوع</label>
    <select name="type" class="form-select" required>
      @php($t = old('type', $row->type ?? 'site'))
      <option value="site" @selected($t==='site')>موقع</option>
      <option value="building" @selected($t==='building')>مبنى</option>
      <option value="floor" @selected($t==='floor')>طابق</option>
      <option value="room" @selected($t==='room')>غرفة</option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label">@lang('assets.parent')</label>
    <select name="parent_id" class="form-select select2">
      <option value="">—</option>
      @foreach(($parents ?? []) as $p)
        <option value="{{ $p->id }}" @selected(old('parent_id', $row->parent_id ?? null)==$p->id)>{{ $p->name_ar ?? $p->name_en ?? $p->code }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $row->is_active ?? 1))>
      <label class="form-check-label" for="is_active">@lang('assets.active')</label>
    </div>
  </div>
</div>
<script>$('.select2').select2({width:'100%'});</script>
