@extends('layouts.master_table')
@section('title', __('assets.brands'))
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">@lang('assets.brands')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success text-center" style="position: fixed; top: 20px; left:50%; transform: translateX(-50%); z-index:9999;">
    {{ session('success') }}
  </div>
  <script>$(function(){ setTimeout(function(){ $('.alert-success').fadeOut(600,function(){ $(this).remove(); }); }, 3000); });</script>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <input type="search" id="search" class="form-control" placeholder="@lang('assets.search')" style="width:250px;">
    <div class="hstack gap-2">
      <a href="{{ route('assets.settings.brands.create') }}" class="btn btn-outline-secondary"><i class="ri-add-line"></i> @lang('assets.create')</a>
      <button id="reload" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
    </div>
  </div>
  <div class="card-body">
    <table id="tbl" class="table dt-responsive nowrap align-middle w-100 text-center">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>@lang('assets.code')</th>
          <th>@lang('assets.name')</th>
          <th>@lang('assets.status')</th>
          <th>@lang('assets.actions')</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

{{-- مودال العرض --}}
<div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">@lang('assets.brands')</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="showModalBody">
        <div class="text-center text-muted my-4">... @lang('assets.loading') ...</div>
      </div>
    </div>
  </div>
</div>

{{-- مودال الحذف --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger-subtle">
        <h6 class="modal-title"><i class="ri-alert-line me-1 text-danger"></i> @lang('assets.delete_confirm')</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">@lang('assets.brand_delete_warning')</div>
        <div class="p-2 bg-light rounded border">
          <div class="small text-muted">@lang('assets.name')</div>
          <div id="delName" class="fw-semibold">-</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">@lang('assets.cancel')</button>
        <button class="btn btn-danger" id="confirm-delete"><i class="ri-delete-bin-6-line"></i> @lang('assets.delete')</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function(){
  const t = $('#tbl').DataTable({
    processing:true, serverSide:true,
    ajax:{ url:'{{ route('assets.settings.brands.ajax') }}' },
    columns:[
      {data:'DT_RowIndex',orderable:false,searchable:false},
      {data:'code'},
      {data:'name'},
      {data:'is_active'},
      {data:'action',orderable:false,searchable:false}
    ],
    order:[[1,'asc']],
    language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'}
  });

  $('#reload').on('click',()=>t.ajax.reload(null,false));
  $('#search').on('input',function(){ t.search(this.value).draw(); });

  let delId=null;

  $(document).on('click','.js-show', async function(){
    const id=$(this).data('id');
    const url="{{ route('assets.settings.brands.show', ':id') }}".replace(':id', id);
    $('#showModalBody').html('<div class="text-center text-muted my-4">... @lang("assets.loading") ...</div>');
    const res = await fetch(url+'?modal=1', {headers:{'Accept':'application/json'}});
    const j = res.ok ? await res.json() : {html:'<div class="text-danger">@lang("assets.load_error")</div>'};
    $('#showModalBody').html(j.html || '');
    new bootstrap.Modal(document.getElementById('showModal')).show();
  });

  $(document).on('click','.js-del', function(){
    delId=$(this).data('id');
    $('#delName').text($(this).data('name')||'-');
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
  });

  $('#confirm-delete').on('click', async function(){
    if(!delId) return;
    const url="{{ route('assets.settings.brands.destroy', ':id') }}".replace(':id', delId);
    const res = await fetch(url,{
      method:'DELETE',
      headers:{
        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'),
        'Accept':'application/json'
      }
    });
    if(res.ok){
      bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
      t.ajax.reload(null,false);
      alert('@lang("assets.deleted_success")');
    }
  });
});
</script>
@endsection
