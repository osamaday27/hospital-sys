@extends('layouts.master_table')

@section('title')
  {{ __('supply_chain_trans.edit_item_expiry_date.title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Toasts --}}
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

<div class="card">
  <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <strong>{{ __('supply_chain_trans.edit_item_expiry_date.title') }}</strong>

    {{-- حقل البحث الخارجي --}}
    <div class="d-flex align-items-center gap-2">
      <div class="input-group">
        <span class="input-group-text"><i class="ri-search-line"></i></span>
        <input type="text" id="quick-search" class="form-control" placeholder="{{ __('supply_chain_trans.search') }}">
        <button id="clear-search" class="btn btn-outline-secondary">{{ __('supply_chain_trans.clear') }}</button>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table id="items-table" class="table table-bordered align-middle w-100">
        <thead class="table-light">
          <tr>
            <th style="width:40px">#</th>
            <th>{{ __('supply_chain_trans.product') }}</th>
            <th>{{ __('supply_chain_trans.product_code') }}</th>
            <th>{{ __('supply_chain_trans.batch_no') }}</th>
            <th>{{ __('supply_chain_trans.expiry_date') }}</th>
            <th>{{ __('supply_chain_trans.unit') }}</th>
            <th>{{ __('supply_chain_trans.system_qty') }}</th>
            <th style="width:120px">{{ __('supply_chain_trans.actions') }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

{{-- Modal: تعديل تاريخ الانتهاء --}}
<div class="modal fade" id="editExpiryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary-subtle">
        <h6 class="modal-title">{{ __('supply_chain_trans.edit_item_expiry_date.modal_title') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('supply_chain_trans.close') }}"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="m-row-id"> {{-- ← هذا هو المهم --}}
        <div class="mb-2">
          <label class="form-label">{{ __('supply_chain_trans.expiry_date') }}</label>
          <input type="date" id="m-expiry" class="form-control">
          <div class="form-text">{{ __('supply_chain_trans.leave_empty_to_clear') }}</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">{{ __('supply_chain_trans.cancel') }}</button>
        <button class="btn btn-primary" id="btn-save-expiry">{{ __('supply_chain_trans.save') }}</button>
      </div>
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
  new bootstrap.Toast(document.getElementById(id), { delay: 2500 }).show();
}

let dt;

$(function () {
  dt = $('#items-table').DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    pageLength: 10,
    lengthChange: true,
    searching: true,          // نستخدم البحث ولكن نخفي صندوق داتا تيبلز الافتراضي
    order: [[4, 'asc']],      // افتراضيًا حسب تاريخ الانتهاء
    dom: 'rtip',              // بدون صندوق البحث الافتراضي (f)
    ajax: {
      url: "{{ route('edit_item_expiry.ajax.list') }}",
      type: 'GET',
      data: function (d) {
        d.q = $('#quick-search').val() || '';
      }
    },
    columns: [
      { data: null, orderable: false, searchable: false },
      { data: 'product_name', name: 'product_name' },
      { data: 'product_code', name: 'product_code' },
      { data: 'batch_no',     name: 'batch_no' },
      { data: 'expiry_date',  name: 'expiry_date' },
      { data: 'unit_name',    name: 'unit_name' },
      { data: 'quantity_in_base_unit', name: 'quantity_in_base_unit' },
      {
        data: null, orderable: false, searchable: false,
        render: function (row) {
          const title = "{{ __('supply_chain_trans.edit_item_expiry_date.edit_expiry') }}";
          return `
            <button class="btn btn-sm btn-outline-primary btn-edit-expiry"
              data-id="${row.id}"             /* ← نرسل id فقط */
              data-expiry="${row.expiry_date ?? ''}">
              <i class="ri-calendar-2-line"></i> ${title}
            </button>`;
        }
      }
    ]
  });

  // ترقيم الصفوف
  dt.on('draw', function(){
    dt.column(0, { page: 'current' }).nodes().each((cell, i) => {
      cell.innerHTML = (dt.page.info().start + i + 1);
    });
  });

  // بحث خارجي مع debounce بسيط
  let t;
  $('#quick-search').on('input', function () {
    clearTimeout(t);
    t = setTimeout(() => {
      dt.search(this.value).draw();
    }, 300);
  });
  $('#clear-search').on('click', function(){
    $('#quick-search').val('');
    dt.search('').draw();
  });

  // فتح المودال
  $(document).on('click', '.btn-edit-expiry', function(){
    $('#m-row-id').val($(this).data('id'));      // ← خزّن id
    $('#m-expiry').val($(this).data('expiry') ?? '');
    new bootstrap.Modal(document.getElementById('editExpiryModal')).show();
  });

  // حفظ التعديل
  $('#btn-save-expiry').on('click', function(){
    const payload = {
      id: $('#m-row-id').val(),                  // ← أرسل id
      expiry_date: ($('#m-expiry').val() || null)
    };
    $(this).prop('disabled', true);

    $.post("{{ route('edit_item_expiry.update') }}", payload)
      .done(resp => {
        showToast('success', resp?.message || "{{ __('supply_chain_trans.saved_successfully') }}");
        const m = bootstrap.Modal.getInstance(document.getElementById('editExpiryModal'));
        if (m) m.hide();
        dt.ajax.reload(null, false);
      })
      .fail(xhr => {
        const msg = xhr?.responseJSON?.message || 'Error';
        showToast('danger', msg);
      })
      .always(() => $(this).prop('disabled', false));
  });
});
</script>
@endsection
