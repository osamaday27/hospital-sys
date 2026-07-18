@extends('layouts.master_table')

@section('title')
  {{ trans('assets.master_movement_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.master_movement_title') }}</a>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<style>
  .select2-container{width:100%!important;max-width:100%!important}
  .select2-dropdown{z-index:2000}
  .sticky-filters{position:sticky;top:12px}
</style>

<div class="row">
  <!-- Filters (Right col-2) -->
  <div class="col-md-2">
    <div class="card sticky-filters">
      <div class="card-header">
        <h6 class="mb-0">{{ trans('assets.filters_title') }}</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_purchase_from') }}</label>
          <input type="date" id="f_purchase_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_purchase_to') }}</label>
          <input type="date" id="f_purchase_to" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.category') }}</label>
          <select id="f_category_ids" class="form-select select2" multiple>
            @foreach($filters['categories'] as $c)
              <option value="{{ $c->id }}">{{ $c->name_ar ?? $c->name_en }}</option>
            @endforeach
          </select>
        </div>
        {{-- <div class="mb-3">
          <label class="form-label">{{ trans('assets.brand') }}</label>
          <select id="f_brand_ids" class="form-select select2" multiple>
            @foreach($filters['brands'] as $b)
              <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
          </select>
        </div> --}}
        {{-- <div class="mb-3">
          <label class="form-label">{{ trans('assets.model') }}</label>
          <select id="f_model_ids" class="form-select select2" multiple>
            @foreach($filters['models'] as $m)
              <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
          </select>
        </div> --}}
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.location') }}</label>
          <select id="f_location_ids" class="form-select select2" multiple>
            @foreach($filters['locations'] as $l)
              <option value="{{ $l->id }}">{{ $l->name_ar ?? $l->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.store') }}</label>
          <select id="f_store_ids" class="form-select select2" multiple>
            @foreach($filters['stores'] as $s)
              <option value="{{ $s->id }}">{{ ($s->name_ar ?? $s->name_en ?? $s->id) }}{{ $s->manual_code ? ' - '.$s->manual_code : '' }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.supplier') }}</label>
          <select id="f_supplier_ids" class="form-select select2" multiple>
            @foreach($filters['suppliers'] as $s)
              <option value="{{ $s->id }}">{{ ($s->name_ar ?? $s->name_en ?? $s->id) }}{{ $s->code ? ' - '.$s->code : '' }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.status') }}</label>
          <select id="f_status" class="form-select select2" multiple>
            @foreach($filters['statuses'] as $s)
              <option value="{{ $s }}">{{ trans('assets.status_'.$s) }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.addition_source') }}</label>
          <select id="f_addition_source" class="form-select">
            <option value="">{{ trans('assets.any') }}</option>
            <option value="conversion">{{ trans('assets.source_conversion') }}</option>
            <option value="direct">{{ trans('assets.source_direct') }}</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.cost_range') }}</label>
          <div class="input-group">
            <input type="number" step="0.01" id="f_cost_min" class="form-control" placeholder="{{ trans('assets.min') }}">
            <input type="number" step="0.01" id="f_cost_max" class="form-control" placeholder="{{ trans('assets.max') }}">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.nbv_range') }}</label>
          <div class="input-group">
            <input type="number" step="0.01" id="f_nbv_min" class="form-control" placeholder="{{ trans('assets.min') }}">
            <input type="number" step="0.01" id="f_nbv_max" class="form-control" placeholder="{{ trans('assets.max') }}">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.qty_range') }}</label>
          <div class="input-group">
            <input type="number" step="1" id="f_qty_min" class="form-control" placeholder="{{ trans('assets.min') }}">
            <input type="number" step="1" id="f_qty_max" class="form-control" placeholder="{{ trans('assets.max') }}">
          </div>
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-primary" id="btn-apply-filter">
            <i class="ri-filter-3-line"></i> {{ trans('assets.apply_filters') }}
          </button>
          <button class="btn btn-light" id="btn-reset-filter">
            <i class="ri-refresh-line"></i> {{ trans('assets.reset_filters') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Table (Left col-10) -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('assets.master_movement_title') }}</h5>

        <div class="d-flex align-items-center gap-2">
          <input type="search" class="form-control" id="search-report"
                 placeholder="{{ trans('assets.search_placeholder') }}" style="width:280px;">
          <button class="btn btn-info" id="reload-table" title="{{ trans('assets.reload') }}">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button class="btn btn-secondary" id="print-table">
            <i class="ri-printer-line"></i> {{ trans('assets.print') }}
          </button>
          <button class="btn btn-success" id="export-excel">
            <i class="ri-file-excel-2-line"></i> {{ trans('assets.export_excel') }}
          </button>
        </div>
      </div>

      <div class="card-body">
        <table id="mmr-table" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>{{ trans('assets.col_code') }}</th>
              <th>{{ trans('assets.col_name') }}</th>
              <th>{{ trans('assets.col_category') }}</th>
              {{-- <th>{{ trans('assets.col_brand') }}</th> --}}
              {{-- <th>{{ trans('assets.col_model') }}</th> --}}
              <th>{{ trans('assets.col_serial') }}</th>
              <th>{{ trans('assets.col_location') }}</th>
              {{-- <th>{{ trans('assets.col_custodian') }}</th> --}}
              <th>{{ trans('assets.col_store') }}</th>
              <th>{{ trans('assets.col_supplier') }}</th>
              <th>{{ trans('assets.col_purchase_date') }}</th>
              <th>{{ trans('assets.col_cost') }}</th>
              <th>{{ trans('assets.col_status') }}</th>
              <th>{{ trans('assets.col_addition_source') }}</th>
              <th>{{ trans('assets.col_last_movement') }}</th>
              <th>{{ trans('assets.col_qty_total') }}</th>
              <th>{{ trans('assets.col_qty_assigned') }}</th>
              <th>{{ trans('assets.col_qty_available') }}</th>
              <th>{{ trans('assets.col_nbv') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        <div id="no-data-tip" class="text-muted small mt-2" style="display:none">
          {{ trans('assets.no_data_until_filter') }}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function(){
  $('.select2').select2({ theme:'bootstrap-5', width:'100%', placeholder:'{{ trans('assets.select_placeholder') }}' });
  let filtersApplied = false;

  var table = $('#mmr-table').DataTable({
    processing: true,
    serverSide: true,
    searching: true,
    lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'{{ trans('assets.all') }}']],
    pageLength: 10,
    ajax: {
      url: "{{ route('master_movement_report.index') }}",
      data: function(d){
        if (!filtersApplied) d.no_data = 1;
        d.purchase_from  = $('#f_purchase_from').val();
        d.purchase_to    = $('#f_purchase_to').val();
        d.category_ids   = $('#f_category_ids').val();
        // d.brand_ids      = $('#f_brand_ids').val();
        // d.model_ids      = $('#f_model_ids').val();
        d.location_ids   = $('#f_location_ids').val();
        d.store_ids      = $('#f_store_ids').val();
        d.supplier_ids   = $('#f_supplier_ids').val();
        d.status         = $('#f_status').val();
        d.addition_source= $('#f_addition_source').val();
        d.cost_min       = $('#f_cost_min').val();
        d.cost_max       = $('#f_cost_max').val();
        d.nbv_min        = $('#f_nbv_min').val();
        d.nbv_max        = $('#f_nbv_max').val();
        d.qty_min        = $('#f_qty_min').val();
        d.qty_max        = $('#f_qty_max').val();
      }
    },
    columns: [
      {data:'code'},{data:'name'},{data:'category'},
    //   {data:'brand'},{data:'model'},
      {data:'serial'},{data:'location'},
    //   {data:'custodian'},
      {data:'store'},
      {data:'supplier'},
      {data:'purchase_date'},{data:'cost', className:'text-end'},{data:'status'},{data:'addition_source'},
      {data:'last_movement'},{data:'qty_total', className:'text-end'},{data:'qty_assigned', className:'text-end'},
      {data:'qty_available', className:'text-end'},{data:'nbv', className:'text-end'}
    ],
    order: [], // ترتيب بالسيرفر
    language: {
      url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
    },
    initComplete: function(){ $('#no-data-tip').show(); }
  });

  // بحث عام
  $('#search-report').on('input', function(){ table.search(this.value).draw(); });

  // تطبيق الفلاتر
  $('#btn-apply-filter').on('click', function(){
    filtersApplied = true;
    $('#no-data-tip').hide();
    table.page.len(10).draw();
  });

  // إعادة ضبط
  $('#btn-reset-filter').on('click', function(){
    filtersApplied = false;
    $('#f_purchase_from,#f_purchase_to,#f_cost_min,#f_cost_max,#f_nbv_min,#f_nbv_max,#f_qty_min,#f_qty_max').val('');
    $('#f_category_ids,#f_brand_ids,#f_model_ids,#f_location_ids,#f_store_ids,#f_supplier_ids,#f_status').val(null).trigger('change');
    $('#f_addition_source').val('');
    $('#search-report').val('');
    table.search('').draw();
    table.ajax.reload();
    $('#no-data-tip').show();
  });

  // إعادة تحميل
  $('#reload-table').on('click', function(){ table.ajax.reload(); });

  // طباعة / اكسل
  function buildReportUrl(action){
    const p = new URLSearchParams();
    p.set('action', action);
    if ($('#f_purchase_from').val()) p.set('purchase_from', $('#f_purchase_from').val());
    if ($('#f_purchase_to').val())   p.set('purchase_to',   $('#f_purchase_to').val());
    ($('#f_category_ids').val()||[]).forEach(v=>p.append('category_ids[]',v));
    ($('#f_brand_ids').val()   ||[]).forEach(v=>p.append('brand_ids[]',v));
    ($('#f_model_ids').val()   ||[]).forEach(v=>p.append('model_ids[]',v));
    ($('#f_location_ids').val()||[]).forEach(v=>p.append('location_ids[]',v));
    ($('#f_store_ids').val()   ||[]).forEach(v=>p.append('store_ids[]',v));
    ($('#f_supplier_ids').val()||[]).forEach(v=>p.append('supplier_ids[]',v));
    ($('#f_status').val()      ||[]).forEach(v=>p.append('status[]',v));
    if ($('#f_addition_source').val()) p.set('addition_source',$('#f_addition_source').val());
    if ($('#f_cost_min').val()) p.set('cost_min',$('#f_cost_min').val());
    if ($('#f_cost_max').val()) p.set('cost_max',$('#f_cost_max').val());
    if ($('#f_nbv_min').val())  p.set('nbv_min',$('#f_nbv_min').val());
    if ($('#f_nbv_max').val())  p.set('nbv_max',$('#f_nbv_max').val());
    if ($('#f_qty_min').val())  p.set('qty_min',$('#f_qty_min').val());
    if ($('#f_qty_max').val())  p.set('qty_max',$('#f_qty_max').val());
    const q = $('#search-report').val(); if (q) p.set('search[value]', q);
    return "{{ route('master_movement_report.index') }}?"+p.toString();
  }

  $('#print-table').on('click', function(){
    const url = buildReportUrl('print');
    window.open(url, 'mmrPrint', 'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
  });

  $('#export-excel').on('click', function(){
    window.location.href = buildReportUrl('export_excel');
  });
});
</script>
@endsection
