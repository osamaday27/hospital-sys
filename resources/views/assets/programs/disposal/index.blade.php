@extends('layouts.master_table')
@section('title') {{ __('assets.disposal.index_title') }} @stop

@section('content')
@include('assets.programs.disposal.flash')

{{-- ====== Breadcrumb ====== --}}
<div class="row mb-3">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="#">{{ __('الأصول') }}</a></li>
          <li class="breadcrumb-item active">{{ __('طلبات التخلص/الشطب') }}</li>
        </ol>
      </div>
      <a href="{{ route('assets.programs.disposal.create') }}" class="btn btn-success">
        <i class="ri-add-line"></i> {{ __('assets.disposal.add_request') }}
      </a>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex gap-2 align-items-center">
    <input type="search" id="search-disposals" class="form-control" style="width:250px;"
           placeholder="{{ __('ابحث...') }}">
    <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
    <button id="export-csv"  class="btn btn-primary"><i class="fa fa-file-csv"></i> CSV</button>
    <button id="export-xlsx" class="btn btn-success"><i class="fas fa-file-excel"></i> XLSX</button>
  </div>
  <div class="card-body">
    <table id="tbl" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('assets.disposal.request_no') }}</th>
          <th>{{ __('assets.disposal.method') }}</th>
          <th>{{ __('assets.disposal.status') }}</th>
          <th>{{ __('assets.disposal.items') }}</th>
          <th>{{ __('assets.disposal.requested_at') }}</th>
          <th>{{ __('assets.actions') }}</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

{{-- DataTables Buttons --}}
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script> --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script> --}}

<script>
$(function(){
  const t = $('#tbl').DataTable({
    processing:true, serverSide:true, searching:true,
    ajax:'{{ route("assets.programs.disposal.ajax.index") }}',
    columns:[
      {data:'DT_RowIndex', orderable:false, searchable:false},
      {data:'request_no'}, {data:'method'}, {data:'status'},
      {data:'items'}, {data:'requested_at'},
      {data:'action', orderable:false, searchable:false},
    ],
    order:[[1,'desc']],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'csvHtml5',   text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-outline-primary' },
      { extend: 'excelHtml5', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
    ],
  });

  $('#reload-table').on('click', () => t.ajax.reload(null, false));
  $('#search-disposals').on('input', function(){ t.search(this.value).draw(); });
  $('#export-csv').on('click',  () => t.button('.buttons-csv').trigger());
  $('#export-xlsx').on('click', () => t.button('.buttons-excel').trigger());
});
</script>
@endsection
