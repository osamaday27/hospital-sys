@extends('layouts.master_table')

@section('title')
    {{ trans('hr.investigations_title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('hr.investigations_title') }}</a>
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
                        <input type="search" class="form-control" id="search-investigations-table"
                            placeholder="{{ trans('hr.investigations_search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <a href="{{ route('hr_investigations.create') }}" class="btn btn-warning">
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
                    <table id="investigations-table" class="table dt-responsive nowrap align-middle" style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.sr_no') }}</th>
                                <th>{{ trans('hr.action') }}</th>
                                <th>{{ trans('hr.employee_name') }}</th>
                                <th>{{ trans('hr.investigation_address') }}</th>
                                <th>{{ trans('hr.investigation_type') }}</th>
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
        var table = $('#investigations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { 
               url: "{{ route('get-investigations') }}",
                data: function(d) {}
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'employee_name', name: 'employee_name' },
                { data: 'investigation_address', name: 'investigation_address' },
                { data: 'investigation_type', name: 'investigation_type' },
                { data: 'created_at', name: 'created_at' }
            ],
            pageLength: 20,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', title: 'Investigations Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                { extend: 'excel', title: 'Investigations Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
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

        $('#search-investigations-table').on('input', function() {
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
                const url = "{{ route('hr_investigations.destroy', ['id' => '__ID__']) }}".replace('__ID__', id);
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": "DELETE"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "{{ __('hr.success') }}",
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                $('#investigations-table').DataTable().ajax.reload();
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