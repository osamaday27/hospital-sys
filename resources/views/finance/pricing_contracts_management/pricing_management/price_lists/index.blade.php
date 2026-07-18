@extends('layouts.master_table')

@section('title')
    {{ trans('pricing_list.title') }}
@stop


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ trans('pricing_list.title') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url()->previous() }}">{{ trans('dashboard.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('pricing_list.title') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                        <div  id="alert-container"></div>

                    <div class="hstack gap-2">
                        <a href="{{ route('price_lists.create') }}" class="btn btn-outline-primary">
                            <i class="ri-add-line"></i> {{ trans('pricing_list.create') }}
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
                    <table id="pricing-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('pricing_list.id') }}</th>
                                <th>{{ trans('pricing_list.actions') }}</th>
                                <th>{{ trans('pricing_list.name') }}</th>
                                <th>{{ trans('pricing_list.fiscal_year') }}</th>
                                <th>{{ trans('pricing_list.branches') }}</th>
                                <th>{{ trans('pricing_list.start_date') }}</th>
                                <th>{{ trans('pricing_list.end_date') }}</th>
                                <th>{{ trans('pricing_list.list_type') }}</th>
                                <th>{{ trans('pricing_list.user') }}</th>
                                <th>{{ trans('pricing_list.status') }}</th>
                                <th>{{ trans('pricing_list.currency') }}</th>
                                <th>{{ trans('pricing_list.created_at') }}</th>

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
    </div>

    <script>
        $(function() {
            var table = $('#pricing-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('get-pricelists') }}",
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
                        data: 'fiscal_year',
                        name: 'fiscal_year'
                    },
                    {
                        data: 'branch',
                        name: 'branch'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'list_type',
                        name: 'list_type'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'currency',
                        name: 'currency'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },

                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        title: 'pricing list Data',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'pricing list Data',
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

        // دالة لفتح نافذة التأكيد للحذف
        function deleteFunction(id) {
            $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
            $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
        }

        // تنفيذ عملية الحذف باستخدام AJAX
        $('#delete-record').click(function() {
            var id = $('#id').val(); // الحصول على ID من الحقل المخفي
            var url = '{{ route('service_management_level3.destroy', ':id') }}';
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
                    $('#level3-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable

                    // عرض الرسالة من الـ Controller
                    if (response.status == 'success') {
                        showAlert('success', response.message); // عرض رسالة النجاح
                    } else {
                        showAlert('error', response.message); // عرض رسالة الخطأ
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    showAlert('error',
                        "There was an error deleting the record."); // عرض رسالة الخطأ العامة
                }
            });
        });

        // دالة لعرض الرسائل بشكل مخصص
        function showAlert(type, message) {
            // تحديد نوع التنبيه
            var alertClass = (type === 'success') ? 'alert-success' : 'alert-danger';

            // إنشاء رسالة التنبيه
            var alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="ri-${type === 'success' ? 'check-line' : 'alert-line'} align-middle"></i>
                </div>
                <div class="flex-grow-1">
                    ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    `;

            // إضافة الرسالة إلى الحاوية
            $('#alert-container').append(alertHtml);
            $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة

            // إخفاء الرسالة تلقائيًا بعد 5 ثوانٍ
            setTimeout(function() {
                $('#alert-container .alert').first().alert('close');
            }, 15000);
        }
    </script>

@endsection
