@extends('layouts.master_table')

@section('title')
    {{ trans('service_settings.service_management_service_levels') }}
@stop



@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('service_settings.service_management') }}</a>
                        </li>
                    </ol>
                </div>
                  <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a
                                href="{{ route('pricing_management.index') }}">{{ trans('service_settings.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('service_settings.service_management_service_levels') }}</li>
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
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-level"
                            placeholder="{{ trans('service_settings.search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2">
                        <a href="{{ route('service_management_level1.create') }}" class="btn btn-outline-secondary waves-effect waves-light shadow-none">
                            <i class="ri-add-line"></i> {{ trans('service_settings.create_level') }}
                        </a>
                        <a href="{{ route('service_management_level2.create') }}" class="btn btn-outline-success waves-effect waves-light shadow-none">
                            <i class="ri-add-line"></i> {{ trans('service_settings.create_level2') }}
                        </a>
                        <a href="{{ route('service_management_level3.create') }}" class="btn btn-outline-dark waves-effect waves-light shadow-none">
                            <i class="ri-add-line"></i> {{ trans('service_settings.create_level3') }}
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

                <!-- Table Section -->
                <div class="card-body">
                    <table id="level1-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('service_settings.sr_no') }}</th>
                                <th>{{ trans('service_settings.action') }}</th>
                                <th>{{ trans('service_settings.name') }}</th>
                                <th>{{ trans('service_settings.hospital_code') }}</th>
                                <th>{{ trans('service_settings.level_type') }}</th>
                                <th>{{ trans('service_settings.status') }}</th>

                                <th>{{ trans('service_settings.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
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
                                    colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                <div class="mt-4">
                                    <h4>{{ __('service_settings.h4_delete') }}</h4>
                                    <p>{{ __('service_settings.p_delete') }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <form action="" method="post" id="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" id="id" name="id">
                                    <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">{{ __('service_settings.close') }}</button>
                                    <button type="button" class="btn btn-danger"
                                        id="delete-record">{{ __('service_settings.yes_delete') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            var table = $('#level1-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('get-servicelevel1') }}",
                    data: function(d) {}
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'hospital_code',
                        name: 'hospital_code'
                    },
                    {
                        data: 'level_type',
                        name: 'level_type'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        title: 'service Data',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'service Data',
                        text: '<i class="fas fa-file-excel"></i> XLSX',
                        className: 'btn btn-outline-success'
                    }
                ],
                language: {
                    lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                    info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                    processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }}",
                    paginate: {
                        "previous": "{{ __('datatables.previous') }}",
                        "next": "{{ __('datatables.next') }}"
                    }
                }
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-level').on('input', function() {
                table.search(this.value).draw();
            });

            $('#export-pdf').click(function() {
                table.button('.buttons-pdf').trigger();
            });
            $('#export-csv').click(function() {
                table.button('.buttons-csv').trigger();
            });
            $('#export-xlsx').click(function() {
                table.button('.buttons-excel').trigger();
            });
            $('#export-print').click(function() {
                table.button('.buttons-print').trigger();
            });
        });

        function deleteFunction(id) {
            $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
            $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
        }

        $('#delete-record').click(function() {
            var id = $('#id').val(); // الحصول على ID من الحقل المخفي
            var url = '{{ route('service_management_level1.destroy', ':id') }}';
            url = url.replace(':id', id); // استبدال الـ :id بالـ ID الفعلي

            // إرسال طلب DELETE باستخدام AJAX
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
                },
                success: function(response) {
                    $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                    $('#level1-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("There was an error deleting the record.");
                }
            });
        });
    </script>
@endsection
