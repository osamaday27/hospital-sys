@extends('layouts.master_table')

@section('title')
{{ __('purchases.edit_requisition') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ===== Select2 CSS ===== --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
<style>
  .select2-container{width:100%!important; max-width:100%!important;}
  .select2-dropdown{width:auto!important; max-width:100%!important; border:1px solid #ced4da; box-shadow:0 .5rem 1rem rgba(0,0,0,.15); z-index:2000;}
  .select2-results__options{max-height:280px!important; overflow-y:auto!important;}
  .select2-results__option{white-space:normal; word-break:break-word;}
  .select2-container--bootstrap-5 .select2-selection--single{
    height:calc(2.5rem + 2px); padding:.375rem .75rem; line-height:1.5;
  }
</style>

{{-- ========= Breadcrumb ========= --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('purchase_requisitions.index') }}">{{ __('purchases.requisitions') }}</a>
          </li>
          <li class="breadcrumb-item active">{{ __('purchases.edit_requisition') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

{{-- Toast container --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

<form id="edit-requisition-form" method="POST" action="{{ route('purchase_requisitions.update', $req->id) }}">
  @csrf
  @method('PUT')

  {{-- ========= بيانات طلب الاحتياج الأساسية ========= --}}
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ __('purchases.requisition_info') }}</h5>
    </div>
    <div class="card-body row gy-3 gx-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">{{ __('purchases.requisition_number') }}</label>
        <input type="text" class="form-control" value="{{ $req->requisition_number }}" readonly>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('purchases.requisition_date') }}</label>
        <input type="date" name="requisition_date" class="form-control"
               value="{{ \Illuminate\Support\Carbon::parse($req->requisition_date)->format('Y-m-d') }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('purchases.warehouse') }}</label>
        <select name="warehouse_id" class="form-select select2"
                data-placeholder="{{ __('purchases.select_warehouse') }}" required>
          <option value="">{{ __('purchases.select_warehouse') }}</option>
          @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" @if($wh->id == $req->warehouse_id) selected @endif>
              {{ $wh->name_ar }} — {{ $wh->name_en }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('purchases.department') }}</label>
        <select name="department_id" class="form-select select2"
                data-placeholder="{{ __('purchases.select_department') }}" required>
          <option value="">{{ __('purchases.select_department') }}</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}" @if($dept->id == $req->department_id) selected @endif>
              {{ $dept->name_ar }} — {{ $dept->name_ne }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('purchases.preferred_supplier') }}</label>
        <select name="preferred_supplier_id" class="form-select select2"
                data-placeholder="{{ __('purchases.select_supplier') }}">
          <option value="">{{ __('purchases.select_supplier') }}</option>
          @foreach($suppliers as $sup)
            <option value="{{ $sup->id }}" @if($sup->id == $req->preferred_supplier_id) selected @endif>
              {{ $sup->name_ar }} — {{ $sup->name_en }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-9">
        <label class="form-label">{{ __('purchases.reason') }}</label>
        <textarea name="reason" class="form-control" rows="1">{{ $req->reason }}</textarea>
      </div>
    </div>
  </div>

  {{-- ========= اختيار الأصناف والوحدات ========= --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ __('purchases.select_items') }}</h5>
      <div class="d-flex w-75">
        <select id="item-search" class="form-select select2 me-2"
                data-placeholder="{{ __('purchases.select_item') }}">
          <option value="">{{ __('purchases.select_item') }}</option>
          @foreach($items as $it)
            <option value="{{ $it->id }}"
                    data-code="{{ $it->system_code }}"
                    data-name-ar="{{ $it->name_ar }}"
                    data-name-en="{{ $it->name_en }}">
              {{ $it->system_code }} — {{ $it->name_ar }}
            </option>
          @endforeach
        </select>

        <select id="unit-search" class="form-select select2 me-2"
                data-placeholder="{{ __('purchases.select_unit') }}" disabled>
          <option value="">{{ __('purchases.select_unit') }}</option>
        </select>

        <button id="add-item-btn" type="button" class="btn btn-success">
          <i class="fa fa-plus"></i> {{ __('purchases.add_item') }}
        </button>
      </div>
    </div>

    {{-- توضيح حدود المخزن (قيمة الطلب) --}}
    <div class="card-body pb-0">
      <div id="store-limits-hint" class="alert alert-info py-2 small d-none"></div>
    </div>

    <div class="card-body p-0">
      <table id="items-table" class="table table-bordered mb-0">
        <thead>
          <tr>
            <th>{{ __('purchases.item_code') }}</th>
            <th>{{ __('purchases.item_name') }}</th>
            <th style="width:140px">{{ __('purchases.requested_qty') }}</th>
            <th>{{ __('purchases.unit') }}</th>
            <th>{{ __('purchases.available_qty') }}</th>
            <th>{{ __('purchases.other_stores_balance') }}</th>
            <th>{{ __('purchases.last_price') }}</th>
            <th>{{ __('purchases.notes') }}</th>
            <th>{{ __('general.actions') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-between">
      <div>{{ __('purchases.total_items') }}: <strong id="total-items">0</strong></div>
      <div>{{ __('purchases.total_amount') }}: <strong id="total-amount">0.00</strong></div>
    </div>
  </div>

  {{-- زر التحديث --}}
  <div class="text-end mb-5">
    <button type="submit" class="btn btn-warning">
      <i class="fa fa-save"></i> {{ __('purchases.update_requisition') }}
    </button>
  </div>
</form>

{{-- مودال التحذيرات (كما في create) --}}
<div class="modal fade" id="limitsModal" tabindex="-1" aria-labelledby="limitsModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning-subtle">
        <h5 class="modal-title" id="limitsModalLabel">{{ __('purchases.limits_warnings_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
      </div>
      <div class="modal-body">
        @php
          $violations = session('limitViolations', []);
          $orderTotals = session('orderTotals', []);
        @endphp

        @if(!empty($orderTotals))
          <div class="mb-3">
            <strong>{{ __('purchases.total_amount_for_store') }}:</strong>
            <span>{{ number_format($orderTotals['total_amount'] ?? 0, 2) }}</span>
            @if(!empty($orderTotals['min_amount']))
              <span class="ms-3 badge bg-secondary">{{ __('purchases.min_order_total') }}: {{ number_format($orderTotals['min_amount'], 2) }}</span>
            @endif
            @if(!empty($orderTotals['max_amount']))
              <span class="ms-2 badge bg-secondary">{{ __('purchases.max_order_total') }}: {{ number_format($orderTotals['max_amount'], 2) }}</span>
            @endif
          </div>
        @endif

        @if(!empty($violations))
          <ul class="mb-0">
            @foreach($violations as $msg)
              <li class="text-danger">{{ $msg }}</li>
            @endforeach
          </ul>
        @else
          <div class="text-success">{{ __('purchases.no_limit_issues') }}</div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('general.close') }}</button>
      </div>
    </div>
  </div>
</div>

<script>
  const unitsMap         = @json($unitsMap);
  const productBalances  = @json($productBalances);
  const oldItems         = @json($oldItems);
  const SELECT_UNIT_TEXT = @json(__('purchases.select_unit'));
  const SELECT_ITEM_TEXT = @json(__('purchases.select_item'));
</script>

{{-- ===== Select2 JS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {
  // تفعيل Select2
  const select2Defaults = {
    theme:'bootstrap-5', dir:'rtl', width:'100%', dropdownAutoWidth:false, minimumResultsForSearch:0,
    placeholder:function(){ return $(this).data('placeholder') || ''; },
    dropdownParent:$('body')
  };
  $('.select2').each(function(){ $(this).select2(select2Defaults); });

  function showToast(message, type='danger'){
    const id = `toast-${Date.now()}`;
    $('#toast-container').append(
      `<div id="${id}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" data-bs-delay="3000">
         <div class="d-flex">
           <div class="toast-body">${message}</div>
           <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
         </div>
       </div>`);
    new bootstrap.Toast(document.getElementById(id)).show();
  }

  // حدود المخزن + حدود الأصناف (يُملأ عبر AJAX)
  let limitsMap = {}; // { product_id: {min_qty, min_unit_name, max_qty, max_unit_name} }
  let storeAmountLimits = {min:null, max:null};

  // عناصر وتوابع عرض الحدود
  function renderStoreLimitsHint(){
    const el = $('#store-limits-hint');
    const min = storeAmountLimits.min, max = storeAmountLimits.max;
    if ((min && Number(min) > 0) || (max && Number(max) > 0)) {
      const chunks = [];
      if (min && Number(min) > 0) chunks.push(`{{ __('purchases.min_order_total') }}: <strong>${Number(min).toFixed(2)}</strong>`);
      if (max && Number(max) > 0) chunks.push(`{{ __('purchases.max_order_total') }}: <strong>${Number(max).toFixed(2)}</strong>`);
      el.html(`{{ __('purchases.store_amount_limits_hint') }} ${chunks.join(' &nbsp; | &nbsp; ')}`);
      el.removeClass('d-none');
    } else {
      el.addClass('d-none').empty();
    }
  }
  function itemLimitsText(productId){
    const l = limitsMap[productId];
    if (!l) return '';
    const parts = [];
    if (l.min_qty && l.min_unit_name) parts.push(`{{ __('purchases.item_min_limit') }}: ${l.min_qty} ${l.min_unit_name}`);
    if (l.max_qty && l.max_unit_name) parts.push(`{{ __('purchases.item_max_limit') }}: ${l.max_qty} ${l.max_unit_name}`);
    return parts.length ? parts.join(' — ') : '';
  }

  // بيانات الصفحة
  let items = Array.isArray(oldItems) ? JSON.parse(JSON.stringify(oldItems)) : [];

  function updateFooter(){
    const totalItems  = items.length;
    const totalAmount = items.reduce((s, it)=> s + (Number(it.requested_qty)||0)*(Number(it.last_price)||0), 0);
    $('#total-items').text(totalItems);
    $('#total-amount').text(totalAmount.toFixed(2));
  }

  function renderTable(){
    const $tb = $('#items-table tbody').empty();
    items.forEach((it, idx)=>{
      const unitName  = (it.units||[]).find(u=>u.id===it.unit_id)?.name || '';
      const limitsTxt = itemLimitsText(it.id);

      $('<tr>').append(
        $('<td>').text(it.code),
        $('<td>').text(`${it.name_ar} / ${it.name_en}`),
        $('<td>').append($('<input>', {
          type:'number', min:1, step:1, class:'form-control form-control-sm qty-input',
          'data-idx':idx, value: it.requested_qty
        })),
        $('<td>').append(
          $('<div>').text(unitName),
          limitsTxt ? $('<div class="form-text text-muted mt-1">').html(limitsTxt) : ''
        ),
        $('<td>').text(it.current_balance),
        $('<td>').text(it.other_stores_balance),
        $('<td>').append($('<span>', {class:'last-price','data-idx':idx}).text(Number(it.last_price).toFixed(2))),
        $('<td>').append($('<input>', {type:'text', class:'form-control form-control-sm notes-input','data-idx':idx, value: it.notes || ''})),
        $('<td>').append($('<button>', {type:'button', class:'btn btn-danger btn-sm remove-item','data-idx':idx})
              .html('<i class="fa fa-trash"></i> {{ __("purchases.delete") }}')),
        // hidden inputs
        $('<input>', {type:'hidden', name:`items[${idx}][item_id]`, value: it.id}),
        $('<input>', {type:'hidden', class:'hid-unit',  name:`items[${idx}][unit_id]`,       'data-idx':idx, value: it.unit_id}),
        $('<input>', {type:'hidden', class:'hid-price', name:`items[${idx}][last_price]`,     'data-idx':idx, value: Number(it.last_price)}),
        $('<input>', {type:'hidden', class:'hid-qty',   name:`items[${idx}][requested_qty]`,  'data-idx':idx, value: it.requested_qty}),
        $('<input>', {type:'hidden', class:'hid-notes', name:`items[${idx}][notes]`,          'data-idx':idx, value: it.notes || ''})
      ).appendTo($tb);
    });
    updateFooter();
  }
  renderTable();

  // تعبئة وحدات الصنف المختار
  $('#item-search').on('change', function(){
    const itemId  = +$(this).val();
    const $unit   = $('#unit-search');
    $unit.prop('disabled',true).html(`<option value="">${SELECT_UNIT_TEXT}</option>`);
    const map = window.unitsMap || {};
    if(itemId && map[itemId]){
      map[itemId].forEach(u=>{
        const opt = new Option(u.name, u.id, false, false);
        $(opt).data('price', u.price);
        $unit.append(opt);
      });
      $unit.prop('disabled',false);
    }
    $unit.trigger('change.select2');
  });

  // إضافة الصنف إلى الجدول
  $('#add-item-btn').on('click', function(){
    const itemId = +$('#item-search').val();
    const unitId = +$('#unit-search').val();
    if(!itemId){ return showToast('{{ __("purchases.error_select_item") }}','warning'); }
    if(!unitId){ return showToast('{{ __("purchases.error_select_unit") }}','warning'); }
    if(items.some(it=> it.id===itemId && it.unit_id===unitId)){
      return showToast('{{ __("purchases.error_duplicate_item_unit") }}','warning');
    }

    const whId = +$('select[name="warehouse_id"]').val();
    let currentBalance=0, otherStoresBalance=0;
    const balances = window.productBalances || {};
    if(balances[itemId]){
      currentBalance = balances[itemId][whId] || 0;
      otherStoresBalance = Object.keys(balances[itemId]).filter(sid=>sid!=whId)
                            .reduce((sum,sid)=> sum + Number(balances[itemId][sid]), 0);
    }

    const unitList = (window.unitsMap||{})[itemId] || [];
    const selected = unitList.find(u=>u.id===unitId) || {};
    const lastPrice = Number(selected.price)||0;
    const $opt = $('#item-search option:selected');

    items.push({
      id:itemId,
      code:$opt.data('code'), name_ar:$opt.data('name-ar'), name_en:$opt.data('name-en'),
      pending_qty:0, requested_qty:1, units:unitList, unit_id:unitId, last_price:lastPrice,
      current_balance:currentBalance, other_stores_balance:otherStoresBalance, notes:''
    });
    renderTable();
  });

  // تحميل حدود المخزن/الأصناف للعرض دون تغيير العناصر (أثناء التحميل الأول)
  function loadStoreMeta(storeId){
    if(!storeId){ limitsMap={}; storeAmountLimits={min:null,max:null}; renderStoreLimitsHint(); renderTable(); return; }
    $.get("{{ route('purchase_requisitions.items_by_store') }}", { store_id: storeId })
     .done(function(resp){
        limitsMap         = resp.limitsMap || {};
        storeAmountLimits = resp.storeAmountLimits || {min:null,max:null};
        renderStoreLimitsHint();
        // لا نغيّر items هنا — فقط نضيف التلميحات
        renderTable();
     });
  }

  // عند تغيير المخزن: حمّل أصناف هذا المخزن فقط وافرغ الجدول الحالي (كما كان)، + حدّث التلميحات
  $('select[name="warehouse_id"]').on('change', function(){
    const storeId = $(this).val();

    items = []; renderTable();
    $('#item-search').prop('disabled',true).html(`<option value="">${SELECT_ITEM_TEXT}</option>`).trigger('change.select2');
    $('#unit-search').prop('disabled',true).html(`<option value="">${SELECT_UNIT_TEXT}</option>`).trigger('change.select2');

    if(!storeId){ limitsMap={}; storeAmountLimits={min:null,max:null}; renderStoreLimitsHint(); return; }

    $.get("{{ route('purchase_requisitions.items_by_store') }}", { store_id: storeId })
     .done(function(resp){
        window.unitsMap        = resp.unitsMap || {};
        window.productBalances = resp.productBalances || {};
        limitsMap              = resp.limitsMap || {};
        storeAmountLimits      = resp.storeAmountLimits || {min:null,max:null};

        renderStoreLimitsHint();

        const $item = $('#item-search');
        $item.html(`<option value="">${SELECT_ITEM_TEXT}</option>`);
        (resp.items||[]).forEach(it=>{
          $item.append(`<option value="${it.id}" data-code="${it.system_code}" data-name-ar="${it.name_ar}" data-name-en="${it.name_en}">
                          ${it.system_code} — ${it.name_ar}
                        </option>`);
        });
        $item.prop('disabled',false).trigger('change.select2');
     })
     .fail(()=> showToast('{{ __("general.error") }}','danger'));
  });

  // تفويض مدخلات الجدول
  $(document)
    .on('input','.qty-input', function(){
      const idx=+$(this).data('idx'), v=parseInt(this.value)||1;
      items[idx].requested_qty=v; $(`.hid-qty[data-idx="${idx}"]`).val(v); updateFooter();
    })
    .on('input','.notes-input', function(){
      const idx=+$(this).data('idx'), v=this.value;
      items[idx].notes=v; $(`.hid-notes[data-idx="${idx}"]`).val(v);
    })
    .on('click','.remove-item', function(){
      items.splice(+$(this).data('idx'),1); renderTable();
    });

  // تحميل حدود المخزن/الأصناف عند الدخول لأول مرة للمخزن المختار
  loadStoreMeta($('select[name="warehouse_id"]').val());

  // إظهار مودال المخالفات لو وُجدت بيانات بالجلسة
  @if(session('showLimitModal') && !empty(session('limitViolations')))
    const m = new bootstrap.Modal(document.getElementById('limitsModal'));
    m.show();
  @endif
});
</script>
@endsection
