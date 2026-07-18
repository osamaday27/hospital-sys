@extends('layouts.master_table')

@section('title', 'مرتجعات الموردين')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Toast container --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

<div class="row mb-3">
  <div class="col-12 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">مرتجعات الموردين</h5>
    <a href="{{ route('return_to_supplier.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> إضافة مرتجع
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    @if($rows->count() === 0)
      <div class="text-center text-muted py-5">
        لا توجد سجلات حتى الآن.
        <div class="mt-2">
          <a href="{{ route('return_to_supplier.create') }}" class="btn btn-sm btn-outline-primary">إنشاء أول مرتجع</a>
        </div>
      </div>
    @else
    <div class="table-responsive">
      <table class="table table-striped table-bordered align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:70px;">#</th>
            <th>الكود</th>
            <th style="width:140px;">التاريخ</th>
            <th>المورد</th>
            <th>المخزن</th>
            <th style="width:160px;">الإجمالي (شامل)</th>
            <th style="width:200px;">الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $i => $r)
          <tr>
            <td class="text-muted">{{ $rows->firstItem() + $i }}</td>
            <td>
              <span class="fw-semibold">{{ $r->code }}</span>
            </td>
            <td><span class="badge bg-light text-dark">{{ $r->document_date?->format('Y-m-d') }}</span></td>
            <td>{{ $r->supplier?->name_ar ?? $r->supplier?->name_en ?? '-' }}</td>
            <td>{{ $r->store?->name_ar ?? $r->store?->name_en ?? '-' }}</td>
            <td><span class="fw-semibold">{{ number_format((float)$r->grand_total, 2) }}</span></td>
            <td>
              <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#showModal" data-id="{{ $r->id }}">
                  <i class="bi bi-eye"></i> عرض
                </button>

                {{-- تعديل (مستقبلاً) --}}
                {{-- <a href="{{ route('return_to_supplier.edit', $r->id) }}" class="btn btn-warning"><i class="bi bi-pencil-square"></i> تعديل</a> --}}

                <button type="button"
                        class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-id="{{ $r->id }}"
                        data-code="{{ $r->code }}"
                        data-total="{{ number_format((float)$r->grand_total, 2) }}">
                  <i class="bi bi-trash"></i> حذف
                </button>
              </div>

              {{-- نموذج الحذف لكل صف (سيُستدعى من المودال) --}}
              <form id="del-form-{{ $r->id }}" action="{{ route('return_to_supplier.destroy', $r->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="pt-3">
      {{ $rows->links() }}
    </div>
    @endif
  </div>
</div>

{{-- Show Modal --}}
<div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">تفاصيل المرتجع</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="اغلاق"></button>
      </div>
      <div class="modal-body">
        <div id="showSpinner" class="text-center py-4">
          <div class="spinner-border" role="status"></div>
          <div class="mt-2 small text-muted">جاري التحميل…</div>
        </div>
        <div id="showArea" class="d-none"></div>
      </div>
    </div>
  </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header bg-danger text-white">
        <h6 class="modal-title">تأكيد الحذف</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="اغلاق"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning d-flex align-items-start" role="alert">
          <i class="bi bi-exclamation-triangle me-2 fs-5"></i>
          <div>
            سيؤدي الحذف إلى <strong>عكس الأرصدة</strong> وحذف <strong>فاتورة المورد</strong> المرتبطة وإزالة <strong>الحركات المخزنية</strong>.
          </div>
        </div>
        <div class="p-2 bg-light rounded border">
          <div class="row g-2">
            <div class="col-6">
              <div class="small text-muted">كود المرتجع</div>
              <div id="delCode" class="fw-semibold">-</div>
            </div>
            <div class="col-6">
              <div class="small text-muted">الإجمالي شامل</div>
              <div id="delTotal" class="fw-semibold">0.00</div>
            </div>
          </div>
        </div>
        <input type="hidden" id="delId" value="">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
        <button type="button" id="btnConfirmDelete" class="btn btn-danger">
          <i class="bi bi-trash"></i> تأكيد الحذف
        </button>
      </div>
    </div>
  </div>
</div>

{{-- اختياري: أيقونات Bootstrap (لو غير مضمنة في الـ layout) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
(function(){
  // --- Helpers ---
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

  // --- Flash messages (Laravel session) ---
  const flash = @json(['success'=>session('success'), 'error'=>session('error')]);
  if (flash.success) toast(flash.success, 'success');
  if (flash.error)   toast(flash.error,   'danger');

  // --- Show modal logic ---
  const showModal = document.getElementById('showModal');
  showModal?.addEventListener('show.bs.modal', async (e) => {
    const id   = e.relatedTarget?.getAttribute('data-id');
    const spin = document.getElementById('showSpinner');
    const area = document.getElementById('showArea');
    spin.classList.remove('d-none');
    area.classList.add('d-none');
    area.innerHTML = '';

    try {
      const res = await fetch(`{{ url('return_to_supplier') }}/${id}`);
      if(!res.ok){ throw new Error('فشل تحميل البيانات'); }
      const data = await res.json();

      let html = `
        <div class="row g-3 mb-3">
          <div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">الكود</div><div class="fw-semibold">${data.code || ''}</div></div></div>
          <div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">التاريخ</div><div class="fw-semibold">${data.document_date || ''}</div></div></div>
          <div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">المورد</div><div class="fw-semibold">${(data.supplier?.name_ar ?? data.supplier?.name_en) || ''}</div></div></div>
          <div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">المخزن</div><div class="fw-semibold">${(data.store?.name_ar ?? data.store?.name_en) || ''}</div></div></div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:60px;">#</th>
                <th>الصنف</th>
                <th style="width:160px;">الوحدة</th>
                <th style="width:140px;">الكمية</th>
                <th style="width:140px;">سعر/وحدة</th>
                <th style="width:140px;">ضريبة/وحدة</th>
                <th style="width:160px;">شامل/وحدة</th>
                <th style="width:160px;">التشغيلة</th>
              </tr>
            </thead>
            <tbody>`;
      (data.items || []).forEach((it,idx)=>{
        html += `<tr>
          <td class="text-muted">${idx+1}</td>
          <td>${(it.product?.name_ar ?? it.product?.name_en) || '-'}</td>
          <td>${it.unit?.name_ar ?? it.unit?.name_en ?? '-'}</td>
          <td>${Number(it.quantity ?? 0).toFixed(4)}</td>
          <td>${Number(it.unit_price ?? 0).toFixed(2)}</td>
          <td>${Number(it.tax_value ?? 0).toFixed(2)}</td>
          <td>${Number(it.unit_price_with_tax ?? 0).toFixed(2)}</td>
          <td>${it.batch_no ?? ''}</td>
        </tr>`;
      });
      html += `</tbody></table></div>
      <div class="mt-3 text-end">
        <div>الإجمالي قبل الضريبة: <strong>${Number(data.subtotal ?? 0).toFixed(2)}</strong></div>
        <div>إجمالي الضريبة: <strong>${Number(data.tax_total ?? 0).toFixed(2)}</strong></div>
        <div>الإجمالي شامل الضريبة: <strong>${Number(data.grand_total ?? 0).toFixed(2)}</strong></div>
      </div>`;

      area.innerHTML = html;
      spin.classList.add('d-none');
      area.classList.remove('d-none');
    } catch (err) {
      spin.classList.add('d-none');
      area.classList.remove('d-none');
      area.innerHTML = `<div class="alert alert-danger">تعذر تحميل التفاصيل، حاول مرة أخرى.</div>`;
    }
  });

  // --- Delete modal logic ---
  const deleteModal = document.getElementById('deleteModal');
  deleteModal?.addEventListener('show.bs.modal', (e)=>{
    const btn   = e.relatedTarget;
    const id    = btn?.getAttribute('data-id');
    const code  = btn?.getAttribute('data-code') || '-';
    const total = btn?.getAttribute('data-total') || '0.00';
    document.getElementById('delId').value = id;
    document.getElementById('delCode').textContent = code;
    document.getElementById('delTotal').textContent = total;
  });

  document.getElementById('btnConfirmDelete')?.addEventListener('click', ()=>{
    const id = document.getElementById('delId').value;
    const form = document.getElementById('del-form-' + id);
    if (form) form.submit();
  });
})();
</script>
@endsection
