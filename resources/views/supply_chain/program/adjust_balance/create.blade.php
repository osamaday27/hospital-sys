@extends('layouts.master_table')

@section('title')
  تسوية الأرصدة
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

{{-- Modal تأكيد --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">تأكيد العملية</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body"><p id="confirmText" class="mb-0"></p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="button" class="btn btn-primary" id="confirmYes">متابعة</button>
      </div>
    </div>
  </div>
</div>

{{-- الشريط العلوي --}}
<div class="card mb-3 sticky-top" style="top:72px; z-index: 10;">
  <div class="card-body">
    <form id="head-form" class="row g-3 align-items-end">
      @csrf
      <div class="col-md-4">
        <label class="form-label">المخزن</label>
        <select id="store-select" class="form-control" style="width:100%"></select>
      </div>
      <div class="col-md-3">
        <label class="form-label">تاريخ ووقت التسوية</label>
        <input type="datetime-local" id="movement-dt" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">نوع التسوية</label>
        <select id="adjust-type" class="form-select">
          <option value="increase">زيادة</option>
          <option value="decrease">نقص</option>
        </select>
      </div>
      <div class="col-12">
        <div class="d-flex align-items-center gap-2">
          <select id="product-select" class="form-control" style="width:380px"></select>
          <button type="button" id="btn-add-product" class="btn btn-primary">
            <i class="ri-add-line"></i> إضافة للجدول
          </button>
          <span class="text-muted small">* يظهر هنا أصناف المخزن المحدد فقط</span>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- جدول التفاصيل --}}
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>تفاصيل التسوية</strong>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary btn-sm" id="btn-select-none">إلغاء التحديد</button>
      <button class="btn btn-outline-danger btn-sm" id="btn-del-selected">حذف المحدد</button>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="details-table" class="table table-bordered align-middle w-100">
        <thead class="table-light">
          <tr>
            <th style="width:36px"><input type="checkbox" id="chk-all"></th>
            <th style="width:50px">#</th>
            <th style="width:140px">كود التسوية</th>
            <th>الصنف</th>
            <th style="width:170px">الوحدة</th>
            <th style="width:140px">الكمية</th>
            <th style="width:220px">الباتش</th>
            <th style="width:150px">تاريخ الانتهاء</th>
            <th style="width:140px">رصيد النظام</th>
            <th style="width:140px">سعر البيع</th>
            <th style="width:140px">سعر التكلفة</th>
            <th style="width:220px">ملاحظات</th>
            <th style="width:60px">حذف</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button class="btn btn-success" id="btn-save">حفظ التسوية</button>
      <a href="{{ route('adjust_balance.index') }}" class="btn btn-light">رجوع</a>
    </div>
  </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function showToast(type, msg) {
  const id = 't' + Date.now() + Math.random().toString().slice(2);
  const html = `
  <div id="${id}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">${msg}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>`;
  $('#toast-container').append(html);
  new bootstrap.Toast(document.getElementById(id), { delay: 2800 }).show();
}

function confirmDialog(msg){
  return new Promise(resolve=>{
    const modalEl = document.getElementById('confirmModal');
    modalEl.querySelector('#confirmText').textContent = msg;
    const modal = new bootstrap.Modal(modalEl);
    const yesBtn = modalEl.querySelector('#confirmYes');

    const onCancel = () => { cleanup(); resolve(false); };
    const onYes    = () => { cleanup(); modal.hide(); resolve(true);  };

    function cleanup(){
      yesBtn.removeEventListener('click', onYes);
      modalEl.removeEventListener('hidden.bs.modal', onCancel);
    }
    yesBtn.addEventListener('click', onYes, { once:true });
    modalEl.addEventListener('hidden.bs.modal', onCancel, { once:true });

    modal.show();
  });
}

const ROUTES = {
  productsByStore: "{{ route('adjust_balance.ajax.products_by_store') }}",
  stores: "{{ route('adjust_balance.ajax.stores') }}",
  unitsBulk: "{{ route('adjust_balance.ajax.product_units_bulk') }}",
  batches: "{{ route('adjust_balance.ajax.batches') }}",
  currentBalance: "{{ route('adjust_balance.ajax.current_balance') }}",
  store: "{{ route('adjust_balance.store') }}"
};

let dt;
let rowSeq = 0;
const rowBatches = {}; // key => { batch => { base, expiry } }

function key(pid, extra) { return String(pid) + '__' + String(extra || ''); }
function escapeHtml(s){ return $('<div>').text(s||'').html(); }
function todayPrefix() {
  const d = new Date();
  return `AB-${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}-`;
}
function reassignCodes(){
  const prefix = todayPrefix();
  let i = 0;
  $('#details-table tbody tr').each(function(){
    i++;
    $(this).find('.ab-code').val(prefix + String(i).padStart(4,'0'));
  });
}

$(function(){
  dt = $('#details-table').DataTable({
    processing: true,
    deferRender: true,
    pageLength: 10,
    lengthChange: false,
    ordering: false,
    searching: false,
    info: true
  });

  dt.on('draw', function(){
    dt.column(1, {page:'current'}).nodes().each((cell, i)=> cell.innerHTML = (dt.page.info().start + i + 1));
  });

  $('#store-select').select2({
    placeholder: 'اختر المخزن',
    ajax: { url: ROUTES.stores, data: p=>({q:p.term}), processResults: d=>({results:d}) }
  });

  $('#product-select').select2({
    placeholder: 'ابحث عن صنف من مخزنك',
    ajax: {
      url: ROUTES.productsByStore,
      data: p => ({ q: p.term, store_id: $('#store-select').val() }),
      processResults: d => ({ results: d })
    },
    width: '380px'
  });

  $('#btn-add-product').on('click', function(){
    const storeId = $('#store-select').val();
    if (!storeId) { showToast('warning','اختر المخزن أولاً'); return; }

    const pid = $('#product-select').val();
    if (!pid) { showToast('warning','اختر الصنف أولاً'); return; }

    const p1 = $.get(ROUTES.unitsBulk, { ids: [pid] });
    const p2 = $.get(ROUTES.batches,   { store_id: storeId, product_id: pid });

    Promise.all([p1,p2]).then(([unitsMap, batches])=>{
      const units = (unitsMap && unitsMap[pid]) ? unitsMap[pid] : [];
      if (!units.length) { showToast('warning','لا توجد وحدات معرفة لهذا الصنف'); return; }

      units.sort((a,b)=> (a.factor - b.factor));
      const productText = ($('#product-select').select2('data')[0]?.text || '').trim();
      addRow({ product_id: pid, product_text: productText, units, batches: batches || [] });
    }).catch(()=> showToast('danger','تعذر تحميل وحدات/باتشات الصنف.'));
  });

  $('#chk-all').on('change', function(){ $('#details-table tbody .row-check').prop('checked', this.checked); });
  $('#btn-select-none').on('click', ()=> { $('#details-table tbody .row-check').prop('checked', false); $('#chk-all').prop('checked', false); });
  $('#btn-del-selected').on('click', function(){
    $('#details-table tbody .row-check:checked').each(function(){
      const $tr = $(this).closest('tr');
      dt.row($tr).remove();
    });
    dt.draw(false);
    reassignCodes();
  });

  $('#btn-save').on('click', submitSave);

  $('#adjust-type').on('change', function(){
    $('#details-table tbody tr').each(function(){ validateQuantity($(this), false); });
  });
});

/* ========= إنشاء صف ========= */
function addRow({product_id, product_text, units, batches}){
  rowSeq++;
  const tmpKey = key(product_id, 'R'+rowSeq);
  const code = todayPrefix() + String($('#details-table tbody tr').length + 1).padStart(4,'0');

  const tr = $(`
    <tr data-key="${tmpKey}" data-pid="${product_id}">
      <td class="text-center"><input type="checkbox" class="form-check-input row-check"></td>
      <td class="text-muted"></td>
      <td><input type="text" class="form-control form-control-sm ab-code" value="${code}" readonly></td>
      <td>${escapeHtml(product_text)}</td>
      <td><select class="form-select form-select-sm unit-select"></select></td>
      <td><input type="number" min="0" step="0.0001" class="form-control form-control-sm qty-input" placeholder="0"></td>
      <td><select class="form-select form-select-sm batch-select" style="width:220px"></select></td>
      <td><input type="date" class="form-control form-control-sm expiry-input"></td>
      <td><input type="text" class="form-control form-control-sm sys-qty" value="0" readonly></td>
      <td><input type="text" class="form-control form-control-sm sale-price" value="-" readonly></td>
      <td><input type="text" class="form-control form-control-sm cost-price" value="-" readonly></td>
      <td><input type="text" class="form-control form-control-sm notes-input" placeholder="ملاحظات"></td>
      <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-danger del-row"><i class="ri-delete-bin-6-line"></i></button>
      </td>
    </tr>
  `)[0];

  dt.row.add(tr).draw(false);
  reassignCodes();

  const $tr = $(tr);

  rowBatches[tmpKey] = {};
  (batches||[]).forEach(b=>{
    rowBatches[tmpKey][String(b.batch_no)] = { base: parseFloat(b.base||0), expiry: b.expiry||null };
  });

  const $unit = $tr.find('.unit-select').empty();
  units.forEach(u=>{
    $unit.append(`<option value="${u.product_unit_id}" data-unitid="${u.unit_id}" data-factor="${u.factor}" data-sale="${u.sale_price ?? ''}" data-cost="${u.cost_price ?? ''}">
      ${escapeHtml(u.unit_name)} ${u.factor ? `(×${u.factor})` : ''}
    </option>`);
  });
  const first = $unit.find('option:first');
  $unit.val(first.val());
  $tr.find('.sale-price').val(first.data('sale') ?? '-');
  $tr.find('.cost-price').val(first.data('cost') ?? '-');

  const $batch = $tr.find('.batch-select').select2({
    data: (batches||[]).map(b=>({ id: b.batch_no, text: b.label })),
    width: '220px',
    tags: true,
    placeholder: 'اختر/اكتب باتش',
    allowClear: true
  });

  if ((batches||[]).length) {
    let chosen = null;
    for (const b of batches) { if ((b.base||0) > 0) { chosen = b; break; } }
    if (!chosen) chosen = batches[0];
    $batch.val(chosen.batch_no).trigger('change');
    $tr.find('.expiry-input').val(chosen.expiry || '');
  } else {
    $batch.val(null).trigger('change');
  }

  updateSysQtyForRow($tr);

  $tr.on('change', '.unit-select', function(){
    const sel = $(this).find(':selected');
    const sale = sel.data('sale');
    const cost = sel.data('cost');
    $tr.find('.sale-price').val((sale !== undefined && sale !== null && String(sale) !== '') ? sale : '-');
    $tr.find('.cost-price').val((cost !== undefined && cost !== null && String(cost) !== '') ? cost : '-');
    updateSysQtyForRow($tr);
    validateQuantity($tr, true);
  });

  $tr.on('change', '.batch-select', function(){
    const b = String($(this).val()||'');
    const rb = rowBatches[tmpKey][b];
    $tr.find('.expiry-input').val((rb && rb.expiry) ? rb.expiry : '');
    updateSysQtyForRow($tr);

    const pid = $tr.data('pid');
    let duplicate = false;
    $('#details-table tbody tr').not($tr).each(function(){
      const p2 = $(this).data('pid');
      const b2 = String($(this).find('.batch-select').val()||'').trim().toLowerCase();
      if (p2 == pid && b2 === b.trim().toLowerCase()) { duplicate = true; return false; }
    });
    if (duplicate) {
      showToast('warning','لا يمكن تكرار نفس الصنف بنفس الباتش في الجدول.');
      $(this).val(null).trigger('change');
    }
  });

  $tr.on('keypress', '.qty-input', function(e){ if (e.key === '-') e.preventDefault(); });
  $tr.on('blur', '.qty-input', function(){ validateQuantity($tr, true); });

  $tr.on('click', '.del-row', function(){
    dt.row($tr).remove().draw(false);
    reassignCodes();
  });
}

/* ========= رصيد النظام ========= */
function updateSysQtyForRow($tr){
  const storeId = $('#store-select').val();
  const pid     = $tr.data('pid');
  const batch   = String($tr.find('.batch-select').val()||'');
  const factor  = parseFloat($tr.find('.unit-select :selected').data('factor') || 1);

  if (!storeId || !pid) { $tr.find('.sys-qty').val('0'); return; }

  $.get(ROUTES.currentBalance, { store_id: storeId, product_id: pid, batch_no: batch })
    .then(resp => {
      const base   = parseFloat(resp?.base || 0);
      const expiry = resp?.expiry || null;
      const unitQty = factor>0 ? (base / factor) : 0;
      $tr.find('.sys-qty').val(unitQty.toFixed(4));
      $tr.find('.expiry-input').val(expiry || '');
    });
}

/* ========= التحقق من الكمية ========= */
function validateQuantity($tr, showMsg){
  const adjustType = $('#adjust-type').val();
  const sys = parseFloat($tr.find('.sys-qty').val() || 0);
  let qty   = parseFloat($tr.find('.qty-input').val() || 0);

  if (!qty || qty <= 0) {
    if (showMsg) showToast('warning', 'الكمية يجب أن تكون أكبر من صفر.');
    return false;
  }

  if (adjustType === 'increase' && !(qty > sys)) {
    if (showMsg) showToast('danger', 'في تسوية الزيادة يجب أن تكون الكمية أكبر من رصيد النظام.');
    return false;
  }
  if (adjustType === 'decrease' && !(qty < sys)) {
    if (showMsg) showToast('danger', 'في تسوية النقص يجب أن تكون الكمية أقل من رصيد النظام.');
    return false;
  }
  return true;
}

/* ========= الحفظ ========= */
async function submitSave(){
  const storeId = $('#store-select').val();
  if (!storeId) { showToast('warning','اختر المخزن أولاً'); return; }

  const dtVal = $('#movement-dt').val();
  if (!dtVal) { showToast('warning','أدخل تاريخ ووقت التسوية'); return; }

  const adjustType = $('#adjust-type').val();

  const items = [];
  let ok = true;

  $('#details-table tbody tr').each(function(){
    const $tr = $(this);
    const pid   = parseInt($tr.data('pid'),10);
    const puId  = parseInt($tr.find('.unit-select').val(),10);
    const unitId= parseInt($tr.find('.unit-select :selected').data('unitid'),10);
    const qty   = parseFloat($tr.find('.qty-input').val() || 0);
    const batch = String($tr.find('.batch-select').val() || '');
    const exp   = String($tr.find('.expiry-input').val() || '');
    const notes = String($tr.find('.notes-input').val() || '');

    if (!pid || !puId || !unitId || !qty || qty<=0) { ok=false; return false; }
    if (!validateQuantity($tr, true)) { ok=false; return false; }

    const sale = $tr.find('.sale-price').val();
    const cost = $tr.find('.cost-price').val();

    items.push({
      product_id: pid,
      product_unit_id: puId,
      unit_id: unitId,
      quantity: qty,
      batch_no: batch,
      expiry_date: exp || null,
      sale_price: (sale && sale !== '-' ? parseFloat(sale) : null),
      cost_price: (cost && cost !== '-' ? parseFloat(cost) : null),
      notes: notes || null
    });
  });

  if (!ok) return;
  if (items.length === 0) { showToast('warning','أضف سطرًا واحدًا على الأقل'); return; }

  const okConfirm = await confirmDialog('سيتم تعديل رصيد المخزن وتسجيل حركة لا يمكن التراجع عنها. هل تريد المتابعة؟');
  if (!okConfirm) return;

  $('#btn-save').prop('disabled', true);

  $.ajax({
    url: ROUTES.store, type:'POST',
    contentType:'application/json', data: JSON.stringify({
      store_id: storeId,
      movement_datetime: dtVal,
      adjust_type: adjustType,
      items: items
    }),
    success: function(resp){
      showToast('success', resp?.message || 'تم الحفظ بنجاح');
      setTimeout(()=> window.location.href = resp?.redirect || "{{ route('adjust_balance.index') }}", 600);
    },
    error: function(xhr){
      const msg = xhr?.responseJSON?.message || 'خطأ غير متوقع';
      showToast('danger', msg);
      $('#btn-save').prop('disabled', false);
    }
  });
}
</script>
@endsection
