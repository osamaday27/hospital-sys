@extends('layouts.master_table')

@section('title','إنشاء ارتجاع مباشر بين المخازن')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

{{-- Breadcrumb --}}
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('return_direct_to_store.index') }}">الارتجاع المباشر</a></li>
          <li class="breadcrumb-item active">إنشاء</li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- الشريط العلوي --}}
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
        <input type="date" name="transfer_date" class="form-control" value="{{ $today }}" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">المخزن المرتجع منه</label>
        <select name="store_from_id" id="store_from_id" class="form-select sel2" required></select>
      </div>

      <div class="col-md-4">
        <label class="form-label">المخزن المرتجع إليه</label>
        <select name="store_to_id" id="store_to_id" class="form-select sel2" required disabled></select>
      </div>

      <div class="col-12 d-flex gap-2">
        <button type="button" id="btnShowTransfers" class="btn btn-outline-secondary">
          عرض التحويلات من مخزن المصدر
        </button>
      </div>
    </form>
  </div>
</div>

{{-- جدول البنود --}}
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
            <th style="width:180px;">كود التحويل</th>
            <th>ملاحظات</th>
            <th style="width:60px;">حذف</th>
          </tr>
        </thead>
        <tbody id="items-body">
          <tr class="text-muted"><td colspan="8" class="text-center">لا توجد بنود بعد… اضغط "عرض التحويلات" ثم اختر.</td></tr>
        </tbody>
      </table>
    </div>

    <div class="text-end">
      <button id="btnSave" class="btn btn-primary">حفظ المستند</button>
    </div>
  </div>
</div>

{{-- مودال التحويلات --}}
<div class="modal fade" id="transfersModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">تحويلات من مخزن المصدر</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="اغلاق"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2 small text-muted">
          تظهر فقط التحويلات التي ما زال لها رصيد فعلي في مخزن المصدر (حسب الباتش).
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr>
              <th>#</th>
              <th>رقم التحويل</th>
              <th>التاريخ</th>
              <th>الصنف</th>
              <th>التشغيلة</th>
              <th>المتاح (أصغر)</th>
              <th>إجراء</th>
            </tr></thead>
            <tbody id="tr-body">
              <tr><td colspan="7" class="text-center text-muted">اختر مخزن المصدر أولاً…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const routes = {
    // ✅ مصادر المخازن مبنية على store_transfers فقط
    storeFroms : "{{ url('return_direct_to_store/ajax/store-froms') }}",
    storeTos   : "{{ url('return_direct_to_store/ajax/store-tos') }}",

    // باقي الـ AJAX Endpoints
    units      : "{{ url('return_direct_to_store/ajax/units') }}",
    transfers  : "{{ url('return_direct_to_store/ajax/transfers') }}",
    batches    : "{{ url('return_direct_to_store/ajax/batches') }}",
    store      : "{{ route('return_direct_to_store.store') }}",
  };

  function toast(msg, type='info'){
    const id='t'+Date.now();
    const html = `<div class="toast align-items-center text-bg-${type} border-0" id="${id}">
      <div class="d-flex"><div class="toast-body">${msg}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
    const c=document.getElementById('toast-container'); c.insertAdjacentHTML('beforeend',html);
    new bootstrap.Toast(document.getElementById(id),{delay:4000}).show();
  }

  // Select2: المخزن المرتجع منه (من store_transfers: store_to_id)
  $('#store_from_id').select2({
    width:'100%', allowClear:true, placeholder:'اختر…',
    ajax:{ delay:200, url: routes.storeFroms, data:p=>({q:p.term||''}), processResults:d=>d||{results:[]} }
  });

  // Select2: المخزن المرتجع إليه (Disabled حتى اختيار الأول) — شركاء المخزن الأول فقط
  const $to = $('#store_to_id');
  $to.prop('disabled', true).select2({
    width:'100%', allowClear:true, placeholder:'اختر…',
    ajax:{
      delay:200,
      url: routes.storeTos,
      data: p => ({ q: p.term || '', from_store_id: $('#store_from_id').val() || 0 }),
      processResults: d => d || {results:[]}
    }
  });

  // عند اختيار المخزن المرتجع منه → فعّل المرتجع إليه ونظّف قيمته
  $('#store_from_id').on('change', function(){
    $to.val(null).trigger('change');
    $to.prop('disabled', !$(this).val());
  });

  // منع اختيار نفس المخزن
  $('#store_from_id, #store_to_id').on('change', function(){
    const a = $('#store_from_id').val(), b = $('#store_to_id').val();
    if (a && b && a === b) {
      toast('غير مسموح أن يكون مخزن المصدر والوجهة متطابقين.','warning');
      $(this).val(null).trigger('change');
    }
  });

  // كاش واجهة
  const unitsCache  = new Map(); // product_id -> units[]
  const batchCache  = new Map(); // key: product|store|transfer_no -> batches[]

  async function getUnits(product_id){
    if (unitsCache.has(product_id)) return unitsCache.get(product_id);
    const res = await fetch(routes.units+'?product_id='+product_id);
    const arr = (await res.json()).results || [];
    unitsCache.set(product_id, arr);
    return arr;
  }
  async function getBatches(product_id, store_id, transfer_no){
    const key = `${product_id}|${store_id}|${transfer_no||''}`;
    if (batchCache.has(key)) return batchCache.get(key);
    const url = `${routes.batches}?product_id=${product_id}&store_id=${store_id}&transfer_number=${encodeURIComponent(transfer_no||'')}`;
    const arr = (await (await fetch(url)).json()).results || [];
    batchCache.set(key, arr);
    return arr;
  }

  // زر عرض التحويلات
  document.getElementById('btnShowTransfers')?.addEventListener('click', async ()=>{
    const store_from = $('#store_from_id').val();
    const store_to   = $('#store_to_id').val(); // اختياري للتضييق
    if (!store_from) { toast('اختر مخزن المصدر أولاً.','warning'); return; }

    const url = routes.transfers + `?store_from_id=${store_from}` + (store_to ? `&store_to_id=${store_to}` : '');
    const res = await fetch(url);
    const data = await res.json();

    const body = document.getElementById('tr-body');
    if (!data.results?.length) {
      body.innerHTML = '<tr><td colspan="7" class="text-center text-muted">لا توجد تحويلات متاحة لها رصيد.</td></tr>';
    } else {
      body.innerHTML = data.results.map((x,i)=>`
        <tr>
          <td>${i+1}</td>
          <td>${x.transfer_number || ''}</td>
          <td>${x.transfer_date || ''}</td>
          <td>${x.product_text}</td>
          <td>${x.batch_no || ''}</td>
          <td>${Number(x.available_base).toFixed(4)}</td>
          <td><button class="btn btn-sm btn-primary" data-t='${JSON.stringify(x).replace(/'/g,"&#39;")}'>إضافة</button></td>
        </tr>
      `).join('');
    }
    new bootstrap.Modal(document.getElementById('transfersModal')).show();
  });

  // عدم تكرار (product + batch + transfer_no)
  function hasRow(pid,batch,tn){
    let ok=false;
    document.querySelectorAll('#items-body tr.item-row').forEach(tr=>{
      const a = Number(tr.dataset.pid||0);
      const b = (tr.dataset.batch||'');
      const c = (tr.dataset.tn||'');
      if (a===Number(pid) && b===String(batch||'') && c===String(tn||'')) ok=true;
    });
    return ok;
  }

  // إضافة صف من التحويلات
  document.getElementById('tr-body')?.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button[data-t]');
    if (!btn) return;
    const obj = JSON.parse(btn.getAttribute('data-t').replace(/&#39;/g,"'"));
    const store_from = $('#store_from_id').val();
    const store_to   = $('#store_to_id').val();

    if (!store_to) { toast('اختر مخزن الوجهة أولاً.','warning'); return; }
    if (store_from === store_to) { toast('المخزنان متطابقان! اختر وجهة مختلفة.','warning'); return; }

    if (hasRow(obj.product_id, obj.batch_no, obj.transfer_number)) {
      toast('تم تجاهل الصف لتجنّب التكرار (نفس الصنف/الباتش/كود التحويل).','warning'); return;
    }

    const [units, batches] = await Promise.all([
      getUnits(obj.product_id),
      getBatches(obj.product_id, store_from, obj.transfer_number)
    ]);

    if (!batches.length) { toast('لا يوجد رصيد فعلي لهذه الباتشات حالياً.','warning'); return; }

    addRow({
      product_id: obj.product_id,
      product_text: obj.product_text,
      transfer_number: obj.transfer_number,
      units, batches
    });
    bootstrap.Modal.getInstance(document.getElementById('transfersModal'))?.hide();
    toast('تمت إضافة السطر.','success');
  });

  function addRow({product_id, product_text, transfer_number='', units=[], batches=[]}){
    const idx = document.querySelectorAll('#items-body tr.item-row').length + 1;
    const b0  = batches[0];
    const defaultFtB = Number((units[0]?.ftb)||1);
    const avbBase = Number(b0?.available||0);
    const avbUnit = (defaultFtB>0)? (avbBase/defaultFtB) : 0;

    const tr = document.createElement('tr');
    tr.className='item-row';
    tr.dataset.pid   = product_id;
    tr.dataset.batch = b0?.batch_no || '';
    tr.dataset.tn    = transfer_number;

    const unitOpts = units.map(u=>`<option value="${u.id}" data-ftb="${u.ftb||1}">${u.text}</option>`).join('');
    const batchOpts = batches.map(b=>`<option value="${b.batch_no}" data-avb="${Number(b.available)}" data-exp="${b.expiry||''}">${b.batch_no||''} ${b.expiry?(' - '+b.expiry):''} — متاح:${Number(b.available).toFixed(4)}</option>`).join('');

    tr.innerHTML = `
      <td class="text-muted">${idx}</td>
      <td>
        <div class="fw-semibold">${product_text}</div>
        <input type="hidden" name="items[][product_id]" value="${product_id}">
      </td>
      <td>
        <select name="items[][units_id]" class="form-select form-select-sm unit-sel">${unitOpts}</select>
      </td>
      <td>
        <input type="number" step="0.0001" min="0" name="items[][quantity]" class="form-control form-control-sm qty" value="${avbUnit.toFixed(4).replace(/\.?0+$/,'')}">
      </td>
      <td>
        <select name="items[][batch_no]" class="form-select form-select-sm batch-sel">${batchOpts}</select>
      </td>
      <td>
        <input type="text" class="form-control form-control-sm" value="${transfer_number}" readonly>
        <input type="hidden" name="items[][transfer_number]" value="${transfer_number}">
      </td>
      <td><input type="text" name="items[][notes]" class="form-control form-control-sm" ></td>
      <td><button type="button" class="btn btn-sm btn-danger del-row">حذف</button></td>
    `;
    const body = document.getElementById('items-body');
    if (body.querySelector('tr.text-muted')) body.innerHTML='';
    body.appendChild(tr);

    $(tr).find('.unit-sel, .batch-sel').select2({width:'100%'});

    // عند تغيير الباتش: حدّث المتاح واضبط الحد الأقصى والكمية
    $(tr).find('.batch-sel').on('change', function(){
      const opt = this.options[this.selectedIndex];
      tr.dataset.batch = opt?.value || '';
      const avbBase = Number(opt?.dataset?.avb || 0);
      const ftb = Number(tr.querySelector('.unit-sel')?.selectedOptions[0]?.dataset?.ftb || 1);
      const maxUnit = (ftb>0)? (avbBase/ftb) : 0;
      const qty = tr.querySelector('.qty');
      qty.setAttribute('max', maxUnit.toFixed(4));
      if (Number(qty.value||0) > maxUnit) qty.value = maxUnit.toFixed(4).replace(/\.?0+$/,'');
    });

    // عند تغيير الوحدة: أعِد احتساب الحد الأقصى/القيمة الافتراضية
    $(tr).find('.unit-sel').on('change', function(){
      const ftb = Number(this.selectedOptions[0]?.dataset?.ftb || 1);
      const opt = tr.querySelector('.batch-sel')?.selectedOptions[0];
      const avbBase = Number(opt?.dataset?.avb || 0);
      const maxUnit = (ftb>0)? (avbBase/ftb) : 0;
      const qty = tr.querySelector('.qty');
      qty.setAttribute('max', maxUnit.toFixed(4));
      if (Number(qty.value||0) > maxUnit) qty.value = maxUnit.toFixed(4).replace(/\.?0+$/,'');
    });

    // ضبط الحد أثناء الكتابة
    $(tr).find('.qty').on('input', function(){
      let v = Number(this.value || 0);
      const max = Number(this.getAttribute('max') || 0);
      if (v < 0) v = 0;
      if (max && v > max) v = max;
      this.value = v.toFixed(4).replace(/\.?0+$/,'');
    });
  }

  // حذف صف
  document.getElementById('items-body')?.addEventListener('click', (e)=>{
    if (e.target.classList.contains('del-row')) {
      e.target.closest('tr').remove();
      // ترتيب الترقيم
      document.querySelectorAll('#items-body tr.item-row').forEach((tr,i)=> tr.querySelector('td').textContent = i+1);
    }
  });

  // حفظ
document.getElementById('btnSave')?.addEventListener('click', async ()=>{
  const formHead = new FormData(document.getElementById('head-form'));
  const a = formHead.get('store_from_id'), b = formHead.get('store_to_id');
  if (a && b && a === b) { toast('المخزنان متطابقان!','warning'); return; }

  const items = [];
  const setDup = new Set();
  document.querySelectorAll('#items-body tr.item-row').forEach(tr=>{
    const obj = {};
    tr.querySelectorAll('input,select').forEach(inp=>{
      const name = (inp.getAttribute('name')||'').replace(/^items\[\]\[|\]$/g,'');
      if(!name) return; obj[name] = inp.value;
    });
    const k = `${obj.product_id}|${obj.batch_no||''}|${obj.transfer_number||''}`;
    if (setDup.has(k)) return; // تجاهل التكرار احترازيًا
    setDup.add(k);
    items.push(obj);
  });

  if (items.length===0) { toast('لا يوجد بنود للحفظ.','warning'); return; }

  const payload = {
    code: formHead.get('code'),
    transfer_date: formHead.get('transfer_date'),
    store_from_id: formHead.get('store_from_id'),
    store_to_id: formHead.get('store_to_id'),
    items
  };

  try {
    const res = await fetch(routes.store, {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'Accept':'application/json',          // ✅ مهم جدًا
        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify(payload)
    });

    // حاول قراءة JSON أولًا، ولو فشل اعرض النص
    let data = null, text = '';
    try { data = await res.json(); } catch { text = await res.text(); }

    if (!res.ok) {
      // لو رسائل Validate 422
      if (res.status === 422 && data && data.errors) {
        const msgs = Object.values(data.errors).flat().join('<br>');
        toast(msgs || 'خطأ في التحقق من البيانات', 'danger');
      } else {
        toast((data && (data.message||data.error)) || text || 'فشل الحفظ', 'danger');
      }
      return;
    }

    // نجاح JSON
    if (data && data.ok) {
      toast('تم الحفظ بنجاح','success');
      window.location.href = data.redirect || "{{ route('return_direct_to_store.index') }}";
      return;
    }

    // نجاح عادي (احتياطي)
    window.location.href = "{{ route('return_direct_to_store.index') }}";
  } catch (e) {
    toast('حدث خطأ غير متوقع','danger');
  }
});

})();
</script>
@endsection
