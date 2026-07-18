@extends('layouts.master_table')

@section('title','إنشاء مرتجع عميل إلى مخزن')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('return_from_client.index') }}">مرتجعات العملاء</a></li>
          <li class="breadcrumb-item active">إنشاء</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3 sticky-top" style="top:72px; z-index:10;">
  <div class="card-body">
    <form id="head-form" class="row g-3">
      <div class="col-md-2">
        <label class="form-label">الكود</label>
        <input type="text" class="form-control" value="{{ $code }}" readonly>
        <input type="hidden" name="code" value="{{ $code }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">التاريخ</label>
        <input type="date" name="return_date" class="form-control" value="{{ $today }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">العميل</label>
        <select name="client_id" id="client_id" class="form-select sel2" required></select>
      </div>
      <div class="col-md-4">
        <label class="form-label">المخزن</label>
        <select name="store_id" id="store_id" class="form-select sel2" required disabled></select>
      </div>

      <div class="col-12 d-flex align-items-center gap-2">
        <button type="button" id="btnShowRequests" class="btn btn-outline-secondary">
          عرض طلبات العميل
        </button>
        <span id="lock-badge" class="badge bg-secondary d-none">تم قفل بيانات الرأس بعد إضافة بنود</span>
      </div>
    </form>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <h6 class="mb-3">بنود المستند</h6>
    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle" id="items-table">
        <thead class="table-light">
          <tr>
            <th style="width:40px;">#</th>
            <th>الصنف / الكود</th>
            <th style="width:200px;">الوحدة</th>
            <th style="width:140px;">الكمية</th>
            <th style="width:220px;">التشغيلة</th>
            <th style="width:160px;">رقم الطلب</th>
            <th>ملاحظات</th>
            <th style="width:60px;">حذف</th>
          </tr>
        </thead>
        <tbody id="items-body">
          <tr class="text-muted"><td colspan="8" class="text-center">لا توجد بنود بعد… اضغط "عرض طلبات العميل" ثم اختر.</td></tr>
        </tbody>
      </table>
    </div>

    <div class="text-end">
      <button id="btnSave" class="btn btn-primary">حفظ المستند</button>
    </div>
  </div>
</div>

<div class="modal fade" id="requestsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">طلبات العميل المتاحة للإرجاع</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="اغلاق"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2 small text-muted">
          تظهر فقط البنود التي لا يزال لها كمية متاحة للإرجاع حسب رقم الطلب (من طلبات بحالة processed).
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr>
              <th>#</th><th>رقم الطلب</th><th>التاريخ</th><th>الصنف</th><th>المتاح (بالأصغر)</th><th>إجراء</th>
            </tr></thead>
            <tbody id="req-body"><tr><td colspan="6" class="text-center text-muted">اختر العميل والمخزن أولاً…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const routes = {
    clients:  "{{ url('return_from_client/ajax/clients') }}",
    stores:   "{{ url('return_from_client/ajax/stores') }}",
    units:    "{{ url('return_from_client/ajax/units') }}",
    requests: "{{ url('return_from_client/ajax/requests') }}",
    batches:  "{{ url('return_from_client/ajax/batches') }}",
    store:    "{{ route('return_from_client.store') }}",
    firstId:  "{{ url('return_from_client/ajax/first-id-by-code') }}",
  };

  function toast(msg, type='info'){
    const id='t'+Date.now();
    const html = `<div class="toast align-items-center text-bg-${type} border-0" id="${id}">
      <div class="d-flex"><div class="toast-body">${msg}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
    const c=document.getElementById('toast-container'); c.insertAdjacentHTML('beforeend',html);
    new bootstrap.Toast(document.getElementById(id),{delay:4000}).show();
  }

  function initSelect2(sel, url, extraDataFn=null){
    $(sel).select2({
      width:'100%', allowClear:true, placeholder:'اختر…',
      ajax:{
        delay:200, url,
        data: params => Object.assign({ q: params.term || '' }, extraDataFn ? extraDataFn() : {}),
        processResults: data => data || { results: [] }
      }
    });
  }

  // العملاء
  initSelect2('#client_id', routes.clients);

  // المخازن — تتفاعل مع العميل
  initSelect2('#store_id', routes.stores, ()=>({ client_id: $('#client_id').val() || 0 }));
  $('#store_id').prop('disabled', true);

  let headerLocked = false;
  let lastClient = null, lastStore = null;

  function itemsCount(){ return document.querySelectorAll('#items-body tr.item-row').length; }
  function lockHeader(){
    headerLocked = true;
    lastClient = $('#client_id').val();
    lastStore  = $('#store_id').val();
    $('#client_id, #store_id').prop('disabled', true).trigger('change.select2');
    $('#lock-badge').removeClass('d-none');
  }
  function unlockHeader(){
    headerLocked = false;
    $('#client_id, #store_id').prop('disabled', false).trigger('change.select2');
    $('#lock-badge').addClass('d-none');
  }

  $('#client_id').on('change', function(){
    if (itemsCount() > 0) {
      toast('لا يمكن تغيير العميل بعد إضافة بنود. احذف كل البنود أولًا.','warning');
      $(this).val(lastClient).trigger('change.select2');
      return;
    }
    const hasClient = !!$('#client_id').val();
    $('#store_id').prop('disabled', !hasClient).val(null).trigger('change');
    lastClient = $('#client_id').val();
  });

  $('#store_id').on('change', function(){
    if (itemsCount() > 0) {
      toast('لا يمكن تغيير المخزن بعد إضافة بنود. احذف كل البنود أولًا.','warning');
      $(this).val(lastStore).trigger('change.select2');
      return;
    }
    lastStore = $('#store_id').val();
  });

  // تحميل الطلبات المتاحة للإرجاع
  document.getElementById('btnShowRequests')?.addEventListener('click', async ()=>{
    const cid = $('#client_id').val(), store = $('#store_id').val();
    if (!cid)  { toast('اختر العميل أولاً.','warning'); return; }
    if (!store){ toast('اختر المخزن أولاً.','warning'); return; }

    const res = await fetch(`${routes.requests}?client_id=${cid}&store_id=${store}`);
    const data = await res.json();
    const body = document.getElementById('req-body');
    if (!data.results?.length) {
      body.innerHTML = '<tr><td colspan="6" class="text-center text-muted">لا توجد بنود متاحة للإرجاع.</td></tr>';
    } else {
      body.innerHTML = data.results.map((x,i)=>`
        <tr>
          <td>${i+1}</td>
          <td>${x.request_number}</td>
          <td>${x.request_date || ''}</td>
          <td>${x.product_text}</td>
          <td>${Number(x.available_base).toFixed(4)}</td>
          <td><button class="btn btn-sm btn-primary" data-r='${JSON.stringify(x).replace(/'/g,"&#39;")}'>إضافة</button></td>
        </tr>
      `).join('');
    }
    new bootstrap.Modal(document.getElementById('requestsModal')).show();
  });

  // كاش للوحدات والباتشات
  const unitsCache = new Map();
  const batchCache = new Map();

  async function getUnits(product_id){
    if (unitsCache.has(product_id)) return unitsCache.get(product_id);
    const arr = (await (await fetch(`${routes.units}?product_id=${product_id}`)).json()).results || [];
    unitsCache.set(product_id, arr);
    return arr;
  }
  async function getBatches(product_id, store_id){
    const key = `${product_id}|${store_id}`;
    if (batchCache.has(key)) return batchCache.get(key);
    const arr = (await (await fetch(`${routes.batches}?product_id=${product_id}&store_id=${store_id}`)).json()).results || [];
    batchCache.set(key, arr);
    return arr;
  }

  function hasRow(pid,batch,reqNo){
    let ok=false;
    document.querySelectorAll('#items-body tr.item-row').forEach(tr=>{
      const a = Number(tr.dataset.pid||0);
      const b = (tr.dataset.batch||'');
      const c = (tr.dataset.req||'');
      if (a===Number(pid) && b===String(batch||'') && c===String(reqNo||'')) ok=true;
    });
    return ok;
  }

  document.getElementById('req-body')?.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button[data-r]');
    if (!btn) return;
    const obj = JSON.parse(btn.getAttribute('data-r').replace(/&#39;/g,"'"));
    const store = $('#store_id').val();

    const [units, batches] = await Promise.all([
      getUnits(obj.product_id),
      getBatches(obj.product_id, store)
    ]);

    const unitId = units[0]?.id || null;
    const ftb    = Number(units[0]?.ftb || 1);
    const availUnit = ftb>0 ? (Number(obj.available_base)/ftb) : 0;

    const b0 = batches[0] || null;
    const batchNo = b0?.batch_no || '';

    if (hasRow(obj.product_id, batchNo, obj.request_number)) {
      toast('تم تجاهل الصف لتجنّب التكرار (نفس الطلب/الصنف/الباتش).','warning'); return;
    }

    addRow({
      request_number: obj.request_number,
      product_id: obj.product_id,
      product_text: obj.product_text,
      units, batches,
      default_unit_id: unitId,
      default_qty: availUnit,
      default_batch_no: batchNo,
      available_base: Number(obj.available_base)
    });

    if (!headerLocked && document.querySelectorAll('#items-body tr.item-row').length > 0) {
      lockHeader();
    }

    bootstrap.Modal.getInstance(document.getElementById('requestsModal'))?.hide();
    toast('تمت إضافة السطر.','success');
  });

  function addRow({request_number, product_id, product_text, units=[], batches=[], default_unit_id=null, default_qty=0, default_batch_no='', available_base=0}){
    const idx = document.querySelectorAll('#items-body tr.item-row').length + 1;

    const unitOpts  = units.map(u=>`<option value="${u.id}" data-ftb="${u.ftb||1}" ${u.id==default_unit_id?'selected':''}>${u.text}</option>`).join('');
    const batchOpts = batches.map(b=>`<option value="${b.batch_no}">${b.batch_no||''} ${b.expiry?(' - '+b.expiry):''}</option>`).join('');

    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.pid = product_id;
    tr.dataset.batch = default_batch_no || '';
    tr.dataset.req = request_number;

    tr.innerHTML = `
      <td class="text-muted">${idx}</td>
      <td>
        <div class="fw-semibold">${product_text}</div>
        <input type="hidden" name="items[][product_id]" value="${product_id}">
      </td>
      <td>
        <select name="items[][unit_id]" class="form-select form-select-sm unit-sel">${unitOpts}</select>
      </td>
      <td>
        <input type="number" step="0.0001" min="0" name="items[][quantity]" class="form-control form-control-sm qty" value="${Number(default_qty).toFixed(4).replace(/\.?0+$/,'')}">
        <input type="hidden" class="avb-base" value="${available_base}">
      </td>
      <td>
        <select name="items[][batch_no]" class="form-select form-select-sm batch-sel" ${batches.length?'':'disabled'}>${batchOpts}</select>
      </td>
      <td>
        <input type="text" class="form-control form-control-sm" value="${request_number}" readonly>
        <input type="hidden" name="items[][request_number]" value="${request_number}">
      </td>
      <td><input type="text" name="items[][notes]" class="form-control form-control-sm"></td>
      <td><button type="button" class="btn btn-sm btn-danger del-row">حذف</button></td>
    `;

    const body = document.getElementById('items-body');
    if (body.querySelector('tr.text-muted')) body.innerHTML='';
    body.appendChild(tr);

    $(tr).find('.unit-sel, .batch-sel').select2({ width:'100%' });

    function applyMax(){
      const ftb = Number(tr.querySelector('.unit-sel')?.selectedOptions[0]?.dataset?.ftb || 1);
      const avbBase = Number(tr.querySelector('.avb-base')?.value || 0);
      const maxUnit = ftb>0 ? (avbBase/ftb) : 0;
      const qty = tr.querySelector('.qty');
      qty.setAttribute('max', maxUnit.toFixed(4));
      if (Number(qty.value||0) > maxUnit) qty.value = maxUnit.toFixed(4).replace(/\.?0+$/,'');
    }
    applyMax();

    $(tr).find('.unit-sel').on('change', applyMax);
    $(tr).find('.batch-sel').on('change', function(){ tr.dataset.batch = this.value || ''; });

    $(tr).find('.qty').on('input', function(){
      let v = Number(this.value || 0);
      const max = Number(this.getAttribute('max') || 0);
      if (v < 0) v = 0;
      if (max && v > max) v = max;
      this.value = v.toFixed(4).replace(/\.?0+$/,'');
    });
  }

  document.getElementById('items-body')?.addEventListener('click', (e)=>{
    if (e.target.classList.contains('del-row')) {
      e.target.closest('tr').remove();
      const rows = document.querySelectorAll('#items-body tr.item-row');
      rows.forEach((tr,i)=> tr.querySelector('td').textContent = i+1);
      if (rows.length === 0) {
        unlockHeader();
        document.getElementById('items-body').innerHTML =
          '<tr class="text-muted"><td colspan="8" class="text-center">لا توجد بنود بعد… اضغط "عرض طلبات العميل" ثم اختر.</td></tr>';
      }
    }
  });

  // حفظ
  document.getElementById('btnSave')?.addEventListener('click', async ()=>{
    const formHead = new FormData(document.getElementById('head-form'));

    const clientId = $('#client_id').val();
    const storeId  = $('#store_id').val();

    const items = [];
    const setDup = new Set();
    document.querySelectorAll('#items-body tr.item-row').forEach(tr=>{
      const obj = {};
      tr.querySelectorAll('input,select').forEach(inp=>{
        const name = (inp.getAttribute('name')||'').replace(/^items\[\]\[|\]$/g,'');
        if(!name) return; obj[name] = inp.value;
      });
      const k = `${obj.request_number||''}|${obj.product_id}|${obj.batch_no||''}`;
      if (setDup.has(k)) return;
      setDup.add(k);
      items.push(obj);
    });

    if (!clientId) { toast('اختر العميل.','warning'); return; }
    if (!storeId)  { toast('اختر المخزن.','warning'); return; }
    if (items.length===0) { toast('لا يوجد بنود للحفظ.','warning'); return; }

    const payload = {
      code: formHead.get('code'),
      return_date: formHead.get('return_date'),
      client_id: clientId,
      store_id:  storeId,
      items
    };

    try {
      const res = await fetch(routes.store, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(payload)
      });
      if (!res.ok) {
        const err = await res.text();
        toast(err || 'فشل الحفظ','danger'); return;
      }
      window.location.href = "{{ route('return_from_client.index') }}";
    } catch(e) {
      toast('حدث خطأ غير متوقع','danger');
    }
  });
})();
</script>
@endsection
