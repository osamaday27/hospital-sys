@extends('layouts.master_table')

@section('title')
    {{ trans('floors_trans.title') }}
@stop
<style>
    .dataTables_filter {
        display: none;
    }

    .dt-buttons {
        display: none;
    }
    /* تغيير شكل الـ Processing Spinner */
    .dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #333;
        /* يمكنك تغيير اللون */
        font-size: 13px;
        /* حجم الخط */
        z-index: 9999;
        background-color: rgba(255, 255, 255, 0.8);
        /* خلفية شفافة */
        padding: 10px 20px;
        border-radius: 5px;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('floors_trans.title') }}</a>
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
                    <div id="alert-container"></div>

                    <!-- DataTable Search Input moved to the right -->
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-floors_settings"
                            placeholder="{{ trans('floors_trans.search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                        <a href="{{  route('floors_settings.create', ['id' =>Crypt::encryptString($buildingSettings->id)])}}" class="btn btn-warning">
                            <i class="ri-add-box-line"></i>
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
                <input type="hidden" id="building-id" value="{{ $buildingSettings->id }}">
                <!-- floors_settings Table -->
                <div class="card-body">
                    <table id="getfloors_settings-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('floors_trans.sr_no') }}</th>
                                <th>{{ trans('floors_trans.action') }}</th>
                                <th>{{ trans('floors_trans.building_name') }}</th>
                                <th>{{ trans('floors_trans.name_ar') }}</th>
                                <th>{{ trans('floors_trans.name_en') }}</th>
                                <th>{{ trans('floors_trans.rooms_count') }}</th>
                                <th>{{ trans('floors_trans.type') }}</th>
                                <th>{{ trans('floors_trans.status') }}</th>
                                <th>{{ trans('floors_trans.create_date') }}</th>
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
                                        <h4>{{ __('floors_trans.h4_delete') }}</h4>
                                        <p>{{ __('floors_trans.p_delete') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <form action="" method="post" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="id" name="id">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">{{ __('floors_trans.close') }}</button>
                                        <button type="button" class="btn btn-danger"
                                            id="delete-record">{{ __('floors_trans.yes_delete') }}</button>
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
            var buildingId = $('#building-id').val(); // الحصول على id المبنى من عنصر إدخال
            var table = $('#getfloors_settings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('get-floors_settings') }}",
                    data: function(d) {
                        d.building_id = buildingId; // إرسال building_id مع البيانات
                    }
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
                        data: 'building',
                        name: 'building'
                    },
                    {
                        data: 'name_ar',
                        name: 'name_ar'
                    },
                    {
                        data: 'name_en',
                        name: 'name_en'
                    },
                    {
                        data: 'rooms_count',
                        name: 'rooms_count'
                    },
                    {
                        data: 'type',
                        name: 'type'
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
                pageLength: 20,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        title: 'floors_settings Data',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'floors_settings Data',
                        text: '<i class="fas fa-file-excel"></i> XLSX',
                        className: 'btn btn-outline-success'
                    }
                ],
                language: {
                    lengthMenu: "{{ __('datatables.show') }} _MENU_ {{ __('datatables.entries') }}",
                    info: "{{ __('datatables.showing') }} _START_ {{ __('datatables.to') }} _END_ {{ __('datatables.of') }} _TOTAL_ {{ __('datatables.entries') }}",
                    processing: "<i class='spinner-grow flex-shrink-0'></i>{{ __('datatables.Loading') }} ",
                    paginate: {
                        "previous": "{{ __('datatables.previous') }}",
                        "next": "{{ __('datatables.next') }}"
                    }
                }
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-floors_settings').on('input', function() {
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
            var url = '{{ route('floors_settings.destroy', ':id') }}';
            url = url.replace(':id', id); // استبدال الـ :id بالـ ID الفعلي

            $.ajax({
                url: url,
                type: 'DELETE', // استخدام الطريقة DELETE مباشرة
                data: {
                    "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
                },
                success: function(response) {
                    $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                    $('#getfloors_settings-table').DataTable().ajax
                        .reload(); // إعادة تحميل الـ DataTable
                    // عرض الرسالة من الـ Controller
                    if (response.status == 'success') {
                        showAlert('success', response.message); // عرض رسالة النجاح
                    } else {
                        showAlert('error', response.message); // عرض رسالة الخطأ
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("There was an error deleting the record.");
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
