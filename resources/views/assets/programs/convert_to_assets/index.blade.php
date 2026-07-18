@extends('layouts.master_table')

@section('title')
  {{ trans('assets.convert_to_assets.index_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- البانر: عنوان الصفحة والخبز --}}
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.convert_to_assets.index_title') }}</a>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

{{-- رسالة نجاح (عائمة) --}}
@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: auto; min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">
    {{ session('success') }}
  </div>
@endif

{{-- أدوات الجدول: بحث + فلاتر + تصدير + الذهاب للتحويلات السابقة --}}
<div class="row">
  <div class="col-lg-12">
    <div class="card shadow-sm">
      <div class="card-header">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
          {{-- يسار: أدوات البحث/الفلاتر --}}
          <div class="d-flex flex-wrap gap-2 align-items-center">
            <div class="input-group" style="min-width:280px">
              <span class="input-group-text"><i class="ri-search-line"></i></span>
              <input type="search" class="form-control" id="search-ri"
                     placeholder="{{ trans('assets.convert_to_assets.search_placeholder') }}">
            </div>

            <select id="filter-supplier" class="form-select" style="min-width:220px">
              <option value="">{{ trans('assets.convert_to_assets.all_suppliers') }}</option>
              @foreach($suppliers as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
              @endforeach
            </select>

            <select id="filter-store" class="form-select" style="min-width:220px">
              <option value="">{{ trans('assets.convert_to_assets.all_stores') }}</option>
              @foreach($stores as $st)
                <option value="{{ $st->id }}">{{ $st->name }}</option>
              @endforeach
            </select>

            <button id="reset-filters" class="btn btn-light border">
              <i class="ri-refresh-line"></i>
              <span class="ms-1">{{ trans('general.reset') ?? 'Reset' }}</span>
            </button>
          </div>

          {{-- يمين: أزرار الأدوات/التصدير/الذهاب للتحويلات --}}
          <div class="hstack gap-2">
            <a href="{{ route('assets.programs.asset_conversions.index') }}" class="btn btn-outline-dark">
              <i class="ri-history-line"></i>
              <span class="ms-1">{{ trans('assets.asset_conversions.index_title') }}</span>
            </a>
            <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
            <button class="btn btn-primary" id="export-csv"><i class="fa fa-file-csv"></i> CSV</button>
            <button class="btn btn-success" id="export-xlsx"><i class="fas fa-file-excel"></i> XLSX</button>
          </div>
        </div>

        <div class="small text-muted mt-2">
          <i class="ri-information-line"></i>
          {{ trans('assets.convert_to_assets.qty_base_hint') }}
        </div>
      </div>

      <div id="alert-container"></div>

      <div class="card-body">
        <table id="ri-table" class="table table-striped table-hover dt-responsive nowrap align-middle" style="width: 100%;">
          <thead class="table-light">
            <tr>
              <th>{{ trans('assets.convert_to_assets.col_sr') }}</th>
              <th>{{ trans('assets.convert_to_assets.col_code') }}</th>
              <th>{{ trans('assets.convert_to_assets.col_date') }}</th>
              <th>{{ trans('assets.convert_to_assets.col_supplier') }}</th>
              <th>{{ trans('assets.convert_to_assets.col_store') }}</th>
              <th>{{ trans('assets.convert_to_assets.col_status') }}</th>
              <th>{{ trans('assets.convert_to_assets.col_actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div><!-- end card-body -->
    </div><!-- end card -->
  </div><!-- end col-->
</div><!-- end row-->

{{-- Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function() {
  // Select2
  $('#filter-supplier, #filter-store').select2({
    theme: 'bootstrap-5',
    width: 'resolve',
    placeholder: ''
  });

  // DataTable (ServerSide)
  var table = $('#ri-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('assets.programs.convert_to_assets.index') }}",
      data: function (d) {
        d.supplier_id = $('#filter-supplier').val();
        d.store_id    = $('#filter-store').val();
      }
    },
    columns: [
      { data: 'rownum',   name: 'rownum', orderable: false, searchable: false, className: 'text-center' },
      { data: 'code',     name: 'code' },
      { data: 'date',     name: 'document_date', className: 'text-center' },
      { data: 'supplier', name: 'supplier.name_ar', orderable: false, searchable: false },
      { data: 'store',    name: 'store.name_ar', orderable: false, searchable: false },
      { data: 'status',   name: 'status', orderable: false, searchable: false, className: 'text-center' },
      { data: 'action',   name: 'action', orderable: false, searchable: false, className: 'text-center' },
    ],
    order: [[2,'desc']], // ترتيب حسب التاريخ تنازليًا (يعتمد على الـ server-side)
    lengthMenu: [[10,25,50,100],[10,25,50,100]],
    pageLength: 10,
    dom: 'Bfrtip',
    buttons: [
      { extend: 'csv',   title: 'Receiving Approved', text: '<i class="fas fa-file-csv"></i> CSV',   className: 'btn btn-outline-primary' },
      { extend: 'excel', title: 'Receiving Approved', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
    ]
  });

  // بحث عام
  $('#search-ri').on('input', function(){ table.search(this.value).draw(); });

  // فلاتر
  $('#filter-supplier, #filter-store').on('change', function(){ table.ajax.reload(); });

  // إعادة تعيين الفلاتر
  $('#reset-filters').on('click', function(){
    $('#search-ri').val('');
    $('#filter-supplier').val(null).trigger('change');
    $('#filter-store').val(null).trigger('change');
    table.search('').draw();
    table.ajax.reload();
  });

  // إعادة تحميل
  $('#reload-table').on('click', function(){ table.ajax.reload(null, false); });

  // تصدير
  $('#export-csv').on('click', function(){ table.button('.buttons-csv').trigger(); });
  $('#export-xlsx').on('click', function(){ table.button('.buttons-excel').trigger(); });
});
</script>
@endsection
