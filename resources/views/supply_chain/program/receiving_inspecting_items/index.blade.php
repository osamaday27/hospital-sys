@extends('layouts.master_table')

@section('title', trans('supply_chain_trans.ri_title'))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">



<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ route('receiving_inspecting_page') }}">استلام وفحص الاصناف</a>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<style>
#toast-container{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px}
.toast-msg{min-width:260px;max-width:420px;background:#333;color:#fff;padding:10px 14px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.2);opacity:.95}
.toast-msg.success{background:#198754}.toast-msg.warning{background:#f59f00}.toast-msg.danger{background:#dc3545}
</style>
<div id="toast-container"></div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <input type="search" class="form-control" id="search-ri"
               placeholder="{{ trans('supply_chain_trans.ri_search_placeholder') }}" style="width: 250px;">

        <div class="d-flex align-items-center gap-2">
          <label class="mb-0">{{ trans('supply_chain_trans.status_label') }}:</label>
          <select id="status-filter" class="form-select" style="width:180px">
            <option value="">{{ trans('supply_chain_trans.status_all') }}</option>
            <option value="pending">{{ trans('supply_chain_trans.status_pending') }}</option>
            <option value="approved">{{ trans('supply_chain_trans.status_approved') }}</option>
            <option value="rejected">{{ trans('supply_chain_trans.status_rejected') }}</option>
          </select>
        </div>

        <div class="ms-auto hstack gap-2">
          <a href="{{ route('receiving_inspecting.create', [app()->getLocale() ?? 'ar']) }}" class="btn btn-outline-secondary">
            <i class="ri-add-line"></i> {{ trans('supply_chain_trans.ri_create') }}
          </a>
          <button id="reload-table" class="btn btn-info" title="{{ trans('supply_chain_trans.reload') }}">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button class="btn btn-primary" id="export-csv">
            <i class="ri-file-text-line"></i> {{ trans('supply_chain_trans.export_csv') }}
          </button>
          <button class="btn btn-success" id="export-xlsx">
            <i class="ri-file-excel-2-line"></i> {{ trans('supply_chain_trans.export_xlsx') }}
          </button>
        </div>
      </div>

      <div id="alert-container"></div>

      <div class="card-body">
        <table id="ri-table" class="table dt-responsive nowrap align-middle w-100">
<thead>
  <tr>
    <th>{{ trans('supply_chain_trans.col_seq') }}</th>
    <th>{{ trans('supply_chain_trans.col_code') }}</th>
    <th>{{ trans('supply_chain_trans.col_supplier') }}</th>
    <th>{{ trans('supply_chain_trans.col_store') }}</th>
    <th>{{ trans('supply_chain_trans.col_po') }}</th>
    <th>{{ trans('supply_chain_trans.col_date') }}</th>
    <th>{{ __('إجمالي التكلفة') }}</th>
    <th>{{ trans('supply_chain_trans.col_status') }}</th>
    <th>{{ trans('supply_chain_trans.col_actions') }}</th>
  </tr>
</thead>

          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

{{-- Modal عرض التفاصيل --}}
<div class="modal fade" id="riModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ trans('supply_chain_trans.modal_show_title') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('supply_chain_trans.close') }}"></button>
      </div>
      <div class="modal-body">
        <div class="text-center text-muted">{{ trans('supply_chain_trans.loading') }}</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('supply_chain_trans.close') }}</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal تأكيد الحذف (call_center_trans) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('supply_chain_trans.close') }}"></button>
      </div>
      <div class="modal-body text-center">
        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                   colors="primary:#f7b84b,secondary:#f06548"
                   style="width:100px;height:100px"></lord-icon>
        <h4 class="mt-3">{{ trans('call_center_trans.h4_delete') }}</h4>
        <p class="text-muted">{{ trans('call_center_trans.p_delete') }}</p>
        <div class="d-flex gap-2 justify-content-center mt-4">
          <form action="" method="post" id="delete-form">
            @csrf
            @method('DELETE')
            <input type="hidden" id="delete-id">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
              {{ trans('call_center_trans.cancel') }}
            </button>
            <button type="button" class="btn btn-danger" id="delete-record">
              {{ trans('call_center_trans.yes_delete') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.lordicon.com/lordicon.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
(function(){
  function showToast(msg, type){
    const cont = document.getElementById('toast-container');
    const node = document.createElement('div');
    node.className = 'toast-msg '+(type||'warning');
    node.textContent = msg;
    cont.appendChild(node);
    setTimeout(()=>{ node.style.transition='opacity .4s'; node.style.opacity='0'; }, 2500);
    setTimeout(()=>{ node.remove(); }, 3000);
  }

  @if(session('success'))
    showToast(@json(session('success')), 'success');
  @endif
  @if($errors->any())
    showToast(@json($errors->first()), 'danger');
  @endif

  const ajaxUrl = @json(route('receiving_inspecting.datatable', [app()->getLocale() ?? 'ar']));
  const showUrlTpl = @json(route('receiving_inspecting.show', ['id' => '__ID__']));
  const destroyUrlTpl = @json(route('receiving_inspecting.destroy', ['id' => '__ID__']));

  const table = $('#ri-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: ajaxUrl,
      data: function(d){ d.status = $('#status-filter').val(); },
      error: function(xhr, status, err){
        console.error('DataTable AJAX error:', status, err, xhr?.responseText);
        showToast(@json(trans('supply_chain_trans.load_failed')), 'danger');
      }
    },
    order: [[0,'desc']],
columns: [
  {
    data: 'id', name:'id', orderable:false, searchable:false,
    render: function(data, type, row, meta){ return meta.row + meta.settings._iDisplayStart + 1; }
  },
  {data: 'code',           name:'code'},
  {data: 'supplier_name',  name:'supplier_name', orderable:false, searchable:false},
  {data: 'store_name',     name:'store_name',    orderable:false, searchable:false},
  {data: 'po_code',        name:'po_code',       orderable:false, searchable:false},
  {data: 'document_date',  name:'document_date', width: 120},

  // <<< إجمالي التكلفة
  {data: 'total_cost',     name:'total_cost',    orderable:false, searchable:false, width: 130,
   className: 'text-end',
   render: function(data){
     var n = Number(String(data||'').replace(/[^0-9.\-]/g,'')) || 0;
     return n.toFixed(2);
   }
  },

  {data: 'status',         name:'status',        orderable:false, searchable:false, width: 120},
  {data: 'actions',        name:'actions',       orderable:false, searchable:false, width: 240},
],

    dom: 'Bfrtip',
    buttons: [
      { extend: 'csvHtml5',   title: @json(trans('supply_chain_trans.ri_export_title')), text: @json(trans('supply_chain_trans.export_csv')),  className: 'buttons-csv d-none' },
      { extend: 'excelHtml5', title: @json(trans('supply_chain_trans.ri_export_title')), text: @json(trans('supply_chain_trans.export_xlsx')), className: 'buttons-excel d-none' },
    ],
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
  });

  $('#search-ri').on('input', function(){ table.search(this.value).draw(); });
  $('#status-filter').on('change', function(){ table.ajax.reload(); });
  $('#reload-table').on('click', function(){ table.ajax.reload(null, false); });
  $('#export-csv').on('click',  function(){ table.button('.buttons-csv').trigger();  });
  $('#export-xlsx').on('click', function(){ table.button('.buttons-excel').trigger(); });

  // عرض التفاصيل
  $(document).on('click', '.btn-show', function(){
    const id = $(this).data('id');
    const showUrl = showUrlTpl.replace('__ID__', id);

    const $modal = $('#riModal');
    $modal.find('.modal-body').html('<div class="text-center text-muted">{{ trans('supply_chain_trans.loading') }}</div>');
    const modalEl = document.getElementById('riModal');
    const modal   = window.bootstrap && bootstrap.Modal ? new bootstrap.Modal(modalEl) : null;
    if (modal) { modal.show(); }

    $.get(showUrl, function(html){
      $modal.find('.modal-body').html(html);
    }).fail(function(){
      $modal.find('.modal-body').html('<div class="text-danger">{{ trans('supply_chain_trans.load_failed') }}</div>');
    });
  });

  // حذف
  let deleteUrl = null;
  $(document).on('click', '.btn-delete', function(){
    const id = $(this).data('id');
    deleteUrl = destroyUrlTpl.replace('__ID__', id);
    $('#delete-form').attr('action', deleteUrl);
    $('#delete-id').val(id);

    const modalEl = document.getElementById('deleteModal');
    const modal   = window.bootstrap && bootstrap.Modal ? new bootstrap.Modal(modalEl) : null;
    if (modal) { modal.show(); }
  });

  $('#delete-record').on('click', function(){
    if (!deleteUrl) return;
    const $btn = $(this), originalText = $btn.text();
    $btn.prop('disabled', true).text('...');

    $.ajax({
      url: deleteUrl,
      type: 'DELETE',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      success: function(resp){
        const modalEl = document.getElementById('deleteModal');
        const modal   = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        if(resp && resp.ok){
          showToast(resp.message || @json(trans('supply_chain_trans.delete_success')), 'success');
        } else {
          showToast(@json(trans('supply_chain_trans.delete_failed')), 'danger');
        }
        table.ajax.reload(null, false);
      },
      error: function(xhr){
        const msg = xhr.responseJSON?.message || @json(trans('supply_chain_trans.delete_error_generic'));
        showToast(msg,'danger');
      },
      complete: function(){
        $btn.prop('disabled', false).text(originalText || @json(trans('call_center_trans.yes_delete')));
        deleteUrl = null;
      }
    });
  });

})();
</script>
@endsection
