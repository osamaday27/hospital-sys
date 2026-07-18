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
                                <a href="{{ route('complaints.index') }}">{{ trans('call_center_trans.complaints_mangment') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ trans('call_center_trans.create_new_complaint') }}</li>
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
                <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ request()->get('patient_id') }}">
                    <input type="hidden" name="visit_id" id="visit_id" value="{{ request()->get('visit_id') }}">
                    <input type="hidden" name="registered_patient" id="registered_patient" value="{{ request()->get('registered_patient') }}">
                    <input type="hidden" name="name" id="patient_name" value="{{ request()->get('name') }}">
                    <input type="hidden" name="age" id="patient_age" value="{{ request()->get('age') }}">
                    <input type="hidden" name="gender" id="patient_gender" value="{{ request()->get('gender') }}">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0" id="header_view">
                                        {{ trans('call_center_trans.create_new_complaint') }}
                                    </h4>
                                    <div id="patient-details" name="patient-details"></div>
                                </div>
                                <div class="card-body">
                                    <!-- سطر القوائم المنسدلة -->
                                    <div class="row mb-3">
                                        <!-- قائمة فئة الشكوى -->
                                        <div class="col-md-4">
                                            <label for="category"
                                                class="form-label">{{ trans('call_center_trans.category') }}</label>
                                            <select name="category" id="category" class="form-control" required>
                                                <option value="">{{ trans('call_center_trans.select_category') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name_ar . ' / ' . $category->name_en }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- قائمة المشاكل المرتبطة بفئة الشكوى -->
                                        <div class="col-md-4">
                                            <label for="problem" class="form-label">{{ trans('call_center_trans.problem') }}</label>
                                            <select name="problem" id="problem" class="form-control" disabled required>
                                                <option value="">{{ trans('call_center_trans.select_problem') }}</option>
                                                @foreach($problems as $problem)
                                                    <!-- لكل مشكلة خاصية data-category تربطها بالفئة -->
                                                    <option value="{{ $problem->id }}" data-category="{{ $problem->category_id }}">
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
                                                <option value="">{{ trans('call_center_trans.select_priority') }}</option>
                                                <option value="urgent">{{ trans('call_center_trans.urgent') }}</option>
                                                <option value="normal">{{ trans('call_center_trans.normal') }}</option>
                                                <option value="necessary">{{ trans('call_center_trans.necessary') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- سطر الحقول الجديدة: تاريخ الشكوي - الإدارة - القسم -->
                                    <div class="row mb-3">
                                        <!-- تاريخ الشكوي -->
                                        <div class="col-md-4">
                                            <label for="complaint_date"
                                                class="form-label">{{ trans('call_center_trans.complaint_date') }}</label>
                                            <input type="date" name="complaint_date" id="complaint_date" class="form-control" required>
                                        </div>
                                        <!-- قائمة اختيار الإدارة -->
                                        <div class="col-md-4">
                                            <label for="department"
                                                class="form-label">{{ trans('call_center_trans.department') }}</label>
                                            <select name="department" id="department" class="form-control" required>
                                                <option value="">{{ trans('call_center_trans.select_department') }}</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}">{{ $dept->name_ne . '/' . $dept->name_ar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- قائمة اختيار القسم المرتبط بالإدارة -->
                                        <div class="col-md-4">
                                            <label for="section" class="form-label">{{ trans('call_center_trans.section') }}</label>
                                            <select name="section" id="section" class="form-control" disabled required>
                                                <option value="">{{ trans('call_center_trans.select_section') }}</option>
                                                @foreach($sections as $section)
                                                    <!-- لكل قسم خاصية data-department تربطه بالإدارة -->
                                                    <option value="{{ $section->id }}" data-department="{{ $section->department }}">
                                                        {{ $section->name_ne . '/' . $section->name_ar }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!-- سطر بيانات الشكوى -->
                                    <div class="row mb-3">
                                        <!-- حقل مكان الشكوى -->
                                        <div class="col-md-6 ">
                                            <label for="complaint_address" class="form-label">{{ trans('call_center_trans.complaint_address') }}</label>
                                            <input type="text" name="complaint_address" id="complaint_address" class="form-control"
                                                placeholder="{{ trans('call_center_trans.enter_complaint_address') }}" required>
                                        </div>
                                        <!-- حقل عنوان الشكوى -->
                                        <div class="col-md-6">
                                            <label for="complaint_title" class="form-label">{{ trans('call_center_trans.complaint_title') }}</label>
                                            <input type="text" name="complaint_title" id="complaint_title" class="form-control"
                                                placeholder="{{ trans('call_center_trans.enter_complaint_title') }}" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <!-- حقل نص الشكوى (textarea) -->
                                        <div class="col-md-12">
                                            <label for="complaint_text"
                                                class="form-label">{{ trans('call_center_trans.complaint_text') }}</label>
                                            <textarea name="complaint_text" id="complaint_text" class="form-control" rows="5" required
                                                placeholder="{{ trans('call_center_trans.enter_complaint_text') }}"></textarea>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <!-- حقل رفع مرفقات الشكوى -->
                                        <div class="col-md-12">
                                            <label for="complaint_attachments"
                                                class="form-label">{{ trans('call_center_trans.complaint_attachments') }}</label>
                                            <input type="file" name="complaint_attachments[]" id="complaint_attachments"
                                                class="form-control" multiple>
                                        </div>
                                    </div>
                                    <!-- سيكشن بيانات التواصل -->
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h5>{{ trans('call_center_trans.contact_details') }}</h5>
                                        </div>
                                        <div id="contact-details-container" class="col-md-12">
                                            <div class="row contact-detail mb-2">
                                                <!-- رقم الهاتف -->
                                                <div class="col-md-5">
                                                    <label class="form-label">{{ trans('call_center_trans.phone') }}</label>
                                                    <input type="number" name="contact_phone[]" class="form-control"
                                                        placeholder="{{ trans('call_center_trans.enter_phone') }}">
                                                </div>
                                                <!-- الايميل -->
                                                <div class="col-md-5">
                                                    <label class="form-label">{{ trans('call_center_trans.email') }}</label>
                                                    <input type="email" name="contact_email[]" class="form-control"
                                                        placeholder="{{ trans('call_center_trans.enter_email') }}">
                                                </div>
                                                <!-- زر الإضافة -->
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" id="add-contact" class="btn btn-warning">
                                                        <i class="fa fa-plus"></i> {{ trans('call_center_trans.add_another') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <!-- زر العودة -->
                                        <a href="{{ route('complaints.index') }}" class="btn btn-secondary btn-lg">
                                            <i class="fa fa-arrow-left"></i> {{ trans('general.back') }}
                                        </a>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            {{ trans('call_center_trans.save') }}
                                        </button>
                                    </div>
                                    <!-- انتهاء سيكشن بيانات التواصل -->
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            </div>
    <!-- تضمين JS الخاص بـ Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(document).ready(function(){
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
        $('#category').on('change', function(){
             var selectedCategory = $(this).val();
             $('#problem').empty().append(defaultOption);

             if(selectedCategory) {
                 $('#problem').prop('disabled', false);
                 otherOptions.each(function(){
                     var problemCategory = $(this).attr('data-category');
                     if(problemCategory == selectedCategory){
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
        $('#department').on('change', function(){
             var selectedDept = $(this).val();
             $('#section').empty().append(defaultSectionOption);

             if(selectedDept) {
                 $('#section').prop('disabled', false);
                 otherSectionOptions.each(function(){
                     var deptId = $(this).attr('data-department');
                     if(deptId == selectedDept){
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
        $('#add-contact').on('click', function(){
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
        $(document).on('click', '.remove-contact', function(){
             $(this).closest('.contact-detail').remove();
        });
    });
    </script>

@endsection
