@extends('layouts.master_table')

@section('title', trans('supply_chain_trans.approvals_title'))

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* ✅ Toast بسيط */
#toast-container{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px}
.toast-msg{min-width:260px;max-width:460px;background:#333;color:#fff;padding:10px 14px;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,.2);opacity:.95;direction:rtl}
.toast-msg.success{background:#198754}.toast-msg.warning{background:#f59f00}.toast-msg.danger{background:#dc3545}
</style>

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            {{ trans('supply_chain_trans.breadcrumb_approvals') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div id="toast-container"></div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <input type="search" class="form-control" id="search-approvals"
               placeholder="{{ trans('supply_chain_trans.ri_search_placeholder') }}" style="width:250px;">

        {{-- ✅ فلتر حالة موافقتي (افتراضي pending) --}}
        <div class="d-flex align-items-center gap-2">
          <label class="mb-0">{{ trans('supply_chain_trans.status_label') }}:</label>
          <select id="my-status-filter" class="form-select" style="width:180px">
            <option value="pending">{{ trans('supply_chain_trans.status_pending') }}</option>
            <option value="approved">{{ trans('supply_chain_trans.status_approved') }}</option>
            <option value="rejected">{{ trans('supply_chain_trans.status_rejected') }}</option>
            <option value="">{{ trans('supply_chain_trans.status_all') }}</option>
          </select>
        </div>

        <button id="reload-table" class="btn btn-info ms-auto">
          <i class="ri-repeat-2-line"></i> {{ trans('supply_chain_trans.reload') }}
        </button>
      </div>

      <div class="card-body">
        <table id="approvals-table" class="table dt-responsive nowrap align-middle w-100">
          <thead>
            <tr>
              <th>{{ trans('supply_chain_trans.col_seq') }}</th>
              <th>{{ trans('supply_chain_trans.col_code') }}</th>
              <th>{{ trans('supply_chain_trans.col_supplier') }}</th>
              <th>{{ trans('supply_chain_trans.col_store') }}</th>
              <th>{{ trans('supply_chain_trans.col_date') }}</th>
              <th>{{ trans('supply_chain_trans.total_cost') ?? 'إجمالي التكلفة' }}</th>
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
<div class="modal fade" id="approvalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">{{ trans('supply_chain_trans.modal_show_title') }}</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-center text-muted">{{ trans('supply_chain_trans.loading') }}</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="btn-approve">{{ trans('supply_chain_trans.btn_approve') }}</button>
        <button class="btn btn-danger"  id="btn-reject">{{ trans('supply_chain_trans.btn_reject') }}</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('supply_chain_trans.close') }}</button>
      </div>
    </div>
  </div>
</div>

<script>
(function(){

  function showToast(msg, type='success'){
    const cont = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = 'toast-msg ' + (type||'success');
    el.textContent = msg || '';
    cont.appendChild(el);
    setTimeout(()=>{ el.style.transition='opacity .35s'; el.style.opacity='0'; }, 2200);
    setTimeout(()=>{ el.remove(); }, 2600);
  }

  const ajaxUrl       = @json(route('manage_receiving_inspecting_approvals.datatable', ['locale' => app()->getLocale() ?? 'ar']));
  const showUrlTpl    = @json(route('manage_receiving_inspecting_approvals.show',    ['locale' => app()->getLocale() ?? 'ar', 'approval' => '__ID__']));
  const approveUrlTpl = @json(route('manage_receiving_inspecting_approvals.approve', ['locale' => app()->getLocale() ?? 'ar', 'approval' => '__ID__']));
  const rejectUrlTpl  = @json(route('manage_receiving_inspecting_approvals.reject',  ['locale' => app()->getLocale() ?? 'ar', 'approval' => '__ID__']));

  const table = $('#approvals-table').DataTable({
    processing:true, serverSide:true,
    ajax:{
      url: ajaxUrl,
      data: function(d){
        d.my_status = $('#my-status-filter').val() || 'pending';
      }
    },
    order:[[0,'desc']],
    columns:[
      {data:'id', render:(d,t,r,m)=> m.row+m.settings._iDisplayStart+1, searchable:false, orderable:true, name:'id'},
      {data:'code',            name:'code'},
      {data:'supplier_name',   name:'supplier_name'},
      {data:'store_name',      name:'store_name'},
      {data:'document_date',   name:'document_date'},
      // ❗ عمود محسوب: امنع البحث والترتيب حتى لا يحاول السيرفر استخدام receiving_inspecting.total_cost
      {data:'total_cost',      searchable:false, orderable:false, className:'text-end'},
      {data:'status',          orderable:false, searchable:false},
      {data:'actions',         orderable:false, searchable:false}
    ],
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' },
    searchDelay: 300
  });

  $('#search-approvals').on('input', ()=> table.search($('#search-approvals').val()).draw());
  $('#my-status-filter').on('change', ()=> table.ajax.reload());
  $('#reload-table').on('click', ()=> table.ajax.reload(null,false));

  let currentId   = null;
  let currentMySt = 'pending';
  let currentDocSt= 'pending';

  // فتح المودال
  $(document).on('click','.btn-show',function(){
    currentId   = $(this).data('id');
    currentMySt = ($(this).data('my-status') || 'pending').toString();
    currentDocSt= ($(this).data('doc-status')|| 'pending').toString();

    const url = showUrlTpl.replace('__ID__', currentId);
    const $m  = $('#approvalModal');
    $m.find('.modal-body').html('<div class="text-center text-muted">{{ trans('supply_chain_trans.loading') }}</div>');
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('approvalModal'));
    modal.show();

    // ✅ المنطق: إظهار/إخفاء الأزرار طبقًا للحالات
    const shouldHideActions = (currentDocSt !== 'pending') || (currentMySt !== 'pending');
    $('#btn-approve').toggle(!shouldHideActions);
    $('#btn-reject').toggle(!shouldHideActions);

    $.get(url, function(html){
      $m.find('.modal-body').html(html);
    }).fail(function(){
      $m.find('.modal-body').html('<div class="text-danger">{{ trans('supply_chain_trans.load_failed') }}</div>');
    });
  });

  // موافقة
  $('#btn-approve').on('click',function(){
    if(!currentId) return;
    const url = approveUrlTpl.replace('__ID__', currentId);
    $.post(url, {_token:$('meta[name="csrf-token"]').attr('content')})
      .done(function(resp){
        showToast(resp.message || '{{ trans('supply_chain_trans.approval_approved') }}', 'success');
        $('#approvalModal').modal('hide');
        table.ajax.reload(null,false);
      })
      .fail(function(xhr){
        showToast(xhr.responseJSON?.message || 'Error', 'danger');
      });
  });

  // رفض
  $('#btn-reject').on('click',function(){
    if(!currentId) return;
    const url = rejectUrlTpl.replace('__ID__', currentId);
    $.post(url, {_token:$('meta[name="csrf-token"]').attr('content')})
      .done(function(resp){
        showToast(resp.message || '{{ trans('supply_chain_trans.approval_rejected') }}', 'success');
        $('#approvalModal').modal('hide');
        table.ajax.reload(null,false);
      })
      .fail(function(xhr){
        showToast(xhr.responseJSON?.message || 'Error', 'danger');
      });
  });

})();
</script>

@endsection
