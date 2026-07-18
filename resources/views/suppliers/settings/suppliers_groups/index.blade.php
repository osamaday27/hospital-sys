@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.suppliers_groups') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Breadcrumb مطابق للشكل القديم -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            {{ trans('suppliers.suppliers_groups') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center"
             style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: auto; min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <input
                        type="search"
                        id="search-suppliers-groups"
                        class="form-control"
                        placeholder="{{ trans('suppliers.suppliers_groups') }}"
                        style="width: 250px;"
                    >
                    <div class="hstack gap-2">
                        <button id="create-btn" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ trans('suppliers.create_suppliers_groups') }}
                        </button>
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
            <table id="suppliers-groups-table" class="table dt-responsive nowrap align-middle w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('suppliers.code') }}</th>
                        <th>{{ trans('suppliers.name_ar') }}</th>
                        <th>{{ trans('suppliers.name_en') }}</th>
                        <th>{{ trans('suppliers.description') }}</th>
                        <th>{{ trans('suppliers.branch') }}</th>
                        <th>{{ trans('suppliers.created_by') }}</th>
                        <th>{{ trans('suppliers.created_at') }}</th>
                        <th>{{ trans('suppliers.updated_at') }}</th>
                        <th>{{ trans('suppliers.status') }}</th>
                        <th>{{ trans('general.actions') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal: إنشاء/تعديل مجموعة موردين -->
    <div class="modal fade" id="suppliersGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="suppliers-group-form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="group-id">

                        <div class="mb-3">
                            <label class="form-label">
                                {{ trans('suppliers.code') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="code">
                            <div class="invalid-feedback" id="error-code"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                {{ trans('suppliers.name_ar') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name_ar">
                            <div class="invalid-feedback" id="error-name_ar"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                {{ trans('suppliers.name_en') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="name_en">
                            <div class="invalid-feedback" id="error-name_en"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ trans('suppliers.description') }}</label>
                            <textarea class="form-control" id="description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                {{ trans('suppliers.branch') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="branch_id">
                                <option value="">{{ trans('suppliers.choose') }}</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-branch_id"></div>
                        </div>

                        <!-- حقل الحالة -->
                        <div class="mb-3">
                            <label class="form-label">
                                {{ trans('suppliers.status') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="statusgroup">
                                <option value="active">{{ trans('suppliers.active') }}</option>
                                <option value="inactive">{{ trans('suppliers.inactive') }}</option>
                            </select>
                            <div class="invalid-feedback" id="error-status"></div>
                        </div>
                        <!-- نهاية حقل الحالة -->

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


    <!-- Modal الحذف -->
    @include('components.modals.delete-modal')


<script>
$(function() {
    // إعداد DataTable
    var table = $('#suppliers-groups-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('suppliers_groups.index') }}",
        columns: [
            { data: null, orderable: false, render: (d, t, r, m) => m.row + 1 },
            { data: 'code', name: 'code' },
            { data: 'name_ar', name: 'name_ar' },
            { data: 'name_en', name: 'name_en' },
            { data: 'description', name: 'description' },
            { data: 'branch', name: 'branch.name' },
            { data: 'created_by', name: 'creator.name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'status', name: 'status' },
            { data: 'action', orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'csv',   className: 'btn btn-outline-primary', text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel', className: 'btn btn-outline-success', text: '<i class="fas fa-file-excel"></i> XLSX' }
        ]
    });

    // reload, search, export
    $('#reload-table').click(() => table.ajax.reload());
    $('#search-suppliers-groups').on('input', e => table.search(e.target.value).draw());
    $('#export-csv').click(() => table.button('.buttons-csv').trigger());
    $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

    // فتح مودال إنشاء
    $('#create-btn').click(function() {
        $('#modal-title').text("{{ trans('suppliers.create_suppliers_groups') }}");
        $('#suppliers-group-form')[0].reset();
        $('#group-id').val('');
        $('#statusgroup').val('active');
        $('.invalid-feedback').hide();
        $('#suppliersGroupModal').modal('show');
    });

    // فتح مودال تعديل
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        $.get("{{ url('suppliers_groups') }}/" + id, function(res) {
            $('#modal-title').text("{{ trans('general.edit') }}");
            $('#group-id').val(res.id);
            $('#code').val(res.code);
            $('#name_ar').val(res.name_ar);
            $('#name_en').val(res.name_en);
            $('#description').val(res.description);
            $('#branch_id').val(res.branch_id);
            $('#statusgroup').val(res.status);
            $('.invalid-feedback').hide();
            $('#suppliersGroupModal').modal('show');
        });
    });

    // حفظ (إنشاء/تعديل)
    $('#suppliers-group-form').submit(function(e) {
        e.preventDefault();
        let id   = $('#group-id').val(),
            url  = id ? "{{ url('suppliers_groups') }}/" + id : "{{ route('suppliers_groups.store') }}",
            type = id ? 'PUT' : 'POST';

        let data = {
            code:        $('#code').val(),
            name_ar:     $('#name_ar').val(),
            name_en:     $('#name_en').val(),
            description: $('#description').val(),
            branch_id:   $('#branch_id').val(),
            status:      $('#statusgroup').val(),
            _token:      $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url:  url,
            type: type,
            data: data,
            success: function(res) {
                $('#suppliersGroupModal').modal('hide');
                showAlert('success', res.message);
                table.ajax.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors || {};
                $('.invalid-feedback').hide();
                $.each(errors, function(k, v) {
                    $('#error-' + k).text(v[0]).show();
                });
            }
        });
    });

    // حذف
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        $('#delete-id').val(id);
        $('#deleteModal').modal('show');
    });
    $('#delete-record').click(function() {
        let id  = $('#delete-id').val(),
            url = "{{ url('suppliers_groups') }}/" + id;
        $.ajax({
            url:  url,
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

    // دالة عرض التنبيه
    function showAlert(type, message) {
        let html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        $('#alert-container').html(html);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }
});
</script>
@endsection
