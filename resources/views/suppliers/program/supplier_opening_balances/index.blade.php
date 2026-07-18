@extends('layouts.master_table')

@section('title')
    {{ trans('suppliers.opening_balance_title') }}
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
                    <li class="breadcrumb-item active">{{ trans('suppliers.opening_balance_title') }}</li>
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
                    id="search-opening-balance"
                    class="form-control"
                    placeholder="{{ trans('suppliers.opening_balance_list') }}"
                    style="width: 250px;"
                >
                <div class="hstack gap-2">
                    <button id="create-opening-balance-btn" class="btn btn-outline-secondary">
                        <i class="ri-add-line"></i> {{ trans('suppliers.add_opening_balance') }}
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
                <table id="opening-balances-table" class="table dt-responsive nowrap align-middle w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('suppliers.supplier_ar') }}</th>
                            <th>{{ trans('suppliers.supplier_en') }}</th>
                            <th>{{ trans('suppliers.supplier_code') }}</th>
                            <th>{{ trans('suppliers.year') }}</th>
                            <th>{{ trans('suppliers.credit_balance') }}</th>
                            <th>{{ trans('suppliers.debit_balance') }}</th>
                            <th>{{ trans('general.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: إضافة/تعديل رصيد افتتاحي -->
<div class="modal fade" id="openingBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="opening-balance-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="opening-id">

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
                            {{ trans('suppliers.year') }} <span class="text-danger">*</span>
                        </label>
                        <select id="year" class="form-select">
                            <option value="">{{ trans('suppliers.choose_year') }}</option>
                            @foreach($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="error-year"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            {{ trans('suppliers.credit_balance') }} <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" id="credit_balance" class="form-control">
                        <div class="invalid-feedback" id="error-credit_balance"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            {{ trans('suppliers.debit_balance') }} <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.01" id="debit_balance" class="form-control">
                        <div class="invalid-feedback" id="error-debit_balance"></div>
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

<!-- Modal الحذف -->
@include('components.modals.delete-modal')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function() {
    var usedSuppliers = @json($usedSupplierIds);

    // إعداد DataTable
    var table = $('#opening-balances-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('supplier_opening_balances.index') }}",
        columns: [
            { data: null, orderable: false, render: (d, t, r, m) => m.row + 1 },
            { data: 'supplier_ar',   name:'supplier.name_ar' },
            { data: 'supplier_en',   name:'supplier.name_en' },
            { data: 'supplier_code', name:'supplier.code' },
            { data: 'year',          name:'year' },
            { data: 'credit_balance',name:'credit_balance' },
            { data: 'debit_balance', name:'debit_balance' },
            { data: 'action',        orderable: false, searchable: false }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'csv',   className: 'btn btn-outline-primary', text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel', className: 'btn btn-outline-success', text: '<i class="fas fa-file-excel"></i> XLSX' }
        ]
    });

    // reload, search, export
    $('#reload-table').click(() => table.ajax.reload());
    $('#search-opening-balance').on('input', e => table.search(e.target.value).draw());
    $('#export-csv').click(() => table.button('.buttons-csv').trigger());
    $('#export-xlsx').click(() => table.button('.buttons-excel').trigger());

    // دالة مسح أخطاء الفورم
    function clearErrors() {
        $('.invalid-feedback').text('').hide();
        $('.is-invalid').removeClass('is-invalid');
    }

    // فتح مودال إنشاء
    $('#create-opening-balance-btn').click(function() {
        $('#modal-title').text("{{ trans('suppliers.add_opening_balance') }}");
        $('#opening-balance-form')[0].reset();
        $('#opening-id').val('');
        clearErrors();
        $('#supplier_id').prop('disabled', false);
        $('#openingBalanceModal').modal('show');
    });

    // التحقق من المورد المكرر
    $('#supplier_id').change(function(){
        var id = parseInt(this.value) || 0;
        if (usedSuppliers.includes(id) && !$('#opening-id').val()) {
            showAlert('warning', '{{ __("suppliers.toast_already_exists") }}');
            $(this).val('');
        }
    });

    // فتح مودال تعديل
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        clearErrors();
        $.get("{{ url('supplier_opening_balances') }}/" + id + "/edit", function(res) {
            $('#modal-title').text("{{ trans('suppliers.edit_opening_balance') }}");
            $('#opening-id').val(res.id);
            $('#supplier_id').val(res.supplier_id).prop('disabled', true);
            $('#year').val(res.year);
            $('#credit_balance').val(res.credit_balance);
            $('#debit_balance').val(res.debit_balance);
            $('#openingBalanceModal').modal('show');
        });
    });

    // حفظ (إنشاء/تعديل)
    $('#opening-balance-form').submit(function(e) {
        e.preventDefault();
        clearErrors();
        let id   = $('#opening-id').val(),
            url  = id ? "{{ url('supplier_opening_balances') }}/" + id : "{{ route('supplier_opening_balances.store') }}",
            type = id ? 'PUT' : 'POST';
        let data = {
            supplier_id:    $('#supplier_id').val(),
            year:           $('#year').val(),
            credit_balance: $('#credit_balance').val(),
            debit_balance:  $('#debit_balance').val(),
            _token:         $('meta[name="csrf-token"]').attr('content')
        };
        $.ajax({
            url: url,
            type: type,
            data: data,
            success: function(res) {
                $('#openingBalanceModal').modal('hide');
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

    // حذف
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        $('#delete-id').val(id);
        $('#deleteModal').modal('show');
    });
    $('#delete-record').click(function() {
        let id  = $('#delete-id').val(),
            url = "{{ url('supplier_opening_balances') }}/" + id;
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
$('.supplier-select').select2({
    dropdownParent: $('#openingBalanceModal'),
    width: '100%',
    dir: "rtl", // لو الموقع عربي بالكامل
    placeholder: "{{ trans('suppliers.choose_supplier') }}",
    language: {
        noResults: function () {
            return "{{ trans('general.no_results_found') }}";
        }
    }
});

</script>
@endsection
