@extends('layouts.master_table')

@section('title')
  {{ trans('assets.depreciation_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('assets.depreciation_report_title') }}</a>
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
          <label class="form-label">{{ trans('assets.filter_period_from') }}</label>
          <input type="date" id="f_period_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('assets.filter_period_to') }}</label>
          <input type="date" id="f_period_to" class="form-control">
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
          <label class="form-label">{{ trans('assets.filter_methods') }}</label>
          <select id="f_dep_methods" class="form-select select2" multiple>
            @foreach($filters['methods'] as $m)
              <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
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
          <h5 class="mb-0">{{ trans('assets.depreciation_report_title') }}</h5>

          <ul class="nav nav-pills" id="depTabs">
            <li class="nav-item">
              <a class="nav-link active" data-tab="lines" href="javascript:void(0)">{{ trans('assets.tab_lines') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="runs" href="javascript:void(0)">{{ trans('assets.tab_runs') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-tab="missing" href="javascript:void(0)">{{ trans('assets.tab_missing') }}</a>
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
          {{-- سيتم بناء الجدول داخل هذا الـ div عند كل تبويب --}}
          <table id="dep-table" class="table dt-responsive nowrap align-middle" style="width:100%">
            <thead id="dep-thead">
              <!-- يُملأ ديناميكيًا -->
            </thead>
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
  let dt; // DataTable instance

  // ===== رؤوس الأعمدة حسب التبويب =====
  function headerHtml(tab){
    if (tab==='runs') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_period') }}</th>
          <th>{{ trans('assets.col_status') }}</th>
          <th>{{ trans('assets.col_lines_count') }}</th>
          <th>{{ trans('assets.col_total_amount') }}</th>
          <th>{{ trans('assets.col_posted_at') }}</th>
        </tr>`;
    } else if (tab==='missing') {
      return `
        <tr>
          <th>#</th>
          <th>{{ trans('assets.col_code') }}</th>
          <th>{{ trans('assets.col_name') }}</th>
          <th>{{ trans('assets.col_category') }}</th>
          <th>{{ trans('assets.col_location') }}</th>
          <th>{{ trans('assets.col_dep_method') }}</th>
          <th>{{ trans('assets.col_in_service') }}</th>
          <th>{{ trans('assets.col_nbv') }}</th>
          <th>{{ trans('assets.col_last_dep') }}</th>
        </tr>`;
    }
    // lines
    return `
      <tr>
        <th>#</th>
        <th>{{ trans('assets.col_code') }}</th>
        <th>{{ trans('assets.col_name') }}</th>
        <th>{{ trans('assets.col_category') }}</th>
        <th>{{ trans('assets.col_location') }}</th>
        <th>{{ trans('assets.col_dep_method') }}</th>
        <th>{{ trans('assets.col_useful_life') }}</th>
        <th>{{ trans('assets.col_dep_rate') }}</th>
        <th>{{ trans('assets.col_period') }}</th>
        <th>{{ trans('assets.col_amount') }}</th>
        <th>{{ trans('assets.col_nbv_after') }}</th>
      </tr>`;
  }

  // ===== أعمدة DataTables حسب التبويب =====
  function columnsFor(tab){
    const srCol = {
      data: null, orderable:false, searchable:false,
      render: function (data, type, row, meta) {
        return meta.row + meta.settings._iDisplayStart + 1;
      }
    };

    if (tab==='runs') {
      return [
        srCol,
        {data:'period'},
        {data:'status'},
        {data:'lines_count', className:'text-end'},
        {data:'total_amount', className:'text-end'},
        {data:'posted_at'}
      ];
    } else if (tab==='missing') {
      return [
        srCol,
        {data:'code'},{data:'name'},{data:'category'},{data:'location'},
        {data:'dep_method'},{data:'in_service'},
        {data:'nbv', className:'text-end'},
        {data:'last_dep'}
      ];
    }
    // lines
    return [
      srCol,
      {data:'code'},{data:'name'},{data:'category'},{data:'location'},
      {data:'dep_method'},
      {data:'useful_life', className:'text-end'},
      {data:'dep_rate', className:'text-end'},
      {data:'period'},
      {data:'amount', className:'text-end'},
      {data:'nbv_after', className:'text-end'}
    ];
  }

  // ===== إنشاء/تجديد الجدول — إصلاح التداخل =====
  function buildTable(tab){
    // لو في جدول شغّال: انفّذه بشكل صحيح ثم امسح الـ wrapper من الجذر
    if ($.fn.DataTable.isDataTable('#dep-table')) {
      $('#dep-table').DataTable().clear().destroy();
    }
    const wrap = $('#table-wrap');
    wrap.empty();

    // ابنِ جدول نظيف + رأس للتبويب الحالي
    wrap.html(`
      <table id="dep-table" class="table dt-responsive nowrap align-middle" style="width:100%">
        <thead id="dep-thead">
          ${headerHtml(tab)}
        </thead>
        <tbody></tbody>
      </table>
    `);

    // فعّل DataTables
    dt = $('#dep-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      deferRender: true,
      lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'{{ trans('assets.all') }}']],
      pageLength: 10,
      ajax: {
        url: "{{ route('depreciation_report.index') }}",
        data: function(d){
          if (!filtersApplied) d.no_data = 1;
          d.tab          = tab;
          d.period_from  = $('#f_period_from').val();
          d.period_to    = $('#f_period_to').val();
          d.category_ids = $('#f_category_ids').val();
          d.location_ids = $('#f_location_ids').val();
          d.asset_ids    = $('#f_asset_ids').val();
          d.dep_methods  = $('#f_dep_methods').val();
        }
      },
      columns: columnsFor(tab),
      order: [],
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      }
    });

    // إظهار/إخفاء رسالة "لا توجد بيانات"
    $('#no-data-tip').toggle(!filtersApplied);
    $('#dep-table').off('xhr.dt').on('xhr.dt', function(e, settings, json){
      const hasData = json && Array.isArray(json.data) && json.data.length > 0;
      $('#no-data-tip').toggle(!hasData);
    });

    // ربط الأدوات بعد كل بناء (مع إزالة أي ربط قديم)
    $('#search-report').off('input').on('input', function(){ dt.search(this.value).draw(); });
    $('#reload-table').off('click').on('click', function(){ dt.ajax.reload(null, false); });
    $('#print-table').off('click').on('click', function(){
      window.open(buildReportUrl('print'), 'depPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
    });
    $('#export-excel').off('click').on('click', function(){
      window.location.href = buildReportUrl('export_excel');
    });
  }

  // بداية: ابنِ جدول تبويب السطور
  buildTable(currentTab);

  // تبويبات (مرة واحدة)
  $('#depTabs .nav-link').off('click').on('click', function(){
    $('#depTabs .nav-link').removeClass('active');
    $(this).addClass('active');
    currentTab = $(this).data('tab');
    buildTable(currentTab);
  });

  // تطبيق الفلاتر
  $('#btn-apply-filter').on('click', function(){
    filtersApplied = true;
    $('#no-data-tip').hide();
    if (dt) dt.ajax.reload();
  });

  // إعادة ضبط
  $('#btn-reset-filter').on('click', function(){
    filtersApplied = false;
    $('#f_period_from,#f_period_to').val('');
    $('#f_category_ids,#f_location_ids,#f_dep_methods,#f_asset_ids').val(null).trigger('change');
    $('#search-report').val('');
    if (dt) {
      dt.search('').draw();
      dt.ajax.reload();
    }
    $('#no-data-tip').show();
  });

  // طباعة / اكسل
  function buildReportUrl(action){
    const p = new URLSearchParams();
    p.set('action', action);
    p.set('tab', currentTab);
    if ($('#f_period_from').val()) p.set('period_from', $('#f_period_from').val());
    if ($('#f_period_to').val())   p.set('period_to',   $('#f_period_to').val());
    ($('#f_category_ids').val()||[]).forEach(v=>p.append('category_ids[]',v));
    ($('#f_location_ids').val()||[]).forEach(v=>p.append('location_ids[]',v));
    ($('#f_dep_methods').val() ||[]).forEach(v=>p.append('dep_methods[]',v));
    ($('#f_asset_ids').val()   ||[]).forEach(v=>p.append('asset_ids[]',v));
    const q = $('#search-report').val(); if (q) p.set('search[value]', q);
    return "{{ route('depreciation_report.index') }}?"+p.toString();
  }
});
</script>
@endsection
