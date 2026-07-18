@extends('layouts.masterbooking')

@section('title')
    {{ trans('call_center_trans.create_new_questionnaire') }}
@stop

<style>
    .fixed-bottom {
        position: fixed !important;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    }
</style>

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font">{{ trans('call_center_trans.patient_questionnaires') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('all_questionnaires.index') }}">{{ trans('call_center_trans.questionnaires_mangment') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('call_center_trans.create_new_questionnaire') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- نموذج إنشاء الاستبيان -->
<form action="{{ route('all_questionnaires.store') }}" method="POST">
    @csrf
    <input type="hidden" name="patient_id" id="patient_id" value="{{ request()->get('patient_id') }}">
<input type="hidden" name="visit_id" id="visit_id" value="{{ request()->get('visit_id') }}">

      <!-- اختيار الاستبيان داخل Card -->
<div class="row justify-content-center mt-4">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body text-center">
                <h5 class="card-title">{{ trans('call_center_trans.choose_questionnaire') }}</h5>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="questionnaire_id" class="form-label">
                                {{ trans('call_center_trans.select_questionnaire') }}
                            </label>
                            <select name="questionnaire_id" id="questionnaire_id" class="form-control select2">
                                <option value="">{{ trans('call_center_trans.select_questionnaire') }}</option>
                                @foreach($questionnaires as $questionnaire)
                                    <option value="{{ $questionnaire->id }}">
                                        {{ $questionnaire->name_ar }} / {{ $questionnaire->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- الجزء الثاني: عرض الأسئلة والإجابات -->
       <!-- عرض الأسئلة والإجابات داخل Card -->
       <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title text-center">{{ trans('call_center_trans.questions_list') }}</h5>
                    <div class="row">
                        <div class="col-md-12" id="questions-container">
                            <!-- سيتم تحميل الأسئلة والإجابات هنا عند اختيار الاستبيان -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- زر الإرسال مثبت أسفل الصفحة -->
    <div class="fixed-bottom bg-light p-3 text-end shadow border-top">

        <a href="{{ route('all_questionnaires.create') }}" class="btn btn-secondary me-2">
             {{ trans('general.back') }}
        </a>
        <button type="submit" class="btn btn-success">
             {{ trans('inpatient_trans.submit') }}
        </button>
    </div>

</form>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

$(document).ready(function(){
      // تفعيل Select2 على القائمة المنسدلة
      $('#questionnaire_id').select2({
            placeholder: "{{ trans('call_center_trans.select_questionnaire') }}",
            allowClear: true,
            width: '100%'
        });
    // عند تغيير اختيار الاستبيان
    $('#questionnaire_id').on('change', function(){
        var questionnaireId = $(this).val();
        var patientId = $('#patient_id').val();
        var visitId = $('#visit_id').val();

        if(questionnaireId) {
            // أولاً: التحقق من وجود نفس الاستبيان لنفس المريض ونفس الزيارة
            $.ajax({
                url: "{{ route('checkQuestionnaire') }}", // تأكد من تعريف هذا المسار في routes/web.php
                type: "GET",
                data: {
                    questionnaire_id: questionnaireId,
                    patient_id: patientId,
                    visit_id: visitId
                },
                success: function(response) {
                    if(response.exists) {
                        Swal.fire({
                            title: "{{ trans('call_center_trans.error_reservation') }}",
                            icon: "error",
                            text: "{{ trans('call_center_trans.error_saving_reservation') }}",
                            showConfirmButton: true
                        });
                        // إعادة تعيين القائمة وإفراغ المحتوى
                        $('#questionnaire_id').val('');
                        $('#questions-container').html('');
                    } else {
                        // إذا لم يكن موجوداً، نقوم بتحميل الأسئلة والإجابات
                        $.ajax({
                            url: "{{ route('getQuestions') }}",
                            type: "GET",
                            data: {
                                questionnaire_id: questionnaireId,
                                patient_id: patientId,
                                visit_id: visitId
                            },
                            success: function(response) {
                                console.log(response); // للتأكد من استلام البيانات في Console
                                var html = '';
                                if(response.questions && response.questions.length > 0) {
                                    response.questions.forEach(function(question, index) {
                                        html += '<div class="card mb-3">';
                                        html += '<div class="card-header"><strong>' + (index+1) + '. ' + question.name_ar + '</strong></div>';
                                        html += '<div class="card-body">';

                                        // حالة السؤال: إذا كان يحتوي على answer_id (أي يحتوي على خيارات متعددة)
                                        if(question.answer_id != null) {
                                            if(question.answer_values && question.answer_values.length > 0) {
                                                question.answer_values.forEach(function(answer) {
                                                    html += '<div class="form-check">';
                                                    html += '<input class="form-check-input" type="radio" name="answers[' + question.id + ']" id="answer_' + answer.id + '" value="' + answer.id + '">';
                                                    html += '<label class="form-check-label" for="answer_' + answer.id + '"> ' + answer.name_ar + '</label>';
                                                    html += '</div>';
                                                });
                                            } else {
                                                html += '<p>{{ trans("call_center_trans.no_answers_available") }}</p>';
                                            }
                                            // إضافة مربع نص للملاحظات (اختياري)
                                            html += '<div class="form-group mt-2">';
                                            html += '<input type="text" name="notes[' + question.id + ']" class="form-control" placeholder="{{ trans("call_center_trans.enter_notes") }}" />';
                                            html += '</div>';
                                        } else {
                                            // إذا كانت answer_id فارغة و الخاصية text تحتوي على true أو 1
                                            if(question.text == true || question.text == 1) {
                                                html += '<div class="form-group">';
                                                html += '<input type="text" name="answers[' + question.id + ']" class="form-control" placeholder="{{ trans("call_center_trans.enter_answer") }}"  data-prepend="true"/>';
                                                html += '</div>';
                                            } else {
                                                html += '<p>{{ trans("call_center_trans.no_answers_available") }}</p>';
                                            }
                                        }
                                        html += '</div></div>';
                                    });
                                } else {
                                    html = '<p>{{ trans("call_center_trans.no_questions_available") }}</p>';
                                }
                                $('#questions-container').html(html);
                            },
                            error: function(xhr, status, error) {
                                console.log(xhr.responseText);
                                Swal.fire({
                                    title: "{{ trans('outpatient_trans.error_saving_reservation') }}",
                                    icon: "error",
                                    text: error || "{{ trans('outpatient_trans.please_try_again') }}",
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        title: "{{ trans('outpatient_trans.error_saving_reservation') }}",
                        icon: "error",
                        text: error || "{{ trans('outpatient_trans.please_try_again') }}",
                        showConfirmButton: true
                    });
                }
            });
        } else {
            $('#questions-container').html('');
        }
    });

    // قبل تقديم النموذج، يتم التحقق من تعبئة جميع حقول الإجابات المطلوبة (باستثناء حقول الملاحظات)
    $('form').on('submit', function(e) {
        var hasEmpty = false;
        $('input[type="text"][data-prepend="true"]').each(function() {
            var value = $(this).val().trim();
            if (value !== "" && !value.startsWith("text_")) {
                $(this).val("text_" + value);
            }
        });
        // التحقق من كل مجموعات خيارات الراديو
        $('#questions-container input[type="radio"]').each(function(){
            var name = $(this).attr('name');
            if ($('input[name="'+name+'"]:checked').length === 0) {
                hasEmpty = true;
                return false;
            }
        });

        // التحقق من حقول الإدخال النصية (باستثناء حقول الملاحظات)
        $('#questions-container input[type="text"]:not([name^="notes"])').each(function(){
            if ($(this).val().trim() === "") {
                hasEmpty = true;
                return false;
            }
        });

        if (hasEmpty) {
            e.preventDefault();
            Swal.fire({
                title: "{{ trans('inpatient_trans.error_saving_reservation') }}",
                icon: "error",
                text: "{{ trans('inpatient_trans.please_fill_required_fields') }}",
                showConfirmButton: true
            });
        }
    });
});

</script>


