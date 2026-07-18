@extends('layouts.master_table')

@section('title')
    {{ trans('hr.allowances_types') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.allowances_types') }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('hr_allowances_types.create') }}" class="btn btn-primary">{{ trans('hr.add') }}</a>
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
                                <th>{{ trans('hr.name_ar') }}</th>
                                <th>{{ trans('hr.name_en') }}</th>
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
                ajax: '{{ route('hr_allowances_types.get_allowances_types') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name_ar', name: 'name_ar' },
                    { data: 'name_en', name: 'name_en' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function deleteFunction(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                var url = '{{ route("hr_allowances_types.destroy", ":id") }}';
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