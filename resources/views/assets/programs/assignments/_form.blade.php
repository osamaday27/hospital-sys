@csrf

@php
  $tp          = old('assignable_type', $row->assignable_type ?? 'employee');
  $currentId   = old('assignable_id', $row->assignable_id ?? null);
  $currentText = $currentText ?? null;
@endphp

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">{{ __('assets.asset') }}</label>
    <select name="asset_id" class="form-select sel2" required>
      <option value="">{{ __('assets.choose') }}</option>
      @foreach($assets as $a)
        <option value="{{ $a->id }}"
                data-available="{{ (int)($a->available_qty ?? 0) }}"
                @selected(old('asset_id', $row->asset_id ?? null)==$a->id)>
          {{ $a->code }} — {{ $a->name_ar ?? $a->name_en }}
        </option>
      @endforeach
    </select>
    @error('asset_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label">{{ __('assets.assignable_type') }}</label>
    <select name="assignable_type" id="assignable_type" class="form-select sel2" required>
      <option value="employee"   @selected($tp==='employee')>{{ __('assets.type_employee') }}</option>
      <option value="department" @selected($tp==='department')>{{ __('assets.type_department') }}</option>
      <option value="store"      @selected($tp==='store')>{{ __('assets.type_store') }}</option>
      <option value="supplier"   @selected($tp==='supplier')>{{ __('assets.type_supplier') }}</option>
      <option value="other"      @selected($tp==='other')>{{ __('assets.type_other') }}</option>
    </select>
    @error('assignable_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- معرّف الجهة --}}
  <input type="hidden" name="assignable_id" id="assignable_id" value="{{ $currentId ?? '' }}"/>

  {{-- قوائم الجهة Ajax --}}
  <div class="col-md-4 type-field type-employee">
    <label class="form-label">{{ __('assets.employee') }}</label>
    <select class="form-select sel2-ajax" id="employee_select"
            data-url="{{ route('assets.programs.assignments.select2.employees') }}"
            data-current-id="{{ $tp==='employee' ? $currentId : '' }}"
            data-current-text="{{ $tp==='employee' ? ($currentText ?? '') : '' }}">
    </select>
  </div>

  <div class="col-md-4 type-field type-department">
    <label class="form-label">{{ __('assets.department') }}</label>
    <select class="form-select sel2-ajax" id="department_select"
            data-url="{{ route('assets.programs.assignments.select2.sections') }}"
            data-current-id="{{ $tp==='department' ? $currentId : '' }}"
            data-current-text="{{ $tp==='department' ? ($currentText ?? '') : '' }}">
    </select>
  </div>

  <div class="col-md-4 type-field type-store">
    <label class="form-label">{{ __('assets.store') }}</label>
    <select class="form-select sel2-ajax" id="store_select"
            data-url="{{ route('assets.programs.assignments.select2.stores') }}"
            data-current-id="{{ $tp==='store' ? $currentId : '' }}"
            data-current-text="{{ $tp==='store' ? ($currentText ?? '') : '' }}">
    </select>
  </div>

  <div class="col-md-4 type-field type-supplier">
    <label class="form-label">{{ __('assets.supplier') }}</label>
    <select class="form-select sel2-ajax" id="supplier_select"
            data-url="{{ route('assets.programs.assignments.select2.suppliers') }}"
            data-current-id="{{ $tp==='supplier' ? $currentId : '' }}"
            data-current-text="{{ $tp==='supplier' ? ($currentText ?? '') : '' }}">
    </select>
  </div>

  {{-- الاسم الظاهر --}}
  <div class="col-md-4">
    <label class="form-label">{{ __('assets.assignable_name') }}</label>
    <input type="text" name="assignable_name" class="form-control"
           value="{{ old('assignable_name', $row->assignable_name ?? '') }}"
           placeholder="{{ __('assets.assignable_name_ph') }}">
    <div class="form-text">{{ __('assets.assignable_name_hint') }}</div>
    @error('assignable_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- الكمية المُسندة --}}
  <div class="col-md-4">
    <label class="form-label">{{ __('assets.qty') }}</label>
    <input type="number" min="1" name="qty" id="assign-qty" class="form-control"
           value="{{ old('qty', $row->qty ?? 1) }}" required>
    <div class="form-text">
      <span class="fw-bold" id="available-qty-hint">المتاح: -</span>
    </div>
    @error('qty')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3">
    <label class="form-label">{{ __('assets.assigned_at') }}</label>
    <input type="date" name="assigned_at" class="form-control"
           value="{{ old('assigned_at', optional($row->assigned_at ?? null)->toDateString() ?? $today ?? '') }}" required>
    @error('assigned_at')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3">
    <label class="form-label">{{ __('assets.due_back_at') }}</label>
    <input type="date" name="due_back_at" class="form-control"
           value="{{ old('due_back_at', optional($row->due_back_at ?? null)->toDateString() ?? '') }}">
    @error('due_back_at')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('assets.handover') }}</label>
    <input type="file" name="handover" class="form-control">
    @error('handover')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('assets.notes') }}</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $row->notes ?? '') }}</textarea>
    @error('notes')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>
</div>

<script>
$(function(){
  $('.sel2').select2({ width:'100%', placeholder:'{{ __('assets.choose') }}', allowClear:true });

  function initAjaxSelect2($el){
    const url = $el.data('url');
    const currentId = $el.data('current-id');
    const currentText = $el.data('current-text');

    $el.select2({
      width:'100%',
      placeholder: '{{ __('assets.choose') }}',
      allowClear:true,
      ajax:{
        url: url, dataType:'json', delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => data,
        cache: true
      }
    });

    if (currentId && currentText){
      const opt = new Option(currentText, String(currentId), true, true);
      $el.append(opt).trigger('change');
    }
  }

  $('.sel2-ajax').each(function(){ initAjaxSelect2($(this)); });

  const $type = $('#assignable_type');
  const $id   = $('#assignable_id');

  const $emp = $('#employee_select');
  const $dep = $('#department_select');
  const $sto = $('#store_select');
  const $sup = $('#supplier_select');

  function syncVisible(){
    const t = $type.val();
    $('.type-field').hide();
    if (t==='employee')   $('.type-employee').show();
    if (t==='department') $('.type-department').show();
    if (t==='store')      $('.type-store').show();
    if (t==='supplier')   $('.type-supplier').show();

    $emp.prop('required', t==='employee');
    $dep.prop('required', t==='department');
    $sto.prop('required', t==='store');
    $sup.prop('required', t==='supplier');
  }

  function bindSelect($select){
    $select.on('change', function(){ $id.val($(this).val() || ''); });
  }

  bindSelect($emp);
  bindSelect($dep);
  bindSelect($sto);
  bindSelect($sup);

  // ===== عرض "المتاح" تحت حقل الكمية + ضبط max =====
  const $assetSel = $('select[name="asset_id"]');
  const $qty      = $('#assign-qty');
  const $hint     = $('#available-qty-hint');

  function updateAvailableHint(){
    const $opt = $assetSel.find('option:selected');
    const avail = parseInt($opt.data('available')) || 0;
    $hint.text('المتاح: ' + (isNaN(avail) ? '-' : avail));
    if (avail > 0) $qty.attr('max', String(avail));
    else $qty.removeAttr('max');
  }

  $assetSel.on('change', updateAvailableHint);
  updateAvailableHint(); // عند التحميل

  // ===== مزامنة حقول الجهة =====
  const currentType = $type.val();
  const currentId   = @json($currentId);
  if (currentId) {
    if (currentType==='employee')   $emp.val(String(currentId)).trigger('change');
    if (currentType==='department') $dep.val(String(currentId)).trigger('change');
    if (currentType==='store')      $sto.val(String(currentId)).trigger('change');
    if (currentType==='supplier')   $sup.val(String(currentId)).trigger('change');
  }

  $type.on('change', function(){
    $id.val('');
    syncVisible();
  });

  syncVisible();
});
</script>
