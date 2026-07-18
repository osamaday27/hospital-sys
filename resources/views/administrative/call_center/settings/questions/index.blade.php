@extends('layouts.master')

@section('title', 'إدارة الأسئلة')

@section('content')
    <div class="container mt-4">
        <h3>إدارة الأسئلة</h3>
        <div class="row">
            <!-- القسم العلوي لإضافة / تعديل الأسئلة -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">إضافة / تعديل سؤال</div>
                    <div class="card-body">
                        <form id="questionForm">
                            @csrf
                            <input type="hidden" id="question_id" name="question_id">
                            <div class="form-group">
                                <input type="hidden" id="type_id" name="type_id" value="{{ request('type_id') }}">

                                <label>اسم السؤال</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" id="name_ar" name="name_ar" class="form-control"
                                            placeholder="اسم السؤال بالعربية" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="name_en" name="name_en" class="form-control"
                                            placeholder="اسم السؤال بالإنجليزية" required>
                                    </div>

                                </div>
                            </div>



                            <div class="form-group">
                                <label>الإجابة</label>
                                <div class="d-flex">
                                    <select id="answer_id" name="answer_id" class="form-control" >
                                        <option value="">نص / Text</option>
                                        @foreach($answerTypes as $answer)
                                            <option value="{{ $answer->id }}">{{ $answer->name_ar }} / {{ $answer->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> {{ trans('call_center_trans.save') }}
                            </button>

                            <a href="{{ route('questionnaires.index') }}" class="btn btn-secondary">
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
                                    <input type="search" class="form-control" id="search-question"
                                        placeholder="ابحث عن سؤال" style="width: 250px;">
                                    <div class="hstack gap-2">
                                        <a href="{{ route('questionnaires.index') }}" class="btn btn-outline-secondary">
                                            <i class="ri-add-line"></i> {{ trans('call_center_trans.questionnaires') }}
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
                                    <table id="questionsTable" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>الاسم بالعربية</th>
                                                <th>الاسم بالإنجليزية</th>
                                                <th>اسم النموذج </th>
                                                <th> الإجابة</th>
                                                <th>مدخل البيانات</th>
                                                <th>إجراءات</th>
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
                                <h4 class="mt-3">هل أنت متأكد؟</h4>
                                <p>سيتم حذف نوع الإجابة نهائيًا، هل تريد المتابعة؟</p>
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

                    var typeId = new URLSearchParams(window.location.search).get('type_id');

                    // تهيئة DataTable
                    var table = $('#questionsTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('questions.index') }}",
                            data: function (d) {
                                d.type_id = typeId; // تمرير `typeId` إلى السيرفر
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
                            { data: 'type_name', name: 'type_name' },
                            { data: 'answer_name', name: 'answer_name' },
                            { data: 'created_by', name: 'created_by' },
                            { data: 'action', name: 'action', orderable: false, searchable: false }
                        ]
                        ,
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'csv', title: 'Questions Data', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-primary buttons-csv' },
                            { extend: 'excel', title: 'Questions Data', text: '<i class="fas fa-file-excel"></i> XLSX', className: 'btn btn-outline-success buttons-excel' }
                        ]
                    });

                    // 🔄 زر إعادة تحميل البيانات
                    $('#reload-table').click(function () {
                        table.ajax.reload();
                    });

                    // 🔍 البحث في الجدول
                    $('#search-question').on('input', function () {
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

                    // جلب type_id من الـ URL
                    var typeId = new URLSearchParams(window.location.search).get('type_id');

                    // إدخال القيمة تلقائيًا في الحقل المخفي
                    if (typeId) {
                        $('#type_id').val(typeId);
                    }
                    // ✅ إضافة سؤال أو تعديله
                    $('#questionForm').on('submit', function (e) {
                        e.preventDefault();

                        var questionId = $('#question_id').val();
                        var url = questionId ? `/questions/${questionId}` : "{{ route('questions.store') }}";
                        var method = questionId ? 'PUT' : 'POST';

                        var formData = new FormData(this);
                        formData.append('_method', method);

                        if (typeId) {
                            formData.append('type_id', typeId);
                        }

                        // ✅ تعديل `answer_id` إذا كان "text"
                        var answerId = $('#answer_id').val();
                        if (answerId === '') {
                            formData.set('text', 1);
                        } else {
                            formData.set('text', 0);

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
                                    $('#questionForm').trigger('reset');
                                    $('#question_id').val('');

                                    table.ajax.reload(null, false);
                                } else {
                                    alert('حدث خطأ أثناء العملية.');
                                }
                            },
                            error: function (xhr) {
                                console.log(xhr.responseText);
                                alert('حدث خطأ أثناء الحفظ. يرجى المحاولة مرة أخرى.');
                            }
                        });
                    });

                    // ✅ تحميل بيانات السؤال عند الضغط على زر "تعديل"
                    $(document).on('click', '.edit-btn', function () {
                        var id = $(this).data('id');

                        $.ajax({
                            url: `/questions/${id}/edit`,
                            type: 'GET',
                            success: function (data) {
                                $('#question_id').val(data.id);
                                $('#name_ar').val(data.name_ar);
                                $('#name_en').val(data.name_en);
                                $('#type_id').val(data.type_id);
                                $('#answer_id').val(data.answer_id);

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
                            url: `/questions/${id}`,
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

            </script>
@endsection
