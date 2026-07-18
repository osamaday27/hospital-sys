@extends('layouts.master_table')

@section('title')
    {{ trans('hr.loans_management') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.loans_management') }}</a>
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
                        <input type="search" class="form-control" id="search-loans-table"
                            placeholder="{{ trans('hr.search_loans') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <a href="{{ route('hr_loans.create') }}" class="btn btn-warning">
                            <i class="ri-user-add-line"></i> {{ trans('hr.new_loan') }}
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
                    <table id="loans-table" class="table dt-responsive nowrap align-middle" style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.employee') }}</th>
                                <th>{{ trans('hr.loan_type') }}</th>
                                <th>{{ trans('hr.loan_amount') }}</th>
                                <th>{{ trans('hr.installment_value') }}</th>
                                <th>{{ trans('hr.installments') }}</th>
                                <th>{{ trans('hr.loan_date') }}</th>
                                <th>{{ trans('hr.status') }}</th>
                                <th>{{ trans('hr.actions') }}</th>
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
        function notify(type, message) {
            if (window.toastr && typeof toastr[type] === 'function') {
                toastr[type](message);
            } else if (window.Swal && type === 'success') {
                Swal.fire({ icon: 'success', title: message, timer: 1500, showConfirmButton: false });
            } else if (window.Swal && type === 'error') {
                Swal.fire({ icon: 'error', title: message });
            } else {
                alert(message);
            }
        }
        var table = $('#loans-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-loans') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'loan_type_text', name: 'loan_type' },
                { data: 'amount', name: 'amount' },
                { data: 'installment_value', name: 'installment_value' },
                { data: 'no_installments', name: 'no_installments' },
                { data: 'loan_date', name: 'loan_date' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Loans Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Loans Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-loans-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
    });

    function deleteFunction(id) {
        $('#id').val(id);
    var action = '{{ route('hr_loans.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id);
    $('#delete-form').attr('action', action);
        $('#deleteModal').modal('show');
    }

    $('#delete-record').click(function() {
    var id = $('#id').val();
    var url = $('#delete-form').attr('action');
    var $btn = $('#delete-record');
    $btn.prop('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: {
            "_token": "{{ csrf_token() }}",
            "_method": "DELETE"
        },
        success: function(response) {
            notify('success', '{{ trans("hr.loan_deleted_success") }}');
        },
        error: function(xhr, status, error) {
            notify('error', '{{ trans("hr.loan_deleted_error") }}');
        },
        complete: function() {
            $('#deleteModal').modal('hide');
            $('#loans-table').DataTable().ajax.reload();
            $btn.prop('disabled', false);
        }
    });
    });

    // Handle approve/reject buttons
    $(document).on('click', '.approve-btn', function() {
        const loanId = $(this).data('id');
        if (confirm('{{ trans("hr.confirm_approve_loan") }}')) {
            $.ajax({
                url: "{{ url('hr_loans') }}/" + loanId + "/approve",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#loans-table').DataTable().ajax.reload();
                    notify('success', response.message);
                }
            });
        }
    });

    $(document).on('click', '.reject-btn', function() {
        const loanId = $(this).data('id');
        if (confirm('{{ trans("hr.confirm_reject_loan") }}')) {
            $.ajax({
                url: "{{ url('hr_loans') }}/" + loanId + "/reject",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#loans-table').DataTable().ajax.reload();
                    notify('success', response.message);
                }
            });
        }
    });
</script>
@endsection
