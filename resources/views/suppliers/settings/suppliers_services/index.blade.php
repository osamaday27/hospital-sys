@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.supplier_services') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Breadcrumb -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">{{ trans('suppliers.supplier_services') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top:20px; left:50%; transform: translateX(-50%); z-index:9999;">
    {{ session('success') }}
  </div>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <input type="search" id="search-services" class="form-control"
               placeholder="{{ trans('suppliers.supplier_services') }}" style="width:250px;">
        <div class="hstack gap-2">
          <button id="create-btn" class="btn btn-outline-secondary">
            <i class="ri-add-line"></i> {{ trans('suppliers.create_supplier_service') }}
          </button>
          <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
          <button id="export-csv" class="btn btn-primary"><i class="fa fa-file-csv"></i> CSV</button>
          <button id="export-xlsx" class="btn btn-success"><i class="fas fa-file-excel"></i> XLSX</button>
        </div>
      </div>

      <div id="alert-container"></div>

      <div class="card-body">
        <table id="services-table" class="table dt-responsive nowrap align-middle w-100">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ trans('suppliers.code') }}</th>
              <th>{{ trans('suppliers.name') }}</th>
              <th>{{ trans('suppliers.description') }}</th>
              <th>{{ trans('suppliers.price') }}</th>
              <th>{{ trans('suppliers.status') }}</th>
              <th>{{ trans('general.actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- مودال الحذف --}}
@include('components.modals.delete-modal')

{{-- مودال إنشاء/تعديل خدمة --}}
<div class="modal fade" id="supplierServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="supplier-service-form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="service-id">

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.code') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="code">
            <div class="invalid-feedback" id="error-code"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.name_ar') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name_ar">
            <div class="invalid-feedback" id="error-name_ar"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.name_en') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name_en">
            <div class="invalid-feedback" id="error-name_en"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.price') }} <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="price">
            <div class="invalid-feedback" id="error-price"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.description') }}</label>
            <textarea class="form-control" id="description"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.notes') }}</label>
            <textarea class="form-control" id="notes"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">{{ trans('suppliers.status') }} <span class="text-danger">*</span></label>
            <select class="form-select" id="statuss">
              <option value="active">{{ trans('suppliers.active') }}</option>
              <option value="inactive">{{ trans('suppliers.inactive') }}</option>
            </select>
            <div class="invalid-feedback" id="error-status"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('general.cancel') }}</button>
          <button type="submit" class="btn btn-primary">{{ trans('general.save') }}</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- مودال عرض التفاصيل --}}
<div class="modal fade" id="serviceDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ trans('suppliers.service_details') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <dl class="row">
          <dt class="col-sm-4">{{ trans('suppliers.code') }}</dt>
          <dd class="col-sm-8" id="detail-code"></dd>
          <dt class="col-sm-4">{{ trans('suppliers.name_ar') }}</dt>
          <dd class="col-sm-8" id="detail-name_ar"></dd>
          <dt class="col-sm-4">{{ trans('suppliers.name_en') }}</dt>
          <dd class="col-sm-8" id="detail-name_en"></dd>
          <dt class="col-sm-4">{{ trans('suppliers.price') }}</dt>
          <dd class="col-sm-8" id="detail-price"></dd>
          <dt class="col-sm-4">{{ trans('suppliers.description') }}</dt>
          <dd class="col-sm-8" id="detail-description"></dd>
          <dt class="col-sm-4">{{ trans('suppliers.notes') }}</dt>
          <dd class="col-sm-8" id="detail-notes"></dd>
          <dt class="col-sm-4">{{ trans('suppliers.status') }}</dt>
          <dd class="col-sm-8" id="detail-status"></dd>
        </dl>
      </div>
    </div>
  </div>
</div>

<script>
$(function () {
  let table = $('#services-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('suppliers_services.index') }}",
    columns: [
      { data: null, orderable: false, render: (d,t,r,m) => m.row+1 },
      { data: 'code', name: 'code' },
      { data: null, render: r => r.name_ar + ' / ' + r.name_en },
      { data: 'description', name: 'description' },
      { data: 'price', name: 'price' },
      {
        data: 'status',name: 'status' },
      { data: 'action', orderable: false, searchable: false }
    ],
    dom: 'Bfrtip',
    buttons: [
      { extend:'csv',   className:'btn btn-outline-primary', text:'<i class="fa fa-file-csv"></i> CSV' },
      { extend:'excel', className:'btn btn-outline-success', text:'<i class="fas fa-file-excel"></i> XLSX' }
    ]
  });

  // reload, search, export
  $('#reload-table').click(() => table.ajax.reload());
  $('#search-services').on('input', e => table.search(e.target.value).draw());
  $('#export-csv').click(() => table.button('.buttons-csv').trigger());
  $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

  // فتح مودال الإنشاء
  $('#create-btn').click(function() {
    $('#modal-title').text("{{ trans('suppliers.create_supplier_service') }}");
    $('#supplier-service-form')[0].reset();
    $('#service-id').val('');
    $('#statuss').val('active');
    $('.invalid-feedback').hide();
    $('#supplierServiceModal').modal('show');
  });

  // فتح مودال التعديل
  $(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    $.get("{{ url('suppliers_services') }}/"+id, function(res) {
      $('#modal-title').text("{{ trans('general.edit') }}");
      $('#service-id').val(res.id);
      $('#code').val(res.code);
      $('#name_ar').val(res.name_ar);
      $('#name_en').val(res.name_en);
      $('#price').val(res.price);
      $('#description').val(res.description);
      $('#notes').val(res.notes);
      $('#statuss').val(res.status);
      $('.invalid-feedback').hide();
      $('#supplierServiceModal').modal('show');
    });
  });

  // حفظ/تعديل عبر AJAX
  $('#supplier-service-form').submit(function(e) {
    e.preventDefault();
    let id   = $('#service-id').val(),
        url  = id
          ? "{{ url('suppliers_services') }}/"+id
          : "{{ route('suppliers_services.store') }}",
        type = id ? 'PUT' : 'POST';

    $.ajax({
      url, type,
      data: {
        code:        $('#code').val(),
        name_ar:     $('#name_ar').val(),
        name_en:     $('#name_en').val(),
        price:       $('#price').val(),
        description: $('#description').val(),
        notes:       $('#notes').val(),
        status:      $('#statuss').val(),
        _token:      $('meta[name="csrf-token"]').attr('content')
      },
      success(res) {
        $('#supplierServiceModal').modal('hide');
        table.ajax.reload();
        showAlert('success', res.message);
      },
      error(xhr) {
        let errs = xhr.responseJSON.errors || {};
        $('.invalid-feedback').hide();
        $.each(errs, (k,v) => $(`#error-${k}`).text(v[0]).show());
      }
    });
  });

  // تفاصيل
  $(document).on('click','.details-btn',function(){
    let id = $(this).data('id');
    $.get("{{ url('suppliers_services') }}/"+id, res => {
      $('#detail-code').text(res.code);
      $('#detail-name_ar').text(res.name_ar);
      $('#detail-name_en').text(res.name_en);
      $('#detail-price').text(res.price);
      $('#detail-description').text(res.description||'-');
      $('#detail-notes').text(res.notes||'-');
      $('#detail-status').text(res.status);
      $('#serviceDetailModal').modal('show');
    });
  });

  // حذف
  $(document).on('click','.delete-btn',function(){
    $('#delete-id').val($(this).data('id'));
    $('#deleteModal').modal('show');
  });
  $('#delete-record').click(function(){
    let id = $('#delete-id').val();
    $.ajax({
      url: "{{ url('suppliers_services') }}/"+id,
      type: 'DELETE',
      data: { _token: $('meta[name="csrf-token"]').attr('content') },
      success(res){
        $('#deleteModal').modal('hide');
        table.ajax.reload();
        showAlert('success', res.message);
      }
    });
  });

  // تنبيه
  function showAlert(type,msg){
    let html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
      ${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    $('#alert-container').html(html);
    setTimeout(()=>$('.alert').alert('close'),5000);
  }
});
</script>
@endsection
