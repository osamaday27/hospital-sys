@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.notifications') }}
@stop

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Breadcrumb -->
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item active">{{ trans('suppliers.notifications') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center"
       style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
              min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">
    {{ session('success') }}
  </div>
@endif

<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <button id="create-notification-btn" class="btn btn-outline-secondary">
            <i class="ri-add-line"></i> {{ trans('suppliers.add_notification') }}
          </button>
          <input
            type="search"
            id="search-notifications"
            class="form-control ms-2"
            placeholder="{{ trans('suppliers.notifications_list') }}"
            style="width: 250px;"
          >
        </div>
        <div class="hstack gap-2">
          <button id="reload-table" class="btn btn-info">
            <i class="ri-repeat-2-line"></i>
          </button>
          <button id="export-csv" class="btn btn-primary">
            <i class="fa fa-file-csv"></i> CSV
          </button>
          <button id="export-xlsx" class="btn btn-success">
            <i class="fas fa-file-excel"></i> XLSX
          </button>
        </div>
      </div>

      <div id="alert-container"></div>

      <div class="card-body">
        <table id="notifications-table" class="table dt-responsive nowrap align-middle w-100">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ trans('suppliers.supplier_ar') }}</th>
              <th>{{ trans('suppliers.supplier_en') }}</th>
              <th>{{ trans('suppliers.type') }}</th>
              <th>{{ trans('suppliers.value') }}</th>
              <th>{{ trans('suppliers.date') }}</th>
              <th>{{ trans('suppliers.notes') }}</th>
              <th>{{ trans('general.actions') }}</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: إضافة/تعديل إشعار -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="notification-form">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modal-title"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="notification-id">

          <div class="mb-3">
            <label class="form-label">
              {{ trans('suppliers.supplier_ar') }} <span class="text-danger">*</span>
            </label>
            <select id="supplier_id" class="form-select supplier-select">
              <option value="">{{ trans('suppliers.choose_supplier') }}</option>
              @foreach($suppliers as $s)
                <option value="{{ $s->id }}">
                  [{{ $s->code }}] {{ $s->name_ar }} - {{ $s->name_en }}
                </option>
              @endforeach
            </select>
            <div class="invalid-feedback" id="error-supplier_id"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">
              {{ trans('suppliers.type') }} <span class="text-danger">*</span>
            </label>
            <select id="type" class="form-select">
              <option value="">{{ trans('suppliers.choose_type') }}</option>
              <option value="addition">{{ trans('suppliers.addition') }}</option>
              <option value="discount">{{ trans('suppliers.discount') }}</option>
            </select>
            <div class="invalid-feedback" id="error-type"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">
              {{ trans('suppliers.value') }} <span class="text-danger">*</span>
            </label>
            <input type="number" step="0.01" id="value" class="form-control">
            <div class="invalid-feedback" id="error-value"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">
              {{ trans('suppliers.date') }} <span class="text-danger">*</span>
            </label>
            <input type="date" id="notify_date" class="form-control">
            <div class="invalid-feedback" id="error-notify_date"></div>
          </div>

          <div class="mb-3">
            <label class="form-label">
              {{ trans('suppliers.notes') }}
            </label>
            <textarea id="notes" class="form-control" rows="3"></textarea>
            <div class="invalid-feedback" id="error-notes"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ trans('general.cancel') }}
          </button>
          <button type="submit" id="save-btn" class="btn btn-primary">
            {{ trans('general.save') }}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

@include('components.modals.delete-modal')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    var table = $('#notifications-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier_alerts.index') }}",
        columns: [
            { data: null, orderable: false, render: (d, t, r, m) => m.row + 1 },
            { data: 'supplier_ar', name: 'supplier.name_ar' },
            { data: 'supplier_en', name: 'supplier.name_en' },
            { data: 'type',         name: 'type' },
            { data: 'value',        name: 'value' },
            { data: 'notify_date',  name: 'notify_date' },
            { data: 'notes',        name: 'notes' },
            { data: 'action',       orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'csv',   className: 'btn btn-outline-primary', text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel', className: 'btn btn-outline-success',  text: '<i class="fas fa-file-excel"></i> XLSX' }
        ]
    });

    // search box
    $('#search-notifications').on('input', e => table.search(e.target.value).draw());
    // reload & exports
    $('#reload-table').click(() => table.ajax.reload());
    $('#export-csv').click(() => table.button('.buttons-csv').trigger());
    $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

    // clear errors
    function clearErrors() {
        $('.invalid-feedback').text('').hide();
        $('.is-invalid').removeClass('is-invalid');
    }

    // open create modal
    $('#create-notification-btn').click(function() {
        $('#modal-title').text("{{ trans('suppliers.add_notification') }}");
        $('#notification-form')[0].reset();
        $('#notification-id').val('');
        clearErrors();
        $('#supplier_id').prop('disabled', false);
        $('#notificationModal').modal('show');
    });

    // open edit modal
    $(document).on('click', '.edit-btn', function() {
        clearErrors();
        let id = $(this).data('id');
        $.get("{{ url('supplier_alerts') }}/" + id + "/edit", function(res) {
            $('#modal-title').text("{{ trans('suppliers.edit_notification') }}");
            $('#notification-id').val(res.id);
            $('#supplier_id').val(res.supplier_id).prop('disabled', true);
            $('#type').val(res.type);
            $('#value').val(res.value);
            $('#notify_date').val(res.notify_date);
            $('#notes').val(res.notes);
            $('#notificationModal').modal('show');
        });
    });

    // submit create/update
    $('#notification-form').submit(function(e) {
        e.preventDefault();
        clearErrors();
        let id   = $('#notification-id').val(),
            url  = id ? "{{ url('supplier_alerts') }}/" + id : "{{ route('supplier_alerts.store') }}",
            type = id ? 'PUT' : 'POST';
        $.ajax({
            url:  url,
            type: type,
            data: {
                supplier_id: $('#supplier_id').val(),
                type:        $('#type').val(),
                value:       $('#value').val(),
                notify_date: $('#notify_date').val(),
                notes:       $('#notes').val(),
                _token:      $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                $('#notificationModal').modal('hide');
                showAlert('success', res.message);
                table.ajax.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors || {};
                $.each(errors, function(k, v) {
                    $('#error-' + k).text(v[0]).show();
                    $('#' + k).addClass('is-invalid');
                });
            }
        });
    });

    // delete
    $(document).on('click', '.delete-btn', function() {
        $('#delete-id').val($(this).data('id'));
        $('#deleteModal').modal('show');
    });
    $('#delete-record').click(function() {
        let id = $('#delete-id').val();
        $.ajax({
            url:  "{{ url('supplier_alerts') }}/" + id,
            type: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                $('#deleteModal').modal('hide');
                showAlert('success', res.message);
                table.ajax.reload();
            },
            error: function() {
                showAlert('danger', "{{ trans('general.error_occurred') }}");
            }
        });
    });

    // alert helper
    function showAlert(type, message) {
        let html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
        $('#alert-container').html(html);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // init Select2
    $('.supplier-select').select2({
        dropdownParent: $('#notificationModal'),
        width: '100%',
        dir: "rtl",
        placeholder: "{{ trans('suppliers.choose_supplier') }}",
        language: {
            noResults: () => "{{ trans('general.no_results_found') }}"
        }
    });
});
</script>
@endsection
