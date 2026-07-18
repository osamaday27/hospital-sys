@extends('layouts.master_table')

@section('title')
    {{ trans('hr.allowances') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.allowances') }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('hr_allowances.create') }}" class="btn btn-primary">{{ trans('hr.add') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>{{ trans('hr.Number') }}</th>
                                <th>{{ trans('hr.name') }}</th>
                                <th>{{ trans('hr.allowance_type') }}</th>
                                <th>{{ trans('hr.amount') }}</th>
                                <th>{{ trans('hr.create_date') }}</th>
                                <th>{{ trans('hr.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('hr_allowances.get_allowances') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'allowance_type_name', name: 'allowance_type_name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function deleteFunction(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                var url = '{{ route("hr_allowances.destroy", ":id") }}';
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        $('#datatable').DataTable().ajax.reload();
                    }
                });
            }
        }
    </script>
@endsection 