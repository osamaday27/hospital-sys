@extends('layouts.master_table')

@section('title')
  {{ trans('suppliers.supplier_payment_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('suppliers.supplier_payment_report_title') }}</a>
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
        <h6 class="mb-0">{{ trans('suppliers.filters_title') }}</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">{{ trans('suppliers.filter_date_from') }}</label>
          <input type="date" id="f_date_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('suppliers.filter_date_to') }}</label>
          <input type="date" id="f_date_to" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('suppliers.filter_suppliers') }}</label>
          <select id="f_suppliers" class="form-select select2" multiple>
            @foreach($suppliers as $s)
              <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
            @endforeach
          </select>
          <small class="text-muted">{{ trans('suppliers.multiple_select_hint') }}</small>
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('suppliers.filter_receipt_no') }}</label>
          <input type="text" id="f_receipt" class="form-control" placeholder="{{ trans('suppliers.receipt_placeholder') }}">
        </div>

        <div class="d-grid gap-2">
          <button class="btn btn-primary" id="btn-apply-filter">
            <i class="ri-filter-3-line"></i> {{ trans('suppliers.apply_filters') }}
          </button>
          <button class="btn btn-light" id="btn-reset-filter">
            <i class="ri-refresh-line"></i> {{ trans('suppliers.reset_filters') }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="col-md-10">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('suppliers.supplier_payment_report_title') }}</h5>

        <div class="d-flex align-items-center gap-2">
          <input type="search" class="form-control" id="search-report"
                 placeholder="{{ trans('suppliers.search_placeholder') }}" style="width: 280px;">
          <button class="btn btn-info" id="reload-table" title="{{ trans('suppliers.reload') }}">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button class="btn btn-secondary" id="print-table">
            <i class="ri-printer-line"></i> {{ trans('suppliers.print') }}
          </button>
          <button class="btn btn-success" id="export-excel">
            <i class="ri-file-excel-2-line"></i> {{ trans('suppliers.export_excel') }}
          </button>
        </div>
      </div>

      <div class="card-body">
        <table id="payments-table" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>{{ trans('suppliers.table_sr_no') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_receipt') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_date') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_supplier') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_cheque_amount') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_cheque_no') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_cash_amount') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_deduction') }}</th>
              <th>{{ trans('suppliers.supplier_payment_report_th_credit_settle') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
        <div id="no-data-tip" class="text-muted small mt-2" style="display:none">
          {{ trans('suppliers.no_data_until_filter') }}
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
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '{{ trans('suppliers.select_placeholder') }}' });

    let filtersApplied = false;

    var table = $('#payments-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'{{ trans('suppliers.all') }}']],
      pageLength: 10,
      ajax: {
        url: "{{ route('supplier_payment_report.index') }}",
        data: function (d) {
          if (!filtersApplied) d.no_data = 1;
          d.date_from  = $('#f_date_from').val();
          d.date_to    = $('#f_date_to').val();
          d.suppliers  = $('#f_suppliers').val();
          d.receipt_no = $('#f_receipt').val();
        }
      },
      columns: [
        {
          data: null, orderable: false, searchable: false,
          render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
        },
        { data: 'receipt_no',   name: 'spi.reference_no' },
        { data: 'paid_date',    name: 'spi.paid_date' },
        { data: 'supplier',     name: 'sd.name_ar' },
        { data: 'cheque_amount',orderable:false, className:'text-end' },
        { data: 'cheque_no',    orderable:false },
        { data: 'cash_amount',  orderable:false, className:'text-end' },
        { data: 'deduction',    orderable:false, className:'text-end' },
        { data: 'credit_note',  orderable:false, className:'text-end' },
      ],
      order: [[2, 'desc']],
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      },
      initComplete: function () { $('#no-data-tip').show(); }
    });

    // بحث عام
    $('#search-report').on('input', function(){ table.search(this.value).draw(); });

    // تطبيق الفلاتر
    $('#btn-apply-filter').on('click', function(){
      filtersApplied = true;
      $('#no-data-tip').hide();
      table.page.len(10).draw();
    });

    // إعادة الضبط
    $('#btn-reset-filter').on('click', function(){
      filtersApplied = false;
      $('#f_date_from').val('');
      $('#f_date_to').val('');
      $('#f_suppliers').val(null).trigger('change');
      $('#f_receipt').val('');
      $('#search-report').val('');
      table.search('').draw();
      table.ajax.reload();
      $('#no-data-tip').show();
    });

    // إعادة تحميل
    $('#reload-table').on('click', function(){ table.ajax.reload(); });

    // طباعة/إكسل
    $('#print-table').on('click', function(){
      const url = buildReportUrl('print');
      window.open(url, 'SupplierPaymentsPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
    });
    $('#export-excel').on('click', function(){
      const url = buildReportUrl('export_excel');
      window.location.href = url;
    });

    function buildReportUrl(action) {
      const params = new URLSearchParams();
      params.set('action', action);
      if ($('#f_date_from').val()) params.set('date_from', $('#f_date_from').val());
      if ($('#f_date_to').val())   params.set('date_to',   $('#f_date_to').val());
      ($('#f_suppliers').val() || []).forEach(v => params.append('suppliers[]', v));
      if ($('#f_receipt').val()) params.set('receipt_no', $('#f_receipt').val());
      const q = $('#search-report').val(); if (q) params.set('q', q);
      return "{{ route('supplier_payment_report.index') }}" + '?' + params.toString();
    }
  });
</script>
@endsection
