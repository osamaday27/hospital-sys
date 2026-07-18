@extends('layouts.master_table')

@section('title')
  {{ trans('supply_chain_trans.item_card_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.item_card_report_title') }}</a>
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
  .kpi-card{border:1px solid #e6e9ef; border-radius:10px; padding:12px; background:#fff;}
  .kpi-title{font-size:12px; color:#6c757d; margin-bottom:6px;}
  .kpi-value{font-size:18px; font-weight:700;}
  .status-dot{display:inline-block; width:10px; height:10px; border-radius:50%; margin-inline-end:6px;}
  .dot-ok{background:#28a745;} .dot-near{background:#ffc107;} .dot-expired{background:#dc3545;}
</style>

<div class="row">
  <!-- الفلاتر -->
  <div class="col-md-2">
    <div class="card sticky-filters">
      <div class="card-header">
        <h6 class="mb-0">{{ trans('supply_chain_trans.filters_title') }}</h6>
      </div>
      <div class="card-body">
        <!-- المخازن -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_store') }}</label>
          <select id="f_store_id" class="form-select select2" multiple>
            @foreach($stores as $s)
              <option value="{{ $s['id'] }}">{{ $s['name'] }}{{ $s['manual_code'] ? ' - '.$s['manual_code'] : '' }}</option>
            @endforeach
          </select>
        </div>

        <!-- التاريخ من/إلى -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_date_from') }}</label>
          <input type="date" id="f_date_from" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_date_to') }}</label>
          <input type="date" id="f_date_to" class="form-control">
        </div>

        <!-- الأصناف -->
        <div class="mb-3">
          <label class="form-label">{{ trans('supply_chain_trans.filter_products') }}</label>
          <select id="f_products" class="form-select select2" multiple>
            @foreach($products as $p)
              <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
            @endforeach
          </select>
        </div>

        <!-- خيارات عرض بسيطة -->
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="opt_show_prices" checked>
          <label class="form-check-label" for="opt_show_prices">{{ __('إظهار الأعمدة السعرية') }}</label>
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

  <!-- يمين: KPIs + Tabs -->
  <div class="col-md-10">
    <div class="card mb-2">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('supply_chain_trans.item_card_report_title') }}</h5>

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
        <!-- KPIs -->
        <div class="row g-2 mb-2" id="kpis-row">
          <div class="col-md-2 col-6">
            <div class="kpi-card">
              <div class="kpi-title">{{ __('الرصيد الحالي (كمية)') }}</div>
              <div class="kpi-value" id="kpi-qty">—</div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="kpi-card">
              <div class="kpi-title">{{ __('قيمة المخزون الحالية') }}</div>
              <div class="kpi-value" id="kpi-value">—</div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="kpi-card">
              <div class="kpi-title">{{ __('متوسط التكلفة الجاري') }}</div>
              <div class="kpi-value" id="kpi-avg">—</div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="kpi-card">
              <div class="kpi-title">{{ __('آخر تكلفة واردة') }}</div>
              <div class="kpi-value" id="kpi-last">—</div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="kpi-card">
              <div class="kpi-title">{{ __('إجمالي وارد/منصرف') }}</div>
              <div class="kpi-value"><span id="kpi-in">—</span> / <span id="kpi-out">—</span></div>
            </div>
          </div>
          <div class="col-md-2 col-6">
            <div class="kpi-card">
              <div class="kpi-title">{{ __('أقرب صلاحية / عدد منتهي') }}</div>
              <div class="kpi-value"><span id="kpi-exp">—</span> / <span id="kpi-expired">0</span></div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="ic-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-overview" data-bs-toggle="tab" data-bs-target="#pane-overview" type="button" role="tab">
              {{ __('نظرة عامة') }}
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-batches" data-bs-toggle="tab" data-bs-target="#pane-batches" type="button" role="tab">
              {{ __('الباتشات') }}
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-ledger" data-bs-toggle="tab" data-bs-target="#pane-ledger" type="button" role="tab">
              {{ __('دفتر الحركات') }}
            </button>
          </li>
        </ul>

        <div class="tab-content pt-3">
          <!-- Overview -->
          <div class="tab-pane fade show active" id="pane-overview" role="tabpanel">

            <div class="row g-3">
              <div class="col-lg-6">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ __('توزيع المخزون حسب المخزن') }}</strong>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-sm mb-0" id="ov-dist-table">
                        <thead>
                          <tr>
                            <th>{{ __('المخزن') }}</th>
                            <th class="text-end">{{ __('الكمية') }}</th>
                            <th class="text-end">{{ __('القيمة') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr><td colspan="3">{{ __('لا توجد بيانات') }}</td></tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th class="text-end">{{ __('الإجمالي') }}</th>
                            <th class="text-end" id="ov-dist-qty">—</th>
                            <th class="text-end" id="ov-dist-val">—</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ __('أقرب باتشات انتهاء') }}</strong>
                    <small class="text-muted">{{ __('أقرب 5 تواريخ صلاحية') }}</small>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-sm mb-0" id="ov-exp-table">
                        <thead>
                          <tr>
                            <th>{{ __('المخزن') }}</th>
                            <th>{{ __('الباتش') }}</th>
                            <th>{{ __('الصلاحية') }}</th>
                            <th class="text-end">{{ __('الكمية') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr><td colspan="4">{{ __('لا توجد بيانات') }}</td></tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Batches -->
          <div class="tab-pane fade" id="pane-batches" role="tabpanel">
            <div class="table-responsive">
              <table id="batches-table" class="table table-striped table-sm w-100">
                <thead>
                  <tr>
                    <th>{{ __('المخزن') }}</th>
                    <th>{{ __('الباتش') }}</th>
                    <th>{{ __('الصلاحية') }}</th>
                    <th class="text-end">{{ __('الكمية') }}</th>
                    <th class="text-end">{{ __('القيمة') }}</th>
                    <th>{{ __('آخر حركة') }}</th>
                    <th>{{ __('الحالة') }}</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

          <!-- Ledger -->
          <div class="tab-pane fade" id="pane-ledger" role="tabpanel">
            <table id="card-table" class="table dt-responsive nowrap align-middle" style="width:100%">
              <thead>
              <tr>
                <th>{{ trans('supply_chain_trans.table_sr_no') }}</th>
                <th>{{ trans('supply_chain_trans.table_product_code') }}</th>
                <th>{{ trans('supply_chain_trans.table_product_name') }}</th>
                <th>{{ trans('supply_chain_trans.table_beneficiary_code') }}</th>
                <th>{{ trans('supply_chain_trans.table_beneficiary_store') }}</th>
                <th>{{ trans('supply_chain_trans.table_movement_type') }}</th>
                <th>{{ trans('supply_chain_trans.table_movement_date') }}</th>
                <th class="col-price">{{ trans('supply_chain_trans.table_sale_price') }}</th>
                <th class="col-price">{{ trans('supply_chain_trans.table_cost_price') }}</th>
                <th>{{ trans('supply_chain_trans.table_unit') }}</th>
                <th>{{ trans('supply_chain_trans.table_ledger_balance') }}</th>
                <th>{{ trans('supply_chain_trans.table_batch_no') }}</th>
                <th>{{ trans('supply_chain_trans.table_first_addition_at') }}</th>
                <th>{{ trans('supply_chain_trans.table_expiry_date') }}</th>
                <th>{{ trans('supply_chain_trans.table_user') }}</th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div><!-- /tab-content -->

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

    function buildParams(obj){
      const params = new URLSearchParams();
      if (obj.action) params.set('action', obj.action);
      if (obj.dataset) params.set('dataset', obj.dataset);
      ($('#f_store_id').val() || []).forEach(v => params.append('store_id[]', v));
      ($('#f_products').val() || []).forEach(v => params.append('products[]', v));
      if ($('#f_date_from').val()) params.set('date_from', $('#f_date_from').val());
      if ($('#f_date_to').val())   params.set('date_to',   $('#f_date_to').val());
      const q = $('#search-report').val(); if (q && !obj.action) params.set('q', q);
      return params.toString();
    }
    function buildUrl(obj){ return "{{ route('item_card_report.index') }}" + '?' + buildParams(obj); }

    /* ===== Helpers ===== */
    function fmt(n){
      if(n === null || n === undefined) return '—';
      const x = (typeof n === 'string') ? n.replace(/,/g,'') : n;
      const v = parseFloat(x);
      return isNaN(v) ? '—' : v.toLocaleString(undefined, {maximumFractionDigits: 2});
    }

    /* ============= KPIs ============= */
    function loadKpis(){
      $.getJSON(buildUrl({dataset:'kpis'}), function(res){
        $('#kpi-qty').text(res.qty_now ?? '—');
        $('#kpi-value').text(res.val_now ?? '—');
        $('#kpi-avg').text(res.avg_cost ?? '—');
        $('#kpi-last').text(res.last_cost ?? '—');
        $('#kpi-in').text(res.qty_in ?? '—');
        $('#kpi-out').text(res.qty_out ?? '—');
        $('#kpi-exp').text(res.nearest_expiry ?? '—');
        $('#kpi-expired').text(res.expired_batches ?? 0);
      });
    }

    /* ============= Overview (uses batches dataset) ============= */
    function renderOverviewDistribution(batches){
      const $tb = $('#ov-dist-table tbody');
      $tb.empty();

      const acc = {}; // store_name -> {qty, val}
      batches.forEach(r=>{
        const store = r.store_name || '—';
        const qty = parseFloat((r.qty||'0').toString().replace(/,/g,'')) || 0;
        const val = parseFloat((r.value||'0').toString().replace(/,/g,'')) || 0;
        if(!acc[store]) acc[store] = {qty:0, val:0};
        acc[store].qty += qty;
        acc[store].val += val;
      });

      const entries = Object.entries(acc);
      if(!entries.length){
        $tb.append('<tr><td colspan="3">{{ trans("supply_chain_trans.no_results") }}</td></tr>');
        $('#ov-dist-qty').text('—'); $('#ov-dist-val').text('—');
        return;
      }

      let tQty = 0, tVal = 0;
      entries.sort((a,b)=> a[0].localeCompare(b[0], undefined, {numeric:true}));
      entries.forEach(([store, o])=>{
        tQty += o.qty; tVal += o.val;
        $tb.append(
          `<tr>
            <td>${store}</td>
            <td class="text-end">${fmt(o.qty)}</td>
            <td class="text-end">${fmt(o.val)}</td>
          </tr>`
        );
      });
      $('#ov-dist-qty').text(fmt(tQty));
      $('#ov-dist-val').text(fmt(tVal));
    }

    function renderOverviewExpiring(batches){
      const $tb = $('#ov-exp-table tbody');
      $tb.empty();
      const rows = (batches || [])
        .filter(r=> r.expiry_date && r.expiry_date !== '—')
        .sort((a,b)=> a.expiry_date.localeCompare(b.expiry_date))
        .slice(0,5);

      if(!rows.length){
        $tb.append('<tr><td colspan="4">{{ trans("supply_chain_trans.no_results") }}</td></tr>');
        return;
      }

      rows.forEach(r=>{
        $tb.append(
          `<tr>
            <td>${r.store_name}</td>
            <td>${r.batch_no}</td>
            <td>${r.expiry_date}</td>
            <td class="text-end">${r.qty}</td>
          </tr>`
        );
      });
    }

    function loadOverview(){
      // نستخدم dataset=batches لتعبئة كلا الجدولين
      $.getJSON(buildUrl({dataset:'batches'}), function(res){
        const arr = res.data || [];
        renderOverviewDistribution(arr);
        renderOverviewExpiring(arr);
      });
    }

    /* ============= Batches (tab table) ============= */
    function loadBatches(){
      const $tb = $('#batches-table tbody');
      $tb.empty().append('<tr><td colspan="7">{{ __("جاري التحميل...") }}</td></tr>');
      $.getJSON(buildUrl({dataset:'batches'}), function(res){
        $tb.empty();
        if(!res.data || !res.data.length){
          $tb.append('<tr><td colspan="7">{{ trans("supply_chain_trans.no_results") }}</td></tr>');
          return;
        }
        res.data.forEach(function(r){
          let badge = '<span class="status-dot dot-ok"></span>{{ __("متاح") }}';
          if(r.status === 'near')    badge = '<span class="status-dot dot-near"></span>{{ __("قريب الانتهاء") }}';
          if(r.status === 'expired') badge = '<span class="status-dot dot-expired"></span>{{ __("منتهي") }}';
          $tb.append(
            `<tr>
              <td>${r.store_name}</td>
              <td>${r.batch_no}</td>
              <td>${r.expiry_date}</td>
              <td class="text-end">${r.qty}</td>
              <td class="text-end">${r.value}</td>
              <td>${r.last_move}</td>
              <td>${badge}</td>
            </tr>`
          );
        });
      });
    }

    /* ============= Ledger (DataTables) ============= */
    var table = $('#card-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      ajax: {
        url: "{{ route('item_card_report.index') }}",
        data: function (d) {
          d.dataset  = 'ledger';
          d.store_id  = $('#f_store_id').val();    // array
          d.products  = $('#f_products').val();    // array
          d.date_from = $('#f_date_from').val();
          d.date_to   = $('#f_date_to').val();
        }
      },
      columns: [
        { // مسلسل
          data: null, orderable: false, searchable: false,
          render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
        },
        { data: 'product_code',     name: 'p.system_code' },
        { data: 'product_name',     name: 'p.name_ar' },
        { data: 'beneficiary_code', name: 'm.reference_no' },
        { data: 'beneficiary_store',name: 'benef_store_name' },
        { data: 'movement_type',    name: 'm.movement_type' },
        { data: 'movement_date',    name: 'm.movement_date' },
        { data: 'sale_price',       name: 'm.sale_price', className: 'text-end col-price' },
        { data: 'cost_price',       name: 'm.cost_price', className: 'text-end col-price' },
        { data: 'unit_name',        name: 'un.name_ar' },
        { data: 'current_balance',  name: 'm.product_store_balance', className: 'text-end' },
        { data: 'batch_no',         name: 'm.batch_no' },
        { data: 'first_addition_at',name: 'm.id', orderable:false, searchable:false },
        { data: 'expiry_date',      name: 'm.expiry_date' },
        { data: 'user_name',        name: 'u.name' },
      ],
      order: [[6, 'asc']], // تاريخ الحركة
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      }
    });

    // إظهار/إخفاء الأعمدة السعرية
    function togglePriceCols(){
      const show = $('#opt_show_prices').is(':checked');
      ['.col-price'].forEach(cls=>{
        if(show) $(cls).removeClass('d-none'); else $(cls).addClass('d-none');
      });
    }
    $('#opt_show_prices').on('change', togglePriceCols);

    // بحث عام
    $('#search-report').on('input', function(){ table.search(this.value).draw(); });

    // تطبيق الفلاتر
    $('#btn-apply-filter').on('click', function(){
      loadKpis();
      loadOverview();
      loadBatches();
      table.ajax.reload();
    });

    // إعادة ضبط الفلاتر
    $('#btn-reset-filter').on('click', function(){
      $('#f_store_id').val(null).trigger('change');
      $('#f_products').val(null).trigger('change');
      $('#f_date_from').val('');
      $('#f_date_to').val('');
      $('#search-report').val('');
      $('#opt_show_prices').prop('checked', true);
      togglePriceCols();
      table.search('').draw();
      loadKpis();
      loadOverview();
      loadBatches();
      table.ajax.reload();
    });

    // إعادة تحميل
    $('#reload-table').on('click', function(){
      loadKpis();
      loadOverview();
      loadBatches();
      table.ajax.reload();
    });

    // تحديث حسب التبويب
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
      const target = $(e.target).attr('data-bs-target');
      if (target === '#pane-overview') loadOverview();
      if (target === '#pane-batches')  loadBatches();
      if (target === '#pane-ledger')   table.columns.adjust().draw(false);
    });

    // طباعة
    $('#print-table').on('click', function(){
      const url = buildUrl({action:'print'}) + '&orient=landscape#auto-print';
      window.open(url,'itemCardPrint','width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
    });

    // إكسل
    $('#export-excel').on('click', function(){
      const url = buildUrl({action:'export_excel'});
      window.location.href = url;
    });

    // أول تحميل
    loadKpis();
    loadOverview();
    loadBatches();
    togglePriceCols();
  });
</script>
@endsection
