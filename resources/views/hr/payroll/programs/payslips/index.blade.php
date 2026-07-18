@extends('layouts.master_table')

@section('title')
    {{ __('hr.payslips') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ __('hr.payslips') }}</a>
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
                        <input type="search" class="form-control" id="search-payslips-table"
                            placeholder="{{ __('hr.search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Add Button -->
                        <a href="{{ route('hr_payslips.create') }}" class="btn btn-warning">
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
                <!-- Payslips Table -->
                <div class="card-body">
                    <table id="payslips-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ __('hr.sr_no') }}</th>
                                <th>{{ __('hr.action') }}</th>
                                <th>{{ __('hr.employee') }}</th>
                                <th>{{ __('hr.period') }}</th>
                                <th>{{ __('hr.total_amount') }}</th>
                                <th>{{ __('hr.status') }}</th>
                                <th>{{ __('hr.create_date') }}</th>
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
    $(document).ready(function() {
        var table = $('#payslips-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-payslips') }}",
            columns: [
                {data: 'created_at', name: 'created_at'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'period', name: 'period'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            order: [[0, 'desc']]
        });

        // Delete function
        window.deleteFunction = function(id) {
            Swal.fire({
                title: "{{ __('hr.are_you_sure') }}",
                text: "{{ __('hr.you_wont_be_able_to_revert_this') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
                cancelButtonClass: "btn btn-danger w-xs mt-2",
                confirmButtonText: "{{ __('hr.yes_delete_it') }}",
                cancelButtonText: "{{ __('hr.cancel') }}",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: "{{ __('hr.deleted') }}!",
                                    text: "{{ __('hr.payslip_has_been_deleted') }}",
                                    icon: "success",
                                    confirmButtonClass: "btn btn-primary w-xs mt-2",
                                    buttonsStyling: false
                                });
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    title: "{{ __('hr.error') }}!",
                                    text: response.message || "{{ __('hr.something_went_wrong') }}",
                                    icon: "error",
                                    confirmButtonClass: "btn btn-primary w-xs mt-2",
                                    buttonsStyling: false
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "{{ __('hr.error') }}!",
                                text: xhr.responseJSON?.message || "{{ __('hr.something_went_wrong') }}",
                                icon: "error",
                                confirmButtonClass: "btn btn-primary w-xs mt-2",
                                buttonsStyling: false
                            });
                        }
                    });
                }
            });
        };

        // Search functionality
        $('#search-payslips-table').on('input', function() {
            table.search(this.value).draw();
        });

        // Reload table
        $('#reload-table').on('click', function() {
            table.ajax.reload();
        });

        // Export to CSV
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
    });
</script>
@endsection 