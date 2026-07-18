@extends('layouts.master_table')

@section('title')
  {{ trans('assets.asset_conversions.index_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item">
            <a href="{{ route('assets.programs.convert_to_assets.index') }}">{{ trans('assets.convert_to_assets.index_title') }}</a>
          </li>
          <li class="breadcrumb-item active">
            {{ trans('assets.asset_conversions.index_title') }}
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: auto; min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">
    {{ session('success') }}
  </div>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <input type="search" class="form-control" id="search-conv" placeholder="{{ trans('assets.asset_conversions.search_placeholder') }}" style="width: 250px;">
        <div class="hstack gap-2">
          <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
          <button class="btn btn-primary" id="export-csv"><i class="fa fa-file-csv"></i> CSV</button>
          <button class="btn btn-success" id="export-xlsx"><i class="fas fa-file-excel"></i> XLSX</button>
        </div>
      </div>

      <div class="card-body">
        <table id="conv-table" class="table dt-responsive nowrap align-middle" style="width: 100%;">
          <thead>
            <tr>
              <th>{{ trans('assets.asset_conversions.col_sr') }}</th>
              <th>{{ trans('assets.asset_conversions.col_product') }}</th>
              <th>{{ trans('assets.asset_conversions.col_store') }}</th>
              <th>{{ trans('assets.asset_conversions.col_batch') }}</th>
              <th>{{ trans('assets.asset_conversions.col_expiry') }}</th>
              <th>{{ trans('assets.asset_conversions.col_qty') }}</th>
              <th>{{ trans('assets.asset_conversions.col_status') }}</th>
              <th>{{ trans('assets.asset_conversions.col_actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
$(function(){
  var table = $('#conv-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('assets.programs.asset_conversions.index') }}",
    columns: [
      { data: 'rownum',  name: 'rownum', orderable:false, searchable:false },
      { data: 'product', name: 'product', orderable:false, searchable:false },
      { data: 'store',   name: 'store',   orderable:false, searchable:false },
      { data: 'batch',   name: 'batch_no' },
      { data: 'expiry',  name: 'expiry_date' },
      { data: 'qty',     name: 'qty_in_base' },
      { data: 'status',  name: 'status', orderable:false, searchable:false },
      { data: 'action',  name: 'action', orderable:false, searchable:false },
    ],
    dom: 'Bfrtip',
    buttons: [
      { extend: 'csv', title: 'Asset Conversions', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
      { extend: 'excel', title: 'Asset Conversions', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
    ]
  });

  $('#search-conv').on('input', function(){ table.search(this.value).draw(); });
  $('#reload-table').on('click', function(){ table.ajax.reload(null, false); });
  $('#export-csv').on('click', function(){ table.button('.buttons-csv').trigger(); });
  $('#export-xlsx').on('click', function(){ table.button('.buttons-excel').trigger(); });
});
</script>
@endsection
