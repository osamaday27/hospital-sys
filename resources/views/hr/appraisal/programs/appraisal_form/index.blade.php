@extends('layouts.master_table')

@section('title')
    {{ trans('hr.appraisal_management') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.appraisal_management') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <!-- DataTable Search Input moved to the right -->
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-appraisal-table"
                            placeholder="{{ trans('hr.appraisal_management') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                         <a  href="{{ url('/' . ($page = 'hr_appraisal/create')) }}"  class="btn btn-warning">
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
                <!-- Appraisal Table -->
                <div class="card-body">
                    <table id="appraisal-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.employee') }}</th>
                                <th>{{ trans('hr.employee_code') }}</th>
                                <th>{{ trans('hr.job') }}</th>
                                <th>{{ trans('hr.department') }}</th>
                                <th>{{ trans('hr.appraisal_interval') }}</th>
                                <th>{{ trans('hr.total_achieved') }}</th>
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
        var table = $('#appraisal-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ route('hr.appraisal.data') }}",
                data: function(d) {},
                error: function(xhr, error, thrown) {
                    console.log('DataTable Error:', error);
                    console.log('XHR:', xhr);
                    console.log('Thrown:', thrown);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'employee_code', name: 'employee_code' },
                { data: 'job_name', name: 'job_name' },
                { data: 'department_name', name: 'department_name' },
                { data: 'appraisal_interval', name: 'appraisal_interval' },
                { data: 'total_achieved', name: 'total_achieved' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Appraisal Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Appraisal Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-appraisal-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-pdf').click(function() { table.button('.buttons-pdf').trigger(); });
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
        $('#export-print').click(function() { table.button('.buttons-print').trigger(); });
    });

    function deleteFunction(id) {
        $('#id').val(id);
        $('#deleteModal').modal('show');
    }

    $('#delete-record').click(function() {
        var id = $('#id').val();
        var url = '{{ route('hr.appraisal.destroy', ':id') }}';
        url = url.replace(':id', id);

        console.log('Deleting appraisal with ID:', id);
        console.log('URL:', url);

        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    $('#deleteModal').modal('hide');
                    $('#appraisal-table').DataTable().ajax.reload();
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Record deleted successfully');
                    } else {
                        alert(response.message || 'Record deleted successfully');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Error deleting record');
                    } else {
                        alert(response.message || 'Error deleting record');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log('Error response:', xhr);
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response text:', xhr.responseText);
                var errorMessage = 'There was an error deleting the record.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMessage);
                } else {
                    alert(errorMessage);
                }
            }
        });
    });
</script>
@endsection 