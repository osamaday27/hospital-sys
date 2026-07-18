@extends('layouts.master_table')

@section('title')
    {{ trans('purchases.create_requisition') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('purchase_requisitions.index') }}">{{ trans('purchases.requisitions') }}</a>
          </li>
          <li class="breadcrumb-item active">{{ trans('purchases.create_requisition') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

<form id="create-requisition-form" method="POST" action="{{ route('purchase_requisitions.store') }}">
  @csrf

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0">{{ trans('purchases.requisition_info') }}</h5>
    </div>
    <div class="card-body row gy-3 gx-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">{{ trans('purchases.requisition_number') }}</label>
        <input type="text" class="form-control" value="{{ $number }}" readonly>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ trans('purchases.requisition_date') }}</label>
        <input type="date" name="requisition_date" id="requisition_date" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ trans('purchases.warehouse') }}</label>
        <select name="warehouse_id" class="form-select select2" data-placeholder="{{ trans('purchases.select_warehouse') }}" required>
          <option value="">{{ trans('purchases.select_warehouse') }}</option>
          @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}">{{ $wh->name_ar }} — {{ $wh->name_en }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ trans('purchases.department') }}</label>
        <select name="department_id" class="form-select select2" data-placeholder="{{ trans('purchases.select_department') }}" required>
          <option value="">{{ trans('purchases.select_department') }}</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}">{{ $dept->name_ar }} — {{ $dept->name_ne }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ trans('purchases.preferred_supplier') }}</label>
        <select name="preferred_supplier_id" class="form-select select2" data-placeholder="{{ trans('purchases.select_supplier') }}">
          <option value="">{{ trans('purchases.select_supplier') }}</option>
          @foreach($suppliers as $sup)
            <option value="{{ $sup->id }}">{{ $sup->name_ar }} — {{ $sup->name_en }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-9">
        <label class="form-label">{{ trans('purchases.reason') }}</label>
        <textarea name="reason" class="form-control" rows="1">{{ old('reason') }}</textarea>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{ trans('purchases.select_items') }}</h5>
      <div class="d-flex w-75">
        <select id="item-search" class="form-select select2 me-2" data-placeholder="{{ trans('purchases.select_item') }}">
          <option value="">{{ trans('purchases.select_item') }}</option>
        </select>

        <select id="unit-search" class="form-select select2 me-2" data-placeholder="{{ trans('purchases.select_unit') }}" disabled>
          <option value="">{{ trans('purchases.select_unit') }}</option>
        </select>

        <button id="add-item-btn" type="button" class="btn btn-success">
          <i class="fa fa-plus"></i> {{ trans('purchases.add_item') }}
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
            <th>{{ trans('purchases.item_code') }}</th>
            <th>{{ trans('purchases.item_name') }}</th>
            <th style="width:140px">{{ trans('purchases.requested_qty') }}</th>
            <th>{{ trans('purchases.unit') }}</th>
            <th>{{ trans('purchases.available_qty') }}</th>
            <th>{{ trans('purchases.other_stores_balance') }}</th>
            <th>{{ trans('purchases.last_price') }}</th>
            <th>{{ trans('purchases.notes') }}</th>
            <th>{{ trans('general.actions') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-between">
      <div>{{ trans('purchases.total_items') }}: <strong id="total-items">0</strong></div>
      <div>{{ trans('purchases.total_amount') }}: <strong id="total-amount">0.00</strong></div>
    </div>
  </div>

  <div class="text-end mb-5">
    <button type="submit" class="btn btn-primary">
      {{ trans('purchases.confirm_requisition') }}
    </button>
  </div>
</form>

{{-- مودال التحذيرات --}}
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
  document.addEventListener('DOMContentLoaded', function () {
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('requisition_date').value = today;

      @if(session('showLimitModal') && !empty(session('limitViolations')))
        const m = new bootstrap.Modal(document.getElementById('limitsModal'));
        m.show();
      @endif
  });
</script>

<script>let unitsMap = @json($unitsMap);</script>
<script>let productBalances = @json($productBalances);</script>
<script>let limitsMap = {}; let storeAmountLimits = {min:null, max:null};</script>

<script>
$(function(){
  const select2Defaults = {
    theme: 'bootstrap-5', dir: 'rtl', width: '100%', dropdownAutoWidth: true,
    minimumResultsForSearch: 0,
    placeholder: function(){ return $(this).data('placeholder') || ''; }
  };

  $('.select2').each(function(){
    const parent = $(this).closest('.card, .modal, form, body');
    $(this).select2({ ...select2Defaults, dropdownParent: parent.length ? parent : $('body') });
  });

  function showToast(message, type = 'danger') {
    const toastId = `toast-${Date.now()}`;
    const toastHtml = `
      <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>`;
    $('#toast-container').append(toastHtml);
    new bootstrap.Toast(document.getElementById(toastId)).show();
  }

  let items = [];

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

  // تحميل أصناف المخزن المختار
  $('select[name="warehouse_id"]').on('change', function(){
    const storeId = $(this).val();

    items = [];
    renderTable();

    $('#item-search').prop('disabled', true).html(`<option value="">${@json(trans('purchases.select_item'))}</option>`).trigger('change.select2');
    $('#unit-search').prop('disabled', true).html(`<option value="">${@json(trans('purchases.select_unit'))}</option>`).trigger('change.select2');

    if(!storeId){ storeAmountLimits = {min:null, max:null}; renderStoreLimitsHint(); return; }

    $.ajax({
      url: "{{ route('purchase_requisitions.items_by_store') }}",
      method: 'GET',
      data: { store_id: storeId },
      success: function(resp){
        unitsMap           = resp.unitsMap || {};
        productBalances    = resp.productBalances || {};
        limitsMap          = resp.limitsMap || {};
        storeAmountLimits  = resp.storeAmountLimits || {min:null,max:null};

        renderStoreLimitsHint();

        const $itemSel = $('#item-search');
        $itemSel.html(`<option value="">${@json(trans('purchases.select_item'))}</option>`);
        (resp.items || []).forEach(function(it){
          $itemSel.append(
            `<option value="${it.id}"
                     data-code="${it.system_code}"
                     data-name-ar="${it.name_ar}"
                     data-name-en="${it.name_en}">
                ${it.system_code} — ${it.name_ar}
             </option>`
          );
        });
        $itemSel.prop('disabled', false).trigger('change.select2');
      },
      error: function(){
        showToast('{{ __("general.error") }}', 'danger');
      }
    });
  });

  $('#item-search').on('change', function(){
    const itemId = +$(this).val();
    const $unitSel = $('#unit-search');

    $unitSel.prop('disabled', true);
    $unitSel.empty().append(`<option value="">${@json(trans('purchases.select_unit'))}</option>`);

    if (itemId && unitsMap[itemId]) {
      unitsMap[itemId].forEach(u => {
        const opt = new Option(u.name, u.id, false, false);
        $(opt).data('price', u.price);
        $unitSel.append(opt);
      });
      $unitSel.prop('disabled', false);
    }

    $unitSel.trigger('change.select2');
  });

  function updateFooter(){
    const totalItems  = items.length;
    const totalAmount = items.reduce((sum, it) => sum + it.requested_qty * it.last_price, 0);
    $('#total-items').text(totalItems);
    $('#total-amount').text(totalAmount.toFixed(2));
  }

  function renderTable(){
    const $tbody = $('#items-table tbody').empty();
    items.forEach((it, idx) => {
      const unitName = it.units.find(u => u.id === it.unit_id)?.name || '';
      const limitsText = itemLimitsText(it.id); // نص توضيح حدود الصنف

      // الصف الرئيسي
      const $tr = $('<tr>').append(
        $('<td>').text(it.code),
        $('<td>').text(`${it.name_ar} / ${it.name_en}`),
        $('<td>').append(
          $('<input>', {
            type: 'number', min:1, step:1,
            class:'form-control form-control-sm qty-input',
            'data-idx': idx, value: it.requested_qty
          })
        ),
        $('<td>').append(
          $('<div>').text(unitName),
          limitsText ? $('<div class="form-text text-muted mt-1">').html(limitsText) : ''
        ),
        $('<td>').text(it.current_balance),
        $('<td>').text(it.other_stores_balance),
        $('<td>').append(
          $('<span>', {class:'last-price','data-idx':idx}).text(it.last_price.toFixed(2))
        ),
        $('<td>').append(
          $('<input>', { type:'text', class:'form-control form-control-sm notes-input', 'data-idx': idx, value: it.notes })
        ),
        $('<td>').append(
          $('<button>', { type:'button', class:'btn btn-danger btn-sm remove-item', 'data-idx': idx })
            .html('<i class="fa fa-trash"></i> {{ __("purchases.delete") }}')
        ),
        // hidden inputs
        $('<input>', {type:'hidden', name:`items[${idx}][item_id]`, value: it.id}),
        $('<input>', {type:'hidden', class:'hid-unit',  name:`items[${idx}][unit_id]`, 'data-idx':idx, value: it.unit_id}),
        $('<input>', {type:'hidden', class:'hid-price', name:`items[${idx}][last_price]`, 'data-idx':idx, value: it.last_price}),
        $('<input>', {type:'hidden', class:'hid-qty',   name:`items[${idx}][requested_qty]`, 'data-idx':idx, value: it.requested_qty}),
        $('<input>', {type:'hidden', class:'hid-notes', name:`items[${idx}][notes]`, 'data-idx':idx, value: it.notes})
      );

      $tbody.append($tr);
    });
    updateFooter();
  }

  $('#add-item-btn').on('click', function(){
    const itemId = +$('#item-search').val();
    const unitId = +$('#unit-search').val();

    if (!itemId) { showToast('{{ __("purchases.error_select_item") }}', 'warning'); return; }
    if (!unitId) { showToast('{{ __("purchases.error_select_unit") }}', 'warning'); return; }
    if (items.some(it => it.id === itemId && it.unit_id === unitId)) {
      showToast('{{ __("purchases.error_duplicate_item_unit") }}', 'warning'); return;
    }

    const warehouseId = +$('select[name="warehouse_id"]').val();
    let currentBalance = 0, otherStoresBalance = 0;
    if (productBalances[itemId]) {
      currentBalance = productBalances[itemId][warehouseId] || 0;
      otherStoresBalance = Object.keys(productBalances[itemId])
        .filter(sid => sid != warehouseId)
        .reduce((sum, sid) => sum + Number(productBalances[itemId][sid]), 0);
    }

    const unitList     = unitsMap[itemId] || [];
    const selectedUnit = unitList.find(u => u.id === unitId) || {};
    const lastPrice    = Number(selectedUnit.price) || 0;

    items.push({
      id: itemId,
      code: $('#item-search option:selected').data('code'),
      name_ar: $('#item-search option:selected').data('name-ar'),
      name_en: $('#item-search option:selected').data('name-en'),
      pending_qty: 0,
      requested_qty: 1,
      units: unitList,
      unit_id: unitId,
      last_price: lastPrice,
      current_balance: currentBalance,
      other_stores_balance: otherStoresBalance,
      notes: ''
    });

    renderTable();
  });

  $(document)
    .on('input', '.qty-input', function(){
      const idx = +$(this).data('idx'), v = parseInt(this.value) || 1;
      items[idx].requested_qty = v;
      $(`.hid-qty[data-idx="${idx}"]`).val(v);
      updateFooter();
    })
    .on('input', '.notes-input', function(){
      const idx = +$(this).data('idx');
      items[idx].notes = this.value;
      $(`.hid-notes[data-idx="${idx}"]`).val(this.value);
    })
    .on('click', '.remove-item', function(){
      items.splice(+$(this).data('idx'), 1);
      renderTable();
    });
});
</script>
@endsection
