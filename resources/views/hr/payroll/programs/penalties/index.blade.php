@extends('layouts.master_table')

@section('title')
    {{ trans('hr.penalties_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.penalties_title') }}</a>
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
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-penalties-table"
                            placeholder="{{ trans('hr.penalties_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <a href="{{ route('hr_penalties.create') }}" class="btn btn-warning">
                            <i class="ri-user-add-line"></i>
                        </a>
                        <button id="reload-table" class="btn btn-info">
                            <i class="ri-repeat-2-line"></i>
                        </button>
                        <button class="btn btn-primary" id="export-csv">
                            <i class="fa fa-file-csv"></i> CSV
                        </button>
                        <button class="btn btn-success" id="export-xlsx">
                            <i class="fas fa-file-excel"></i> XLSX
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <table id="penalties-table" class="table dt-responsive nowrap align-middle" style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.employee') }}</th>
                                <th>{{ trans('hr.violation') }}</th>
                                <th>{{ trans('hr.date') }}</th>
                                <th>{{ trans('hr.reason') }}</th>
                                <th>{{ trans('hr.create_date') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                        colors="primary:#f7b84b,secondary:#f06548"
                                        style="width:100px;height:100px"></lord-icon>
                                    <div class="mt-4">
                                        <h4>{{ __('hr.h4_delete') }}</h4>
                                        <p>{{ __('hr.p_delete') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <form action="" method="post" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="id" name="id">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">{{ __('hr.close') }}</button>
                                        <button type="button" class="btn btn-danger"
                                            id="delete-record">{{ __('hr.yes_delete') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function() {
        var table = $('#penalties-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ route('get-penalties') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'violation_name', name: 'violation_name' },
                { data: 'date', name: 'date' },
                { data: 'reason', name: 'reason' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Penalties Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Penalties Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
            ],
            language: {
                lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }} ",
                paginate: { "previous": "{{ __('datatables.previous') }}", "next": "{{ __('datatables.next') }}" }
            }
        });

        $('#reload-table').click(function() {
            table.ajax.reload();
        });

        $('#search-penalties-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
    });

    function deleteFunction(id) {
        $('#id').val(id);
        $('#deleteModal').modal('show');
    }

    $('#delete-record').click(function() {
        var id = $('#id').val();
        var url = '{{ route('hr_penalties.destroy', ':id') }}';
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                $('#penalties-table').DataTable().ajax.reload();
                toastr.success('{{ trans("hr.penalties_deleted_success") }}');
            },
            error: function(xhr, status, error) {
                toastr.error('{{ trans("hr.penalties_deleted_error") }}');
            }
        });
    });
</script>
@endsection
