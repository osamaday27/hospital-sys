@extends('layouts.master_table')

@section('title')
    {{ trans('supply_chain_trans.store_main_data') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            {{ trans('supply_chain_trans.store_main_data') }}
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
                    <input type="search" class="form-control" id="search-stores" placeholder="{{ trans('supply_chain_trans.search_stores') }}" style="width: 250px;">
                    <div class="hstack gap-2">
                        <a href="{{ route('store_main_data.create') }}" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ trans('supply_chain_trans.create_store') }}
                        </a>
                        <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
                        <button class="btn btn-primary" id="export-csv"><i class="fa fa-file-csv"></i> CSV</button>
                        <button class="btn btn-success" id="export-xlsx"><i class="fas fa-file-excel"></i> XLSX</button>
                    </div>
                </div>

                <div id="alert-container"></div>

                <div class="card-body">
                    <table id="stores-table" class="table dt-responsive nowrap align-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('supply_chain_trans.system_code') }}</th>
                                <th>{{ trans('supply_chain_trans.manual_code') }}</th>
                                <th>{{ trans('supply_chain_trans.store_name') }}</th>
                                <th>{{ trans('supply_chain_trans.store_type') }}</th>
                                <th>{{ trans('inpatient_trans.status') }}</th>
                                <th>{{ trans('general.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal الحذف -->
    @include('components.modals.delete-modal')


    <!-- JavaScript -->
    <script>
        $(function() {
            var table = $('#stores-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('store_main_data.index') }}",
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'id', name: 'id' }, // كود النظام
                    { data: 'manual_code', name: 'manual_code' },
                    { data: 'name_ar', name: 'name_ar' },
                    { data: 'store_type', name: 'store_type' },
                    {
                        data: 'status',
                        name: 'status',
                        
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                    { extend: 'excel', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
                ]
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-stores').on('input', function() {
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
                $('#delete-id').val(id);
                $('#deleteModal').modal('show');
            });

            $('#delete-record').click(function() {
                var id = $('#delete-id').val();
                var url = '{{ route('store_main_data.destroy', ':id') }}'.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { "_token": "{{ csrf_token() }}" },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        if (response.success) {
                            showAlert('success', response.message);
                            table.ajax.reload();
                        } else {
                            showAlert('error', response.message);
                        }
                    },
                    error: function() {
                        showAlert('error', "حدث خطأ أثناء الحذف.");
                    }
                });
            });

            function showAlert(type, message) {
                let alertHtml = `<div class="alert alert-${type} alert-dismissible fade show shadow" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
                $('#alert-container').html(alertHtml);
                setTimeout(() => $(".alert").alert('close'), 5000);
            }
        });
    </script>
@endsection
