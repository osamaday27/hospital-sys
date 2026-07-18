
@extends('layouts.master_table')

@section('title')
    {{ trans('call_center_trans.questionnaires') }}
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('call_center_trans.questionnaires') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <input type="search" class="form-control" id="search-questionnaires" placeholder="ابحث عن استبيان" style="width: 250px;">
                    <div class="hstack gap-2">
                        <a href="{{ route('questionnaires.create') }}" class="btn btn-outline-secondary">
                            <i class="ri-add-line"></i> {{ trans('call_center_trans.create_questionnaire') }}
                        </a>
                        <button id="reload-table" class="btn btn-info"><i class="ri-repeat-2-line"></i></button>
                        <button class="btn btn-primary" id="export-csv"><i class="fa fa-file-csv"></i> CSV</button>
                        <button class="btn btn-success" id="export-xlsx"><i class="fas fa-file-excel"></i> XLSX</button>
                    </div>
                </div>
                <div id="alert-container"></div>

                <div class="card-body">
                    <table id="questionnaires-table" class="table dt-responsive nowrap align-middle" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>{{ trans('inpatient_trans.sr_no') }}</th>
                                <th>{{ trans('inpatient_trans.action') }}</th>
                                <th>{{ trans('inpatient_trans.name_ar') }}</th>
                                <th>{{ trans('inpatient_trans.name_en') }}</th>
                                <th>{{ trans('inpatient_trans.type') }}</th>
                                <th>{{ trans('inpatient_trans.status') }}</th>
                                <th>{{ trans('inpatient_trans.created_at') }}</th>
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
                    <h4 class="mt-3">هل أنت متأكد؟</h4>
                    <p>سيتم حذف الاستبيان نهائيًا، هل تريد المتابعة؟</p>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <form action="" method="post" id="delete-form">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" id="delete-id">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                            <button type="button" class="btn btn-danger" id="delete-record">نعم، احذف</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var table = $('#questionnaires-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('questionnaires.index') }}",
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
                    { data: 'type', name: 'type' },
                    {
                        data: 'status', name: 'status', render: function (data) {
                            return data == 1 ? 'نشط' : 'غير نشط';
                        }
                    },


                    { data: 'created_at', name: 'created_at' }
                ],
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', title: 'Questionnaires Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                    { extend: 'excel', title: 'Questionnaires Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
                ]
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-questionnaires').on('input', function() {
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
                var url = '{{ route('questionnaires.destroy', ':id') }}'.replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { "_token": "{{ csrf_token() }}" },
                    success: function (response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        showAlert('success', response.message);
                    },
                    error: function (xhr) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'حدث خطأ.';
                        showAlert('error', message);
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
    </script>
@endsection
