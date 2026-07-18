@extends('layouts.master_table')

@section('title')
    {{ trans('hr.salary_rules_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.salary_rules_title') }}</a>
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
                        <input type="search" class="form-control" id="search-salary-rules-table"
                            placeholder="{{ trans('hr.salary_rules_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <a href="{{ route('hr_salary_rules.create') }}" class="btn btn-warning">
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
                    <table id="salary-rules-table" class="table dt-responsive nowrap align-middle" style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.name') }}</th>
                                <th>{{ trans('hr.type') }}</th>
                                <th>{{ trans('hr.rule_type') }}</th>
                                <th>{{ trans('hr.added_automatically') }}</th>
                                <th>{{ trans('hr.sequence') }}</th>
                                <th>{{ trans('hr.status') }}</th>
                                <th>{{ trans('hr.create_date') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<script>
    $(function() {
        var table = $('#salary-rules-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ route('get-salary-rules') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'rule_type', name: 'rule_type' },
                { data: 'added_automatically', name: 'added_automatically' },
                { data: 'sequence', name: 'sequence' },
                { data: 'is_active', name: 'is_active' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Salary Rules Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Salary Rules Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-salary-rules-table').on('input', function() {
            table.search(this.value).draw();
        });

        $('#export-csv').click(function() { table.button('.buttons-csv').trigger(); });
        $('#export-xlsx').click(function() { table.button('.buttons-excel').trigger(); });
    });

    function deleteFunction(id) {
        Swal.fire({
            title: "{{ __('hr.h4_delete') }}",
            text: "{{ __('hr.p_delete') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{ __('hr.yes_delete') }}",
            cancelButtonText: "{{ __('hr.close') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('hr/salary-rules') }}/" + id,
                    type: "DELETE",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "{{ __('hr.success') }}",
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                $('#salary-rules-table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "{{ __('hr.error') }}",
                                text: response.message,
                                icon: "error"
                            });
                        }
                    }
                });
            }
        });
    }
</script>
@endsection 