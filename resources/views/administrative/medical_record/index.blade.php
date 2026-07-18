@extends('layouts.masterbooking')

@section('title')
    {{ trans('patient_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('patient_trans.title') }}</a>
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
                <!-- Filters Section -->
                <div class="row" style="margin: 13px;">
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="ForminputState" class="form-label">{{ trans('patient_trans.country') }}</label>
                            <select id="id_country" class="js-example-basic-single" name="id_country"
                                onclick="console.log($(this).val())" onchange="console.log($(this).val())">
                                <option value="" selected>
                                    {{ trans('patient_trans.select_option') }}
                                </option>
                                @foreach ($country as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="ForminputState" class="form-label">{{ trans('patient_trans.governorate') }}</label>
                            <select id="id_governmentes" name="id_governmentes"class="js-example-basic-single"
                                onselect="get_city($(this).val())" onchange="get_city($(this).val())"
                                onvolumechange="get_city($(this).val())">
                                <option value="" selected>
                                    {{ trans('patient_trans.select_option') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="ForminputState" class="form-label">{{ trans('patient_trans.city') }}</label>
                            <select id="id_city" name="id_city" class="js-example-basic-single"
                                onselect="get_area($(this).val())" onchange="get_area($(this).val())"
                                onvolumechange="get_area($(this).val())">
                                <option value="" selected>
                                    {{ trans('patient_trans.select_option') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <label for="ForminputState" class="form-label">{{ trans('patient_trans.area') }}</label>
                            <select id="id_area" name="id_area" class="js-example-basic-single">
                                <option value="" selected>
                                    {{ trans('patient_trans.select_option') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-header d-flex justify-content-between align-items-center">

                    <!-- DataTable Search Input moved to the right -->
                    <div class="d-flex align-items-center">
                        <input type="search" class="form-control" id="search-patient"
                            placeholder="{{ trans('patient_trans.search') }}" style="width: 250px;">
                    </div>


                    <div class="hstack gap-2 ">
                        <!-- Reload Button -->
                        <a href="{{ url('/' . ($page = 'medical_record/create')) }}" class="btn btn-warning">
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
                <!-- Patient Table -->
                <div class="card-body">
                    <table id="patient-table" class="table dt-responsive nowrap align-middle"
                        style="width: 100%; border: aliceblue;">
                        <thead>
                            <tr>
                                <th>{{ trans('patient_trans.sr_no') }}</th>
                                <th>{{ trans('patient_trans.action') }}</th>
                                <th>{{ trans('patient_trans.id') }}</th>
                                <th>{{ trans('patient_trans.full_name') }}</th>
                                <th>{{ trans('patient_trans.phone_number') }}</th>
                                <th>{{ trans('patient_trans.date_birth') }}</th>
                                <th>{{ trans('patient_trans.gender') }}</th>
                                <th>{{ trans('patient_trans.status') }}</th>
                                <th>{{ trans('patient_trans.notes') }}</th>
                                <th>{{ trans('patient_trans.create_date') }}</th>
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
                                        <h4>{{ __('patient_trans.h4_delete') }}</h4>
                                        <p>{{ __('patient_trans.p_delete') }}</p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <form action="" method="post" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="id" name="id">
                                        <button type="button" class="btn btn-light"
                                            data-bs-dismiss="modal">{{ __('patient_trans.close') }}</button>
                                        <button type="button" class="btn btn-danger"
                                            id="delete-record">{{ __('patient_trans.yes_delete') }}</button>
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
            var table = $('#patient-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('get-medical_record') }}",
                    data: function(d) {
                        d.country = $('#id_country').val(); // جلب قيمة الفلتر الخاص بالدولة
                        d.area = $('#id_area').val(); // جلب قيمة الفلتر الخاص بالمنطقة
                        d.city = $('#id_city').val(); // جلب قيمة الفلتر الخاص بالمدينة
                        d.governorate = $('#id_governmentes')
                            .val(); // جلب قيمة الفلتر الخاص بالمحافظة
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
                        data: 'medical_number',
                        name: 'medical_number'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'date_of_birth',
                        name: 'date_of_birth'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                     {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csv',
                        title: 'Patient Data',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-outline-primary'
                    },
                    {
                        extend: 'excel',
                        title: 'Patient Data',
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

            // إعادة تحميل البيانات عند تغيير أي من الفلاتر
            $('#id_country, #id_area, #id_city, #id_governmentes').on('change', function() {
                table.ajax.reload(); // إعادة تحميل البيانات
            });

            $('#reload-table').click(function() {
                table.ajax.reload();
            });

            $('#search-patient').on('input', function() {
                table.search(this.value).draw();
            });
        });

        function deleteFunction(id) {
            $('#id').val(id); // وضع المعرف في حقل الإدخال المخفي
            $('#deleteModal').modal('show'); // فتح النافذة المنبثقة
        }

        $('#delete-record').click(function() {
            var id = $('#id').val(); // الحصول على ID من الحقل المخفي
            var url = '{{ route('medical_record.destroy', ':id') }}';
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
                    $('#patient-table').DataTable().ajax.reload(); // إعادة تحميل الـ DataTable
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    alert("There was an error deleting the record.");
                }
            });
        });
    </script>
@endsection
