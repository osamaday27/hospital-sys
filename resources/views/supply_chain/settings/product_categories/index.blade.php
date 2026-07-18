
@extends('layouts.master_table')

@section('title')
    {{ trans('supply_chain_trans.product_categories') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('supply_chain_trans.title') }}</a>
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
                    <input type="search" class="form-control" id="search-product_categories" placeholder="{{ trans('supply_chain_trans.search_product_categories') }}" style="width: 250px;">
                    <div class="hstack gap-2">
                        <a href="{{ route('product_categories.create') }}" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ trans('supply_chain_trans.create_product_categories') }}
                        </a>

                        <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
                        <button class="btn btn-primary" id="export-csv"><i class="fa fa-file-csv"></i> CSV</button>
                        <button class="btn btn-success" id="export-xlsx"><i class="fas fa-file-excel"></i> XLSX</button>
                    </div>
                </div>
                <div id="alert-container"></div>

                <div class="card-body">
                    <table id="product-categories-table" class="table dt-responsive nowrap align-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>{{ trans('inpatient_trans.sr_no') }}</th>
                                <th>{{ trans('inpatient_trans.action') }}</th>
                                <th>{{ trans('inpatient_trans.name_ar') }}</th>
                                <th>{{ trans('inpatient_trans.name_en') }}</th>
                                <th>{{ trans('inpatient_trans.status') }}</th>
                                <th>{{ trans('supply_chain_trans.group_type') }}</th>
                                <th>{{ trans('supply_chain_trans.branch') }}</th>
                                <th>{{ trans('supply_chain_trans.sort') }}</th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <h4 class="mt-3">{{ trans('call_center_trans.h4_delete') }}</h4>
                    <p>{{ trans('call_center_trans.p_delete') }}</p>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <form action="" method="post" id="delete-form">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="delete-id">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('call_center_trans.cancel') }}</button>
                            <button type="button" class="btn btn-danger" id="delete-record">{{ trans('call_center_trans.yes_delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var table = $('#product-categories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('product_categories.index') }}",
                columns: [
    {
        data: 'id',
        name: 'id',
        orderable: false,
        render: function (data, type, row, meta) {
            return meta.row + 1;
        }
    },
    { data: 'action', name: 'action', orderable: false, searchable: false },
    { data: 'name_ar', name: 'name_ar' },
    { data: 'name_en', name: 'name_en' },
    {
        data: 'status',
        name: 'status',
        render: function (data) {
            return data == 1 ? 'نشط' : 'غير نشط';
        }
    },
    {
        data: 'type',
        name: 'type',
        render: function (data) {
            return data == 'main' ? 'رئيسي' : 'فرعي';
        }
    },
    { data: 'branch_name', name: 'branch.name' },
    { data: 'sort', name: 'sort' }
],

                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', title: 'Product Categories Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                    { extend: 'excel', title: 'Product Categories Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
                ]
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-product_categories').on('input', function() {
                table.search(this.value).draw();
            });

            $('#export-csv').click(function() {
                table.button('.buttons-csv').trigger();
            });

            $('#export-xlsx').click(function() {
                table.button('.buttons-excel').trigger();
            });

            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                deleteFunction(id);
            });

            $('#delete-record').click(function() {
                var id = $('#delete-id').val();
                var url = '{{ route('product_categories.destroy', ':id') }}'.replace(':id', id);

                $.ajax({
        url: url,
        type: 'DELETE',
        data: { "_token": "{{ csrf_token() }}" },
        success: function(response) {
            $('#deleteModal').modal('hide');

            // التحقق من نجاح العملية
            if (response.success) {
                // في حالة النجاح
                showAlert('success', response.message);
                table.ajax.reload();
            } else {
                // في حالة الفشل (مثل وجود فئات فرعية)
                showAlert('error', response.message);
            }
        },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        showAlert('error', "حدث خطأ أثناء الحذف.");
                    }
                });
            });
        });

        function deleteFunction(id) {
            $('#delete-id').val(id);
            $('#deleteModal').modal('show');
        }


        function showAlert(type, message) {
            var alertHtml = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow" role="alert">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 me-2">
                        <i class="ri-${type === 'success' ? 'check-line' : 'alert-line'} align-middle"></i>
                    </div>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>`;
            $('#alert-container').append(alertHtml);
            setTimeout(() => $(".alert").alert('close'), 5000);
        }
        setTimeout(() => {
        $(".alert").fadeOut();
    }, 3000);
    </script>
@endsection
