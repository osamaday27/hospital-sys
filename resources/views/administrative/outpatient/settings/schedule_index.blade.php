@extends('layouts.master_table')

@section('title')
    {{ trans('outpatient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="highlight-text">{{ $admsclinic->name_ar }}</h3>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('outpatient_settings.index') }}">{{ trans('outpatient_trans.settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('outpatient_trans.schedule_settings') }}</li>
                    </ol>
                </div>
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
                        <input type="search" class="form-control" id="search-schedule_settings"
                            placeholder="{{ trans('outpatient_trans.search') }}" style="width: 250px;">
                    </div>
                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                        <a href="{{ route('schedule_settings.create', ['id' => encrypt($admsclinic->id)]) }}"
                            class="btn btn-warning">
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
                <!-- schedule_settings Table -->
                <div class="card-body">
                    <input type="hidden" id="clinic_id" name="clinic_id" value="{{ $admsclinic->id }}">
                    <table id="schedule_settings-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('outpatient_trans.sr_no') }}</th>
                                <th>{{ trans('outpatient_trans.doctor') }}</th>
                                <th>{{ trans('outpatient_trans.day') }}</th>
                                <th>{{ trans('outpatient_trans.shift_1') }}</th>
                                <th>{{ trans('outpatient_trans.shift_2') }}</th>
                                <th>{{ trans('outpatient_trans.shift_3') }}</th>
                                <th>{{ trans('outpatient_trans.status') }}</th>
                                <th>{{ trans('outpatient_trans.created_at') }}</th>
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
                                        <h4>{{ __('outpatient_trans.h4_delete') }}</h4>
                                        <p>{{ __('outpatient_trans.p_delete') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <form action="" method="post" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="id" name="id">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">{{ __('outpatient_trans.close') }}</button>
                                        <button type="button" class="btn btn-danger"
                                            id="delete-record">{{ __('outpatient_trans.yes_delete') }}</button>
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
            var clinic_id = $('#clinic_id').val(); // الحصول على id المبنى من عنصر إدخال

            var table = $('#schedule_settings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('get-schedule') }}",
                    data: function(d) {
                        d.clinic_id = clinic_id; // إرسال building_id مع البيانات
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },

                    {
                        data: 'doctor',
                        name: 'doctor'
                    },
                    {
                        data: 'day',
                        name: 'day'
                    },
                    {
                        data: 'shift_1',
                        name: 'shift_1'
                    },
                    {
                        data: 'shift_2',
                        name: 'shift_2'
                    },
                    {
                        data: 'shift_3',
                        name: 'shift_3'
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
                        title: 'schedule_settings Data',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'schedule_settings Data',
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

            $('#search-schedule_settings').on('input', function() {
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
            var url = '{{ route('schedule_settings.destroy', ':id') }}';
            url = url.replace(':id', id); // استبدال الـ :id بالـ ID الفعلي

            $.ajax({
                url: url,
                type: 'DELETE', // استخدام الطريقة DELETE مباشرة
                data: {
                    "_token": "{{ csrf_token() }}", // توكن CSRF للأمان
                },
                success: function(response) {
                    $('#deleteModal').modal('hide'); // إغلاق النافذة المنبثقة
                    $('#schedule_settings-table').DataTable().ajax
                        .reload(); // إعادة تحميل الـ DataTable
                    alert(response.success);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("There was an error deleting the record.");
                }
            });
        });
        $(document).on('change', '.status-toggle', function() {
            const checkbox = $(this);
            const schedulingId = checkbox.data('id'); // الحصول على معرف السجل
            const status = checkbox.prop('checked') ? 'active' :
            'inactive'; // تحديد الحالة بناءً على حالة الـ checkbox

            // إرسال طلب AJAX لتحديث الحالة
            $.ajax({
                url: '{{ route('schedule_settings.update_day') }}', // المسار الذي يتعامل مع تحديث الحالة
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // إضافة رمز الحماية
                    id: schedulingId,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        alert('تم تحديث الحالة بنجاح!');
                    } else {
                        alert('حدث خطأ أثناء تحديث الحالة.');
                        checkbox.prop('checked', !checkbox.prop(
                        'checked')); // إعادة الـ checkbox إلى حالته السابقة
                    }
                },
                error: function() {
                    alert('حدث خطأ في الاتصال بالسيرفر.');
                    checkbox.prop('checked', !checkbox.prop(
                    'checked')); // إعادة الـ checkbox إلى حالته السابقة
                }
            });
        });
    </script>
@endsection
