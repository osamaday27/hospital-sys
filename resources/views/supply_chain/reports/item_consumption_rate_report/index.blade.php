@extends('layouts.master_table')

@section('title')
  {{ trans('supply_chain_trans.item_consumption_rate_report_title', [], app()->getLocale()) ?? 'تقرير معدل استهلاك الأصناف' }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">
              {{ trans('supply_chain_trans.item_consumption_rate_report_title', [], app()->getLocale()) ?? 'تقرير معدل استهلاك الأصناف' }}
            </a>
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
  <!-- Filters -->
  <div class="col-md-2">
    <div class="card sticky-filters">
      <div class="card-header">
        <h6 class="mb-0">{{ trans('supply_chain_trans.filters_title') }}</h6>
      </div>
      <div class="card-body">
        <!-- Stores (multi) -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_store') }}</label>
          <select id="f_store_id" class="form-select select2" multiple>
            @foreach($stores as $s)
              <option value="{{ $s['id'] }}">
                {{ $s['name'] }}{{ $s['manual_code'] ? ' - '.$s['manual_code'] : '' }}
              </option>
            @endforeach
          </select>
          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="f_aggregate">
            <label class="form-check-label" for="f_aggregate">
              تجميع الاستهلاك عبر كل المخازن المحددة
            </label>
          </div>
        </div>

        <!-- Date From/To (movement_date) -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_date_from') }}</label>
          <input type="date" id="f_date_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_date_to') }}</label>
          <input type="date" id="f_date_to" class="form-control">
        </div>

        <!-- Products (multi) -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_products') }}</label>
          <select id="f_products" class="form-select select2" multiple>
            @foreach($products as $p)
              <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
            @endforeach
          </select>
          <small class="text-muted">{{ trans('supply_chain_trans.multiple_select_hint') }}</small>
        </div>

        <!-- Price source -->
        <div class="mb-3">
          <label class="form-label d-block">مصدر التسعير</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="price_source" id="ps_latest" value="latest" checked>
            <label class="form-check-label" for="ps_latest">آخر حركة</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="price_source" id="ps_avg" value="avg">
            <label class="form-check-label" for="ps_avg">متوسط خلال الفترة</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="price_source" id="ps_units" value="units">
            <label class="form-check-label" for="ps_units">من جدول الوحدات (أصغر وحدة)</label>
          </div>
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-primary" id="btn-apply-filter">
            <i class="ri-filter-3-line"></i> {{ trans('supply_chain_trans.apply_filters') }}
          </button>
          <button class="btn btn-light" id="btn-reset-filter">
            <i class="ri-refresh-line"></i> {{ trans('supply_chain_trans.reset_filters') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('supply_chain_trans.item_consumption_rate_report_title') }}</h5>

        <div class="d-flex align-items-center gap-2">
          <input type="search" class="form-control" id="search-report"
                 placeholder="{{ trans('supply_chain_trans.search_placeholder') }}" style="width: 280px;">
          <button class="btn btn-info" id="reload-table" title="{{ trans('supply_chain_trans.reload') }}">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button class="btn btn-secondary" id="print-table">
            <i class="ri-printer-line"></i> {{ trans('supply_chain_trans.print') }}
          </button>
          <button class="btn btn-success" id="export-excel">
            <i class="ri-file-excel-2-line"></i> {{ trans('supply_chain_trans.export_excel') }}
          </button>
        </div>
      </div>

      <div class="card-body">
        <table id="consumption-table" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>{{ trans('supply_chain_trans.table_sr_no') }}</th>
              <th>{{ trans('supply_chain_trans.table_store_name') }}</th>
              <th>{{ trans('supply_chain_trans.table_product_code') }}</th>
              <th>{{ trans('supply_chain_trans.table_product_name') }}</th>
              <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
              <th>{{ trans('supply_chain_trans.table_sale_price') }}</th>
              <th>{{ trans('supply_chain_trans.table_cost_price') }}</th>
              <th>{{ trans('supply_chain_trans.table_unit_small') }}</th>
              <th>{{ trans('supply_chain_trans.table_balance_total') }}</th>
              <th>{{ trans('supply_chain_trans.table_rate_period') }}</th>
              <th>{{ trans('supply_chain_trans.table_rate_monthly') }}</th>
              <th>{{ trans('supply_chain_trans.table_rate_yearly') }}</th>
              <th>{{ trans('supply_chain_trans.table_suffices_until') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        <div id="no-data-tip" class="text-muted small mt-2" style="display:none">
          {{ trans('supply_chain_trans.no_data_until_filter') }}
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
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '{{ trans('supply_chain_trans.select_placeholder') }}' });
    let filtersApplied = false;

    var table = $('#consumption-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      ajax: {
        url: "{{ route('item_consumption_rate_report.index') }}",
        data: function (d) {
          if (!filtersApplied) d.no_data = 1;
          d.store_id  = $('#f_store_id').val();    // array
          d.products  = $('#f_products').val();    // array
          d.date_from = $('#f_date_from').val();
          d.date_to   = $('#f_date_to').val();
          d.aggregate = $('#f_aggregate').is(':checked') ? 1 : 0;
          d.price_source = $('input[name="price_source"]:checked').val();
        }
      },
      columns: [
        { data: null, orderable: false, searchable: false,
          render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
        { data: 'store_name',   name: 'store_name' },
        { data: 'product_code', name: 'product_code' },
        { data: 'product_name', name: 'product_name' },
        { data: 'batch_no',     name: 'batch_no' },
        { data: 'sale_price',   name: 'sale_price', className: 'text-end' },
        { data: 'cost_price',   name: 'cost_price', className: 'text-end' },
        { data: 'unit_name',    name: 'unit_name' },
        { data: 'balance',      name: 'balance', className: 'text-end' },
        { data: 'rate_period',  name: 'rate_period', className: 'text-end' },
        { data: 'rate_monthly', name: 'rate_monthly', className: 'text-end' },
        { data: 'rate_yearly',  name: 'rate_yearly', className: 'text-end' },
        { data: 'suffices_until', name: 'suffices_until' },
      ],
      order: [[9, 'desc']], // معدل الفترة
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      },
      initComplete: function () { $('#no-data-tip').show(); }
    });

    $('#search-report').on('input', function(){ table.search(this.value).draw(); });

    $('#btn-apply-filter').on('click', function(){
      filtersApplied = true;
      $('#no-data-tip').hide();
      table.ajax.reload();
    });

    $('#btn-reset-filter').on('click', function(){
      filtersApplied = false;
      $('#f_store_id').val(null).trigger('change');
      $('#f_products').val(null).trigger('change');
      $('#f_date_from').val('');
      $('#f_date_to').val('');
      $('#f_aggregate').prop('checked', false);
      $('#ps_latest').prop('checked', true);
      $('#search-report').val('');
      table.search('').draw();
      table.ajax.reload();
      $('#no-data-tip').show();
    });

    $('#reload-table').on('click', function(){ table.ajax.reload(); });

    $('#print-table').on('click', function(){
      const url = buildReportUrl('print');
      window.open(
        url + '#auto-print',
        'consumptionPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes'
      );
    });

    $('#export-excel').on('click', function(){
      const url = buildReportUrl('export_excel');
      window.location.href = url;
    });

    function buildReportUrl(action) {
      const params = new URLSearchParams();
      params.set('action', action);
      ($('#f_store_id').val() || []).forEach(v => params.append('store_id[]', v));
      ($('#f_products').val() || []).forEach(v => params.append('products[]', v));
      if ($('#f_date_from').val()) params.set('date_from', $('#f_date_from').val());
      if ($('#f_date_to').val())   params.set('date_to',   $('#f_date_to').val());
      if ($('#f_aggregate').is(':checked')) params.set('aggregate', 1);
      params.set('price_source', $('input[name="price_source"]:checked').val());
      const q = $('#search-report').val(); if (q) params.set('q', q);
      return "{{ route('item_consumption_rate_report.index') }}" + '?' + params.toString();
    }
  });
</script>
@endsection
