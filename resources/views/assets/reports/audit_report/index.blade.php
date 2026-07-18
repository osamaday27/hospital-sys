@extends('layouts.master_table')

@section('title')
  {{ trans('assets.audit_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.audit_report_title') }}</a>
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
          <label class="form-label">{{ trans('assets.filter_date_on') }}</label>
          <select id="f_date_on" class="form-select">
            <option value="finished_at">{{ trans('assets.audit_date_on_finished') }}</option>
            <option value="started_at">{{ trans('assets.audit_date_on_started') }}</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_date_from') }}</label>
          <input type="date" id="f_date_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_date_to') }}</label>
          <input type="date" id="f_date_to" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_audit_codes') }}</label>
          <select id="f_audit_codes" class="form-select select2" multiple>
            @foreach($filters['audits'] as $a)
              <option value="{{ $a->code }}">{{ $a->code }}{{ $a->finished_at ? ' — '.\Carbon\Carbon::parse($a->finished_at)->format('Y-m-d') : '' }}</option>
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
          <label class="form-label">{{ trans('assets.filter_results') }}</label>
          <select id="f_results" class="form-select select2" multiple>
            @foreach($filters['results'] as $r)
              <option value="{{ $r['value'] }}">{{ $r['label'] }}</option>
            @endforeach
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
          <h5 class="mb-0">{{ trans('assets.audit_report_title') }}</h5>

          <ul class="nav nav-pills" id="auditTabs">
            <li class="nav-item">
              <a class="nav-link active" data-tab="lines" href="javascript:void(0)">{{ trans('assets.tab_lines_audits') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="audits" href="javascript:void(0)">{{ trans('assets.tab_audits') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="unreviewed" href="javascript:void(0)">{{ trans('assets.tab_unreviewed') }}</a>
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
              <div class="kpi-title">{{ trans('assets.kpi_audit_compliance') }}</div>
              <div class="kpi-value" id="kpi-compliance">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_missing_count') }}</div>
              <div class="kpi-value" id="kpi-missing">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_missing_value') }}</div>
              <div class="kpi-value" id="kpi-missing-value">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_unreviewed') }}</div>
              <div class="kpi-value" id="kpi-unreviewed">—</div>
            </div>
          </div>
        </div>

        <div id="table-wrap">
          <table id="audit-table" class="table dt-responsive nowrap align-middle" style="width:100%">
            <thead id="audit-thead"></thead>
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
    if (tab==='audits') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_audit_code') }}</th>
          <th>{{ trans('assets.col_title') }}</th>
          <th>{{ trans('assets.col_status') }}</th>
          <th>{{ trans('assets.col_started_at') }}</th>
          <th>{{ trans('assets.col_finished_at') }}</th>
          <th>{{ trans('assets.col_expected_count') }}</th>
          <th>{{ trans('assets.col_scanned_count') }}</th>
          <th>{{ trans('assets.col_matched_count') }}</th>
          <th>{{ trans('assets.col_mismatch_count') }}</th>
          <th>{{ trans('assets.col_missing_count') }}</th>
          <th>{{ trans('assets.col_extra_count') }}</th>
          <th>{{ trans('assets.col_compliance') }}</th>
        </tr>`;
    } else if (tab==='unreviewed') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_asset_code') }}</th>
          <th>{{ trans('assets.col_asset_name') }}</th>
          <th>{{ trans('assets.col_category') }}</th>
          <th>{{ trans('assets.col_location') }}</th>
          <th>{{ trans('assets.col_status') }}</th>
          <th>{{ trans('assets.col_nbv') }}</th>
        </tr>`;
    }
    // lines
    return `
      <tr>
        <th>#</th>
        <th>{{ trans('assets.col_audit_code') }}</th>
        <th>{{ trans('assets.col_asset_code') }}</th>
        <th>{{ trans('assets.col_asset_name') }}</th>
        <th>{{ trans('assets.col_category') }}</th>
        <th>{{ trans('assets.col_exp_location') }}</th>
        <th>{{ trans('assets.col_found_location') }}</th>
        <th>{{ trans('assets.col_result') }}</th>
        <th>{{ trans('assets.col_scan_count') }}</th>
        <th>{{ trans('assets.col_last_scanned_at') }}</th>
        <th>{{ trans('assets.col_user') }}</th>
        <th>{{ trans('assets.col_nbv_at_audit') }}</th>
        <th>{{ trans('assets.col_notes') }}</th>
      </tr>`;
  }

  function columnsFor(tab){
    const srCol = { data:null, orderable:false, searchable:false,
      render: (d,t,r,m)=> m.row + m.settings._iDisplayStart + 1
    };

    if (tab==='audits') {
      return [
        srCol,
        {data:'code'}, {data:'title'}, {data:'status'},
        {data:'started_at'}, {data:'finished_at'},
        {data:'expected_count', className:'text-end'},
        {data:'scanned_count', className:'text-end'},
        {data:'matched_count', className:'text-end'},
        {data:'mismatch_count', className:'text-end'},
        {data:'missing_count', className:'text-end'},
        {data:'extra_count', className:'text-end'},
        {data:'compliance', className:'text-end'},
      ];
    } else if (tab==='unreviewed') {
      return [
        srCol,
        {data:'asset_code'}, {data:'asset_name'}, {data:'category'}, {data:'location'},
        {data:'status'}, {data:'nbv', className:'text-end'}
      ];
    }
    return [
      srCol,
      {data:'audit_code'}, {data:'asset_code'}, {data:'asset_name'}, {data:'category'},
      {data:'exp_loc'}, {data:'found_loc'}, {data:'result'},
      {data:'scan_count', className:'text-end'},
      {data:'last_scan'}, {data:'user'},
      {data:'nbv', className:'text-end'},
      {data:'notes'}
    ];
  }

  function buildTable(tab){
    if ($.fn.DataTable.isDataTable('#audit-table')) {
      $('#audit-table').DataTable().clear().destroy();
    }
    const wrap = $('#table-wrap');
    wrap.find('thead').html(headerHtml(tab));
    $('#audit-table tbody').empty();

    dt = $('#audit-table').DataTable({
      processing:true, serverSide:true, searching:true, deferRender:true,
      lengthMenu:[[10,25,50,100,-1],[10,25,50,100,'{{ trans('assets.all') }}']],
      pageLength:10,
      ajax:{
        url:"{{ route('audit_report.index') }}",
        data:function(d){
          if (!filtersApplied) d.no_data = 1;
          d.tab          = tab;
          d.date_on      = $('#f_date_on').val();
          d.date_from    = $('#f_date_from').val();
          d.date_to      = $('#f_date_to').val();
          d.audit_codes  = $('#f_audit_codes').val();
          d.category_ids = $('#f_category_ids').val();
          d.location_ids = $('#f_location_ids').val();
          d.results      = $('#f_results').val();
        }
      },
      columns: columnsFor(tab),
      order: [],
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      }
    });

    // no-data tip
    $('#no-data-tip').toggle(!filtersApplied);
    $('#audit-table').off('xhr.dt').on('xhr.dt', function(e, settings, json){
      const hasData = json && Array.isArray(json.data) && json.data.length > 0;
      $('#no-data-tip').toggle(!hasData);
    });

    // أدوات
    $('#search-report').off('input').on('input', function(){ dt.search(this.value).draw(); });
    $('#reload-table').off('click').on('click', function(){ dt.ajax.reload(null,false); });
    $('#print-table').off('click').on('click', function(){ window.open(buildReportUrl('print'), 'auditPrint', 'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes'); });
    $('#export-excel').off('click').on('click', function(){ window.location.href = buildReportUrl('export_excel'); });
  }

  function buildReportUrl(action){
    const p = new URLSearchParams();
    p.set('action', action);
    p.set('tab', currentTab);
    p.set('date_on', $('#f_date_on').val());
    if ($('#f_date_from').val()) p.set('date_from', $('#f_date_from').val());
    if ($('#f_date_to').val())   p.set('date_to',   $('#f_date_to').val());
    ($('#f_audit_codes').val()||[]).forEach(v=>p.append('audit_codes[]',v));
    ($('#f_category_ids').val()||[]).forEach(v=>p.append('category_ids[]',v));
    ($('#f_location_ids').val()||[]).forEach(v=>p.append('location_ids[]',v));
    ($('#f_results').val()||[]).forEach(v=>p.append('results[]',v));
    const q = $('#search-report').val(); if (q) p.set('search[value]', q);
    return "{{ route('audit_report.index') }}?"+p.toString();
  }

  function fetchKpis(){
    const p = {
      action:'kpis',
      tab: currentTab,
      date_on: $('#f_date_on').val(),
      date_from: $('#f_date_from').val(),
      date_to: $('#f_date_to').val(),
      audit_codes: $('#f_audit_codes').val(),
      category_ids: $('#f_category_ids').val(),
      location_ids: $('#f_location_ids').val(),
      results: $('#f_results').val(),
    };
    $.get("{{ route('audit_report.index') }}", p, function(res){
      if (!res) return;
      $('#kpi-compliance').text((res.compliance_pct ?? 0)+'%');
      $('#kpi-missing').text(res.missing_count ?? 0);
      $('#kpi-missing-value').text(res.missing_value ?? 0);
      $('#kpi-unreviewed').text(res.unreviewed ?? 0);
    });
  }

  // init
  buildTable(currentTab);

  // tabs
  $('#auditTabs .nav-link').on('click', function(){
    $('#auditTabs .nav-link').removeClass('active');
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
    $('#f_date_on').val('finished_at');
    $('#f_date_from,#f_date_to').val('');
    $('#f_audit_codes,#f_category_ids,#f_location_ids,#f_results').val(null).trigger('change');
    $('#search-report').val('');
    if (dt) { dt.search('').draw(); dt.ajax.reload(); }
    $('#no-data-tip').show();
    $('#kpi-compliance,#kpi-missing,#kpi-missing-value,#kpi-unreviewed').text('—');
  });
});
</script>
@endsection
