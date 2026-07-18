@extends('layouts.master_table')

@section('title')
    {{ trans('hr.certificates_types') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('hr.certificates_types') }}</h4>
                <div class="page-title-right">
                    <a href="{{ route('hr_certificates_types.create') }}" class="btn btn-primary">{{ trans('hr.add') }}</a>
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
                ajax: '{{ route('hr_certificates_types.get_certificates_types') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name_ar', name: 'name_ar' },
                    { data: 'name_en', name: 'name_en' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                pageLength: 20,
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'csv', title: 'Certificate Types', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary' },
                    { extend: 'excel', title: 'Certificate Types', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success' }
                ],
                language: {
                    lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                    info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                    processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }} ",
                    paginate: { "previous": "{{ __('datatables.previous') }}", "next": "{{ __('datatables.next') }}" }
                }
            });
        });

        function deleteFunction(id) {
            if (confirm("{{ trans('hr.del_confirmation_msg') }}")) {
                var url = '{{ route("hr_certificates_types.destroy", ":id") }}';
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