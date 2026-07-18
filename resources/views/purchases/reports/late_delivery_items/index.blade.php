@extends('layouts.master_table')

@section('title')
{{ trans('purchases.late_delivery_items_title') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('purchases.late_delivery_items_title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <style>
        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
        }

        .select2-dropdown {
            z-index: 2000;
        }

        .sticky-filters {
            position: sticky;
            top: 12px
        }
    </style>

    <div class="row">
        <div class="col-md-2">
            <div class="card sticky-filters">
                <div class="card-header">
                    <h6 class="mb-0">{{ trans('purchases.filters_title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ trans('purchases.filter_date_from') }}</label>
                        <input type="date" id="f_date_from" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('purchases.filter_date_to') }}</label>
                        <input type="date" id="f_date_to" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('purchases.filter_order_no') }}</label>
                        <input type="text" id="f_order_no" class="form-control"
                            placeholder="{{ trans('purchases.search_placeholder') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('purchases.filter_store') }}</label>
                        <select id="f_store_id" class="form-select select2" multiple>
                            @foreach($stores as $s)
                                <option value="{{ $s['id'] }}">
                                    {{ $s['name'] }}{{ $s['manual_code'] ? ' - ' . $s['manual_code'] : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('purchases.filter_products') }}</label>
                        <select id="f_products" class="form-select select2" multiple>
                            @foreach($products as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ trans('purchases.multiple_select_hint') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ trans('purchases.filter_department') }}</label>
                        <select id="f_department" class="form-select select2" multiple>
                            @foreach($departments as $d)
                                <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" id="btn-apply-filter">
                            <i class="ri-filter-3-line"></i> {{ trans('purchases.apply_filters') }}
                        </button>
                        <button class="btn btn-light" id="btn-reset-filter">
                            <i class="ri-refresh-line"></i> {{ trans('purchases.reset_filters') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">{{ trans('purchases.late_delivery_items_title') }}</h5>

                    <div class="d-flex align-items-center gap-2">
                        <input type="search" class="form-control" id="search-report"
                            placeholder="{{ trans('purchases.search_placeholder') }}" style="width: 280px;">
                        <button class="btn btn-info" id="reload-table" title="{{ trans('purchases.reload') }}">
                            <i class="ri-repeat-2-line"></i>
                        </button>
                        <button class="btn btn-secondary" id="print-table">
                            <i class="ri-printer-line"></i> {{ trans('purchases.print') }}
                        </button>
                        <button class="btn btn-success" id="export-excel">
                            <i class="ri-file-excel-2-line"></i> {{ trans('purchases.export_excel') }}
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <table id="late-items-table" class="table dt-responsive nowrap align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>{{ trans('purchases.table_sr_no') }}</th>
                                <th>{{ trans('purchases.late_delivery_items_th_order_no') }}</th>
                                <th>{{ trans('purchases.late_delivery_items_th_date') }}</th>
                                <th>{{ trans('purchases.late_delivery_items_th_items') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="no-data-tip" class="text-muted small mt-2" style="display:none">
                        {{ trans('purchases.no_data_until_filter') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(function () {
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%', placeholder: '{{ trans('purchases.select_placeholder') }}' });

            let filtersApplied = false;

            var table = $('#late-items-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, '{{ trans('purchases.all') }}']],
                pageLength: 10,
                ajax: {
                    url: "{{ route('late_delivery_items.index') }}",
                    data: function (d) {
                        if (!filtersApplied) d.no_data = 1;
                        d.date_from = $('#f_date_from').val();
                        d.date_to = $('#f_date_to').val();
                        d.order_no = $('#f_order_no').val();
                        d.warehouse_id = $('#f_store_id').val();
                        d.products = $('#f_products').val();
                        d.department_id = $('#f_department').val();
                    }
                },
                columns: [
                    {
                        data: null, orderable: false, searchable: false,
                        render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
                    },
                    { data: 'order_number', name: 'purchase_orders.order_number' },
                    { data: 'order_date', name: 'purchase_orders.order_date' },
                    { data: 'items_html', name: 'purchase_orders.id', orderable: false, searchable: false },
                ],
                order: [[2, 'desc']],
                language: {
                    url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
                },
                initComplete: function () { $('#no-data-tip').show(); }
            });

            $('#search-report').on('input', function () { table.search(this.value).draw(); });

            $('#btn-apply-filter').on('click', function () {
                filtersApplied = true;
                $('#no-data-tip').hide();
                table.ajax.reload();
            });

            $('#btn-reset-filter').on('click', function () {
                filtersApplied = false;
                $('#f_date_from').val('');
                $('#f_date_to').val('');
                $('#f_order_no').val('');
                $('#f_store_id').val(null).trigger('change');
                $('#f_products').val(null).trigger('change');
                $('#f_department').val(null).trigger('change');
                $('#search-report').val('');
                table.search('').draw();
                table.ajax.reload();
                $('#no-data-tip').show();
            });

            $('#reload-table').on('click', function () { table.ajax.reload(); });

            $('#print-table').on('click', function () {
                const url = buildReportUrl('print');
                window.open(url, 'lateItemsPrint',
                    'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');
            });

            $('#export-excel').on('click', function () {
                const url = buildReportUrl('export_excel');
                window.location.href = url;
            });

            function buildReportUrl(action) {
                const params = new URLSearchParams();
                params.set('action', action);
                if ($('#f_date_from').val()) params.set('date_from', $('#f_date_from').val());
                if ($('#f_date_to').val()) params.set('date_to', $('#f_date_to').val());
                if ($('#f_order_no').val()) params.set('order_no', $('#f_order_no').val());
                ($('#f_store_id').val() || []).forEach(v => params.append('warehouse_id[]', v));
                ($('#f_products').val() || []).forEach(v => params.append('products[]', v));
                ($('#f_department').val() || []).forEach(v => params.append('department_id[]', v));
                const q = $('#search-report').val(); if (q) params.set('q', q);
                return "{{ route('late_delivery_items.index') }}" + '?' + params.toString();
            }
        });
    </script>
@endsection
