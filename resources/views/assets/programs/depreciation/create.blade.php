@extends('layouts.master_table')

@section('title') {{ __('assets.depreciation.create_title') }} @stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('assets.depreciation.create_title') }}</h4>
    <a href="{{ route('assets.programs.depreciation.index') }}" class="btn btn-light">
      <i class="ri-arrow-go-back-line"></i> {{ __('assets.back') }}
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form class="row g-3" id="preview-form" onsubmit="return false;">
      <div class="col-md-3">
        <label class="form-label">{{ __('assets.depreciation.period') }}</label>
        <input type="month" name="period" value="{{ \Carbon\Carbon::parse($defaultPeriod)->format('Y-m') }}" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">{{ __('assets.notes') }}</label>
        <input type="text" name="notes" class="form-control" placeholder="{{ __('assets.depreciation.notes_hint') }}">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button id="btn-preview" class="btn btn-primary w-100"><i class="ri-eye-line"></i> {{ __('assets.depreciation.preview') }}</button>
      </div>
    </form>
  </div>
</div>

<div class="card mt-3 d-none" id="card-preview">
  <div class="card-body">
    <div id="alert-posted" class="alert alert-warning d-none" role="alert"></div>

    <div class="d-flex justify-content-between mb-2">
      <div>
        <strong>{{ __('assets.depreciation.period') }}:</strong>
        <span id="pv-period"></span>
      </div>
      <div>
        <strong>{{ __('assets.depreciation.total_amount') }}:</strong>
        <span id="pv-total" class="text-success"></span>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle" id="pv-table">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('assets.depreciation.asset') }}</th>
            <th>{{ __('assets.depreciation.method') }}</th>
            <th>{{ __('assets.depreciation.life') }}</th>
            <th>{{ __('assets.depreciation.rate') }}</th>
            <th>{{ __('assets.depreciation.residual') }}</th>
            <th>{{ __('assets.depreciation.cost') }}</th>
            <th>{{ __('assets.depreciation.qty') }}</th>
            <th>{{ __('assets.depreciation.uop_est') }}</th>
            <th>{{ __('assets.depreciation.uop_used_to_date') }}</th>
            <th>{{ __('assets.depreciation.uop_remaining') }}</th>
            <th>{{ __('assets.depreciation.uop_units') }}</th>
            <th>{{ __('assets.depreciation.amount') }}</th>
            <th>{{ __('assets.depreciation.accum_before') }}</th>
            <th>{{ __('assets.depreciation.accum_after') }}</th>
            <th>{{ __('assets.depreciation.nbv_before') }}</th>
            <th>{{ __('assets.depreciation.nbv_after') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    <div class="d-flex gap-2 mt-3">
      <button id="btn-save" class="btn btn-success"><i class="ri-save-3-line"></i> {{ __('assets.depreciation.save_draft') }}</button>
    </div>
  </div>
</div>

<script>
const $card = document.getElementById('card-preview');
const $tbody = document.querySelector('#pv-table tbody');
const $pvPeriod = document.getElementById('pv-period');
const $pvTotal  = document.getElementById('pv-total');
const $alertPosted = document.getElementById('alert-posted');
let lastPreview = null;

// ========= Helpers =========
function toFixed2(n){ return (Math.round((Number(n||0)+Number.EPSILON)*100)/100).toFixed(2); }

function recomputeUopRow(row){
  if (row.dep_method !== 'UOP') return row;
  const est       = Number(row.uop_estimated_total || 0);
  const remaining = Number(row.uop_remaining || 0);
  const cost      = Number(row.cost_basis || 0);
  const resid     = Number(row.residual_value || 0);
  const accumBefore = Number(row.accum_before || 0);
  const nbvBefore   = Number(row.nbv_before || (cost - accumBefore));
  const base = Math.max(cost - resid, 0);

  // قيّد الوحدات بالمتبقي
  let units = Number(row.uop_units_period || 0);
  if (units < 0) units = 0;
  if (remaining > 0 && units > remaining) units = remaining;
  row.uop_units_period = units;

  let amount = 0;
  if (units > 0 && est > 0 && base > 0) {
    amount = (base * units) / est;
    // لا نتجاوز NBV المتاح فوق المتبقي
    const maxAllowed = Math.max(nbvBefore - resid, 0);
    amount = Math.max(Math.min(amount, maxAllowed), 0);
  }
  row.amount      = Number(toFixed2(amount));
  row.accum_after = Number(toFixed2(accumBefore + row.amount));
  row.nbv_after   = Number(toFixed2(cost - row.accum_after));
  return row;
}

function refreshTotals(){
  if (!lastPreview) return;
  const total = lastPreview.rows.reduce((s,r)=>s + Number(r.amount||0), 0);
  $pvTotal.textContent = toFixed2(total);
}

function updateRowUI(assetId){
  const tr = $tbody.querySelector(`tr[data-asset="${assetId}"]`);
  if (!tr) return;
  const row = lastPreview.rows.find(r => String(r.asset_id) === String(assetId));
  if (!row) return;

  // لو تم ضبط الوحدات داخلياً (بسبب الحد الأقصى)، حدث حقل الإدخال
  const uopInp = tr.querySelector('.js-uop');
  if (uopInp && String(row.dep_method)==='UOP') {
    const max = Number(row.uop_remaining || 0);
    if (max > 0) uopInp.setAttribute('max', String(max));
    uopInp.value = row.uop_units_period || 0;
  }

  tr.querySelector('[data-col="amount"]').textContent      = toFixed2(row.amount);
  tr.querySelector('[data-col="accum_after"]').textContent = toFixed2(row.accum_after);
  tr.querySelector('[data-col="nbv_after"]').textContent   = toFixed2(row.nbv_after);
  refreshTotals();
}

function attachUopListeners(){
  $tbody.querySelectorAll('.js-uop').forEach(inp=>{
    inp.addEventListener('input', e=>{
      const aid = e.target.dataset.asset;
      const row = lastPreview.rows.find(x=> String(x.asset_id)===String(aid));
      if (!row) return;
      row.uop_units_period = parseInt(e.target.value || '0', 10);

      // إعادة الحساب مع التقيد بالمتبقي
      recomputeUopRow(row);
      updateRowUI(aid);
    });
  });
}

// ========= Events =========
document.getElementById('btn-preview').addEventListener('click', async ()=>{
  const form = document.getElementById('preview-form');
  // month -> YYYY-MM-01
  const monthVal = form.period.value;
  const period = monthVal ? monthVal + '-01' : '';
  const fd = new FormData(form);
  fd.set('period', period);

  // 🔽 أرسل وحدات UOP المُدخلة مسبقاً (إن وُجدت) لإعادة الحساب سيرفرياً
  document.querySelectorAll('.js-uop').forEach(inp=>{
    fd.append(`uop_units[${inp.dataset.asset}]`, inp.value || 0);
  });

  const res = await fetch(`{{ route('assets.programs.depreciation.preview') }}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json'},
    body: fd
  });
  const data = await res.json();
  lastPreview = data;
  $pvPeriod.textContent = data.period;
  $pvTotal.textContent  = toFixed2(data.total);

  // تنبيه إن كانت الفترة فيها تشغيل مُرحّل
  if (data.has_posted) {
    $alertPosted.classList.remove('d-none');
    $alertPosted.innerHTML = `<strong>{{ __('assets.warning') }}:</strong>
      {{ __('assets.depreciation.warn_posted_for_period') }}
      <span class="badge bg-warning text-dark">${data.posted_count}</span>`;
  } else {
    $alertPosted.classList.add('d-none');
    $alertPosted.innerHTML = '';
  }

  $tbody.innerHTML = '';
  data.rows.forEach((r,idx)=>{
    const tr = document.createElement('tr');
    tr.setAttribute('data-asset', r.asset_id);

    const uopInputHtml = (r.dep_method==='UOP')
      ? `<input type="number" class="form-control form-control-sm js-uop" data-asset="${r.asset_id}"
                 value="${r.uop_units_period ?? 0}" min="0" ${r.uop_remaining ? `max="${r.uop_remaining}"` : ''}>`
      : '';

    tr.innerHTML = `
      <td>${idx+1}</td>
      <td>[${r.asset_id}] ${r.asset_code ?? ''} - ${r.asset_name ?? ''}</td>
      <td>${r.dep_method ?? ''}</td>
      <td>${r.useful_life_months ?? ''}</td>
      <td>${r.dep_rate ?? ''}</td>
      <td>${toFixed2(r.residual_value)}</td>
      <td>${toFixed2(r.cost_basis)}</td>
      <td>${r.qty}</td>
      <td>${r.uop_estimated_total ?? ''}</td>
      <td>${r.uop_used_to_date ?? ''}</td>
      <td>${r.uop_remaining ?? ''}</td>
      <td>${uopInputHtml}</td>
      <td class="text-end" data-col="amount">${toFixed2(r.amount)}</td>
      <td class="text-end">${toFixed2(r.accum_before)}</td>
      <td class="text-end" data-col="accum_after">${toFixed2(r.accum_after)}</td>
      <td class="text-end">${toFixed2(r.nbv_before)}</td>
      <td class="text-end" data-col="nbv_after">${toFixed2(r.nbv_after)}</td>
    `;
    $tbody.appendChild(tr);
  });

  // اربط المستمعات وقم بتحديث الإجمالي
  attachUopListeners();
  refreshTotals();

  $card.classList.remove('d-none');
});

// حفظ Draft
document.getElementById('btn-save').addEventListener('click', async ()=>{
  if (!lastPreview) return;

  // التقط وحدات UOP من الجدول + اعادة حساب كلينت-سايد (مع التقيد بالمتبقي)
  document.querySelectorAll('.js-uop').forEach(inp=>{
    const aid = inp.dataset.asset;
    const row = lastPreview.rows.find(x=> String(x.asset_id)===String(aid));
    if (row) {
      row.uop_units_period = parseInt(inp.value||'0',10);
      recomputeUopRow(row);
    }
  });
  refreshTotals();

  const payload = new FormData();
  payload.set('period', lastPreview.period);
  payload.set('notes', document.querySelector('[name="notes"]').value || '');

  // lines[index][key]
  lastPreview.rows.forEach((ln, i)=>{
    for (const k in ln) {
      payload.append(`lines[${i}][${k}]`, ln[k] ?? '');
    }
  });

  const res = await fetch(`{{ route('assets.programs.depreciation.store') }}`, {
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
    body: payload
  });
  if (res.redirected) {
    window.location = res.url;
  } else {
    location.reload();
  }
});
</script>
@endsection
