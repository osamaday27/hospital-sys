@extends('layouts.master')

@section('title', trans('call_center_trans.complaints_problems'))

@section('content')
    <div id="alertContainer"></div>

    <div class="container mt-4">
        <h3>{{trans('call_center_trans.complaints_problems')}}</h3>
        <div class="row">
            <!-- القسم العلوي لإضافة / تعديل المشكلات -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{trans('call_center_trans.addedit_problems')}}</div>
                    <div class="card-body">
                        <form id="problemForm">
                            @csrf
                            <input type="hidden" id="problem_id" name="problem_id">
                            <div class="form-group">
                                <input type="hidden" id="category_id" name="category_id"
                                    value="{{ request('category_id') }}">

                                <label>{{trans('call_center_trans.problem_name')}}</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="name_ar" name="name_ar" class="form-control"
                                            placeholder="{{trans('call_center_trans.problem_name_ar')}}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="name_en" name="name_en" class="form-control"
                                            placeholder="{{trans('call_center_trans.problem_name_en')}}" required>
                                    </div>

                                </div>
                            </div>



                            <div class="form-group">
                                <label>{{trans('call_center_trans.status')}}</label>
                                <div class="d-flex">
                                    <select id="problem-status" name="status" class="form-control" required>
                                        <option value="1">{{ trans('call_center_trans.active') }}</option>
                                        <option value="0">{{ trans('call_center_trans.inactive') }}</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> {{ trans('call_center_trans.save') }}
                            </button>

                            <a href="{{ route('complaints-category.index') }}" class="btn btn-secondary">
                                <i class="ri-close-line"></i> {{ trans('call_center_trans.cancel') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>



            <div class="card">
                <!-- القسم السفلي لعرض الأسئلة -->
                <div class="col-md-12 mt-4">
                    <!-- Button Section -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <input type="search" class="form-control" id="search-problem"
                                        placeholder="{{ trans('call_center_trans.search_problem') }}" style="width: 250px;">
                                    <div class="hstack gap-2">
                                        <a href="{{ route('complaints-category.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="ri-add-line"></i>
                                            {{ trans('call_center_trans.complaints_categories') }}
                                        </a>
                                        <button id="reload-table" class="btn btn-info"><i
                                                class="ri-repeat-2-line"></i></button>
                                        <button class="btn btn-primary" id="export-csv"><i class="fa fa-file-csv"></i>
                                            CSV</button>
                                        <button class="btn btn-success" id="export-xlsx"><i class="fas fa-file-excel"></i>
                                            XLSX</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="problemTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{ trans('general.name_ar') }}</th>
                                                <th>{{ trans('general.name_en') }}</th>
                                                <th>{{ trans('general.status') }}</th>
                                                <th>{{ trans('general.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center">
                                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                    colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                                <h4 class="mt-3">{{ trans('call_center_trans.h4_delete') }}</h4>
                                <p>{{ trans('call_center_trans.p_delete') }}</p>
                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <form action="" method="post" id="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" id="delete-id">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="button" class="btn btn-danger" id="delete-record">نعم، احذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- مكتبات DataTables و jQuery -->
                <!-- مكتبة DataTables -->
                <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

                <!-- مكتبة Buttons لتفعيل أزرار التصدير -->
                <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
                <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
                <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>


                <script>
                    $(document).ready(function () {

                        var categoryId = new URLSearchParams(window.location.search).get('category_id');

                        // تهيئة DataTable
                        var table = $('#problemTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: "{{ route('complaints-problem.index') }}",
                                data: function (d) {
                                    d.category_id = categoryId; // تمرير `category_id` إلى السيرفر
                                },
                                error: function (xhr, error, thrown) {
                                    console.error("خطأ في تحميل البيانات:", xhr.responseText);
                                    alert("حدث خطأ أثناء تحميل البيانات، يرجى إعادة المحاولة.");
                                }
                            },
                            columns: [
                                {
                                    data: 'id',
                                    name: 'id',
                                    orderable: false,
                                    render: function (data, type, row, meta) {
                                        return meta.row + 1;
                                    }

                                },
                                { data: 'name_ar', name: 'name_ar' },
                                { data: 'name_en', name: 'name_en' },
                                {
                                    data: 'status',
                                    data: 'status',
                                    name: 'status',
                                    render: function (data, type, row, meta) {
                                        return data == 1 ? `{{ trans('call_center_trans.active') }}` : `{{ trans('call_center_trans.inactive') }}`;
                                    }
                                },
                                { data: 'action', name: 'action', orderable: false, searchable: false }
                            ]
                            ,
                            dom: 'Bfrtip',
                            buttons: [
                                { extend: 'csv', title: 'Problem Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary buttons-csv' },
                                { extend: 'excel', title: 'Problem Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success buttons-excel' }
                            ]
                        });

                        // 🔄 زر إعادة تحميل البيانات
                        $('#reload-table').click(function () {
                            table.ajax.reload();
                        });

                        // 🔍 البحث في الجدول
                        $('#search-problem').on('input', function () {
                            table.search(this.value).draw();
                        });

                        // ⬇️ تصدير CSV
                        $('#export-csv').click(function () {
                            table.button('.buttons-csv').trigger();
                        });

                        // ⬇️ تصدير XLSX
                        $('#export-xlsx').click(function () {
                            table.button('.buttons-excel').trigger();
                        });

                        // جلب category_id من الـ URL
                        var categoryId = new URLSearchParams(window.location.search).get('category_id');

                        // إدخال القيمة تلقائيًا في الحقل المخفي
                        if (categoryId) {
                            $('#category_id').val(categoryId);
                        }
                        // ✅ إضافة سؤال أو تعديله
                        $('#problemForm').on('submit', function (e) {
                            e.preventDefault();

                            var problemId = $('#problem_id').val();
                            var url = problemId ? `/complaints-problem/${problemId}` : "{{ route('complaints-problem.store') }}";
                            var method = problemId ? 'PUT' : 'POST';

                            var formData = new FormData(this);
                            formData.append('_method', method);

                            if (categoryId) {
                                formData.append('category_id', categoryId);
                            }
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        $('#problemForm').trigger('reset');
                                        $('#problem_id').val('');
                                        table.ajax.reload(null, false);
                                        showAlert('success', response.message);
                                    } else {
                                        showAlert('danger', 'حدث خطأ أثناء العملية.');
                                    }
                                },
                                error: function (xhr) {
                                    console.log(xhr.responseText);
                                    showAlert('danger', 'حدث خطأ أثناء الحفظ. يرجى المحاولة مرة أخرى.');
                                }
                            });
                        });

                        // ✅ تحميل بيانات السؤال عند الضغط على زر "تعديل"
                        $(document).on('click', '.edit-btn', function () {
                            var id = $(this).data('id');

                            $.ajax({
                                url: `/complaints-problem/${id}/edit`,
                                type: 'GET',
                                success: function (data) {
                                    $('#problem_id').val(data.id);
                                    $('#name_ar').val(data.name_ar);
                                    $('#name_en').val(data.name_en);
                                    $('#category_id').val(data.category_id);
                                    $('#problem-status').val(data.status);

                                },
                                error: function (xhr) {
                                    console.log(xhr.responseText);
                                    alert('حدث خطأ أثناء جلب البيانات.');
                                }
                            });
                        });

                        // 🗑️ حذف سؤال
                        // عند الضغط على زر الحذف، قم بعرض المودال مع تخزين الـ ID المراد حذفه
                        $(document).on('click', '.delete-btn', function () {
                            var id = $(this).data('id');
                            $('#delete-id').val(id); // حفظ الـ ID في الحقل المخفي داخل الفورم
                            $('#deleteModal').modal('show'); // عرض شاشة تأكيد الحذف
                        });

                        // عند تأكيد الحذف
                        $('#delete-record').click(function () {
                            var id = $('#delete-id').val(); // جلب الـ ID المخزن

                            $.ajax({
                                url: `/complaints-problem/${id}`,
                                type: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        $('#deleteModal').modal('hide'); // إخفاء شاشة التأكيد
                                        table.ajax.reload(); // تحديث الجدول
                                        showAlert('success', response.message);
                                    } else {
                                        showAlert('error', 'حدث خطأ أثناء الحذف.');
                                    }
                                },
                                error: function (xhr) {
                                    console.error(xhr.responseText);
                                    showAlert('error', 'حدث خطأ أثناء الحذف.');
                                }
                            });
                        });




                    });
                    function showAlert(type, message) {
                        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show text-center" ' +
                            'style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); width: auto; min-width: 250px; max-width: 400px; z-index: 9999; font-size: 14px; padding: 10px;">' +
                            message +
                            '</div>';
                        // إضافة التنبيه إلى الحاوية
                        $("#alertContainer").html(alertHtml);

                        // إخفاء التنبيه بعد 3 ثوانٍ
                        setTimeout(function () {
                            $("#alertContainer .alert").fadeOut();
                        }, 3000);
                    }


                </script>
@endsection
