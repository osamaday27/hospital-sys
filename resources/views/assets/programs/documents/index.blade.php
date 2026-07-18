@extends('layouts.master_table')
@section('title', __('assets.documents'))
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

@include('partials.alert-success')
@includeWhen($errors->any(),'partials.alert-errors')

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">@lang('assets.documents')</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="card">

      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-2 flex-wrap">
          <input type="search" id="search-docs" class="form-control" style="width:250px;"
                 placeholder="@lang('assets.search')">

          {{-- فلتر الأصل --}}
          <select id="assetFilter" class="form-select" style="min-width:260px;max-width:100%">
            <option value="">@lang('assets.all_assets')</option>
            @foreach($assets as $a)
              <option value="{{ $a->id }}">{{ $a->code }} — {{ $a->name_ar ?? $a->name_en }}</option>
            @endforeach
          </select>

          {{-- فلتر المورد --}}
          <select id="supplierFilter" class="form-select" style="min-width:260px;max-width:100%">
            <option value="">@lang('assets.all_suppliers')</option>
            @foreach(($suppliers ?? []) as $sp)
              <option value="{{ $sp->id }}">{{ $sp->code ? $sp->code.' — ' : '' }}{{ $sp->name_ar ?? $sp->name_en }}</option>
            @endforeach
          </select>

          <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
          <button id="export-csv"  class="btn btn-primary"><i class="fa fa-file-csv"></i> CSV</button>
          <button id="export-xlsx" class="btn btn-success"><i class="fas fa-file-excel"></i> XLSX</button>
        </div>

        <a href="{{ route('assets.programs.documents.create') }}" class="btn btn-outline-secondary">
          <i class="ri-add-line"></i> @lang('assets.create')
        </a>
      </div>

      <div id="alert-container" class="m-2"></div>

      <div class="card-body">
        <table id="docs-table" class="table dt-responsive nowrap align-middle w-100 text-center">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>@lang('assets.asset')</th>
              <th>@lang('assets.type')</th>
              <th>@lang('assets.doc_no')</th>
              <th>@lang('assets.doc_date')</th>
              <th>@lang('assets.cost')</th>
              <th>@lang('assets.file')</th>
              <th>@lang('assets.actions')</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="showModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">@lang('assets.documents')</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="showModalBody">
        <div class="text-center text-muted my-4">@lang('assets.loading')</div>
      </div>
    </div>
  </div>
</div>

@include('components.modals.delete-modal')
{{-- 
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script> --}}

<script>
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF } });

  $(function(){
    const t = $('#docs-table').DataTable({
      processing:true, serverSide:true,
      ajax:{
        url:'{{ route('assets.programs.documents.ajax') }}',
        data: d => {
          d.asset_id    = $('#assetFilter').val()    || '';
          d.supplier_id = $('#supplierFilter').val() || '';
        }
      },
      columns:[
        {data:'DT_RowIndex',orderable:false,searchable:false},
        {data:'asset'},{data:'doc_type'},{data:'doc_no'},{data:'doc_date'},{data:'cost'},
        {data:'file',orderable:false,searchable:false},
        {data:'action',orderable:false,searchable:false}
      ],
      order:[[0,'desc']],
      dom: 'Bfrtip',
      buttons: [
        { extend:'csvHtml5',   text:'<i class="fas fa-file-csv"></i> CSV',   className:'btn btn-outline-primary' },
        { extend:'excelHtml5', text:'<i class="fas fa-file-excel"></i> XLSX', className:'btn btn-outline-success' },
        { extend:'print',      text:'<i class="ri-printer-line"></i> @lang("assets.print")', className:'btn btn-outline-secondary' }
      ],
      language:{ url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json' }
    });

    $('#reload-table').on('click', ()=> t.ajax.reload(null,false));
    $('#search-docs').on('input', function(){ t.search(this.value).draw(); });
    $('#assetFilter, #supplierFilter').on('change',()=> t.ajax.reload(null,false));
    $('#export-csv').on('click',  ()=> t.button('.buttons-csv').trigger());
    $('#export-xlsx').on('click', ()=> t.button('.buttons-excel').trigger());

    // عرض التفاصيل
    $(document).on('click','.js-show, .view-btn', async function(){
      const id = $(this).data('id');
      const url = "{{ route('assets.programs.documents.show', ':id') }}".replace(':id', id);
      $('#showModalBody').html('<div class="text-center text-muted my-4">@lang("assets.loading")</div>');
      const res = await fetch(url+'?modal=1',{ headers:{ 'Accept':'application/json' }});
      const j = res.ok ? await res.json() : {html:'<div class="text-danger">@lang("assets.load_error")</div>'};
      $('#showModalBody').html(j.html || '<div class="text-danger">@lang("assets.no_content")</div>');
      new bootstrap.Modal('#showModal').show();
    });

    // حذف — أضفنا CSRF Header مع fetch لحل المشكلة
    $(document).on('click','.js-del', function(){
      $('#delete-id').val($(this).data('id'));
      $('#deleteModal').modal('show');
    });

    $('#delete-record').on('click', async function(){
      const id  = $('#delete-id').val();
      const url = '{{ route("assets.programs.documents.destroy", ":id") }}'.replace(':id', id);
      const res = await fetch(url, { method:'DELETE', headers:{ 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF } });
      if (res.ok) {
        $('#deleteModal').modal('hide');
        showAlert('success', '@lang("assets.deleted_success")');
        t.ajax.reload(null,false);
      } else {
        showAlert('danger', '@lang("assets.delete_failed")');
      }
    });

    function showAlert(type,msg){
      $('#alert-container').html(
        `<div class="alert alert-${type} alert-dismissible fade show">
           ${msg}
           <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>`
      );
      setTimeout(()=>$('.alert').alert('close'),5000);
    }
  });
</script>
@endsection
