@extends('layouts.masterbooking')

@section('title')
    {{ trans('call_center_trans.complaints') }}
@stop

@section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font">{{ trans('call_center_trans.complaints') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a
                                    href="{{ route('complaints.index') }}">{{ trans('call_center_trans.complaints_mangment') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ trans('call_center_trans.edit_complaint') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form id="complaint-form" action="{{ route('complaints.update', $complaint->id) }}" method="POST"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <!-- الحقول المخفية الخاصة ببيانات المريض -->
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ $complaint->patient_id }}">
                    <input type="hidden" name="visit_id" id="visit_id" value="{{ $complaint->visit_id }}">
                    <input type="hidden" name="registered_patient" id="registered_patient"
                        value="{{ $complaint->registered_patient }}">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0" id="header_view">
                                        {{ trans('call_center_trans.edit_complaint') }}
                                    </h4>
                                    <div id="patient-details" name="patient-details"></div>
                                </div>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="card-body">
                                    @if ($complaint->registered_patient == 0)
                                        <div class="row mb-3">
                                            <!-- حقل تعديل الاسم -->
                                            <div class="col-md-4">
                                                <label for="patient_name"
                                                    class="form-label">{{ trans('call_center_trans.patient_name') }}</label>
                                                <input type="text" name="name" id="patient_name" class="form-control"
                                                    value="{{ $complaint->patient_name }}" required>
                                            </div>
                                            <!-- حقل تعديل العمر -->
                                            <div class="col-md-4">
                                                <label for="patient_age"
                                                    class="form-label">{{ trans('call_center_trans.age') }}</label>
                                                <input type="number" name="age" id="patient_age" class="form-control"
                                                    value="{{ $complaint->patient_age }}" required>
                                            </div>
                                            <!-- حقل تعديل الجنس -->
                                            <div class="col-md-4">
                                                <label for="patient_gender"
                                                    class="form-label">{{ trans('call_center_trans.patient_gender') }}</label>
                                                <select name="gender" id="patient_gender" class="form-control" required>
                                                    <option value="">
                                                        {{ trans('call_center_trans.select_patient_gender') }}</option>
                                                    <option value="male"
                                                        {{ $complaint->patient_gender == 'male' ? 'selected' : '' }}>
                                                        {{ trans('general.male') }}
                                                    </option>
                                                    <option value="female"
                                                        {{ $complaint->patient_gender == 'female' ? 'selected' : '' }}>
                                                        {{ trans('general.female') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif


                                    <!-- سطر القوائم المنسدلة -->
                                    <div class="row mb-3">
                                        <!-- قائمة فئة الشكوى -->
                                        <div class="col-md-4">
                                            <label for="category"
                                                class="form-label">{{ trans('call_center_trans.category') }}</label>
                                            <select name="category" id="category" class="form-control" required>
                                                <option value="">{{ trans('call_center_trans.select_category') }}
                                                </option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name_ar . ' / ' . $category->name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- قائمة المشاكل المرتبطة بفئة الشكوى -->
                                        <div class="col-md-4">
                                            <label for="problem"
                                                class="form-label">{{ trans('call_center_trans.problem') }}</label>
                                            <select name="problem" id="problem" class="form-control" disabled required>
                                                <option value="">{{ trans('call_center_trans.select_problem') }}</option>
                                                @foreach ($problems as $problem)
                                                    <option value="{{ $problem->id }}"
                                                        data-category="{{ $problem->category_id }}"
                                                        {{ $complaint->problem_id == $problem->id ? 'selected' : '' }}>
                                                        {{ $problem->name_ar . ' / ' . $problem->name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- قائمة اختيار الأولوية -->
                                        <div class="col-md-4">
                                            <label for="priority"
                                                class="form-label">{{ trans('call_center_trans.priority') }}</label>
                                            <select name="priority" id="priority" class="form-control" required>
                                                <option value="">{{ trans('call_center_trans.select_priority') }}
                                                </option>
                                                <option value="urgent"
                                                    {{ $complaint->priority == 'urgent' ? 'selected' : '' }}>
                                                    {{ trans('call_center_trans.urgent') }}
                                                </option>
                                                <option value="normal"
                                                    {{ $complaint->priority == 'normal' ? 'selected' : '' }}>
                                                    {{ trans('call_center_trans.normal') }}
                                                </option>
                                                <option value="necessary"
                                                    {{ $complaint->priority == 'necessary' ? 'selected' : '' }}>
                                                    {{ trans('call_center_trans.necessary') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- سطر الحقول الجديدة: تاريخ الشكوي - الإدارة - القسم -->
                                    <div class="row mb-3">
                                        <!-- تاريخ الشكوي -->
                                        <div class="col-md-4">
                                            <label for="complaint_date"
                                                class="form-label">{{ trans('call_center_trans.complaint_date') }}</label>
                                            <input type="date" name="complaint_date" id="complaint_date"
                                                class="form-control" required value="{{ $complaint->complaint_date }}">
                                        </div>
                                        <!-- قائمة اختيار الإدارة -->
                                        <div class="col-md-4">
                                            <label for="department"
                                                class="form-label">{{ trans('call_center_trans.department') }}</label>
                                            <select name="department" id="department" class="form-control" required>
                                                <option value="">{{ trans('call_center_trans.select_department') }}
                                                </option>
                                                @foreach ($departments as $dept)
                                                    <option value="{{ $dept->id }}"
                                                        {{ $complaint->department_id == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->name_ne . '/' . $dept->name_ar }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- قائمة اختيار القسم المرتبط بالإدارة -->
                                        <div class="col-md-4">
                                            <label for="section"
                                                class="form-label">{{ trans('call_center_trans.section') }}</label>
                                            <select name="section" id="section" class="form-control" disabled required>
                                                <option value="">{{ trans('call_center_trans.select_section') }}
                                                </option>
                                                @foreach ($sections as $section)
                                                    <option value="{{ $section->id }}"
                                                        data-department="{{ $section->department }}"
                                                        {{ $complaint->sections_id == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name_ne . '/' . $section->name_ar }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- سطر بيانات الشكوى -->
                                    <div class="row mb-3">
                                        <!-- حقل مكان الشكوى -->
                                        <div class="col-md-6">
                                            <label for="complaint_address"
                                                class="form-label">{{ trans('call_center_trans.complaint_address') }}</label>
                                            <input type="text" name="complaint_address" id="complaint_address"
                                                class="form-control"
                                                placeholder="{{ trans('call_center_trans.enter_complaint_address') }}"
                                                required value="{{ $complaint->complaint_place }}">
                                        </div>
                                        <!-- حقل عنوان الشكوى -->
                                        <div class="col-md-6">
                                            <label for="complaint_title"
                                                class="form-label">{{ trans('call_center_trans.complaint_title') }}</label>
                                            <input type="text" name="complaint_title" id="complaint_title"
                                                class="form-control"
                                                placeholder="{{ trans('call_center_trans.enter_complaint_title') }}" required
                                                value="{{ $complaint->title }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <!-- حقل نص الشكوى (textarea) -->
                                        <div class="col-md-12">
                                            <label for="complaint_text"
                                                class="form-label">{{ trans('call_center_trans.complaint_text') }}</label>
                                            <textarea name="complaint_text" id="complaint_text" class="form-control" rows="5" required
                                                placeholder="{{ trans('call_center_trans.enter_complaint_text') }}">{{ $complaint->complaint }}</textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <!-- حقل رفع مرفقات الشكوى -->
                                        <div class="col-md-12">
                                            <label for="complaint_attachments"
                                                class="form-label">{{ trans('call_center_trans.complaint_attachments') }}</label>
                                            <input type="file" name="complaint_attachments[]" id="complaint_attachments"
                                                class="form-control" multiple>

                                                @php
                                                    $attachments = json_decode($complaint->attachments, true) ?? [];
                                                @endphp
                                                <br> <strong>{{ trans('call_center_trans.previous_complaint_attachments') }}:</strong><br>

                                            @if(count($attachments) > 0)
                                                <ul id="fileList" class="list-group">
                                                    @foreach ($attachments as $index => $file)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <a href="{{ asset('attachments/' . $file) }}" target="_blank">{{ basename($file) }}</a>

                                                            <button type="button" class="btn btn-danger btn-sm delete-attachment deleteAttachment" data-file="{{ $file }}" data-index="{{ $index }}">
                                                                <i class="fas fa-trash"></i> {{ trans('general.delete') }}
                                                            </button>

                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted">{{ trans('call_center_trans.no_attachments') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- سيكشن بيانات التواصل -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h5>{{ trans('call_center_trans.contact_details') }}</h5>
                                        </div>
                                        <div id="contact-details-container" class="col-md-12">
                                            @if (is_array($complaint->contact_information) && count($complaint->contact_information) > 0)
                                                @foreach ($complaint->contact_information as $contact)
                                                    <div class="row contact-detail mb-2">
                                                        <!-- رقم الهاتف -->
                                                        <div class="col-md-5">
                                                            <label
                                                                class="form-label">{{ trans('call_center_trans.phone') }}</label>
                                                            <input type="number" name="contact_phone[]" class="form-control"
                                                                placeholder="{{ trans('call_center_trans.enter_phone') }}"
                                                                value="{{ $contact['phone'] ?? '' }}">
                                                        </div>
                                                        <!-- الايميل -->
                                                        <div class="col-md-5">
                                                            <label
                                                                class="form-label">{{ trans('call_center_trans.email') }}</label>
                                                            <input type="email" name="contact_email[]" class="form-control"
                                                                placeholder="{{ trans('call_center_trans.enter_email') }}"
                                                                value="{{ $contact['email'] ?? '' }}">
                                                        </div>
                                                        <!-- زر الحذف -->
                                                        <div class="col-md-2 d-flex align-items-end">
                                                            <button type="button" class="btn btn-danger remove-contact">
                                                                <i class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row contact-detail mb-2">
                                                    <!-- رقم الهاتف -->
                                                    <div class="col-md-5">
                                                        <label
                                                            class="form-label">{{ trans('call_center_trans.phone') }}</label>
                                                        <input type="number" name="contact_phone[]" class="form-control"
                                                            placeholder="{{ trans('call_center_trans.enter_phone') }}">
                                                    </div>
                                                    <!-- الايميل -->
                                                    <div class="col-md-5">
                                                        <label
                                                            class="form-label">{{ trans('call_center_trans.email') }}</label>
                                                        <input type="email" name="contact_email[]" class="form-control"
                                                            placeholder="{{ trans('call_center_trans.enter_email') }}">
                                                    </div>
                                                    <!-- زر الحذف -->
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="button" class="btn btn-danger remove-contact">
                                                            <i class="fa fa-trash"></i> {{ trans('general.delete') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- زر الإضافة خارج الشرط لعرضه دائمًا -->
                                        <div class="col-md-12 text-end mt-2">
                                            <button type="button" id="add-contact" class="btn btn-warning">
                                                <i class="fa fa-plus"></i> {{ trans('call_center_trans.add_another') }}
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <!-- زر العودة -->
                                        <a href="{{ route('complaints.index') }}" class="btn btn-secondary btn-lg">
                                            <i class="fa fa-arrow-left"></i> {{ trans('general.back') }}
                                        </a>

                                        <!-- زر الحفظ -->
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fa fa-save"></i> {{ trans('call_center_trans.save') }}
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- تضمين JS الخاص بـ Select2 -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $('#complaint-form').on('submit', function() {
                // إزالة خاصية التعطيل من الحقول قبل الإرسال
                $('#problem, #section').prop('disabled', false);
            });

            $(document).ready(function() {
                // تهيئة Select2 للقوائم الحالية
                $('#category').select2({
                    placeholder: "{{ trans('call_center_trans.select_category') }}",
                    allowClear: true
                });
                $('#problem').select2({
                    placeholder: "{{ trans('call_center_trans.select_problem') }}",
                    allowClear: true
                });
                $('#priority').select2({
                    placeholder: "{{ trans('call_center_trans.select_priority') }}",
                    allowClear: true
                });
                // تهيئة Select2 للقوائم الجديدة
                $('#department').select2({
                    placeholder: "{{ trans('call_center_trans.select_department') }}",
                    allowClear: true
                });
                $('#section').select2({
                    placeholder: "{{ trans('call_center_trans.select_section') }}",
                    allowClear: true
                });

                // تخزين الخيار الافتراضي وباقي الخيارات لقائمة المشاكل
                var defaultOption = $('#problem option:first');
                var otherOptions = $('#problem option:not(:first)').clone();

                // عند تغيير الفئة لتصفية المشاكل
                $('#category').on('change', function() {
                    var selectedCategory = $(this).val();
                    $('#problem').empty().append(defaultOption);

                    if (selectedCategory) {
                        $('#problem').prop('disabled', false);
                        otherOptions.each(function() {
                            var problemCategory = $(this).attr('data-category');
                            if (problemCategory == selectedCategory) {
                                $('#problem').append($(this));
                            }
                        });
                    } else {
                        $('#problem').prop('disabled', true);
                    }
                    // تحديث Select2 لقائمة المشاكل
                    $('#problem').trigger('change.select2');
                });

                // تخزين الخيار الافتراضي وباقي الخيارات لقائمة الأقسام
                var defaultSectionOption = $('#section option:first');
                var otherSectionOptions = $('#section option:not(:first)').clone();

                // عند تغيير الإدارة لتصفية الأقسام
                $('#department').on('change', function() {
                    var selectedDept = $(this).val();
                    $('#section').empty().append(defaultSectionOption);

                    if (selectedDept) {
                        $('#section').prop('disabled', false);
                        otherSectionOptions.each(function() {
                            var deptId = $(this).attr('data-department');
                            if (deptId == selectedDept) {
                                $('#section').append($(this));
                            }
                        });
                    } else {
                        $('#section').prop('disabled', true);
                    }
                    // تحديث Select2 لقائمة الأقسام
                    $('#section').trigger('change.select2');
                });

                // إضافة حقول جديدة لبيانات التواصل عند الضغط على زر "اضافة أخرى"
                $('#add-contact').on('click', function() {
                    var newContactRow = `
                         <div class="row contact-detail mb-2">
                            <div class="col-md-5">
                                <input type="text" name="contact_phone[]" class="form-control" placeholder="{{ trans('call_center_trans.enter_phone') }}">
                            </div>
                            <div class="col-md-5">
                                <input type="email" name="contact_email[]" class="form-control" placeholder="{{ trans('call_center_trans.enter_email') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-center">
                                <button type="button" class="btn btn-danger remove-contact">
                                    <i class="fa fa-trash">{{ trans('general.delete') }}</i>
                                </button>
                            </div>
                         </div>
                     `;
                    $('#contact-details-container').append(newContactRow);
                });

                // حذف صف بيانات التواصل عند الضغط على زر الحذف
                $(document).on('click', '.remove-contact', function() {
                    $(this).closest('.contact-detail').remove();
                });
            });

function attachDeleteComplaintAttachmentEvent() {
    $('.delete-attachment').off('click').on('click', function () {
        var filePath = $(this).data('file');
        var complaintId = "{{ $complaint->id }}"; // متوفر في الصفحة
        var $button = $(this);

        Swal.fire({
            title: "{{ trans('call_center_trans.confirm_delete') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "{{ trans('call_center_trans.yes_delete') }}",
            cancelButtonText: "{{ trans('general.cancel') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('complaints.deleteAttachment') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        file: filePath,
                        complaint_id: complaintId
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                text: "{{ trans('call_center_trans.file_deleted_successfully') }}",
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // إزالة العنصر بدون إعادة تحميل
                            $button.closest('li').remove();
                        } else {
                            Swal.fire({
                                icon: "error",
                                text: "{{ trans('call_center_trans.error_deleting_file') }}",
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: "error",
                            text: xhr.responseJSON?.error || "{{ trans('call_center_trans.error_deleting_file') }}",
                            showConfirmButton: true
                        });
                    }
                });
            }
        });
    });
}
$(document).ready(function() {
    attachDeleteComplaintAttachmentEvent();
});

        </script>

@endsection
