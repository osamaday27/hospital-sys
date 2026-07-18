@extends('layouts.master_table')

@section('title')
  {{ trans('supply_chain_trans.item_groups_report_title') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">
            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.item_groups_report_title') }}</a>
          </li>
        </ol>
      </div>
    </div>
  </div>
</div>

<style>
  .select2-container{width:100%!important; max-width:100%!important;}
  .select2-dropdown{z-index:2000;}
</style>

<div class="row">
  <!-- لا توجد فلاتر لهذا التقرير -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ trans('supply_chain_trans.item_groups_report_title') }}</h5>

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
        <table id="groups-table" class="table dt-responsive nowrap align-middle" style="width:100%">
          <thead>
            <tr>
              <th>{{ trans('supply_chain_trans.table_sr_no') }}</th>
              <th>{{ trans('supply_chain_trans.table_category_name') }}</th>
              <th>{{ trans('supply_chain_trans.table_type') }}</th>
              <th>{{ trans('supply_chain_trans.table_status') }}</th>
              <th>{{ trans('supply_chain_trans.table_products_count') }}</th>
              <th>{{ trans('supply_chain_trans.table_parent_category') }}</th>
              <th>{{ trans('supply_chain_trans.table_sort') }}</th>
              <th>{{ trans('supply_chain_trans.table_created_at') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  $(function () {
    var table = $('#groups-table').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      ajax: { url: "{{ route('item_groups_report.index') }}" },
      columns: [
        {
          data: null, orderable: false, searchable: false,
          render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; }
        },
        { data: 'category_name',  name: 'c.name_ar' },
        { data: 'type',           name: 'c.type' },
        { data: 'status_label',   name: 'c.status' },
        { data: 'products_count', name: 'products_count', className: 'text-end' },
        { data: 'parent_name',    name: 'parent.name_ar' },
        { data: 'sort',           name: 'c.sort', className: 'text-end' },
        { data: 'created_at',     name: 'c.created_at' },
      ],
      order: [[1, 'asc']],
      language: {
        url: "{{ app()->getLocale() === 'ar' ? '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json' : '//cdn.datatables.net/plug-ins/1.13.4/i18n/en-GB.json' }}"
      }
    });

    // بحث نصي
    $('#search-report').on('input', function(){ table.search(this.value).draw(); });

    // إعادة تحميل
    $('#reload-table').on('click', function(){ table.ajax.reload(); });

    // طباعة
    $('#print-table').on('click', function(){
      const url = "{{ route('item_groups_report.index') }}" + '?action=print';
      window.open(url, 'groupsPrint',
        'width=1200,height=800,top=50,left=50,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes'
      );
    });

    // إكسل
    $('#export-excel').on('click', function(){
      const url = "{{ route('item_groups_report.index') }}" + '?action=export_excel';
      window.location.href = url;
    });
  });
</script>
@endsection
