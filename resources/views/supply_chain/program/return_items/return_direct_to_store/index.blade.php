@extends('layouts.master_table')

@section('title', 'الارتجاع المباشر بين المخازن')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- صفحة العنوان / Breadcrumb --}}
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">الارتجاع المباشر بين المخازن</li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- تنبيه فلاش --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
    {{ session('success') }}
  </div>
  <script>
    $(function(){ setTimeout(function(){ $('.alert-success').fadeOut(600,function(){ $(this).remove(); }); }, 4000); });
  </script>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      {{-- شريط الأدوات أعلى الجدول --}}
      <div class="card-header d-flex justify-content-between align-items-center">
        <input type="search" id="search-docs" class="form-control" placeholder="بحث..." style="width:250px;">

        <div class="hstack gap-2">
          <a href="{{ route('return_direct_to_store.create') }}" class="btn btn-outline-secondary">
            <i class="ri-add-line"></i> إنشاء مستند
          </a>
          <button id="reload-table" class="btn btn-info" title="إعادة تحميل">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button id="export-csv" class="btn btn-primary">
            <i class="fa fa-file-csv"></i> CSV
          </button>
          <button id="export-xlsx" class="btn btn-success">
            <i class="fas fa-file-excel"></i> XLSX
          </button>
        </div>
      </div>

      <div id="alert-container" class="m-2"></div>

      <div class="card-body">
        <table id="docs-table" class="table dt-responsive nowrap align-middle w-100 text-center">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>الكود</th>
              <th>من مخزن</th>
              <th>إلى مخزن</th>
              <th>التاريخ</th>
              <th>عدد البنود</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

{{-- مودال التفاصيل --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">تفاصيل المستند</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <div id="details-head" class="row g-3 mb-3"></div>

        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>الصنف</th>
                <th>الوحدة</th>
                <th>الكمية</th>
                <th>التشغيلة</th>
                <th>كود التحويل</th>
                <th>ملاحظات</th>
              </tr>
            </thead>
            <tbody id="details-items"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- مودال حذف مؤكد --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger-subtle">
        <h6 class="modal-title"><i class="ri-alert-line me-1 text-danger"></i> تأكيد الحذف</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning d-flex align-items-start" role="alert">
          <i class="ri-error-warning-line me-2 fs-4"></i>
          <div>سيتم عكس الأرصدة في المخزنين وحذف جميع الحركات المرتبطة بهذا الكود.</div>
        </div>
        <div class="p-2 bg-light rounded border">
          <div class="small text-muted">كود المستند</div>
          <div id="delCode" class="fw-semibold">-</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
        <button class="btn btn-danger" id="confirm-delete"><i class="ri-delete-bin-6-line"></i> حذف</button>
      </div>
    </div>
  </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

$(function () {
  // DataTable
  const table = $('#docs-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: { url: "{{ route('return_direct_to_store.index') }}" },
    columns: [
      { data: 'DT_RowIndex',   name: 'DT_RowIndex',   orderable: false, searchable: false },
      { data: 'code',          name: 'code' },
      { data: 'store_from',    name: 'store_from',    orderable: false, searchable: true },
      { data: 'store_to',      name: 'store_to',      orderable: false, searchable: true },
      { data: 'document_date', name: 'document_date' },
      { data: 'items_count',   name: 'items_count',   orderable: true, searchable: false },
      { data: 'action',        orderable: false, searchable: false }
    ],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'csvHtml5',   text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-outline-primary' },
      { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
    ],
    order: [[4, 'desc']],
    language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
  });

  // أدوات أعلى الجدول
  $('#reload-table').on('click', ()=> table.ajax.reload(null, false));
  $('#search-docs').on('input', function(){ table.search(this.value).draw(); });
  $('#export-csv').on('click',  ()=> table.button('.buttons-csv').trigger());
  $('#export-xlsx').on('click', ()=> table.button('.buttons-excel').trigger());

  // عرض التفاصيل
  $(document).on('click', '.view-btn', async function(){
    const code = $(this).data('code');
    try {
      // ✅ استخدم الراوت الصحيح تحت /ajax/
      const idRes = await fetch(`{{ url('return_direct_to_store/ajax/first-id-by-code') }}?code=${encodeURIComponent(code)}`);
      if (!idRes.ok) return showAlert('danger','تعذر إيجاد المستند.');
      const j   = await idRes.json();
      const id  = j.first_id || null;
      if (!id)  return showAlert('danger', 'تعذر إيجاد مستند بهذا الكود.');

      const res = await fetch(`{{ url('return_direct_to_store') }}/${id}`);
      if (!res.ok) return showAlert('danger','تعذر تحميل التفاصيل.');
      const data = await res.json();

      // رأس التفاصيل (لو الأسماء غير متاحة من الـAPI نعرض الـID)
      const fromName = (data.store_from && (data.store_from.name_ar || data.store_from.name_en))
                       ? (data.store_from.name_ar || data.store_from.name_en)
                       : (data.store_from_id || '-');
      const toName   = (data.store_to && (data.store_to.name_ar || data.store_to.name_en))
                       ? (data.store_to.name_ar || data.store_to.name_en)
                       : (data.store_to_id || '-');

      $('#details-head').html(`
        <div class="col-md-3"><div class="border p-2 rounded"><div class="small text-muted">الكود</div><div class="fw-semibold">${data.code||''}</div></div></div>
        <div class="col-md-3"><div class="border p-2 rounded"><div class="small text-muted">التاريخ</div><div class="fw-semibold">${data.transfer_date||''}</div></div></div>
        <div class="col-md-3"><div class="border p-2 rounded"><div class="small text-muted">من مخزن</div><div class="fw-semibold">${fromName}</div></div></div>
        <div class="col-md-3"><div class="border p-2 rounded"><div class="small text-muted">إلى مخزن</div><div class="fw-semibold">${toName}</div></div></div>
      `);

      // عناصر
      let itemsHtml = '';
      (data.items || []).forEach((it, idx) => {
        const ptext = (it.product && (it.product.product_code || it.product.name_ar || it.product.name_en))
          ? ((it.product.product_code ? (it.product.product_code+' / ') : '') + (it.product.name_ar ?? it.product.name_en ?? '-'))
          : '-';
        const utext = (it.unit && (it.unit.name_ar || it.unit.name_en)) ? (it.unit.name_ar ?? it.unit.name_en) : '-';

        itemsHtml += `
          <tr>
            <td>${idx+1}</td>
            <td>${ptext}</td>
            <td>${utext}</td>
            <td>${Number(it.quantity ?? 0).toFixed(4)}</td>
            <td>${it.batch_no ?? ''}</td>
            <td>${it.transfer_number ?? ''}</td>
            <td>${it.notes ?? ''}</td>
          </tr>`;
      });
      $('#details-items').html(itemsHtml || `<tr><td colspan="7" class="text-muted">لا توجد بنود.</td></tr>`);

      $('#detailsModal').modal('show');
    } catch (e) {
      showAlert('danger', 'حدث خطأ غير متوقع أثناء تحميل التفاصيل.');
    }
  });

  // حذف (مودال)
  let delCode = null, delFirstId = null;

  $(document).on('click', '.delete-btn', async function(){
    delCode = $(this).data('code') || null;
    $('#delCode').text(delCode || '-');

    try {
      // ✅ استخدم الراوت الصحيح تحت /ajax/
      const res = await fetch(`{{ url('return_direct_to_store/ajax/first-id-by-code') }}?code=${encodeURIComponent(delCode)}`);
      if (!res.ok) return showAlert('danger','تعذر تجهيز الحذف.');
      const j   = await res.json();
      delFirstId = j.first_id || null;
      if (!delFirstId) return showAlert('danger','تعذر إيجاد المستند.');

      $('#deleteModal').modal('show');
    } catch {
      showAlert('danger', 'تعذر تجهيز الحذف.');
    }
  });

  $('#confirm-delete').on('click', function(){
    if (!delFirstId) return;
    const url = '{{ route("return_direct_to_store.destroy", ":id") }}'.replace(':id', delFirstId);
    $.ajax({
      url, type: 'DELETE',
      headers: { 'Accept': 'application/json' }, // ✅ خلي الكنترولر يرجّع JSON
      success(resp){
        $('#deleteModal').modal('hide');
        showAlert('success', resp?.message || 'تم الحذف بنجاح.');
        table.ajax.reload(null, false);
      },
      error(xhr){
        $('#deleteModal').modal('hide');
        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'حدث خطأ أثناء الحذف.';
        showAlert('danger', msg);
      }
    });
  });

  function showAlert(type, msg) {
    $('#alert-container').html(
      `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
      </div>`
    );
    setTimeout(()=> $('.alert').alert('close'), 5000);
  }
});
</script>
@endsection
