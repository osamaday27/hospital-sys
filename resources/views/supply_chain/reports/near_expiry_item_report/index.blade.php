@extends('layouts.master_table')

@section('title')
  {{ trans('supply_chain_trans.near_expiry_item_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.near_expiry_item_report_title') }}</a>
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
        <h6 class="mb-0">{{ trans('supply_chain_trans.filters_title') }}</h6>
      </div>
      <div class="card-body">
        <!-- Products -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_products') }}</label>
          <select id="f_products" class="form-select select2" multiple>
            @foreach($products as $p)
              <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
            @endforeach
          </select>
        </div>

        <!-- Categories -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_categories') }}</label>
          <select id="f_categories" class="form-select select2" multiple>
            @foreach($categories as $c)
              <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
            @endforeach
          </select>
        </div>

        <!-- Stores -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_store') }}</label>
          <select id="f_store_id" class="form-select select2" multiple>
            @foreach($stores as $s)
              <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
            @endforeach
          </select>
        </div>

        <!-- Days to expire -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_days_to_expire') }}</label>
          <input type="number" id="f_days" class="form-control" min="1" value="30">
          <small class="text-muted">{{ trans('supply_chain_trans.filter_days_hint') }}</small>
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

  <!-- Table (Left) -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('supply_chain_trans.near_expiry_item_report_title') }}</h5>
        <div class="d-flex align-items-center gap-2">
          <input type="search" class="form-control" id="search-report"
                 placeholder="{{ trans('supply_chain_trans.search_placeholder') }}" style="width:280px;">
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
        <table id="near-expiry-table" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>{{ trans('supply_chain_trans.table_sr_no') }}</th>
              <th>{{ trans('supply_chain_trans.table_product_code') }}</th>
              <th>{{ trans('supply_chain_trans.table_product_name') }}</th>
              <th>{{ trans('supply_chain_trans.table_category_name') }}</th>
              <th>{{ trans('supply_chain_trans.table_sale_price') }}</th>
              <th>{{ trans('supply_chain_trans.table_cost_price') }}</th>
              <th>{{ trans('supply_chain_trans.table_unit_small') }}</th>
              <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
              <th>{{ trans('supply_chain_trans.table_quantity') }}</th>
              <th>{{ trans('supply_chain_trans.table_store_name') }}</th>
              <th>{{ trans('supply_chain_trans.table_created_at') }}</th>
              <th>{{ trans('supply_chain_trans.table_expiry_date') }}</th>
              <th>{{ trans('supply_chain_trans.table_created_by') }}</th>
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
  $(function(){
    $('.select2').select2({ theme:'bootstrap-5', width:'100%', placeholder:'{{ trans('supply_chain_trans.select_placeholder') }}' });

    let filtersApplied = true; // نفعّل افتراضيًا لأن الأيام موجودة بقيمة 30

    var table = $('#near-expiry-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      ajax: {
        url: "{{ route('near_expiry_item_report.index') }}",
        data: function(d){
          if (!filtersApplied) d.no_data = 1;
          d.products   = $('#f_products').val();    // array
          d.categories = $('#f_categories').val();  // array
          d.store_id   = $('#f_store_id').val();    // array
          d.days       = $('#f_days').val();        // int
        }
      },
      columns: [
        {
          data:null, orderable:false, searchable:false,
          render:function(data,type,row,meta){ return meta.row + meta.settings._iDisplayStart + 1; }
        },
        { data:'product_code',  name:'p.system_code' },
        { data:'product_name',  name:'p.name_ar' },
        { data:'category_name', name:'c.name_ar' },
        { data:'sale_price',    name:'' , className:'text-end'},
        { data:'cost_price',    name:'' , className:'text-end'},
        { data:'unit_name',     name:'' },
        { data:'batch_no',      name:'b.batch_no' },
        { data:'quantity',      name:'b.quantity_in_base_unit', className:'text-end' },
        { data:'store_name',    name:'s.name_ar' },
        { data:'created_at',    name:'first_add.min_date' },
        { data:'expiry_date',   name:'b.expiry_date' },
        { data:'created_by',    name:'u.name' },
      ],
      order: [[11, 'asc']], // الأقرب انتهاءً
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      },
      initComplete: function(){ $('#no-data-tip').hide(); }
    });

    $('#search-report').on('input', function(){ table.search(this.value).draw(); });

    $('#btn-apply-filter').on('click', function(){
      filtersApplied = true;
      $('#no-data-tip').hide();
      table.ajax.reload();
    });

    $('#btn-reset-filter').on('click', function(){
      filtersApplied = true; // لأن الأيام سنرجعها 30
      $('#f_products').val(null).trigger('change');
      $('#f_categories').val(null).trigger('change');
      $('#f_store_id').val(null).trigger('change');
      $('#f_days').val(30);
      $('#search-report').val('');
      table.search('').draw();
      table.ajax.reload();
      $('#no-data-tip').hide();
    });

    $('#reload-table').on('click', function(){ table.ajax.reload(); });

    $('#print-table').on('click', function(){
      const url = buildReportUrl('print');
      window.open(url, 'nearExpiryPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
    });

    $('#export-excel').on('click', function(){
      const url = buildReportUrl('export_excel');
      window.location.href = url;
    });

    function buildReportUrl(action){
      const params = new URLSearchParams();
      params.set('action', action);
      ($('#f_products').val()   || []).forEach(v => params.append('products[]', v));
      ($('#f_categories').val() || []).forEach(v => params.append('categories[]', v));
      ($('#f_store_id').val()   || []).forEach(v => params.append('store_id[]', v));
      const days = $('#f_days').val(); if (days) params.set('days', days);
      const q = $('#search-report').val(); if (q) params.set('q', q);
      return "{{ route('near_expiry_item_report.index') }}" + '?' + params.toString();
    }
  });
</script>
@endsection
