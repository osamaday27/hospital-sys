@extends('layouts.master_table')

@section('title')
  {{ trans('assets.disposal_lifecycle_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.disposal_lifecycle_report_title') }}</a>
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
          <label class="form-label">{{ trans('assets.filter_status') }}</label>
          <select id="f_status" class="form-select select2" multiple>
            @foreach($filters['statuses'] as $s)
              <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_method') }}</label>
          <select id="f_methods" class="form-select select2" multiple>
            @foreach($filters['methods'] as $m)
              <option value="{{ $m }}">{{ $m }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_reason') }}</label>
          <select id="f_reasons" class="form-select select2" multiple>
            @foreach($filters['reasons'] as $r)
              <option value="{{ $r }}">{{ $r }}</option>
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

  <!-- Table & Toolbar -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <h5 class="mb-0">{{ trans('assets.disposal_lifecycle_report_title') }}</h5>

          <ul class="nav nav-pills" id="dlTabs">
            <li class="nav-item">
              <a class="nav-link active" data-tab="requests" href="javascript:void(0)">{{ trans('assets.tab_requests') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="executions" href="javascript:void(0)">{{ trans('assets.tab_executions') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="approvals" href="javascript:void(0)">{{ trans('assets.tab_approvals') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="by_reason" href="javascript:void(0)">{{ trans('assets.tab_by_reason') }}</a>
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
        <div id="table-wrap">
          <table id="dl-table" class="table dt-responsive nowrap align-middle" style="width:100%">
            <thead id="dl-thead"></thead>
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
  let currentTab = 'requests';
  let dt;

  function headerHtml(tab){
    if (tab === 'executions') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_request_no') }}</th>
          <th>{{ trans('assets.col_method') }}</th>
          <th>{{ trans('assets.col_reason') }}</th>
          <th>{{ trans('assets.col_executed_at') }}</th>
          <th>{{ trans('assets.col_assets_count') }}</th>
          <th>{{ trans('assets.col_nbv_total') }}</th>
          <th>{{ trans('assets.col_proceeds') }}</th>
          <th>{{ trans('assets.col_profit') }}</th>
        </tr>`;
    } else if (tab === 'approvals') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_request_no') }}</th>
          <th>{{ trans('assets.col_level') }}</th>
          <th>{{ trans('assets.col_role') }}</th>
          <th>{{ trans('assets.col_assignee') }}</th>
          <th>{{ trans('assets.col_approver') }}</th>
          <th>{{ trans('assets.col_decision') }}</th>
          <th>{{ trans('assets.col_decided_at') }}</th>
        </tr>`;
    } else if (tab === 'by_reason') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_reason') }}</th>
          <th>{{ trans('assets.col_ops_count') }}</th>
          <th>{{ trans('assets.col_nbv_total') }}</th>
          <th>{{ trans('assets.col_proceeds') }}</th>
          <th>{{ trans('assets.col_profit') }}</th>
        </tr>`;
    }
    // requests
    return `
      <tr>
        <th>#</th>
        <th>{{ trans('assets.col_request_no') }}</th>
        <th>{{ trans('assets.col_status') }}</th>
        <th>{{ trans('assets.col_requested_at') }}</th>
        <th>{{ trans('assets.col_method') }}</th>
        <th>{{ trans('assets.col_reason') }}</th>
        <th>{{ trans('assets.col_created_by') }}</th>
        <th>{{ trans('assets.col_assets_count') }}</th>
        <th>{{ trans('assets.col_qty') }}</th>
        <th>{{ trans('assets.col_nbv_total') }}</th>
        <th>{{ trans('assets.col_proceeds') }}</th>
        <th>{{ trans('assets.col_profit') }}</th>
      </tr>`;
  }

  function columnsFor(tab){
    const srCol = { data:null, orderable:false, searchable:false,
      render:(d,t,r,m)=> m.row + m.settings._iDisplayStart + 1
    };
    if (tab === 'executions') {
      return [srCol,
        {data:'request_no'},{data:'method'},{data:'reason'},{data:'executed_at'},
        {data:'assets_count', className:'text-end'},
        {data:'nbv_total', className:'text-end'},
        {data:'proceeds', className:'text-end'},
        {data:'profit', className:'text-end'}
      ];
    } else if (tab === 'approvals') {
      return [srCol,
        {data:'request_no'},{data:'level'},{data:'role'},{data:'assignee'},{data:'approver'},
        {data:'decision'},{data:'decided_at'}
      ];
    } else if (tab === 'by_reason') {
      return [srCol,
        {data:'reason'},
        {data:'ops_count', className:'text-end'},
        {data:'nbv_total', className:'text-end'},
        {data:'proceeds', className:'text-end'},
        {data:'profit', className:'text-end'},
      ];
    }
    // requests
    return [srCol,
      {data:'request_no'},{data:'status'},{data:'requested_at'},{data:'method'},{data:'reason'},{data:'created_by'},
      {data:'assets_count', className:'text-end'},
      {data:'qty', className:'text-end'},
      {data:'nbv_total', className:'text-end'},
      {data:'proceeds', className:'text-end'},
      {data:'profit', className:'text-end'}
    ];
  }

  function buildTable(tab){
    if ($.fn.DataTable.isDataTable('#dl-table')) $('#dl-table').DataTable().clear().destroy();
    const wrap = $('#table-wrap'); wrap.empty();
    wrap.html(`
      <table id="dl-table" class="table dt-responsive nowrap align-middle" style="width:100%">
        <thead id="dl-thead">${headerHtml(tab)}</thead>
        <tbody></tbody>
      </table>
    `);

    dt = $('#dl-table').DataTable({
      processing:true, serverSide:true, searching:true, deferRender:true,
      lengthMenu:[[10,25,50,100,-1],[10,25,50,100,'{{ trans('assets.all') }}']], pageLength:10,
      ajax:{
        url: "{{ route('disposal_lifecycle_report.index') }}",
        data: function(d){
          if (!filtersApplied) d.no_data = 1;
          d.tab          = tab;
          d.date_from    = $('#f_date_from').val();
          d.date_to      = $('#f_date_to').val();
          d.status       = $('#f_status').val();
          d.methods      = $('#f_methods').val();
          d.reasons      = $('#f_reasons').val();
          d.category_ids = $('#f_category_ids').val();
          d.location_ids = $('#f_location_ids').val();
        }
      },
      columns: columnsFor(tab),
      order: [],
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      }
    });

    $('#no-data-tip').toggle(!filtersApplied);
    $('#dl-table').off('xhr.dt').on('xhr.dt', function(e, settings, json){
      const hasData = json && Array.isArray(json.data) && json.data.length > 0;
      $('#no-data-tip').toggle(!hasData);
    });

    // controls
    $('#search-report').off('input').on('input', function(){ dt.search(this.value).draw(); });
    $('#reload-table').off('click').on('click', function(){ dt.ajax.reload(null,false); });
    $('#print-table').off('click').on('click', function(){
      window.open(buildReportUrl('print'), 'dlPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
    });
    $('#export-excel').off('click').on('click', function(){
      window.location.href = buildReportUrl('export_excel');
    });
  }

  // init
  buildTable(currentTab);

  // tabs
  $('#dlTabs .nav-link').off('click').on('click', function(){
    $('#dlTabs .nav-link').removeClass('active');
    $(this).addClass('active');
    currentTab = $(this).data('tab');
    buildTable(currentTab);
  });

  // filters
  $('#btn-apply-filter').on('click', function(){
    filtersApplied = true;
    $('#no-data-tip').hide();
    if (dt) dt.ajax.reload();
  });

  $('#btn-reset-filter').on('click', function(){
    filtersApplied = false;
    $('#f_date_from,#f_date_to').val('');
    $('#f_status,#f_methods,#f_reasons,#f_category_ids,#f_location_ids').val(null).trigger('change');
    $('#search-report').val('');
    if (dt) { dt.search('').draw(); dt.ajax.reload(); }
    $('#no-data-tip').show();
  });

  function buildReportUrl(action){
    const p = new URLSearchParams();
    p.set('action', action);
    p.set('tab', currentTab);
    if ($('#f_date_from').val()) p.set('date_from', $('#f_date_from').val());
    if ($('#f_date_to').val())   p.set('date_to',   $('#f_date_to').val());
    ($('#f_status').val()      ||[]).forEach(v=>p.append('status[]',v));
    ($('#f_methods').val()     ||[]).forEach(v=>p.append('methods[]',v));
    ($('#f_reasons').val()     ||[]).forEach(v=>p.append('reasons[]',v));
    ($('#f_category_ids').val()||[]).forEach(v=>p.append('category_ids[]',v));
    ($('#f_location_ids').val()||[]).forEach(v=>p.append('location_ids[]',v));
    const q = $('#search-report').val(); if (q) p.set('search[value]', q);
    return "{{ route('disposal_lifecycle_report.index') }}?"+p.toString();
  }
});
</script>
@endsection
