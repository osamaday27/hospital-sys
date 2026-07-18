@extends('layouts.master_table')

@section('title', trans('supply_chain_trans.ri_create_title'))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- BreadCrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('receiving_inspecting.index') }}">محاضر الاستلام والفحص</a>
                        </li>
                        <li class="breadcrumb-item active">انشاء محضر استلام وفحص جديد</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ trans('supply_chain_trans.ri_create_title') }}</h5>
    <a href="{{ route('receiving_inspecting.index', [app()->getLocale() ?? 'ar']) }}" class="btn btn-outline-secondary">
      {{ trans('supply_chain_trans.back') }}
    </a>
  </div>
</div>

<form id="ri-form" action="{{ route('receiving_inspecting.store', [app()->getLocale() ?? 'ar']) }}" method="post">
@csrf
<input type="hidden" name="requisition_action" id="requisition_action" value="">

{{-- القسم العلوي: البيانات الرئيسية --}}
<div class="card mb-3">
  <div class="card-header">{{ trans('supply_chain_trans.primary_info') }}</div>
  <div class="card-body row g-3">
<div class="col-md-4">
  <label class="form-label">{{ trans('supply_chain_trans.supplier_label') }}</label>

  {{-- عرض فقط (Select2) --}}
  <select id="supplier_id_display"
          class="form-select select2-ajax"
          data-url="{{ route('receiving_inspecting.suppliers', [app()->getLocale() ?? 'ar']) }}"
          disabled>
  </select>

  {{-- الحقل الذي يُرسل فعليًا --}}
  <input type="hidden" name="supplier_id" id="supplier_id">

  @error('supplier_id')<div class="text-danger small">{{ $message }}</div>@enderror
</div>


    <div class="col-md-4">
      <label class="form-label">{{ trans('supply_chain_trans.store_label') }}</label>
      <select name="store_id" id="store_id" class="form-select select2-ajax"
              data-url="{{ route('receiving_inspecting.stores', [app()->getLocale() ?? 'ar']) }}" required></select>
      @error('store_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
      <label class="form-label">{{ trans('supply_chain_trans.po_unlinked_label') }}</label>
      <select name="purchase_order_id" id="purchase_order_id" class="form-select select2-ajax"
              data-url="{{ route('receiving_inspecting.available_pos', [app()->getLocale() ?? 'ar']) }}" required></select>
      @error('purchase_order_id')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
      <label class="form-label">{{ trans('supply_chain_trans.ri_code') }}</label>
      <input type="text" class="form-control" name="code" value="{{ $nextCode }}" readonly>
      @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
      <label class="form-label">{{ trans('supply_chain_trans.ri_date') }}</label>
      <input type="date" class="form-control" name="document_date" value="{{ $today }}" required>
      @error('document_date')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ trans('supply_chain_trans.notes') }}</label>
      <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ trans('supply_chain_trans.receipt_notes') }}</label>
      <textarea name="receipt_notes" class="form-control" rows="1">{{ old('receipt_notes') }}</textarea>
    </div>
  </div>
</div>

{{-- القسم الثاني: أصناف أمر الشراء --}}
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>{{ trans('supply_chain_trans.items_of_po') }}</span>
    <small class="text-muted">{{ trans('supply_chain_trans.auto_fill_after_po') }}</small>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered align-middle" id="items-table">
        <thead>
          <tr class="text-center">
            <th>{{ trans('supply_chain_trans.col_seq') }}</th>
            <th>{{ trans('supply_chain_trans.col_product_code') }}</th>
            <th>{{ trans('supply_chain_trans.col_product_name') }}</th>
            <th>{{ trans('supply_chain_trans.col_unit') }}</th>
            <th>{{ trans('supply_chain_trans.col_req_qty') }}</th>
            <th>{{ trans('supply_chain_trans.col_po_qty') }}</th>

            <th>{{ trans('supply_chain_trans.col_delivered_qty') }}</th>

            {{-- المقبولة: قيمة + وحدة --}}
            <th style="min-width: 190px;">
              {{ trans('supply_chain_trans.col_accepted_qty') }}
              <div class="small text-muted">{{ trans('supply_chain_trans.enter_in_any_unit') }}</div>
            </th>

            {{-- المرفوضة: عرض فقط --}}
            <th>{{ trans('supply_chain_trans.col_rejected_qty') }}</th>

            {{-- الباتش/الانتهاء --}}
            <th style="min-width: 200px;">{{ __('Batch / Expiry') }}</th>

            <th>{{ trans('supply_chain_trans.col_cost_price') }}</th>
            <th>{{ trans('supply_chain_trans.col_rejection_reason') }}</th>
          </tr>
        </thead>
        <tbody id="items-body"></tbody>
      </table>
    </div>
    @error('items')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>
</div>

{{-- القسم الثالث: لجنة الاستلام والفحص --}}
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>{{ trans('supply_chain_trans.ri_committee') }}</span>
    <div>
      <button type="button" id="add-approver" class="btn btn-sm btn-success">
        + {{ trans('supply_chain_trans.add_member') }}
      </button>
    </div>
  </div>
  <div class="card-body">
    <div id="approvals-list"></div>
    @error('approvals')<div class="text-danger small">{{ $message }}</div>@enderror
  </div>
</div>

<div class="text-center">
  <button type="submit" class="btn btn-primary btn-lg px-5">{{ trans('supply_chain_trans.save') }}</button>
</div>

</form>

{{-- Templates --}}
<template id="item-row-template">
<tr>
  <td class="text-center idx"></td>
  <td class="text-center"><span class="product-code"></span></td>
  <td><span class="product-name"></span></td>
  <td class="text-center"><span class="unit-name"></span></td>

  {{-- طلب الاحتياج (عرض فقط + hidden) --}}
  <td class="text-end">
    <span class="req-qty-text"></span>
    <input type="hidden" name="items[__i__][request_qty]" class="req-qty">
  </td>

  {{-- كمية أمر التوريد (عرض فقط + hidden) --}}
  <td class="text-end">
    <span class="po-qty-text"></span>
    <input type="hidden" name="items[__i__][po_qty]" class="po-qty">
  </td>

  {{-- الموردة (قابل للتعديل — بالوحدة الأساسية للبند) --}}
  <td>
    <input type="number" step="0.001" min="0"
           class="form-control form-control-sm text-end deliv-qty"
           name="items[__i__][delivered_qty]" value="0">
  </td>

  {{-- المقبولة: قيمة + قائمة وحدات (ونخزن المكافئ بالوحدة الأساسية في hidden) --}}
  <td>
    <div class="input-group input-group-sm">
      <input type="number" step="0.001" min="0"
             class="form-control text-end acc-qty-input"
             name="items[__i__][accepted_qty_input]" value="0">
      <select class="form-select acc-unit-select" name="items[__i__][accepted_unit_id]">
        <option value="" selected disabled>...</option>
      </select>
    </div>
    <input type="hidden" class="acc-qty-base" name="items[__i__][accepted_qty]" value="0">
  </td>

  {{-- المرفوضة: عرض فقط + hidden للإرسال --}}
  <td class="text-end">
    <span class="rej-qty-text">0.000</span>
    <input type="hidden" class="rej-qty" name="items[__i__][rejected_qty]" value="0">
  </td>

  {{-- الباتش + الانتهاء + زر تكرار --}}
  <td>
    <div class="row g-1 align-items-center">
      <div class="col">
        <input type="text" class="form-control form-control-sm batch-no" placeholder="{{ __('Batch') }}"
               name="items[__i__][batch_no]">
      </div>
      <div class="col">
        <input type="date" class="form-control form-control-sm expiry-date"
               name="items[__i__][expiry_date]">
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-outline-primary btn-sm btn-duplicate-batch" title="{{ __('Add another batch line') }}">+</button>
      </div>
    </div>
  </td>

  {{-- تكلفة البند (عرض فقط + hidden) --}}
  <td class="text-end">
    <span class="cost-price-text"></span>
    <input type="hidden" name="items[__i__][cost_price]" class="cost-price">
  </td>

  <td>
    <input type="text" class="form-control form-control-sm"
           name="items[__i__][rejection_reason]" value="">
  </td>

  <input type="hidden" name="items[__i__][product_id]" class="product-id">
  <input type="hidden" name="items[__i__][unit_id]"    class="unit-id">
</tr>
</template>

<template id="approval-row-template">
<div class="row g-2 align-items-end approval-row">
  <div class="col-md-5">
    <label class="form-label">{{ trans('supply_chain_trans.employee') }}</label>
    <select class="form-select select2-ajax employee-select" name="approvals[__j__][employee_id]"
            data-url="{{ route('receiving_inspecting.employees', [app()->getLocale() ?? 'ar']) }}"></select>
  </div>
  <div class="col-md-4">
    <label class="form-label">{{ trans('supply_chain_trans.responsibilities') }}</label>
    <select class="form-select resp-select" name="approvals[__j__][responsibilities][]" multiple>
      <option value="جرد">{{ trans('supply_chain_trans.res_inventory') }}</option>
      <option value="فحص كمي">{{ trans('supply_chain_trans.res_qty_check') }}</option>
      <option value="فحص نوعي/جودة">{{ trans('supply_chain_trans.res_quality_check') }}</option>
      <option value="مطابقة مواصفات">{{ trans('supply_chain_trans.res_spec_match') }}</option>
      <option value="استلام وتخزين">{{ trans('supply_chain_trans.res_receipt_storage') }}</option>
      <option value="مراجعة مستندية">{{ trans('supply_chain_trans.res_docs_review') }}</option>
      <option value="تسليم للأقسام الطالبة">{{ trans('supply_chain_trans.res_deliver_depts') }}</option>
      <option value="مراقبة مخزون">{{ trans('supply_chain_trans.res_stock_control') }}</option>
    </select>
  </div>

  <div class="col-md-2 form-check mt-4">
    <input type="checkbox" class="form-check-input" id="chair-__j__" name="approvals[__j__][is_chairman]" value="1">
    <label class="form-check-label" for="chair-__j__">{{ trans('supply_chain_trans.chairman') }}</label>
  </div>
  <div class="col-md-1">
    <button type="button" class="btn btn-outline-danger btn-sm remove-approver">{{ trans('supply_chain_trans.delete') }}</button>
  </div>
</div>
<hr>
</template>
<!-- Modal: اختيار حالة طلب الاحتياج -->
<div class="modal fade" id="reqActionModal" tabindex="-1" aria-labelledby="reqActionLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="reqActionLabel">حالة طلب الاحتياج المرتبط</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        بعد حفظ محضر الاستلام والفحص، هل تريد:
        <ul class="mt-2 mb-0">
          <li><strong>إغلاق</strong> طلب الاحتياج (لن يستقبل عروضًا جديدة)</li>
          <li>أو إبقاؤه <strong>مفتوحًا لتلقي عروض أخرى</strong>؟</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnReqKeepOpen" class="btn btn-outline-secondary" data-choice="open">تركه مفتوحًا</button>
        <button type="button" id="btnReqClose" class="btn btn-primary" data-choice="close">إغلاق الطلب</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>

<script>
(function () {
  const IS_EDIT = @json(isset($doc));
  const INIT = {
    supplier : { id: @json(isset($doc) ? $doc->supplier_id : null),
                 text: @json(isset($supplierText) ? $supplierText : '') },
    store    : { id: @json(isset($doc) ? $doc->store_id : null),
                 text: @json(isset($storeText) ? $storeText : '') },
    po       : { id: @json(isset($doc) ? $doc->purchase_order_id : null),
                 text: @json(isset($poText) ? $poText : '') },
    items    : @json(isset($itemsForEdit) ? $itemsForEdit : []),
    committee: @json(isset($committeeInit) ? $committeeInit : []),
  };

  const I18N = {
    choose:               @json(trans('supply_chain_trans.choose_placeholder')),
    noCommittee:          @json(trans('supply_chain_trans.no_committee_for_po')),
    deliveredGtPo:        @json(trans('supply_chain_trans.delivered_gt_po')),
    acceptedGtDelivered:  @json(trans('supply_chain_trans.accepted_gt_delivered')),
    rejectedGtDelivered:  @json(trans('supply_chain_trans.rejected_gt_delivered')),
    deliveredSumGtPo:     'إجمالي الكمية المُورَّدة لهذا الصنف تجاوز كمية أمر التوريد، تم ضبط القيمة.',
    acceptedSumGtDelivered:'إجمالي الكمية المقبولة لهذا الصنف تجاوز إجمالي المُورَّدة، تم ضبط القيمة.',
    duplicateBatchScoped: 'هذا رقم باتش مستخدم في صنف آخر، أو مكرر لنفس الصنف على نفس الوحدة. استخدم رقمًا مختلفًا.',
    rowRemoved:           'تم حذف السطر.'
  };

  function formatQty(v){ return Number(v || 0).toFixed(3); }
  function formatPrice(v){ return Number(v || 0).toFixed(2); }
  function normBatch(v){ return String(v || '').trim().toUpperCase(); }

  // Toast
  (function injectToastCSS(){
    if (document.getElementById('mini-toast-css')) return;
    const css = `
      #toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;}
      .toast-msg{min-width:260px;max-width:420px;background:#333;color:#fff;padding:10px 14px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.2);opacity:.95;}
      .toast-msg.success{background:#198754}.toast-msg.warning{background:#f59f00}.toast-msg.danger{background:#dc3545}
    `;
    const style = document.createElement('style');
    style.id = 'mini-toast-css';
    style.innerHTML = css;
    document.head.appendChild(style);
  })();
  function showToast(msg, type){
    type = type || 'warning';
    let cont = document.getElementById('toast-container');
    if(!cont){
      cont = document.createElement('div');
      cont.id = 'toast-container';
      document.body.appendChild(cont);
    }
    const node = document.createElement('div');
    node.className = 'toast-msg '+type;
    node.textContent = msg;
    cont.appendChild(node);
    setTimeout(()=>{ node.style.transition='opacity .4s'; node.style.opacity='0'; }, 2500);
    setTimeout(()=>{ node.remove(); }, 3000);
  }

  function initSelect2($el){
    $el.select2({
      width: '100%',
      dir: 'rtl',
      ajax: {
        delay: 250, cache: true, url: $el.data('url'),
        data: params => ({ q: params.term || '' }),
        processResults: data => data
      },
      placeholder: I18N.choose,
      allowClear: true,
      minimumInputLength: 0
    });
  }
  function makeReadOnly($input){
    $input.prop('readonly', true).attr('tabindex', '-1').addClass('bg-light');
  }

  // ===== Init head selects =====
  const $supplierDisp  = $('#supplier_id_display');
  initSelect2($supplierDisp);
  $supplierDisp.prop('disabled', false).empty();
  if (INIT.supplier.id) {
    $supplierDisp.append(new Option(INIT.supplier.text, INIT.supplier.id, true, true)).trigger('change');
  } else {
    $supplierDisp.val(null).trigger('change');
  }
  $supplierDisp.prop('disabled', true);
  const $supplierHidden = $('#supplier_id');

  const $store = $('#store_id');
  initSelect2($store);
  if (INIT.store.id) {
    $store.append(new Option(INIT.store.text, INIT.store.id, true, true)).trigger('change');
  }

  const $po = $('#purchase_order_id');
  initSelect2($po);
  if (INIT.po.id) {
    $po.prop('disabled', false)
       .append(new Option(INIT.po.text, INIT.po.id, true, true))
       .trigger('change')
       .prop('disabled', true);
  }

  // ===== API URLs =====
  const productUnitsUrl     = "{{ route('receiving_inspecting.product_units', ['locale' => app()->getLocale()]) }}";
  const poItemsUrlTemplate  = "{{ route('receiving_inspecting.po_items', [app()->getLocale() ?? 'ar', 'po'=>'__PO__']) }}";
  const poCommUrlTemplate   = "{{ route('receiving_inspecting.po_committee', [app()->getLocale() ?? 'ar', 'po'=>'__PO__']) }}";

  let itemIndex = 0;
  let apprIndex = 0;

  function convLargeToSelected($tr){
    return Number($tr.find('.acc-unit-select option:selected').data('rate') || 1);
  }

  function updateLineCost($tr){
    const accBase   = Number($tr.find('.acc-qty-base').val() || 0);
    const po        = Number($tr.find('.po-qty').val() || 0);
    const unitPrice = Number($tr.data('unitPrice') || 0);
    const tax       = Number($tr.data('taxAmount') || 0);
    let taxShare = 0;
    if (po > 0) taxShare = tax * (accBase / po);
    const lineTotal = (accBase * unitPrice) + taxShare;
    $tr.find('.cost-price-text').text(formatPrice(lineTotal));
    $tr.find('.cost-price').val(lineTotal.toFixed(2));
  }

  function ensureRejDisplay($tr){
    if (!$tr.find('.rej-qty-text').length) {
      const $rejInput = $tr.find('.rej-qty');
      if ($rejInput.length){
        $('<span class="rej-qty-text d-block text-end small text-muted mb-1"></span>').insertBefore($rejInput);
      }
    }
    const $rej = $tr.find('.rej-qty');
    if ($rej.length){ makeReadOnly($rej); }
  }
  function updateAccAndRejForRow($tr, accBase){
    accBase = Math.max(0, Number(accBase) || 0);
    const delBase = Number($tr.find('.deliv-qty-base').val() || 0);
    const r       = convLargeToSelected($tr);
    if (accBase > delBase) accBase = delBase;
    $tr.find('.acc-qty-base').val(formatQty(accBase));
    $tr.find('.acc-qty-input').val(formatQty(accBase * r));
    const rejBase = Math.max(0, delBase - accBase);
    const rejDisp = rejBase * r;
    ensureRejDisplay($tr);
    $tr.find('.rej-qty-text').text(formatQty(rejDisp));
    $tr.find('.rej-qty').val(formatQty(rejBase));
    updateLineCost($tr);
  }

  function enforceProductTotals($tr){
    const pid  = String($tr.find('.product-id').val() || '');
    if (!pid) return;
    const $rows = $('#items-body tr').filter(function(){
      return String($(this).find('.product-id').val() || '') === pid;
    });
    const poLimit = Number($rows.first().find('.po-qty').val() || 0);
    const sum = { del:0, acc:0 };
    $rows.each(function(){
      sum.del += Number($(this).find('.deliv-qty-base').val() || 0);
      sum.acc += Number($(this).find('.acc-qty-base').val() || 0);
    });

    let curDelBase = Number($tr.find('.deliv-qty-base').val() || 0);
    if (poLimit > 0 && sum.del > poLimit) {
      const otherDelBase   = sum.del - curDelBase;
      const allowedDelBase = Math.max(0, poLimit - otherDelBase);
      if (curDelBase > allowedDelBase) {
        $tr.find('.deliv-qty-base').val(formatQty(allowedDelBase));
        const r = convLargeToSelected($tr);
        $tr.find('.deliv-qty-input').val(formatQty(allowedDelBase * r));
        curDelBase = allowedDelBase;
        showToast(I18N.deliveredSumGtPo, 'warning');
      }
    }

    let curAccBase = Number($tr.find('.acc-qty-base').val() || 0);
    if (curAccBase > curDelBase) {
      curAccBase = curDelBase;
      updateAccAndRejForRow($tr, curAccBase);
    }

    let newDelSum = 0, newAccSum = 0;
    $rows.each(function(){
      newDelSum += Number($(this).find('.deliv-qty-base').val() || 0);
      newAccSum += Number($(this).find('.acc-qty-base').val() || 0);
    });

    if (newAccSum > newDelSum) {
      const curAcc   = Number($tr.find('.acc-qty-base').val() || 0);
      const otherAcc = newAccSum - curAcc;
      const allowedAcc = Math.max(0, newDelSum - otherAcc);
      if (curAcc > allowedAcc) {
        updateAccAndRejForRow($tr, allowedAcc);
        showToast(I18N.acceptedSumGtDelivered, 'warning');
      }
    }

    updateLineCost($tr);
  }

  function enforceUniqueBatch($tr){
    const pid   = String($tr.find('.product-id').val() || '');
    const unit  = String($tr.find('.acc-unit-select').val() || '');
    const batch = normBatch($tr.find('.batch-no').val());
    if (!batch) { $tr.find('.batch-no').removeClass('is-invalid'); return; }
    let conflict = false;
    $('#items-body tr').each(function(){
      if (this === $tr[0]) return;
      const pid2   = String($(this).find('.product-id').val() || '');
      const unit2  = String($(this).find('.acc-unit-select').val() || '');
      const batch2 = normBatch($(this).find('.batch-no').val());
      if (!batch2 || batch2 !== batch) return;
      if (pid2 !== pid) { conflict = true; return false; }
      if (unit && unit2 && unit2 === unit) { conflict = true; return false; }
    });
    if (conflict){ $tr.find('.batch-no').addClass('is-invalid'); showToast(I18N.duplicateBatchScoped, 'danger'); }
    else { $tr.find('.batch-no').removeClass('is-invalid'); }
  }

// (المهم) unit_id لازم يفضل = mapping id بتاع سطر الـ PO
function syncRowUnitIdWithSelect($tr){
  const $sel = $tr.find('.acc-unit-select');
  const fallbackMapId = $sel.data('fallbackMapId'); // ده المابينج بتاع سطر الـ PO
  if (fallbackMapId) {
    $tr.find('.unit-id').val(String(fallbackMapId));
  }
}

  // ثنّي حقول delivered
  function ensureDualInputs($tr){
    const $delUI = $tr.find('.deliv-qty');
    if ($delUI.length && !$tr.find('.deliv-qty-base').length) {
      const baseName = $delUI.attr('name');
      $delUI.addClass('deliv-qty-input').removeClass('deliv-qty')
            .attr('name', baseName.replace('[delivered_qty]', '[delivered_qty_input]'));
      $('<input type="hidden" class="deliv-qty-base">').attr('name', baseName).insertAfter($delUI);
    }
    ensureRejDisplay($tr);

    // زر حذف
    if (!$tr.find('.btn-remove-row').length) {
      const $btn = $('<button type="button" class="btn btn-outline-danger btn-sm ms-1 btn-remove-row" title="حذف">✕</button>');
      const $dupHost = $tr.find('.btn-duplicate-batch').parent();
      if ($dupHost.length) $dupHost.append($btn);
    }
  }

  // تحميل وحدات الصنف + اختيار المحددة إن وُجدت
  function loadUnitsForRow($tr, productId, productUnitMapId, deliveredBase, acceptedBase, selectedUnitId){
    const $sel = $tr.find('.acc-unit-select');
    $sel.html('<option value="" disabled selected>...</option>');
    $sel.data('fallbackMapId', productUnitMapId || null);  // مهم

    $.get(productUnitsUrl, { p: productId, m: productUnitMapId }, function(resp){
      const options = resp?.options || [];
      options.forEach(op => {
        const opt = new Option(op.name, op.id, false, false);  // value = units.id (accepted_unit_id)
        $(opt).attr('data-rate', Number(op.rate_to_base || 1));
        $(opt).attr('data-map',  op.map_id || '');             // << product_units.id
        $(opt).attr('data-unit', op.id);
        $sel.append(opt);
      });

      if (selectedUnitId) {
        $sel.val(String(selectedUnitId));
      } else {
        const baseOpt = $sel.find('option').filter(function(){ return Number($(this).data('rate')) === 1; }).first();
        if (baseOpt.length) $sel.val(baseOpt.val());
        else if ($sel[0].options.length > 1) $sel.prop('selectedIndex', 1);
      }

      const r = convLargeToSelected($tr);
      $sel.data('lastRate', r);

      // الموردة/المقبولة بالعرض حسب المختارة
      $tr.find('.deliv-qty-input').val(formatQty(deliveredBase * r));
      $tr.find('.acc-qty-input').val(formatQty(acceptedBase  * r));
      const rejBase = Math.max(0, deliveredBase - acceptedBase);
      $tr.find('.rej-qty').val(formatQty(rejBase));
      $tr.find('.rej-qty-text').text(formatQty(rejBase * r));

      // حدّث unit_id المخفي حسب الخيار الحالي
      syncRowUnitIdWithSelect($tr);

      updateLineCost($tr);
    });
  }

  // ===== أحداث كل صف =====
  function attachRowEvents($tr){
    function clamp(v, min, max){ v = Number(v || 0); if (v < min) v=min; if (v > max) v=max; return v; }
    const getPoBase  = () => Number($tr.find('.po-qty').val() || 0);

    $tr.on('focus mousedown', '.acc-unit-select', function(){
      $(this).data('lastRate', convLargeToSelected($tr));
    });

    $tr.on('change', '.acc-unit-select', function(){
      const prevR = Number($(this).data('lastRate') || convLargeToSelected($tr));
      const newR  = convLargeToSelected($tr);

      let delDispOld = Number($tr.find('.deliv-qty-input').val() || 0);
      if (isNaN(delDispOld) || delDispOld < 0) delDispOld = 0;
      let delBase = delDispOld / prevR;
      delBase = clamp(delBase, 0, getPoBase());
      $tr.find('.deliv-qty-base').val(formatQty(delBase));
      $tr.find('.deliv-qty-input').val(formatQty(delBase * newR));

      let accDispOld = Number($tr.find('.acc-qty-input').val() || 0);
      if (isNaN(accDispOld) || accDispOld < 0) accDispOld = 0;
      let accBase = accDispOld / prevR;
      if (accBase > delBase) accBase = delBase;
      updateAccAndRejForRow($tr, accBase);

      enforceProductTotals($tr);
      enforceUniqueBatch($tr);

      // الأهم: كل تغيير للوحدة يحدّث unit_id (product_units.id)
      syncRowUnitIdWithSelect($tr);

      $(this).data('lastRate', newR);
    });

    // الموردة: input يحسب فقط، و blur/Change ينسّق ويطبّق الحدود
    $tr.off('input change blur', '.deliv-qty-input');
    $tr.on('input', '.deliv-qty-input', function(){
      const r = convLargeToSelected($tr);
      let delDisp = parseFloat(String($(this).val()).replace(/[^0-9.]/g,''));
      if (isNaN(delDisp) || delDisp < 0) delDisp = 0;
      let delBase = delDisp / (r || 1);
      $tr.find('.deliv-qty-base').val(delBase);

      let accBase = Number($tr.find('.acc-qty-base').val() || 0);
      if (accBase > delBase) accBase = delBase;
      $tr.find('.acc-qty-base').val(accBase);

      const rejBase = Math.max(0, delBase - accBase);
      $tr.find('.rej-qty').val(rejBase);
      $tr.find('.rej-qty-text').text(formatQty(rejBase * (r || 1)));

      updateLineCost($tr);
    });
    $tr.on('change blur', '.deliv-qty-input', function(){
      const r = convLargeToSelected($tr);
      const poLimit = Number($tr.find('.po-qty').val() || 0);

      let delDisp = parseFloat($(this).val());
      if (isNaN(delDisp) || delDisp < 0) delDisp = 0;

      let delBase = delDisp / (r || 1);
      if (poLimit > 0 && delBase > poLimit) delBase = poLimit;

      $(this).val(formatQty(delBase * (r || 1)));
      $tr.find('.deliv-qty-base').val(formatQty(delBase));

      let accBase = Math.min(Number($tr.find('.acc-qty-base').val() || 0), delBase);
      $tr.find('.acc-qty-base').val(formatQty(accBase));

      const rejBase = Math.max(0, delBase - accBase);
      $tr.find('.rej-qty').val(formatQty(rejBase));
      $tr.find('.rej-qty-text').text(formatQty(rejBase * (r || 1)));

      enforceProductTotals($tr);
      updateLineCost($tr);
    });

    // المقبولة
    $tr.off('input change blur', '.acc-qty-input');
    $tr.on('input', '.acc-qty-input', function(){
      const r = convLargeToSelected($tr);
      const delBase = Number($tr.find('.deliv-qty-base').val() || 0);

      let accDisp = parseFloat(String($(this).val()).replace(/[^0-9.]/g,''));
      if (isNaN(accDisp) || accDisp < 0) accDisp = 0;

      let accBase = accDisp / (r || 1);
      if (accBase > delBase) accBase = delBase;

      $tr.find('.acc-qty-base').val(accBase);

      const rejBase = Math.max(0, delBase - accBase);
      $tr.find('.rej-qty').val(rejBase);
      $tr.find('.rej-qty-text').text(formatQty(rejBase * (r || 1)));
      updateLineCost($tr);
    });
    $tr.on('change blur', '.acc-qty-input', function(){
      const r = convLargeToSelected($tr);
      const delBase = Number($tr.find('.deliv-qty-base').val() || 0);

      let accDisp = parseFloat($(this).val());
      if (isNaN(accDisp) || accDisp < 0) accDisp = 0;

      let accBase = accDisp / (r || 1);
      if (accBase > delBase) {
        accBase = delBase;
        showToast(I18N.acceptedGtDelivered, 'warning');
      }

      $(this).val(formatQty(accBase * (r || 1)));
      $tr.find('.acc-qty-base').val(formatQty(accBase));

      const rejBase = Math.max(0, delBase - accBase);
      $tr.find('.rej-qty').val(formatQty(rejBase));
      $tr.find('.rej-qty-text').text(formatQty(rejBase * (r || 1)));
      enforceProductTotals($tr);
      updateLineCost($tr);
    });

    // باتش مكرر + حذف
    $tr.on('click', '.btn-duplicate-batch', function(){ duplicateRowAsNewBatch($tr); });
    $tr.on('click', '.btn-remove-row', function(){
      const $nextFocus = $tr.next('tr').length ? $tr.next('tr') : $tr.prev('tr');
      $tr.remove();
      reindexRows();
      if ($nextFocus.length) enforceProductTotals($nextFocus);
      showToast(I18N.rowRemoved, 'success');
    });

    $tr.on('change blur', '.batch-no', function(){ enforceUniqueBatch($tr); });
  }

  // ===== إنشاء صف من الـPO (لصفحة الإنشاء) =====
  function addItemRowFromPO(row){
    const html = $('#item-row-template').html().replaceAll('__i__', itemIndex);
    const $tr  = $(html);

    $tr.find('.product-code').text(row.product_code || '');
    $tr.find('.product-name').text(row.product_name || '');
    $tr.find('.unit-name').text(row.unit_name || '');

    $tr.find('.product-id').val(row.product_id || '');
    $tr.find('.unit-id').val(row.unit_id || ''); // fallback map id (سطر الـPO)

    $tr.find('.req-qty-text').text(formatQty(row.request_qty || 0));
    $tr.find('.req-qty').val(Number(row.request_qty || 0));  makeReadOnly($tr.find('.req-qty'));

    $tr.find('.po-qty-text').text(formatQty(row.po_qty || 0));
    $tr.find('.po-qty').val(Number(row.po_qty || 0));        makeReadOnly($tr.find('.po-qty'));

    // التكلفة
    $tr.data('unitPrice', Number(row.unit_price || 0));
    $tr.data('taxAmount', Number(row.tax_amount || 0));

    $('#items-body').append($tr);
    attachRowEvents($tr);
    ensureDualInputs($tr);

    const initDelBase = Number(row.po_qty || 0);
    const initAccBase = initDelBase;

    $tr.find('.deliv-qty-base').val(formatQty(initDelBase));
    $tr.find('.acc-qty-base').val(formatQty(initAccBase));
    $tr.find('.rej-qty').val(formatQty(0));
    ensureRejDisplay($tr);
    $tr.find('.rej-qty-text').text(formatQty(0));

    // تحميل الوحدات — selectedUnitId فارغ في الإنشاء
    loadUnitsForRow($tr, row.product_id, row.unit_id, initDelBase, initAccBase, null);

    itemIndex++;
  }

  // ===== صف موجود (لصفحة التعديل) =====
  function addItemRowFromExisting(row){
    const html = $('#item-row-template').html().replaceAll('__i__', itemIndex);
    const $tr  = $(html);

    $tr.find('.product-code').text(row.product_code || '');
    $tr.find('.product-name').text(row.product_name || '');
    $tr.find('.unit-name').text(row.unit_name || '');

    $tr.find('.product-id').val(row.product_id || '');
    $tr.find('.unit-id').val(row.unit_id || ''); // mapping id المحفوظ

    $tr.find('.req-qty-text').text(formatQty(row.request_qty || 0));
    $tr.find('.req-qty').val(Number(row.request_qty || 0));  makeReadOnly($tr.find('.req-qty'));

    $tr.find('.po-qty-text').text(formatQty(row.po_qty || 0));
    $tr.find('.po-qty').val(Number(row.po_qty || 0));        makeReadOnly($tr.find('.po-qty'));

    $tr.data('unitPrice', Number(row.unit_price || 0));
    $tr.data('taxAmount', Number(row.tax_amount || 0));

    $('#items-body').append($tr);
    attachRowEvents($tr);
    ensureDualInputs($tr);

    const delBase = Number(row.delivered_qty_base || 0);
    const accBase = Number(row.accepted_qty_base  || 0);
    const rejBase = Math.max(0, delBase - accBase);

    $tr.find('.deliv-qty-base').val(formatQty(delBase));
    $tr.find('.acc-qty-base').val(formatQty(accBase));
    $tr.find('.rej-qty').val(formatQty(rejBase));
    if ($tr.find('.batch-no').length) $tr.find('.batch-no').val(row.batch_no || '');
    if ($tr.find('.expiry-date').length) $tr.find('.expiry-date').val(row.expiry_date || '');

    $tr.find('.cost-price-text').text(formatPrice(row.cost_price || 0));
    $tr.find('.cost-price').val(formatPrice(row.cost_price || 0)); makeReadOnly($tr.find('.cost-price'));

    loadUnitsForRow($tr, row.product_id, row.unit_id, delBase, accBase, row.accepted_unit_id || null);

    itemIndex++;
  }

  function duplicateRowAsNewBatch($src){
    const row = {
      product_id   : $src.find('.product-id').val(),
      unit_id      : $src.find('.unit-id').val(), // mapping id الحالي
      product_code : $src.find('.product-code').text(),
      product_name : $src.find('.product-name').text(),
      unit_name    : $src.find('.unit-name').text(),
      request_qty  : Number($src.find('.req-qty').val() || 0),
      po_qty       : Number($src.find('.po-qty').val() || 0),
      unit_price   : Number($src.data('unitPrice') || 0),
      tax_amount   : Number($src.data('taxAmount') || 0),
      batch_no     : $src.find('.batch-no').val() || ''
    };

    const html = $('#item-row-template').html().replaceAll('__i__', itemIndex);
    const $tr  = $(html);

    $tr.find('.product-code').text(row.product_code);
    $tr.find('.product-name').text(row.product_name);
    $tr.find('.unit-name').text(row.unit_name);

    $tr.find('.product-id').val(row.product_id);
    $tr.find('.unit-id').val(row.unit_id); // fallback map id

    $tr.find('.req-qty-text').text(formatQty(row.request_qty));
    $tr.find('.req-qty').val(row.request_qty);

    $tr.find('.po-qty-text').text(formatQty(row.po_qty));
    $tr.find('.po-qty').val(row.po_qty);

    if ($tr.find('.batch-no').length) $tr.find('.batch-no').val(row.batch_no);

    $tr.data('unitPrice', row.unit_price);
    $tr.data('taxAmount', row.tax_amount);

    $src.after($tr);
    attachRowEvents($tr);
    ensureDualInputs($tr);

    $tr.find('.deliv-qty-base').val(formatQty(0));
    $tr.find('.acc-qty-base').val(formatQty(0));
    $tr.find('.deliv-qty-input').val(formatQty(0));
    $tr.find('.acc-qty-input').val(formatQty(0));
    $tr.find('.rej-qty').val(formatQty(0));
    ensureRejDisplay($tr);
    $tr.find('.rej-qty-text').text(formatQty(0));

    $tr.find('.cost-price-text').text(formatPrice(0));
    $tr.find('.cost-price').val('0.00');

    loadUnitsForRow($tr, row.product_id, row.unit_id, 0, 0, null);

    itemIndex++;
    reindexRows();
    enforceUniqueBatch($tr);
  }

  function reindexRows(){
    $('#items-body tr').each(function(i){
      $(this).find('td.idx').text(i+1);
    });
  }

  // ===== اللجنة =====
  function addApprovalRowFromCommittee(member){
    member = member || {};
    let htmlTmpl = $('#approval-row-template');
    if (!htmlTmpl.length){
      showToast('قالب صف اللجنة غير موجود في الصفحة.', 'danger');
      return;
    }
    let html = htmlTmpl.html().replaceAll('__j__', apprIndex);
    let $row = $(html);

    const $selEmp = $row.find('.employee-select');
    initSelect2($selEmp);

    if (member.employee_id) {
      const text = (member.employee_name && String(member.employee_name).trim().length)
                  ? member.employee_name : ('#' + member.employee_id);
      $selEmp.append(new Option(text, member.employee_id, true, true)).trigger('change');
    } else {
      $selEmp.val(null).trigger('change');
    }

    if (Array.isArray(member.responsibilities) && member.responsibilities.length){
      const $resp = $row.find('select[name^="approvals"][name$="[responsibilities][]"]');
      member.responsibilities.forEach(function(val){
        if ($resp.find('option[value="'+val+'"]').length === 0) {
          $resp.append(new Option(val, val, false, false));
        }
      });
      $resp.val(member.responsibilities).trigger('change');
    }

    if (member.is_chairman) {
      $row.find('input[type="checkbox"][name^="approvals"][name$="[is_chairman]"]').prop('checked', true);
    }

    $row.find('.remove-approver').on('click', function(){
      const $hr = $row.next('hr').length ? $row.next('hr') : $row.prev('hr');
      if ($hr.length) $hr.remove();
      $row.remove();
    });

    $('#approvals-list').append($row);
    apprIndex++;
    return $row;
  }

  // زر إضافة عضو
  $(document).off('click.addMember', '#add-approver').on('click.addMember', '#add-approver', function(e){
    e.preventDefault();
    addApprovalRowFromCommittee({});
  });

  // ===== تعبئة المورد من استجابة أصناف الـPO =====
  function setSupplierFromPOResponse(resp){
    const sId   = resp && resp.supplier && resp.supplier.id   ? resp.supplier.id   : '';
    const sText = resp && resp.supplier && resp.supplier.text ? resp.supplier.text : '';
    $supplierDisp.prop('disabled', false).empty();
    if (sId) $supplierDisp.append(new Option(sText, sId, true, true)).trigger('change');
    else     $supplierDisp.val(null).trigger('change');
    $supplierDisp.prop('disabled', true);
    $supplierHidden.val(sId || '');
  }

  // ===== عند اختيار أمر التوريد: حمّل الأصناف واللجنة =====
  $('#purchase_order_id').on('change', function(){
    const po = $(this).val();
    $('#items-body').empty(); itemIndex = 0;
    $('#approvals-list').empty(); apprIndex = 0;

    if(!po){
      $supplierDisp.prop('disabled', false).val(null).trigger('change').prop('disabled', true);
      $supplierHidden.val('');
      return;
    }

    // 1) أصناف الـPO + المورد
    $.get(poItemsUrlTemplate.replace('__PO__', po), function(resp){
      setSupplierFromPOResponse(resp);
      const items = resp && resp.items ? resp.items : [];
      items.forEach(addItemRowFromPO);
      reindexRows();
    });

    // 2) اللجنة من الـPO
    $.get(poCommUrlTemplate.replace('__PO__', po), function(resp){
      const committee = resp && resp.committee ? resp.committee : [];
      if (!committee.length) {
        showToast(I18N.noCommittee, 'warning');
        return;
      }
      committee.forEach(addApprovalRowFromCommittee);
    });
  });

  // ====== تهيئة الصفحة (items + approvals) ======
  (function initPage(){
    (INIT.items || []).forEach(addItemRowFromExisting);
    reindexRows();
    if (INIT.committee && INIT.committee.length) {
      INIT.committee.forEach(addApprovalRowFromCommittee);
    }
  })();
  const form   = document.getElementById('ri-form');
  const action = document.getElementById('requisition_action');

  function getPOValue() {
    // في create: select#purchase_order_id
    // في edit: select معطل + hidden بنفس الاسم
    const sel = document.getElementById('purchase_order_id');
    if (sel && !sel.disabled && sel.value) return sel.value;
    const hidden = form.querySelector('input[name="purchase_order_id"]');
    return hidden ? hidden.value : '';
  }

  form.addEventListener('submit', function (e) {
    // لو اتحدد الاختيار قبل كده، سيب الإرسال يكمل عادي
    if (action.value) return;

    // لازم يكون في PO عشان يبقى في طلب احتياج مرتبط
    const poVal = getPOValue();
    if (!poVal) return; // ما فيش PO → خلّي الإرسال يكمل بدون سؤال

    e.preventDefault();
    const modalEl = document.getElementById('reqActionModal');
    const modal   = new bootstrap.Modal(modalEl, { backdrop: 'static' });

    // اربط أزرار الاختيار مرة واحدة
    const choose = (val) => {
      action.value = val;    // 'close' أو 'open'
      modal.hide();
      // أعد الإرسال — هذه المرة لن يُمنع لأن action.value موجود
      setTimeout(() => form.submit(), 0);
    };

    // لتفادي تعدد الربط عند محاولات حفظ متكررة
    modalEl.querySelector('#btnReqClose').onclick     = () => choose('close');
    modalEl.querySelector('#btnReqKeepOpen').onclick  = () => choose('open');

    modal.show();
  });
})();
</script>





@endsection
