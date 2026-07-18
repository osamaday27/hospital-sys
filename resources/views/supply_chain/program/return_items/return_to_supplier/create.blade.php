@extends('layouts.master_table')

@section('title', 'إنشاء مرتجع مورد')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

{{-- Breadcrumb --}}
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('return_to_supplier.index') }}">مرتجعات الموردين</a></li>
          <li class="breadcrumb-item active">إنشاء مرتجع</li>
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
        <input type="date" name="document_date" class="form-control" value="{{ $today }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">المخزن</label>
        <select name="store_id" id="store_id" class="form-select sel2" required></select>
      </div>
      <div class="col-md-4">
        <label class="form-label">المورد</label>
        <select name="supplier_id" id="supplier_id" class="form-select sel2" required></select>
      </div>
      <div class="col-12">
        <label class="form-label">سبب الارتجاع</label>
        <input type="text" name="reason" class="form-control" placeholder="اختياري">
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="button" id="btnShowInvoices" class="btn btn-outline-secondary">عرض فواتير المورد</button>
      </div>
    </form>
  </div>
</div>

{{-- جدول البنود --}}
<div class="card mb-4">
  <div class="card-body">
    <h6 class="mb-3">بنود المرتجع</h6>
    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle" id="items-table">
        <thead class="table-light">
          <tr>
            <th style="width:40px;">#</th>
            <th>الصنف / الكود</th>
            <th style="width:200px;">الوحدة</th>
            <th style="width:140px;">الكمية</th>
            <th style="width:140px;">سعر/وحدة</th>
            <th style="width:120px;">ضريبة/وحدة</th>
            <th style="width:150px;">شامل/وحدة</th>
            <th style="width:200px;">التشغيلة</th>
            <th style="width:160px;">فاتورة الشراء</th>
            <th>ملاحظات</th>
            <th style="width:60px;">حذف</th>
          </tr>
        </thead>
        <tbody id="items-body">
          <tr class="text-muted"><td colspan="11" class="text-center">لا توجد بنود بعد… اضغط "عرض فواتير المورد" ثم اختر الأصناف.</td></tr>
        </tbody>
        <tfoot class="table-light">
          <tr>
            <th colspan="4" class="text-end">الإجمالي قبل الضريبة:</th>
            <th><span id="ft_subtotal">0.00</span></th>
            <th class="text-end">إجمالي الضريبة:</th>
            <th><span id="ft_tax">0.00</span></th>
            <th colspan="4" class="text-end">الإجمالي شامل الضريبة: <strong><span id="ft_grand">0.00</span></strong></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="text-end">
      <button id="btnSave" class="btn btn-primary">حفظ المرتجع</button>
    </div>
  </div>
</div>

{{-- مودال فواتير المورد --}}
<div class="modal fade" id="invoicesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">فواتير المورد</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="اغلاق"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2 small text-muted">اختر فاتورة لتحميل الأصناف. **الباتشات من نفس الفاتورة فقط، والرصيد من الأرصدة الحالية بالمخزن.**</div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light"><tr>
              <th>#</th><th>رقم الفاتورة</th><th>التاريخ</th><th>النوع</th><th>الإجمالي</th><th>الحالة</th><th>إجراء</th>
            </tr></thead>
            <tbody id="inv-body"><tr><td colspan="7" class="text-center text-muted">اختر موردًا أولًا…</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- jQuery + Select2 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function(){
  const routes = {
    suppliers:     "{{ url('return_to_supplier/ajax/suppliers') }}",
    stores:        "{{ url('return_to_supplier/ajax/stores') }}",
    invoices:      "{{ url('return_to_supplier/ajax/invoices') }}",
    products:      "{{ url('return_to_supplier/ajax/products') }}",
    batches:       "{{ url('return_to_supplier/ajax/batches') }}",
    units:         "{{ url('return_to_supplier/ajax/units') }}",
    invoice_items: "{{ url('return_to_supplier/ajax/invoice_items') }}",
    store:         "{{ route('return_to_supplier.store') }}",
  };

  // كاش بالواجهة
  const unitsCache = new Map();   // product_id -> [{id,text,cost,sale,ftb}]
  const batchCache = new Map();   // "product|store|invoice" -> [{batch_no,expiry,available}]

  function initRemoteSelect2(selector, url) {
    $(selector).select2({
      width: '100%',
      minimumInputLength: 0,
      allowClear: true,
      placeholder: 'اختر…',
      ajax: { delay: 200, url, data: p => ({ q: p.term || '' }), processResults: d => d || {results:[]} }
    });
  }
  initRemoteSelect2('#supplier_id', routes.suppliers);
  initRemoteSelect2('#store_id', routes.stores);

  // زر عرض الفواتير
  document.getElementById('btnShowInvoices')?.addEventListener('click', async ()=>{
    const supplier = $('#supplier_id').val();
    if(!supplier){ toast('الرجاء اختيار المورد أولًا','warning'); return; }
    const res = await fetch(routes.invoices + '?supplier_id='+supplier);
    const data = await res.json();
    const body = document.getElementById('inv-body');
    if(!data.results?.length){ body.innerHTML = '<tr><td colspan="7" class="text-center text-muted">لا توجد فواتير.</td></tr>'; }
    else{
      body.innerHTML = data.results.map((x,idx)=>`
        <tr>
          <td>${idx+1}</td>
          <td>${x.no}</td>
          <td>${x.date ?? ''}</td>
          <td>${x.type}</td>
          <td>${Number(x.total).toFixed(2)}</td>
          <td>${x.status}</td>
          <td><button class="btn btn-sm btn-primary" data-inv="${x.id}" data-no="${x.no}">تحميل الأصناف</button></td>
        </tr>
      `).join('');
    }
    new bootstrap.Modal(document.getElementById('invoicesModal')).show();
  });

  // هل يوجد صف لنفس المنتج ونفس الفاتورة؟
  function hasRow(product_id, invoice_id){
    const rows = document.querySelectorAll('#items-body tr.item-row');
    for (const tr of rows) {
      const pid = Number(tr.querySelector('input[name="items[][product_id]"]')?.value || 0);
      const inv = Number(tr.querySelector('input[name="items[][supplier_invoice_id]"]')?.value || 0);
      if (pid === Number(product_id) && inv === Number(invoice_id)) return true;
    }
    return false;
  }

  // تحميل أصناف الفاتورة المختارة
  document.getElementById('inv-body')?.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button[data-inv]');
    if(!btn) return;

    const invoiceId = btn.getAttribute('data-inv');
    const invoiceNo = btn.getAttribute('data-no');
    const supplier  = $('#supplier_id').val();
    const store     = $('#store_id').val();
    if(!store){ toast('برجاء اختيار المخزن أولاً','warning'); return; }

    // 1) أصناف المورد بالمخزن + عناصر الفاتورة
    const [prodsRes, invItemsRes] = await Promise.all([
      fetch(`${routes.products}?supplier_id=${supplier}&store_id=${store}`),
      fetch(routes.invoice_items + '?invoice_id=' + invoiceId)
    ]);
    const prods = await prodsRes.json();
    const invItems = (await invItemsRes.json()).items || [];
    if (!invItems.length) { toast('لا توجد عناصر منتجات في هذه الفاتورة.', 'warning'); return; }

    const invByProduct = {};
    invItems.forEach(it => { if(it.product_id) invByProduct[it.product_id] = it; });

    const body = document.getElementById('items-body');
    if(body.querySelector('tr.text-muted')) body.innerHTML = '';

    let skipped = 0, added = 0;

    for (const p of (prods.results||[])) {
      const productId = parseInt(p.id,10);
      const invIt = invByProduct[productId];
      if(!invIt) continue;

      // ✅ منع التكرار (نفس الصنف + نفس الفاتورة)
      if (hasRow(productId, invoiceId)) { skipped++; continue; }

      const [units, batches] = await Promise.all([
        getUnits(productId),
        getBatches(productId, store, invoiceId)
      ]);
      const unit_id    = invIt.product_unit_id || (units[0]?.id || 0);
      const unit_price = Number(invIt.unit_price ?? 0);
      const unit_tax   = Number((invIt.tax ?? 0) / Math.max(1, invIt.qty ?? 1));

      addRow({
        product_id: productId,
        product_text: p.text,
        supplier_invoice_id: invoiceId,
        supplier_invoice_no: invoiceNo,
        unit_id, unit_price, unit_tax,
        units, batches
      });
      added++;
    }

    bootstrap.Modal.getInstance(document.getElementById('invoicesModal'))?.hide();
    if (added) toast(`تم تحميل ${added} صنفًا من الفاتورة.`, 'success');
    if (skipped) toast(`تم تجاهل ${skipped} صف لتجنّب التكرار (نفس الصنف من نفس الفاتورة).`, 'warning');
  });

  async function getUnits(product_id){
    if (unitsCache.has(product_id)) return unitsCache.get(product_id);
    const res = await fetch(routes.units + '?product_id=' + product_id);
    const data = await res.json();
    const arr = data.results || [];
    unitsCache.set(product_id, arr);
    return arr;
  }
  async function getBatches(product_id, store_id, invoice_id){
    const key = `${product_id}|${store_id}|${invoice_id}`;
    if (batchCache.has(key)) return batchCache.get(key);
    const url = `${routes.batches}?product_id=${product_id}&store_id=${store_id}&supplier_invoice_id=${invoice_id}`;
    const res = await fetch(url);
    const data = await res.json();
    const arr = data.results || [];
    batchCache.set(key, arr);
    return arr;
  }

  function addRow({product_id, product_text, supplier_invoice_id=null, supplier_invoice_no='', unit_price=0, unit_tax=0, unit_id=0, units=[], batches=[]}) {
    const idx = document.querySelectorAll('#items-body tr.item-row').length + 1;
    const hasBatches = batches.length > 0;
    const b0 = hasBatches ? batches[0] : null;

    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.productId   = product_id;
    tr.dataset.invUnitId   = unit_id;
    tr.dataset.invUnitPrice= unit_price;
    tr.dataset.invUnitTax  = unit_tax;
    tr.dataset.unitsJson   = JSON.stringify(units);
    tr.dataset.availableBase = b0 ? Number(b0.available) : 0;

    const unitOpts = units.map(u=>`<option value="${u.id}" data-ftb="${u.ftb ?? 1}" data-cost="${u.cost ?? ''}" data-sale="${u.sale ?? ''}" ${u.id==unit_id?'selected':''}>${u.text}</option>`).join('');
    const batchOpts = batches.map(b=>`<option value="${b.batch_no}" data-exp="${b.expiry||''}" data-avb="${Number(b.available)}">${b.batch_no ?? ''} ${b.expiry?(' - '+b.expiry):''} — متاح:${Number(b.available).toFixed(4)}</option>`).join('')

    tr.innerHTML = `
      <td class="text-muted">${idx}</td>
      <td>
        <div class="fw-semibold">${product_text}</div>
        <input type="hidden" name="items[][product_id]" value="${product_id}">
      </td>
      <td>
        <select name="items[][units_id]" class="form-select form-select-sm unit-sel">
          ${unitOpts}
        </select>
      </td>
      <td>
        <input type="number" step="0.0001" min="0" name="items[][quantity]" class="form-control form-control-sm qty" value="0">
      </td>
      <td><input type="number" step="0.0001" min="0" name="items[][unit_price]" class="form-control form-control-sm price" value="${unit_price.toFixed(4)}" readonly></td>
      <td><input type="number" step="0.0001" min="0" name="items[][tax_value]" class="form-control form-control-sm tax" value="${unit_tax.toFixed(4)}" readonly></td>
      <td><input type="number" step="0.0001" min="0" name="items[][unit_price_with_tax]" class="form-control form-control-sm price_w" value="${(unit_price+unit_tax).toFixed(4)}" readonly></td>
      <td>
        <select name="items[][batch_no]" class="form-select form-select-sm batch-sel" ${hasBatches?'':'disabled'}>
          ${batchOpts}
        </select>
        <input type="hidden" name="items[][expiry_date]" class="expiry_inp" value="${b0?.expiry || ''}">
      </td>
      <td>
        <input type="text" class="form-control form-control-sm" value="${supplier_invoice_no}" readonly>
        <input type="hidden" name="items[][supplier_invoice_id]" value="${supplier_invoice_id ?? ''}">
      </td>
      <td><input type="text" name="items[][notes]" class="form-control form-control-sm"></td>
      <td><button type="button" class="btn btn-sm btn-danger del-row">حذف</button></td>
    `;
    document.getElementById('items-body').appendChild(tr);

    $(tr).find('.unit-sel').select2({ width:'100%', minimumResultsForSearch: 0 });
    $(tr).find('.batch-sel').select2({ width:'100%', minimumResultsForSearch: 0 });

    // تغيير الباتش -> حدّث المتاح واضبط الكمية
    $(tr).find('.batch-sel').on('change', function(){
      const opt = this.options[this.selectedIndex];
      const avbBase = Number(opt?.dataset?.avb || 0);
      const exp     = opt?.dataset?.exp || '';
      tr.dataset.availableBase = avbBase;
      tr.querySelector('.expiry_inp').value = exp;
      syncQtyWithAvailability(tr);
    });

    // تغيير الوحدة -> تسعير + حدود الكمية
    $(tr).find('.unit-sel').on('change', function(){
      recalcUnitPriceOnUnitChange(tr, this.value);
      syncQtyWithAvailability(tr);
      recalcFooter();
    });

    // ضبط الكمية مع الحدود
    $(tr).find('.qty').on('input', function(){
      const max = Number(this.getAttribute('max') || 0);
      let v = Number(this.value || 0);
      if (v < 0) v = 0;
      if (max && v > max) v = max;
      this.value = v.toFixed(4).replace(/\.?0+$/,'');
      recalcFooter();
    });

    // بداية
    syncQtyWithAvailability(tr);
    recalcFooter();
  }

  function recalcUnitPriceOnUnitChange(tr, newUnitId){
    const invUnitId  = Number(tr.dataset.invUnitId || 0);
    const invP       = Number(tr.dataset.invUnitPrice || 0);
    const invT       = Number(tr.dataset.invUnitTax || 0);

    const units = JSON.parse(tr.dataset.unitsJson || '[]');
    const findU = (id)=> units.find(u=> Number(u.id) === Number(id));
    const invU  = findU(invUnitId);
    const newU  = findU(newUnitId);

    const priceInp = tr.querySelector('.price');
    const taxInp   = tr.querySelector('.tax');
    const pwInp    = tr.querySelector('.price_w');

    if (invU && newU && invP >= 0) {
      const ftbInv = Number(invU.ftb || 1);
      const ftbNew = Number(newU.ftb || 1);
      const ratio  = ftbNew / ftbInv;

      const p = invP * ratio;
      const t = invT * ratio;
      priceInp.value = p.toFixed(4);
      taxInp.value   = t.toFixed(4);
      pwInp.value    = (p + t).toFixed(4);
      return;
    }

    // Fallback
    const p2 = Number(newU?.cost ?? 0);
    const s2 = Number(newU?.sale ?? 0);
    const t2 = (s2>0 && p2>=0 && s2>=p2) ? (s2 - p2) : 0;
    priceInp.value = p2.toFixed(4);
    taxInp.value   = t2.toFixed(4);
    pwInp.value    = (p2 + t2).toFixed(4);
  }

  function syncQtyWithAvailability(tr){
    const avbBase = Number(tr.dataset.availableBase || 0);
    const unitSel = tr.querySelector('.unit-sel');
    const opt = unitSel.options[unitSel.selectedIndex];
    const ftb = Number(opt?.dataset?.ftb || 1);

    const avbUnit = avbBase / ftb;
    const qtyInp = tr.querySelector('.qty');
    qtyInp.setAttribute('max', avbUnit > 0 ? avbUnit.toFixed(4) : 0);
    qtyInp.value = avbUnit > 0 ? avbUnit.toFixed(4).replace(/\.?0+$/,'') : 0;
  }

  // حذف سطر (عادي)
  document.getElementById('items-body')?.addEventListener('click', (e)=>{
    if(e.target.classList.contains('del-row')){
      e.target.closest('tr').remove();
      recalcBodyIndex();
      recalcFooter();
    }
  });

  function recalcBodyIndex(){
    document.querySelectorAll('#items-body tr.item-row').forEach((tr,i)=>{
      tr.querySelector('td').textContent = (i+1);
    });
  }

  function recalcFooter(){
    let subtotal=0, tax=0, grand=0;
    document.querySelectorAll('#items-body tr.item-row').forEach(tr=>{
      const q   = Number(tr.querySelector('.qty')?.value || 0);
      const pu  = Number(tr.querySelector('.price')?.value || 0);
      const t   = Number(tr.querySelector('.tax')?.value || 0);
      const pw  = Number(tr.querySelector('.price_w')?.value || 0);
      subtotal += q*pu;
      tax      += q*t;
      grand    += q*pw;
    });
    document.getElementById('ft_subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('ft_tax').textContent      = tax.toFixed(2);
    document.getElementById('ft_grand').textContent    = grand.toFixed(2);
  }

  // حفظ
  document.getElementById('btnSave')?.addEventListener('click', async ()=>{
    const formHead = new FormData(document.getElementById('head-form'));
    const items = [];
    document.querySelectorAll('#items-body tr.item-row').forEach(tr=>{
      const obj = {};
      tr.querySelectorAll('input,select').forEach(inp=>{
        const name = (inp.getAttribute('name')||'').replace(/^items\[\]\[|\]$/g,'');
        if(!name) return;
        obj[name] = inp.value;
      });
      items.push(obj);
    });
    if(items.length===0){ toast('لا يوجد بنود للحفظ.','warning'); return; }

    // ✅ تحقق واجهة إضافي لمنع التكرار قبل الإرسال
    const checkSet = new Set();
    for (const it of items) {
      const k = `${it.product_id}|${it.supplier_invoice_id}`;
      if (checkSet.has(k)) { toast('ممنوع تكرار نفس الصنف من نفس الفاتورة داخل نفس المرتجع.', 'warning'); return; }
      checkSet.add(k);
    }

    const payload = {
      code: formHead.get('code'),
      document_date: formHead.get('document_date'),
      store_id: formHead.get('store_id'),
      supplier_id: formHead.get('supplier_id'),
      reason: formHead.get('reason'),
      items: items
    };

    try {
      const res = await fetch(routes.store, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(payload)
      });
      if(!res.ok){
        const err = await res.text();
        toast(err || 'فشل الحفظ','danger');
        return;
      }
      window.location.href = "{{ route('return_to_supplier.index') }}";
    } catch (e) {
      toast('حدث خطأ غير متوقع','danger');
    }
  });

  function toast(msg, type='info'){
    const id = 't'+Date.now();
    const html = `<div class="toast align-items-center text-bg-${type} border-0" id="${id}" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>`;
    const c = document.getElementById('toast-container');
    c.insertAdjacentHTML('beforeend', html);
    const t = new bootstrap.Toast(document.getElementById(id), {delay:4000});
    t.show();
  }
})();
</script>
@endsection
