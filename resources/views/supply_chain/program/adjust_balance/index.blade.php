@extends('layouts.master_table')

@section('title')
  {{ __('supply_chain_trans.adjust_balance') ?? 'تسويات الرصيد' }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;"></div>

<div class="card">
  <div class="card-header d-flex flex-wrap align-items-center gap-2 justify-content-between">
    <div class="d-flex flex-wrap align-items-center gap-2">
      <a href="{{ route('adjust_balance.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> {{ __('supply_chain_trans.create') ?? 'إنشاء تسوية' }}
      </a>

      <select id="filter-store" class="form-select" style="min-width:260px"></select>

      <select id="filter-type" class="form-select">
        <option value="">{{ __('supply_chain_trans.all') ?? 'الكل' }}</option>
        <option value="increase">{{ __('supply_chain_trans.surplus') ?? 'زيادة' }}</option>
        <option value="decrease">{{ __('supply_chain_trans.shortage') ?? 'نقص' }}</option>
      </select>
    </div>

    <div class="input-group" style="max-width:320px">
      <span class="input-group-text"><i class="ri-search-line"></i></span>
      <input type="text" id="quick-search" class="form-control" placeholder="{{ __('supply_chain_trans.search') ?? 'بحث' }}">
      <button id="clear-search" class="btn btn-outline-secondary">{{ __('supply_chain_trans.clear') ?? 'مسح' }}</button>
    </div>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table id="adj-table" class="table table-bordered align-middle w-100">
        <thead class="table-light">
          <tr>
            <th style="width:60px">#</th>
            <th>{{ __('supply_chain_trans.code') ?? 'الكود' }}</th>
            <th>{{ __('supply_chain_trans.store') ?? 'المخزن' }}</th>
            <th>{{ __('supply_chain_trans.audit_date') ?? 'التاريخ والوقت' }}</th>
            <th>{{ __('supply_chain_trans.status') ?? 'النوع' }}</th>
            <th>{{ __('supply_chain_trans.product') ?? 'الصنف' }}</th>
            <th>{{ __('supply_chain_trans.unit') ?? 'الوحدة' }}</th>
            <th>{{ __('supply_chain_trans.batch_no') ?? 'الباتش' }}</th>
            <th>{{ __('supply_chain_trans.quantity') ?? 'الكمية' }}</th>
            <th>{{ __('supply_chain_trans.sale_price') ?? 'سعر البيع' }}</th>
            <th>{{ __('supply_chain_trans.cost_price') ?? 'سعر التكلفة' }}</th>
            <th>{{ __('supply_chain_trans.expiry_date') ?? 'انتهاء' }}</th>
            <th>{{ __('supply_chain_trans.notes') ?? 'ملاحظات' }}</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function showToast(type, msg) {
  const id = 't' + Date.now();
  const html = `<div id="${id}" class="toast align-items-center text-bg-${type} border-0" role="alert"><div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
  $('#toast-container').append(html);
  new bootstrap.Toast(document.getElementById(id), { delay: 2500 }).show();
}

let dt;

$(function(){
  // مخازن نشطة
  $('#filter-store').select2({
    placeholder: "{{ __('supply_chain_trans.store') ?? 'المخزن' }}",
    ajax: {
      url: "{{ route('adjust_balance.ajax.stores') }}",
      data: p => ({ q: p.term || '' }),
      processResults: d => ({ results: d })
    },
    allowClear: true
  });

  dt = $('#adj-table').DataTable({
    serverSide: true,
    processing: true,
    ajax: {
      url: "{{ route('adjust_balance.ajax.list') }}",
      data: function(d){
        d.store_id    = $('#filter-store').val() || '';
        d.adjust_type = $('#filter-type').val() || '';
      },
      type: 'GET'
    },
    lengthMenu: [10,25,50,100],
    pageLength: 10,
    order: [],
    dom: 'Bfrtip',
    buttons: ['copy','excel','print'],
    columns: [
      { data: null, orderable:false, searchable:false },
      { data: 'code' },
      { data: 'store_name' },
      {
        data: 'movement_datetime',
        render: v => v ? new Date(v).toLocaleString() : ''
      },
      {
        data: 'adjust_type',
        render: v => v==='increase'
          ? '<span class="badge bg-success">زيادة</span>'
          : '<span class="badge bg-danger">نقص</span>'
      },
      { data: 'product_name' },
      { data: 'unit_name' },
      { data: 'batch_no' },
      { data: 'quantity' },
      { data: 'sale_price' },
      { data: 'cost_price' },
      { data: 'expiry_date' },
      { data: 'notes' },
    ]
  });

  dt.on('draw', function(){
    dt.column(0, {page:'current'}).nodes().each((cell, i)=>{
      cell.innerHTML = (dt.page.info().start + i + 1);
    });
  });

  $('#filter-store, #filter-type').on('change', ()=> dt.ajax.reload());
  $('#quick-search').on('input', function(){ dt.search(this.value).draw(); });
  $('#clear-search').on('click', function(){ $('#quick-search').val(''); dt.search('').draw(); });
});
</script>
@endsection
