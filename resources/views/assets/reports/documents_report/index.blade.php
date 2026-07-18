@extends('layouts.master_table')

@section('title')
  {{ trans('assets.documents_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.documents_report_title') }}</a>
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
  .kpi-card{background:#f8fbff;border:1px solid #d9e7f7;border-radius:8px;padding:12px}
  .kpi-title{font-size:12px;color:#245b8f;margin-bottom:6px;font-weight:600}
  .kpi-value{font-size:20px;font-weight:700}
  .text-end{text-align:right}
</style>

<div class="row">
  <!-- Filters -->
  <div class="col-md-2">
    <div class="card sticky-filters">
      <div class="card-header">
        <h6 class="mb-0">{{ trans('assets.filters_title') }}</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_date_from') }}</label>
          <input type="date" id="f_date_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_date_to') }}</label>
          <input type="date" id="f_date_to" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_doc_types') }}</label>
          <select id="f_doc_types" class="form-select select2" multiple>
            @foreach($filters['doc_types'] as $t)
              <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_suppliers') }}</label>
          <select id="f_supplier_ids" class="form-select select2" multiple>
            @foreach($filters['suppliers'] as $s)
              <option value="{{ $s->id }}">{{ $s->name_ar ?? $s->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.category') }}</label>
          <select id="f_category_ids" class="form-select select2" multiple>
            @foreach($filters['categories'] as $c)
              <option value="{{ $c->id }}">{{ $c->name_ar ?? $c->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.location') }}</label>
          <select id="f_location_ids" class="form-select select2" multiple>
            @foreach($filters['locations'] as $l)
              <option value="{{ $l->id }}">{{ $l->name_ar ?? $l->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_assets') }}</label>
          <select id="f_asset_ids" class="form-select select2" multiple>
            @foreach($filters['assets'] as $a)
              <option value="{{ $a->id }}">{{ $a->code }} — {{ $a->name_ar ?? $a->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_has_file') }}</label>
          <select id="f_has_file" class="form-select">
            <option value="">{{ trans('assets.any') }}</option>
            <option value="1">{{ trans('assets.doc_has_file') }}</option>
            <option value="0">{{ trans('assets.doc_no_file') }}</option>
          </select>
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

  <!-- KPIs + Table -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <h5 class="mb-0">{{ trans('assets.documents_report_title') }}</h5>

          <ul class="nav nav-pills" id="docTabs">
            <li class="nav-item">
              <a class="nav-link active" data-tab="lines" href="javascript:void(0)">{{ trans('assets.documents_tab_lines') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="by_type" href="javascript:void(0)">{{ trans('assets.tab_by_type') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="by_asset" href="javascript:void(0)">{{ trans('assets.tab_by_asset') }}</a>
            </li>
          </ul>
        </div>

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
        <!-- KPIs -->
        <div class="row g-3 mb-3" id="kpis-row">
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_docs_total') }}</div>
              <div class="kpi-value" id="kpi-total">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_with_attachment') }}</div>
              <div class="kpi-value" id="kpi-with-file">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_without_attachment') }}</div>
              <div class="kpi-value" id="kpi-without-file">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_total_cost') }}</div>
              <div class="kpi-value" id="kpi-cost">—</div>
            </div>
          </div>
        </div>

        <div id="table-wrap">
          <table id="docs-table" class="table dt-responsive nowrap align-middle" style="width:100%">
            <thead id="docs-thead"></thead>
            <tbody></tbody>
          </table>
        </div>
        <div id="no-data-tip" class="text-muted small mt-2" style="display:block">
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
  let currentTab = 'lines';
  let dt;

  function headerHtml(tab){
    if (tab==='by_type') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_doc_type') }}</th>
          <th>{{ trans('assets.col_docs_count') }}</th>
          <th>{{ trans('assets.col_with_file') }}</th>
          <th>{{ trans('assets.col_without_file') }}</th>
          <th>{{ trans('assets.col_total_cost') }}</th>
          <th>{{ trans('assets.col_avg_days_since') }}</th>
        </tr>`;
    } else if (tab==='by_asset') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_code') }}</th>
          <th>{{ trans('assets.col_name') }}</th>
          <th>{{ trans('assets.col_category') }}</th>
          <th>{{ trans('assets.col_location') }}</th>
          <th>{{ trans('assets.col_docs_count') }}</th>
          <th>{{ trans('assets.col_with_file') }}</th>
          <th>{{ trans('assets.col_total_cost') }}</th>
          <th>{{ trans('assets.col_latest_doc_date') }}</th>
        </tr>`;
    }
    return `
      <tr>
        <th>#</th>
        <th>{{ trans('assets.col_code') }}</th>
        <th>{{ trans('assets.col_name') }}</th>
        <th>{{ trans('assets.col_category') }}</th>
        <th>{{ trans('assets.col_location') }}</th>
        <th>{{ trans('assets.col_doc_type') }}</th>
        <th>{{ trans('assets.col_doc_no') }}</th>
        <th>{{ trans('assets.col_doc_date') }}</th>
        <th>{{ trans('assets.col_issuer') }}</th>
        <th>{{ trans('assets.col_cost') }}</th>
        <th>{{ trans('assets.col_has_file') }}</th>
        <th>{{ trans('assets.col_days_since') }}</th>
        <th>{{ trans('assets.col_notes') }}</th>
      </tr>`;
  }

  function columnsFor(tab){
    const srCol = { data:null, orderable:false, searchable:false,
      render: (d,t,r,m)=> m.row + m.settings._iDisplayStart + 1
    };

    if (tab==='by_type') {
      return [
        srCol,
        {data:'doc_type'},
        {data:'docs_count', className:'text-end'},
        {data:'with_file', className:'text-end'},
        {data:'without_file', className:'text-end'},
        {data:'total_cost', className:'text-end'},
        {data:'avg_days', className:'text-end'},
      ];
    } else if (tab==='by_asset') {
      return [
        srCol,
        {data:'code'}, {data:'name'}, {data:'category'}, {data:'location'},
        {data:'docs_count', className:'text-end'},
        {data:'with_file', className:'text-end'},
        {data:'total_cost', className:'text-end'},
        {data:'latest_date'}
      ];
    }
    return [
      srCol,
      {data:'ai_code'}, {data:'name'}, {data:'category'}, {data:'location'},
      {data:'doc_type'}, {data:'doc_no'}, {data:'doc_date'}, {data:'issuer'},
      {data:'cost', className:'text-end'},
      {data:'has_file'},
      {data:'days_since', className:'text-end'},
      {data:'notes'}
    ];
  }

  function buildTable(tab){
    if ($.fn.DataTable.isDataTable('#docs-table')) {
      $('#docs-table').DataTable().clear().destroy();
    }
    const wrap = $('#table-wrap');
    wrap.find('thead').html(headerHtml(tab));
    $('#docs-table tbody').empty();

    dt = $('#docs-table').DataTable({
      processing:true, serverSide:true, searching:true, deferRender:true,
      lengthMenu:[[10,25,50,100,-1],[10,25,50,100,'{{ trans('assets.all') }}']],
      pageLength:10,
      ajax:{
        url:"{{ route('documents_report.index') }}",
        data:function(d){
          if (!filtersApplied) d.no_data = 1;
          d.tab          = tab;
          d.date_from    = $('#f_date_from').val();
          d.date_to      = $('#f_date_to').val();
          d.doc_types    = $('#f_doc_types').val();
          d.supplier_ids = $('#f_supplier_ids').val();
          d.category_ids = $('#f_category_ids').val();
          d.location_ids = $('#f_location_ids').val();
          d.asset_ids    = $('#f_asset_ids').val();
          d.has_file     = $('#f_has_file').val();
        }
      },
      columns: columnsFor(tab),
      order: [],
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      }
    });

    $('#no-data-tip').toggle(!filtersApplied);
    $('#docs-table').off('xhr.dt').on('xhr.dt', function(e, settings, json){
      const hasData = json && Array.isArray(json.data) && json.data.length > 0;
      $('#no-data-tip').toggle(!hasData);
    });

    // أدوات
    $('#search-report').off('input').on('input', function(){ dt.search(this.value).draw(); });
    $('#reload-table').off('click').on('click', function(){ dt.ajax.reload(null,false); });
    $('#print-table').off('click').on('click', function(){ window.open(buildReportUrl('print'), 'docsPrint', 'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes'); });
    $('#export-excel').off('click').on('click', function(){ window.location.href = buildReportUrl('export_excel'); });
  }

  function buildReportUrl(action){
    const p = new URLSearchParams();
    p.set('action', action);
    p.set('tab', currentTab);
    if ($('#f_date_from').val()) p.set('date_from', $('#f_date_from').val());
    if ($('#f_date_to').val())   p.set('date_to',   $('#f_date_to').val());
    ($('#f_doc_types').val()   ||[]).forEach(v=>p.append('doc_types[]',v));
    ($('#f_supplier_ids').val()||[]).forEach(v=>p.append('supplier_ids[]',v));
    ($('#f_category_ids').val()||[]).forEach(v=>p.append('category_ids[]',v));
    ($('#f_location_ids').val()||[]).forEach(v=>p.append('location_ids[]',v));
    ($('#f_asset_ids').val()   ||[]).forEach(v=>p.append('asset_ids[]',v));
    const hf = $('#f_has_file').val(); if (hf !== '') p.set('has_file', hf);
    const q = $('#search-report').val(); if (q) p.set('search[value]', q);
    return "{{ route('documents_report.index') }}?"+p.toString();
  }

  function fetchKpis(){
    const p = {
      action:'kpis',
      tab: currentTab,
      date_from: $('#f_date_from').val(),
      date_to:   $('#f_date_to').val(),
      doc_types: $('#f_doc_types').val(),
      supplier_ids: $('#f_supplier_ids').val(),
      category_ids: $('#f_category_ids').val(),
      location_ids: $('#f_location_ids').val(),
      asset_ids:    $('#f_asset_ids').val(),
      has_file:     $('#f_has_file').val(),
    };
    $.get("{{ route('documents_report.index') }}", p, function(res){
      if (!res) return;
      $('#kpi-total').text(res.total_docs ?? 0);
      $('#kpi-with-file').text(res.with_attachment ?? 0);
      $('#kpi-without-file').text(res.without_attachment ?? 0);
      $('#kpi-cost').text(res.total_cost ?? 0);
    });
  }

  // init
  $('.select2').select2({ theme:'bootstrap-5', width:'100%' });
  buildTable(currentTab);

  // tabs
  $('#docTabs .nav-link').on('click', function(){
    $('#docTabs .nav-link').removeClass('active');
    $(this).addClass('active');
    currentTab = $(this).data('tab');
    buildTable(currentTab);
  });

  // filters
  $('#btn-apply-filter').on('click', function(){
    filtersApplied = true;
    if (dt) dt.ajax.reload();
    fetchKpis();
  });

  $('#btn-reset-filter').on('click', function(){
    filtersApplied = false;
    $('#f_date_from,#f_date_to').val('');
    $('#f_doc_types,#f_supplier_ids,#f_category_ids,#f_location_ids,#f_asset_ids').val(null).trigger('change');
    $('#f_has_file').val('');
    $('#search-report').val('');
    if (dt) { dt.search('').draw(); dt.ajax.reload(); }
    $('#no-data-tip').show();
    $('#kpi-total,#kpi-with-file,#kpi-without-file,#kpi-cost').text('—');
  });
});
</script>
@endsection
