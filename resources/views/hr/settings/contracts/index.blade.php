@extends('layouts.master_table')

@section('title')
    {{ trans('hr.contracts_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.contracts_title') }}</a>
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
                        <input type="search" class="form-control" id="search-contracts-table"
                            placeholder="{{ trans('hr.contracts_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                         <a  href="{{ url('/' . ($page = 'hr_contracts/create')) }}"  class="btn btn-warning">
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
                <!-- jobs Table -->
                <div class="card-body">
                    <table id="contracts-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.contract_name') }}</th>
                                <th>{{ trans('hr.employee') }}</th>
                                <th>{{ trans('hr.contract_start_date') }}</th>
                                <th>{{ trans('hr.contract_end_date') }}</th>
                                <th>{{ trans('hr.job_position') }}</th>
                                <th>{{ trans('hr.department') }}</th>
                                <th>{{ trans('hr.salary_structure_type') }}</th>
                                <th>{{ trans('hr.status') }}</th>
                                <th>{{ trans('hr.create_date') }}</th>
                                <th>{{ trans('hr.salary_level') }}</th>
                                <th>{{ trans('hr.hour_amount') }}</th>
                                <th>{{ trans('hr.shift_amount') }}</th>
                                <th>{{ trans('hr.basic_salary') }}</th>
                                <th>{{ trans('hr.allowances') }}</th>
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
        var table = $('#contracts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ url('get-contracts') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'contract_name', name: 'contract_name' },
                { data: 'employee', name: 'employee' },
                { data: 'start_date', name: 'start_date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'job', name: 'job' },
                { data: 'department', name: 'department' },
                { data: 'salary_structure_type', name: 'salary_structure_type' },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'salary_level', name: 'salary_level' },
                { data: 'hour_amount', name: 'hour_amount' },
                { data: 'shift_amount', name: 'shift_amount' },
                { data: 'basic_salary', name: 'basic_salary' },
                { data: 'allowances', name: 'allowances' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Contracts Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Contracts Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-contracts-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-pdf').click(function() { table.button('.buttons-pdf').trigger(); });
        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
        $('#export-print').click(function() { table.button('.buttons-print').trigger(); });
    });

    function deleteFunction(id) {
        $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
        var action = '{{ route('hr_contracts.destroy', ['id' => '__ID__']) }}'.replace('__ID__', id);
        $('#delete-form').attr('action', action);
        $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
    }

    $('#delete-record').click(function() {
        var id = $('#id').val(); // الحصول على ID من الحقل المخفي
        var url = $('#delete-form').attr('action');

        // إرسال طلب DELETE باستخدام AJAX (via POST + _method)
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
                "_method": "DELETE"
            },
            success: function(response) {
                $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                $('#contracts-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                alert("There was an error deleting the record.");
            }
        });
    });
</script>
@endsection
