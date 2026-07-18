@csrf

@php
    use Illuminate\Support\Carbon;

    // تثبيت قيم التاريخ بصيغة Y-m-d كي تظهر داخل <input type="date">
    $purchase_val = old('purchase_date');
    if (!$purchase_val) {
        $purchase_val = isset($row) && $row->purchase_date
            ? Carbon::parse($row->purchase_date)->toDateString()
            : ($today ?? '');
    }

    $in_service_val = old('in_service_date');
    if (!$in_service_val) {
        $in_service_val = isset($row) && $row->in_service_date
            ? Carbon::parse($row->in_service_date)->toDateString()
            : '';
    }
@endphp

<div class="row g-3">
  {{-- الكود (مطلوب) --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.code')</label>
    <input type="text" name="code" value="{{ old('code', $row->code ?? $code ?? '') }}" class="form-control" required>
    <div class="form-text text-danger">{{ __('assets.required_field') }}</div>
    @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الاسم العربي (مطلوب) --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.name_ar')</label>
    <input type="text" name="name_ar" value="{{ old('name_ar', $row->name_ar ?? '') }}" class="form-control" required>
    <div class="form-text text-danger">{{ __('assets.required_field') }}</div>
    @error('name_ar')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الاسم الإنجليزي (مطلوب) --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.name_en')</label>
    <input type="text" name="name_en" value="{{ old('name_en', $row->name_en ?? '') }}" class="form-control" required>
    <div class="form-text text-danger">{{ __('assets.required_field') }}</div>
    @error('name_en')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- السيريال --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.serial_no')</label>
    <input type="text" name="serial_no" value="{{ old('serial_no', $row->serial_no ?? '') }}" class="form-control">
    @error('serial_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- التصنيف --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.category')</label>
    <select name="category_id" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      @foreach($cats as $c)
        <option value="{{ $c->id }}" @selected(old('category_id', $row->category_id ?? null)==$c->id)>
          {{ $c->name_ar ?? $c->name_en ?? $c->code }}
        </option>
      @endforeach
    </select>
    @error('category_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الماركة --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.brand')</label>
    <select name="brand_id" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      @foreach($brands as $b)
        <option value="{{ $b->id }}" @selected(old('brand_id', $row->brand_id ?? null)==$b->id)>
          {{ $b->name ?? $b->code }}
        </option>
      @endforeach
    </select>
    @error('brand_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الموديل --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.model')</label>
    <select name="model_id" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      @foreach($models as $m)
        <option value="{{ $m->id }}" @selected(old('model_id', $row->model_id ?? null)==$m->id)>
          {{ $m->name ?? $m->code }}
        </option>
      @endforeach
    </select>
    @error('model_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الموقع --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.location')</label>
    <select name="location_id" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      @foreach($locs as $l)
        <option value="{{ $l->id }}" @selected(old('location_id', $row->location_id ?? null)==$l->id)>
          {{ $l->name_ar ?? $l->name_en }}
        </option>
      @endforeach
    </select>
    @error('location_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- المخزن --}}
  <div class="col-md-4">
    <label class="form-label">@lang('assets.store')</label>
    <select name="store_id" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      @foreach($stores as $s)
        <option value="{{ $s->id }}" @selected(old('store_id', $row->store_id ?? null)==$s->id)>
          {{ $s->manual_code ? $s->manual_code.' — ' : '' }}{{ $s->name_ar ?? $s->name_en }}
        </option>
      @endforeach
    </select>
    @error('store_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- المورد --}}
  <div class="col-md-4">
    <label class="form-label">@lang('assets.supplier')</label>
    <select name="supplier_id" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      @foreach($suppliers as $sp)
        <option value="{{ $sp->id }}" @selected(old('supplier_id', $row->supplier_id ?? null)==$sp->id)>
          {{ $sp->code ? $sp->code.' — ' : '' }}{{ $sp->name_ar ?? $sp->name_en }}
        </option>
      @endforeach
    </select>
    @error('supplier_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الحالة (مطلوب) --}}
  <div class="col-md-4">
    <label class="form-label">@lang('assets.status')</label>
    @php($st = old('status', $row->status ?? 'in_service'))
    <select name="status" class="form-select sel2" required>
      <option value="in_service"  @selected($st==='in_service')>@lang('assets.status_in_service')</option>
      <option value="maintenance" @selected($st==='maintenance')>@lang('assets.status_maintenance')</option>
      <option value="retired"     @selected($st==='retired')>@lang('assets.status_retired')</option>
      <option value="scrapped"    @selected($st==='scrapped')>@lang('assets.status_scrapped')</option>
    </select>
    <div class="form-text text-danger">{{ __('assets.required_field') }}</div>
    @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- تاريخ الشراء --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.purchase_date')</label>
    <input type="date" name="purchase_date" value="{{ $purchase_val }}" class="form-control">
    @error('purchase_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- تاريخ الدخول للخدمة --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.in_service_date')</label>
    <input type="date" name="in_service_date" value="{{ $in_service_val }}" class="form-control">
    <div class="form-text text-muted">{{ __('assets.hints.in_service_date') }}</div>
    @error('in_service_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- التكلفة --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.cost')</label>
    <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost', $row->cost ?? '') }}" class="form-control">
    @error('cost')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- القيمة المتبقية + شرح --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.residual_value')</label>
    <input type="number" step="0.01" min="0" name="residual_value" value="{{ old('residual_value', $row->residual_value ?? 0) }}" class="form-control">
    <div class="form-text text-muted">{{ __('assets.hints.residual_value') }}</div>
    @error('residual_value')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الكمية --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.qty')</label>
    <input type="number" min="1" name="qty" value="{{ old('qty', $row->qty ?? 1) }}" class="form-control">
    @error('qty')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- طريقة الإهلاك + شرح --}}
  <div class="col-md-3">
    <label class="form-label">@lang('assets.dep_method')</label>
    @php($dm = old('dep_method', $row->dep_method ?? ''))
    <select name="dep_method" id="dep_method" class="form-select sel2">
      <option value="">{{ __('assets.choose_optional') }}</option>
      <option value="SL"  @selected($dm==='SL')>{{ __('assets.dep_method_SL') }}</option>
      <option value="DDB" @selected($dm==='DDB')>{{ __('assets.dep_method_DDB') }}</option>
      <option value="UOP" @selected($dm==='UOP')>{{ __('assets.dep_method_UOP') }}</option>
    </select>
    <div class="form-text text-muted">{{ __('assets.hints.dep_method') }}</div>
    @error('dep_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- العمر الإنتاجي (شهور) + شرح (يظهر في SL/DDB) --}}
  <div class="col-md-3" id="group-life">
    <label class="form-label">@lang('assets.useful_life_months')</label>
    <input type="number" min="1" name="useful_life_months" value="{{ old('useful_life_months', $row->useful_life_months ?? '') }}" class="form-control">
    <div class="form-text text-muted">
      {{ __('assets.hints.useful_life_months') }} —
      <span id="life-or-rate-hint" class="text-warning d-none">{{ __('assets.validation.life_or_rate') }}</span>
    </div>
    @error('useful_life_months')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- نسبة الإهلاك + شرح (يظهر في SL/DDB) --}}
  <div class="col-md-3" id="group-rate">
    <label class="form-label">@lang('assets.dep_rate')</label>
    <div class="input-group">
      <input type="number" step="0.001" min="0" max="100" name="dep_rate" value="{{ old('dep_rate', $row->dep_rate ?? '') }}" class="form-control">
      <span class="input-group-text">%</span>
    </div>
    <div class="form-text text-muted">{{ __('assets.hints.dep_rate') }}</div>
    @error('dep_rate')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- UOP: إجمالي وحدات الإنتاج المقدّرة للعمر + شرح --}}
  <div class="col-md-3 d-none" id="group-uop">
    <label class="form-label">@lang('assets.uop_total_estimated_units')</label>
    <input type="number" min="1" name="uop_total_estimated_units" value="{{ old('uop_total_estimated_units', $row->uop_total_estimated_units ?? '') }}" class="form-control">
    <div class="form-text text-muted">
      {{ __('assets.hints.uop_total_estimated_units') }} —
      <span class="text-warning">{{ __('assets.validation.uop_total_required') }}</span>
    </div>
    @error('uop_total_estimated_units')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- نشط --}}
  <div class="col-md-3">
    <div class="form-check mt-4 pt-2">
      <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $row->is_active ?? 1))>
      <label class="form-check-label" for="is_active">@lang('assets.active')</label>
    </div>
    @error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>
</div>

{{-- Select2 + منطق العرض --}}
<script>
$(function(){
  $('.sel2').select2({
    width:'100%',
    placeholder: '{{ __('assets.choose_optional') }}',
    allowClear:true
  });

  // تثبيت القيم الحالية بعد التهيئة (قائمة ديناميكية)
  const sid  = @json(old('store_id',    $row->store_id    ?? null));
  const spid = @json(old('supplier_id', $row->supplier_id ?? null));
  if (sid)  $('select[name="store_id"]').val(String(sid)).trigger('change');
  if (spid) $('select[name="supplier_id"]').val(String(spid)).trigger('change');

  function toggleDepGroups() {
    const m = $('#dep_method').val();
    if (m === 'UOP') {
      $('#group-uop').removeClass('d-none');
      $('#group-life, #group-rate').addClass('d-none'); // لا يلزم life/rate
    } else if (m === 'SL' || m === 'DDB') {
      $('#group-uop').addClass('d-none');
      $('#group-life, #group-rate').removeClass('d-none'); // يُكتفى بأحدهما
    } else {
      $('#group-uop').addClass('d-none');
      $('#group-life, #group-rate').addClass('d-none');
    }
    checkLifeOrRateHint();
  }

  function checkLifeOrRateHint(){
    const m = $('#dep_method').val();
    const life = $('input[name="useful_life_months"]').val();
    const rate = $('input[name="dep_rate"]').val();
    if ((m === 'SL' || m === 'DDB') && (!life || life === '') && (!rate || rate === '')) {
      $('#life-or-rate-hint').removeClass('d-none');
    } else {
      $('#life-or-rate-hint').addClass('d-none');
    }
  }

  $('#dep_method').on('change', toggleDepGroups);
  $('input[name="useful_life_months"], input[name="dep_rate"]').on('input', checkLifeOrRateHint);
  toggleDepGroups();

  // لو in_service_date فارغ وغيّرت purchase_date، ضعه نفسه
  $('input[name="purchase_date"]').on('change', function(){
    const ins = $('input[name="in_service_date"]');
    if (!ins.val()) ins.val($(this).val());
  });
});
</script>
