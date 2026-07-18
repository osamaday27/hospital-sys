@extends('layouts.master_table')

@section('title')
  {{ trans('assets.maintenance_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.maintenance_report_title') }}</a>
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
            <option value="reported_at">{{ trans('assets.date_on_reported') }}</option>
            <option value="started_at">{{ trans('assets.date_on_started') }}</option>
            <option value="finished_at">{{ trans('assets.date_on_finished') }}</option>
            <option value="next_due_date">{{ trans('assets.date_on_due') }}</option>
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
          <label class="form-label">{{ trans('assets.location') }}</label>
          <select id="f_location_ids" class="form-select select2" multiple>
            @foreach($filters['locations'] as $l)
              <option value="{{ $l->id }}">{{ $l->name_ar ?? $l->name_en }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_types') }}</label>
          <select id="f_types" class="form-select select2" multiple>
            @foreach($filters['types'] as $t)
              <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_priorities') }}</label>
          <select id="f_priorities" class="form-select select2" multiple>
            @foreach($filters['priorities'] as $p)
              <option value="{{ $p['value'] }}">{{ $p['label'] }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_technicians') }}</label>
          <select id="f_technicians" class="form-select select2" multiple>
            @foreach($filters['technicians'] as $t)
              <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
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
          <label class="form-label">{{ trans('assets.filter_vendors') }}</label>
          <select id="f_vendor_ids" class="form-select select2" multiple>
            @foreach($filters['vendors'] as $v)
              <option value="{{ $v->id }}">{{ $v->name_ar ?? $v->name_en }}</option>
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
          <h5 class="mb-0">{{ trans('assets.maintenance_report_title') }}</h5>

          <ul class="nav nav-pills" id="mntTabs">
            <li class="nav-item">
              <a class="nav-link active" data-tab="orders" href="javascript:void(0)">{{ trans('assets.tab_orders') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="tasks" href="javascript:void(0)">{{ trans('assets.tab_tasks') }}</a>
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
              <div class="kpi-title">{{ trans('assets.kpi_pm_compliance') }}</div>
              <div class="kpi-value" id="kpi-pm">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_mttr_hours') }}</div>
              <div class="kpi-value" id="kpi-mttr">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_downtime_hours') }}</div>
              <div class="kpi-value" id="kpi-downtime">—</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="kpi-card">
              <div class="kpi-title">{{ trans('assets.kpi_top_assets_cost') }}</div>
              <div class="kpi-value" id="kpi-topassets">—</div>
            </div>
          </div>
        </div>

        <div id="table-wrap">
          <table id="mnt-table" class="table dt-responsive nowrap align-middle" style="width:100%">
            <thead id="mnt-thead"></thead>
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
  let currentTab = 'orders';
  let dt;

  function headerHtml(tab){
    if (tab==='tasks') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_asset_code') }}</th>
          <th>{{ trans('assets.col_asset_name') }}</th>
          <th>{{ trans('assets.col_wo_code') }}</th>
          <th>{{ trans('assets.col_maint_type') }}</th>
          <th>{{ trans('assets.col_performed_at') }}</th>
          <th>{{ trans('assets.col_technician') }}</th>
          <th>{{ trans('assets.col_hours') }}</th>
          <th>{{ trans('assets.col_cost') }}</th>
          <th>{{ trans('assets.col_task_desc') }}</th>
        </tr>`;
    }
    // orders
    return `
      <tr>
        <th>#</th>
        <th>{{ trans('assets.col_asset_code') }}</th>
        <th>{{ trans('assets.col_asset_name') }}</th>
        <th>{{ trans('assets.col_maint_type') }}</th>
        <th>{{ trans('assets.col_wo_code') }}</th>
        <th>{{ trans('assets.col_reported_at') }}</th>
        <th>{{ trans('assets.col_started_at') }}</th>
        <th>{{ trans('assets.col_finished_at') }}</th>
        <th>{{ trans('assets.col_status') }}</th>
        <th>{{ trans('assets.col_priority') }}</th>
        <th>{{ trans('assets.col_duration_h') }}</th>
        <th>{{ trans('assets.col_mttr_h') }}</th>
        <th>{{ trans('assets.col_last_pm') }}</th>
        <th>{{ trans('assets.col_overdue') }}</th>
        <th>{{ trans('assets.col_technicians') }}</th>
        <th>{{ trans('assets.col_total_cost') }}</th>
      </tr>`;
  }

  function columnsFor(tab){
    const srCol = { data:null, orderable:false, searchable:false,
      render: (d,t,r,m)=> m.row + m.settings._iDisplayStart + 1
    };

    if (tab==='tasks') {
      return [
        srCol,
        {data:'asset_code'}, {data:'asset_name'}, {data:'wo_code'},
        {data:'type'}, {data:'performed_at'},
        {data:'technician'},
        {data:'hours', className:'text-end'},
        {data:'cost', className:'text-end'},
        {data:'task_desc'}
      ];
    }
    return [
      srCol,
      {data:'asset_code'}, {data:'asset_name'}, {data:'type'}, {data:'wo_code'},
      {data:'reported_at'},{data:'started_at'},{data:'finished_at'},
      {data:'status'},{data:'priority'},
      {data:'duration', className:'text-end'},
      {data:'mttr', className:'text-end'},
      {data:'last_pm'},{data:'overdue'},
      {data:'technicians'},
      {data:'cost_total', className:'text-end'}
    ];
  }

  function buildTable(tab){
    if ($.fn.DataTable.isDataTable('#mnt-table')) {
      $('#mnt-table').DataTable().clear().destroy();
    }
    const wrap = $('#table-wrap');
    wrap.find('thead').html(headerHtml(tab));
    $('#mnt-table tbody').empty();

    dt = $('#mnt-table').DataTable({
      processing:true, serverSide:true, searching:true, deferRender:true,
      lengthMenu:[[10,25,50,100,-1],[10,25,50,100,'{{ trans('assets.all') }}']],
      pageLength:10,
      ajax:{
        url:"{{ route('maintenance_report.index') }}",
        data:function(d){
          if (!filtersApplied) d.no_data = 1;
          d.tab         = tab;
          d.date_on     = $('#f_date_on').val();
          d.date_from   = $('#f_date_from').val();
          d.date_to     = $('#f_date_to').val();
          d.location_ids= $('#f_location_ids').val();
          d.types       = $('#f_types').val();
          d.priorities  = $('#f_priorities').val();
          d.technicians = $('#f_technicians').val();
          d.asset_ids   = $('#f_asset_ids').val();
          d.vendor_ids  = $('#f_vendor_ids').val();
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
    $('#mnt-table').off('xhr.dt').on('xhr.dt', function(e, settings, json){
      const hasData = json && Array.isArray(json.data) && json.data.length > 0;
      $('#no-data-tip').toggle(!hasData);
    });

    // أدوات
    $('#search-report').off('input').on('input', function(){ dt.search(this.value).draw(); });
    $('#reload-table').off('click').on('click', function(){ dt.ajax.reload(null,false); });
    $('#print-table').off('click').on('click', function(){ window.open(buildReportUrl('print'), 'mntPrint', 'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes'); });
    $('#export-excel').off('click').on('click', function(){ window.location.href = buildReportUrl('export_excel'); });
  }

  function buildReportUrl(action){
    const p = new URLSearchParams();
    p.set('action', action);
    p.set('tab', currentTab);
    p.set('date_on', $('#f_date_on').val());
    if ($('#f_date_from').val()) p.set('date_from', $('#f_date_from').val());
    if ($('#f_date_to').val())   p.set('date_to',   $('#f_date_to').val());
    ($('#f_location_ids').val()||[]).forEach(v=>p.append('location_ids[]',v));
    ($('#f_types').val()||[]).forEach(v=>p.append('types[]',v));
    ($('#f_priorities').val()||[]).forEach(v=>p.append('priorities[]',v));
    ($('#f_technicians').val()||[]).forEach(v=>p.append('technicians[]',v));
    ($('#f_asset_ids').val()||[]).forEach(v=>p.append('asset_ids[]',v));
    ($('#f_vendor_ids').val()||[]).forEach(v=>p.append('vendor_ids[]',v));
    const q = $('#search-report').val(); if (q) p.set('search[value]', q);
    return "{{ route('maintenance_report.index') }}?"+p.toString();
  }

  function fetchKpis(){
    const p = {
      action:'kpis',
      date_on: $('#f_date_on').val(),
      date_from: $('#f_date_from').val(),
      date_to: $('#f_date_to').val(),
      location_ids: $('#f_location_ids').val(),
      types: $('#f_types').val(),
      priorities: $('#f_priorities').val(),
      technicians: $('#f_technicians').val(),
      asset_ids: $('#f_asset_ids').val(),
      vendor_ids: $('#f_vendor_ids').val(),
    };
    $.get("{{ route('maintenance_report.index') }}", p, function(res){
      if (!res) return;
      $('#kpi-pm').text((res.pm_compliance_pct ?? 0)+'%');
      $('#kpi-mttr').text(res.mttr_hours ?? 0);
      $('#kpi-downtime').text(res.downtime_hours ?? 0);
      if (Array.isArray(res.top_assets) && res.top_assets.length){
        const top1 = res.top_assets[0];
        $('#kpi-topassets').text((top1.code || '') + ' — ' + (parseFloat(top1.total_cost).toFixed(2)));
      } else {
        $('#kpi-topassets').text('—');
      }
    });
  }

  // init
  buildTable(currentTab);

  // tabs
  $('#mntTabs .nav-link').on('click', function(){
    $('#mntTabs .nav-link').removeClass('active');
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
    $('#f_date_on').val('reported_at');
    $('#f_date_from,#f_date_to').val('');
    $('#f_location_ids,#f_types,#f_priorities,#f_technicians,#f_asset_ids,#f_vendor_ids').val(null).trigger('change');
    $('#search-report').val('');
    if (dt) { dt.search('').draw(); dt.ajax.reload(); }
    $('#no-data-tip').show();
    $('#kpi-pm,#kpi-mttr,#kpi-downtime,#kpi-topassets').text('—');
  });
});
</script>
@endsection
