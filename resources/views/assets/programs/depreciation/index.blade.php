@extends('layouts.master_table')

@section('title') {{ __('assets.depreciation.index_title') }} @stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('assets.depreciation.index_title') }}</h4>
    <a href="{{ route('assets.programs.depreciation.create') }}" class="btn btn-success">
      <i class="ri-add-line"></i> {{ __('assets.depreciation.add_run') }}
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form class="row g-2 mb-3" id="filters">
      <div class="col-md-3">
        <label class="form-label">{{ __('assets.depreciation.period_from') }}</label>
        <input type="date" name="from" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('assets.depreciation.period_to') }}</label>
        <input type="date" name="to" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">{{ __('assets.depreciation.status') }}</label>
        <select name="status" class="form-select">
          <option value="">{{ __('assets.depreciation.all') }}</option>
          <option value="draft">{{ __('assets.depreciation.s_draft') }}</option>
          <option value="posted">{{ __('assets.depreciation.s_posted') }}</option>
          <option value="void">{{ __('assets.depreciation.s_void') }}</option>
        </select>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <button type="button" id="btn-load" class="btn btn-primary w-100">
          <i class="ri-search-line"></i> {{ __('assets.search') }}
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle" id="runs-table">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('assets.depreciation.period') }}</th>
            <th>{{ __('assets.depreciation.status') }}</th>
            <th>{{ __('assets.depreciation.lines_count') }}</th>
            <th>{{ __('assets.actions') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

  </div>
</div>

<script>
const $tbody = document.querySelector('#runs-table tbody');

// ترجمة الحالة للاستخدام على جانب العميل (لو لم يرجع السيرفر status_label)
const T_STATUS = {
  draft : @json(__('assets.depreciation.status_draft')),
  posted: @json(__('assets.depreciation.status_posted')),
  void  : @json(__('assets.depreciation.status_void')),
};

// ألوان الشارات حسب الحالة
const STATUS_BADGE = {
  draft : 'badge bg-secondary',
  posted: 'badge bg-success',
  void  : 'badge bg-danger',
};

// تنسيق التاريخ: نضمن إظهاره YYYY-MM-DD حتى لو جاء بصيغة ISO
function formatYMD(val) {
  if (!val) return '';
  // لو السيرفر أرسل period_ymd أو period كـ YYYY-MM-DD بالفعل
  if (/^\d{4}-\d{2}-\d{2}$/.test(val)) return val;
  // جرّب التحويل من ISO إلى YYYY-MM-DD
  try {
    // إن كانت القيمة تحتوي "T" نقطع قبلها كحل سريع
    if (typeof val === 'string' && val.includes('T')) {
      return val.slice(0, 10);
    }
    // محاولة Date → ISO → slice
    const d = new Date(val);
    if (!isNaN(d.getTime())) return d.toISOString().slice(0,10);
  } catch(e) {}
  // آخر حل: رجّعها كما هي
  return String(val);
}

document.getElementById('btn-load').addEventListener('click', loadRuns);

function loadRuns(pageUrl = null){
  const url = pageUrl || (function(){
    const base = `{{ route('assets.programs.depreciation.index') }}`;
    const params = new URLSearchParams(new FormData(document.getElementById('filters'))).toString();
    return params ? `${base}?${params}` : base;
  })();

  fetch(url, { headers:{ 'Accept':'application/json' }})
    .then(r=>r.json())
    .then(data=>{
      $tbody.innerHTML = '';

      // لو لا توجد بيانات
      if (!data.data || data.data.length === 0) {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td colspan="5" class="text-center text-muted py-4">— {{ __('assets.no_records') ?? 'لا توجد سجلات' }} —</td>`;
        $tbody.appendChild(tr);
        return;
      }

      for (const row of data.data) {
        const period = formatYMD(row.period_ymd || row.period);
        const stKey = row.status || 'draft';
        const stLabel = row.status_label || T_STATUS[stKey] || stKey;
        const badgeClass = STATUS_BADGE[stKey] || 'badge bg-light text-dark';

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${row.id}</td>
          <td>${period}</td>
          <td><span class="${badgeClass}">${stLabel}</span></td>
          <td>${row.lines_count ?? ''}</td>
          <td class="text-nowrap">
            <a href="{{ route('assets.programs.depreciation.index') }}/${row.id}" class="btn btn-sm btn-outline-primary">
              <i class="ri-eye-line"></i> {{ __('assets.show') }}
            </a>
          </td>
        `;
        $tbody.appendChild(tr);
      }

      // (اختياري) التعامل مع ترقيم الصفحات إن أردت — يمكنك إضافة أزرار next/prev هنا باستخدام data.next_page_url, data.prev_page_url
      // مثال سريع:
      // console.log('next:', data.next_page_url, 'prev:', data.prev_page_url);
    })
    .catch(()=> {
      $tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">{{ __('assets.error') ?? 'حدث خطأ غير متوقع' }}</td></tr>`;
    });
}

// تحميل أولي
loadRuns();
</script>
@endsection
