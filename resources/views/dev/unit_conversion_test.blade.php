@extends('layouts.master_table')

@section('title', 'اختبار تحويل الوحدات')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">اختبار خدمة تحويل الوحدات</h5>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="POST" action="{{ route('dev.unitconv.calc') }}" id="conv-form" class="row g-3">
      @csrf

      <div class="col-md-4">
        <label class="form-label">الصنف</label>
        <select name="product_id" id="product_id" class="form-select" required>
          <option value="">— اختر الصنف —</option>
          @foreach($products as $id => $name)
            <option value="{{ $id }}" {{ (old('product_id', $selected['product_id'])==$id)?'selected':'' }}>
              {{ $name }}
            </option>
          @endforeach
        </select>
        @error('product_id') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">المخزن (اختياري)</label>
        <select name="store_id" class="form-select">
          <option value="">— لا شيء —</option>
          @foreach($stores as $id => $name)
            <option value="{{ $id }}" {{ (old('store_id', $selected['store_id'])==$id)?'selected':'' }}>
              {{ $name }}
            </option>
          @endforeach
        </select>
        @error('store_id') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-2">
        <label class="form-label">رقم التشغيلة (اختياري)</label>
        <input type="text" name="batch_no" class="form-control" value="{{ old('batch_no', $selected['batch_no']) }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">تاريخ الانتهاء (اختياري)</label>
        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $selected['expiry_date']) }}">
      </div>

      <div class="col-md-3">
        <label class="form-label">الكمية</label>
        <input type="number" step="0.0001" min="0" name="qty" class="form-control" required
               value="{{ old('qty', $selected['qty'] ?? 1) }}">
        @error('qty') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">من وحدة</label>
        <select name="from_unit_id" id="from_unit_id" class="form-select" required>
          <option value="">— اختر —</option>
          @foreach(($units ?? collect()) as $u)
            <option value="{{ $u['unit_id'] }}" {{ (old('from_unit_id', $selected['from_unit'])==$u['unit_id'])?'selected':'' }}>
              {{ $u['unit_name'] }}
            </option>
          @endforeach
        </select>
        @error('from_unit_id') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">إلى وحدة</label>
        <select name="to_unit_id" id="to_unit_id" class="form-select" required>
          <option value="">— اختر —</option>
          @foreach(($units ?? collect()) as $u)
            <option value="{{ $u['unit_id'] }}" {{ (old('to_unit_id', $selected['to_unit'])==$u['unit_id'])?'selected':'' }}>
              {{ $u['unit_name'] }}
            </option>
          @endforeach
        </select>
        @error('to_unit_id') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">تنفيذ التحويل</button>
      </div>
    </form>
  </div>
</div>

{{-- جدول الوحدات/الأسعار للصنف المختار --}}
<div class="card mb-4">
  <div class="card-body">
    <h6 class="mb-3">الوحدات المتاحة وأسعارها</h6>
    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>الوحدة</th>
            <th>هل هي الأصغر؟</th>
            <th>معامل → الأصغر</th>
            <th>سعر التكلفة/وحدة</th>
            <th>سعر البيع/وحدة</th>
          </tr>
        </thead>
        <tbody id="units-table-body">
          @forelse(($units ?? collect()) as $u)
            <tr>
              <td>{{ $u['unit_name'] }}</td>
              <td>{{ $u['is_base'] ? 'نعم' : 'لا' }}</td>
              <td>{{ rtrim(rtrim(number_format($u['factor_to_base'], 4, '.', ''), '0'), '.') }}</td>
              <td>{{ is_null($u['cost_per_unit']) ? '-' : number_format($u['cost_per_unit'], 2) }}</td>
              <td>{{ is_null($u['sale_per_unit']) ? '-' : number_format($u['sale_per_unit'], 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted">اختر صنفًا لعرض وحداته…</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- النتائج بعد التحويل --}}
@if($result)
<div class="row">
  <div class="col-md-4">
    <div class="card border-success mb-3">
      <div class="card-body">
        <div class="text-muted small">الكمية المدخلة</div>
        <div class="fw-semibold">{{ $result['qty_input'] }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-info mb-3">
      <div class="card-body">
        <div class="text-muted small">الكمية بالأصغر (الأساس)</div>
        <div class="fw-semibold">{{ rtrim(rtrim(number_format($result['qty_base'], 4, '.', ''), '0'), '.') }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-primary mb-3">
      <div class="card-body">
        <div class="text-muted small">الكمية بوحدة الهدف</div>
        <div class="fw-semibold">{{ rtrim(rtrim(number_format($result['qty_target'], 4, '.', ''), '0'), '.') }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <h6 class="mb-3">تفكيك الكمية بالأصغر (للعرض)</h6>
    @if(!empty($result['decompose']))
      <ul class="mb-0">
        @foreach($result['decompose'] as $p)
          <li>{{ $p['qty'] }} × {{ $p['unit_name'] }}</li>
        @endforeach
      </ul>
    @else
      <div class="text-muted">—</div>
    @endif
  </div>
</div>

<div class="card mb-5">
  <div class="card-body">
    <h6 class="mb-3">المتاح حسب المخزن/التشغيلة/الصلاحية (محوّل لكل وحدة)</h6>
    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>الوحدة</th>
            <th>المتاح</th>
          </tr>
        </thead>
        <tbody>
          @foreach(($units ?? collect()) as $u)
            <tr>
              <td>{{ $u['unit_name'] }}</td>
              <td>{{ rtrim(rtrim(number_format($availableByUnit[$u['unit_id']] ?? 0, 4, '.', ''), '0'), '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- سكربت بسيط لتحميل الوحدات تلقائيًا عند اختيار الصنف --}}
<script>
(function () {
  const productSelect = document.getElementById('product_id');
  const fromUnit = document.getElementById('from_unit_id');
  const toUnit   = document.getElementById('to_unit_id');
  const tbody    = document.getElementById('units-table-body');

  function clearUnits() {
    fromUnit.innerHTML = '<option value="">— اختر —</option>';
    toUnit.innerHTML   = '<option value="">— اختر —</option>';
    tbody.innerHTML    = '<tr><td colspan="5" class="text-muted">اختر صنفًا لعرض وحداته…</td></tr>';
  }

  async function loadUnits(productId) {
    if (!productId) { clearUnits(); return; }
    const url = '{{ route('dev.unitconv.units', ['product' => 'PID']) }}'.replace('PID', productId);
    const res = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
    if (!res.ok) { clearUnits(); return; }
    const units = await res.json();

    // تعبئة القوائم
    let opts = '<option value="">— اختر —</option>';
    units.forEach(u => { opts += `<option value="${u.unit_id}">${u.unit_name}</option>`; });
    fromUnit.innerHTML = opts;
    toUnit.innerHTML   = opts;

    // تعبئة الجدول
    let rows = '';
    units.forEach(u => {
      const ftb  = Number(u.factor_to_base ?? 1).toFixed(4).replace(/\.?0+$/, '');
      const cost = (u.cost_per_unit == null) ? '-' : Number(u.cost_per_unit).toFixed(2);
      const sale = (u.sale_per_unit == null) ? '-' : Number(u.sale_per_unit).toFixed(2);
      rows += `<tr>
        <td>${u.unit_name}</td>
        <td>${u.is_base ? 'نعم' : 'لا'}</td>
        <td>${ftb}</td>
        <td>${cost}</td>
        <td>${sale}</td>
      </tr>`;
    });
    tbody.innerHTML = rows || '<tr><td colspan="5" class="text-muted">لا توجد وحدات.</td></tr>';
  }

  productSelect?.addEventListener('change', (e) => loadUnits(e.target.value));

  // لو الصفحة رجعت بنتائج وكان في Product مختار مسبقًا، لا نعيد التحميل
  @if(!$result && old('product_id', $selected['product_id']))
    loadUnits('{{ old('product_id', $selected['product_id']) }}');
  @endif
})();
</script>
@endsection
