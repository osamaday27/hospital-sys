@extends('layouts.master_table')
@section('title', __('assets.items'))
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Buttons (Bootstrap 5) CSS --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"> --}}

{{-- ====== Breadcrumb ====== --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">@lang('assets.items')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2">
          <input type="search" id="search-items" class="form-control" style="width:250px;"
                 placeholder="@lang('assets.search_placeholder', [], app()->getLocale())">
          <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
          <button id="export-csv"  class="btn btn-primary"><i class="fa fa-file-csv"></i> CSV</button>
          <button id="export-xlsx" class="btn btn-success"><i class="fas fa-file-excel"></i> XLSX</button>
        </div>

        <a href="{{ route('assets.programs.items.create') }}" class="btn btn-outline-secondary">
          <i class="ri-add-line"></i> @lang('assets.create')
        </a>
      </div>

      <div id="alert-container" class="m-2"></div>

      <div class="card-body">
        <table id="items-table" class="table dt-responsive nowrap align-middle w-100 text-center">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>@lang('assets.code')</th>
              <th>@lang('assets.name')</th>
              <th>@lang('assets.category')</th>
              <th>@lang('assets.brand')</th>
              <th>@lang('assets.model')</th>
              <th>@lang('assets.store')</th>
              <th>@lang('assets.status')</th>
              <th>@lang('assets.actions')</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

{{-- مودال تفاصيل الأصل --}}
<div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">@lang('assets.items')</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="showModalBody">
        <div class="text-center text-muted my-4">@lang('assets.loading')</div>
      </div>
    </div>
  </div>
</div>

{{-- مودال الحذف العام --}}
@include('components.modals.delete-modal')

{{-- JS: Buttons + Bootstrap5 integration + JSZip --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script> --}}

<script>
(function () {
  // اقرأ CSRF من الميتا مرة واحدة
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  // إعدادات jQuery Ajax العامة
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': CSRF,
      'X-Requested-With': 'XMLHttpRequest'
    }
  });

  $(function () {
    // تهيئة الداتا تيبل + أزرار التصدير
    const t = $('#items-table').DataTable({
      processing: true,
      serverSide: true,
      ajax: { url: '{{ route('assets.programs.items.ajax') }}' },
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'code' }, { data: 'name' }, { data: 'category' }, { data: 'brand' }, { data: 'model' }, { data: 'store' },
        { data: 'status', orderable: false, searchable: false },
        { data: 'action', orderable: false, searchable: false }
      ],
      order: [[1, 'asc']],
      dom: 'Bfrtip',
      buttons: [
        { extend: 'csvHtml5',   text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-outline-primary' },
        { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
      ],
    //   language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
    });

    // أدوات أعلى الجدول
    $('#reload-table').on('click', () => t.ajax.reload(null, false));
    $('#search-items').on('input', function () { t.search(this.value).draw(); });
    $('#export-csv').on('click', () => t.button('.buttons-csv').trigger());
    $('#export-xlsx').on('click', () => t.button('.buttons-excel').trigger());

    // عرض التفاصيل داخل مودال (يدعم عدة كلاسات أزرار)
    $(document).on('click', '.js-show, .view-btn, .view-quotation-btn', async function () {
      const id = $(this).data('id');
      const url = "{{ route('assets.programs.items.show', ':id') }}".replace(':id', id);
      $('#showModalBody').html('<div class="text-center text-muted my-4">@lang("assets.loading")</div>');

      try {
        const res = await fetch(url + '?modal=1', {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin' // ✅ لإرسال كوكي الجلسة
        });
        const j = res.ok ? await res.json() : { html: '<div class="text-danger">@lang("assets.load_error")</div>' };
        $('#showModalBody').html(j.html || '<div class="text-danger">@lang("assets.no_content")</div>');
        new bootstrap.Modal('#showModal').show();
      } catch (e) {
        $('#showModalBody').html('<div class="text-danger">@lang("assets.load_error")</div>');
      }
    });

    // فتح مودال الحذف (يدعم كلاسات قديمة أيضًا)
    $(document).on('click', '.js-del, .delete-btn', function () {
      $('#delete-id').val($(this).data('id') || '');
      $('#deleteModal').modal('show');
    });

    // تنفيذ الحذف مع معالجة CSRF والجلسة
    $('#delete-record').on('click', async function () {
      const id = $('#delete-id').val();
      if (!id) return;

      const url = '{{ route("assets.programs.items.destroy", ":id") }}'.replace(':id', id);

      try {
        // ✅ استخدم POST مع _method=DELETE + credentials لإرسال كوكي الجلسة
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ _method: 'DELETE' }),
          credentials: 'same-origin'
        });

        if (res.ok) {
          $('#deleteModal').modal('hide');
          showAlert('success', '@lang("assets.deleted_success")');
          t.ajax.reload(null, false);
        } else {
          let msg = '@lang("assets.delete_failed")';
          try { const j = await res.json(); if (j?.message) msg = j.message; } catch {}
          showAlert('danger', msg);
        }
      } catch (e) {
        showAlert('danger', '@lang("assets.delete_failed")');
      }
    });

    // تنبيه أعلى الصفحة
    function showAlert(type, msg) {
      $('#alert-container').html(
        `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
           ${msg}
           <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>`
      );
      setTimeout(() => $('.alert').alert('close'), 5000);
    }
  });
})();
</script>

@endsection
