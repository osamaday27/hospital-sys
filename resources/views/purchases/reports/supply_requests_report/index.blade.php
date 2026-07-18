@extends('layouts.master_table')

@section('title')
  {{ trans('purchases.supply_requests_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('purchases.supply_requests_report_title') }}</a>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<style>
  .select2-container{width:100%!important; max-width:100%!important;}
  .select2-dropdown{z-index:2000;}
  .sticky-filters{position:sticky; top:12px}
</style>

<div class="row">
  <!-- Filters (Right) -->
  <div class="col-md-2">
    <div class="card sticky-filters">
      <div class="card-header">
        <h6 class="mb-0">{{ trans('purchases.supply_requests_report_filters_title') }}</h6>
      </div>
      <div class="card-body">
        <!-- Date From/To -->
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_date_from') }}</label>
          <input type="date" id="f_date_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_date_to') }}</label>
          <input type="date" id="f_date_to" class="form-control">
        </div>

        <!-- Requisition number -->
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_req_no') }}</label>
          <input type="text" id="f_req_no" class="form-control" placeholder="{{ trans('purchases.supply_requests_report_req_no_ph') }}">
        </div>

        <!-- Warehouse -->
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_store') }}</label>
          <select id="f_warehouse_id" class="form-select select2" multiple>
            @foreach($stores as $s)
              <option value="{{ $s['id'] }}">{{ $s['name'] }}{{ $s['manual_code'] ? ' - '.$s['manual_code'] : '' }}</option>
            @endforeach
          </select>
        </div>

        <!-- Products -->
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_products') }}</label>
          <select id="f_products" class="form-select select2" multiple>
            @foreach($products as $p)
              <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
            @endforeach
          </select>
          <small class="text-muted">{{ trans('purchases.supply_requests_report_multiple_select_hint') }}</small>
        </div>

        <!-- Department -->
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_department') }}</label>
          <select id="f_department_id" class="form-select select2" multiple>
            @foreach($departments as $d)
              <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
            @endforeach
          </select>
        </div>

        <!-- Status -->
        <div class="mb-3">
          <label class="form-label">{{ trans('purchases.supply_requests_report_filter_status') }}</label>
          <select id="f_statuses" class="form-select select2" multiple>
            @foreach($statuses as $st)
              <option value="{{ $st }}">{{ trans('purchases.supply_requests_report_status_'.$st) }}</option>
            @endforeach
          </select>
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-primary" id="btn-apply-filter">
            <i class="ri-filter-3-line"></i> {{ trans('purchases.supply_requests_report_apply_filters') }}
          </button>
          <button class="btn btn-light" id="btn-reset-filter">
            <i class="ri-refresh-line"></i> {{ trans('purchases.supply_requests_report_reset_filters') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Table (Left) -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('purchases.supply_requests_report_title') }}</h5>

        <div class="d-flex align-items-center gap-2">
          <input type="search" class="form-control" id="search-report"
                 placeholder="{{ trans('purchases.supply_requests_report_search_placeholder') }}" style="width: 280px;">
          <button class="btn btn-info" id="reload-table" title="{{ trans('purchases.supply_requests_report_reload') }}">
            <i class="ri-repeat-2-line"></i>
          </button>

          <div class="form-check" style="padding-inline-start:1rem">
            <input class="form-check-input" type="checkbox" id="landscapeToggle">
            <label class="form-check-label" for="landscapeToggle">
              {{ trans('purchases.supply_requests_report_landscape_mode') }}
            </label>
          </div>

          <button class="btn btn-secondary" id="print-table">
            <i class="ri-printer-line"></i> {{ trans('purchases.print') }}
          </button>
          <button class="btn btn-success" id="export-excel">
            <i class="ri-file-excel-2-line"></i> {{ trans('purchases.export_excel') }}
          </button>
        </div>
      </div>

      <div class="card-body">
        <table id="reqs-table" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>{{ trans('purchases.supply_requests_report_th_sr') }}</th>
              <th>{{ trans('purchases.supply_requests_report_th_no') }}</th>
              <th>{{ trans('purchases.supply_requests_report_th_date') }}</th>
              <th>{{ trans('purchases.supply_requests_report_th_status') }}</th>
              <th>{{ trans('purchases.supply_requests_report_th_reason') }}</th>
              <th>{{ trans('purchases.supply_requests_report_th_creator') }}</th>
              <th>{{ trans('purchases.supply_requests_report_th_items_col') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        <div id="no-data-tip" class="text-muted small mt-2" style="display:none">
          {{ trans('purchases.supply_requests_report_no_data_until_filter') }}
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
  $(function () {
    // Select2 multiple
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '{{ trans('purchases.supply_requests_report_select_placeholder') }}' });

    let filtersApplied = false;

    var table = $('#reqs-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      ajax: {
        url: "{{ route('supply_requests_report.index') }}",
        data: function (d) {
          if (!filtersApplied) d.no_data = 1;
          d.date_from     = $('#f_date_from').val();
          d.date_to       = $('#f_date_to').val();
          d.requisition_no= $('#f_req_no').val();
          d.warehouse_id  = $('#f_warehouse_id').val();    // array
          d.products      = $('#f_products').val();        // array
          d.department_id = $('#f_department_id').val();   // array
          d.statuses      = $('#f_statuses').val();        // array
        }
      },
      columns: [
        {
          data: null, orderable: false, searchable: false,
          render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
        },
        { data: 'requisition_number', name: 'requisition_number' },
        { data: 'requisition_date',   name: 'requisition_date' },
        { data: 'status',             name: 'status',
          render: function (data) {
            const key = 'status_' + (data || '').toString();
            const map = {
              'status_pending': '{{ trans('purchases.supply_requests_report_status_pending') }}',
              'status_receiving_quotes': '{{ trans('purchases.supply_requests_report_status_receiving_quotes') }}',
              'status_order_created': '{{ trans('purchases.supply_requests_report_status_order_created') }}',
              'status_closed': '{{ trans('purchases.supply_requests_report_status_closed') }}',
            };
            return map[key] || data || '—';
          }
        },
        { data: 'reason',             name: 'reason' },
        { data: 'creator',            name: 'created_by' },
        { data: 'items_html',         name: 'id', orderable:false, searchable:false }
      ],
      order: [[2, 'desc']], // التاريخ
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      },
      initComplete: function () { $('#no-data-tip').show(); }
    });

    // Global search
    $('#search-report').on('input', function(){ table.search(this.value).draw(); });

    // Apply filters
    $('#btn-apply-filter').on('click', function(){
      filtersApplied = true;
      $('#no-data-tip').hide();
      table.ajax.reload();
    });

    // Reset filters
    $('#btn-reset-filter').on('click', function(){
      filtersApplied = false;
      $('#f_date_from').val(''); $('#f_date_to').val(''); $('#f_req_no').val('');
      $('#f_warehouse_id').val(null).trigger('change');
      $('#f_products').val(null).trigger('change');
      $('#f_department_id').val(null).trigger('change');
      $('#f_statuses').val(null).trigger('change');
      $('#search-report').val('');
      table.search('').draw();
      table.ajax.reload();
      $('#no-data-tip').show();
    });

    // Reload
    $('#reload-table').on('click', function(){ table.ajax.reload(); });

    // Print (open new window) + orientation param
    $('#print-table').on('click', function(){
      const url = buildReportUrl('print', $('#landscapeToggle').is(':checked') ? 1 : 0);
      window.open(
        url,
        'supplyRequestsPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes'
      );
    });

    // Export Excel
    $('#export-excel').on('click', function(){
      const url = buildReportUrl('export_excel', $('#landscapeToggle').is(':checked') ? 1 : 0);
      window.location.href = url;
    });

    function buildReportUrl(action, landscape) {
      const params = new URLSearchParams();
      params.set('action', action);
      if (landscape) params.set('landscape', '1');

      if ($('#f_date_from').val()) params.set('date_from', $('#f_date_from').val());
      if ($('#f_date_to').val())   params.set('date_to',   $('#f_date_to').val());
      if ($('#f_req_no').val())    params.set('requisition_no', $('#f_req_no').val());

      ($('#f_warehouse_id').val() || []).forEach(v => params.append('warehouse_id[]', v));
      ($('#f_products').val() || []).forEach(v => params.append('products[]', v));
      ($('#f_department_id').val() || []).forEach(v => params.append('department_id[]', v));
      ($('#f_statuses').val() || []).forEach(v => params.append('statuses[]', v));

      const q = $('#search-report').val(); if (q) params.set('q', q);
      return "{{ route('supply_requests_report.index') }}" + '?' + params.toString();
    }
  });
</script>
@endsection
