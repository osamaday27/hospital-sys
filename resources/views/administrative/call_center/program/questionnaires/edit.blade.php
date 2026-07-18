@extends('layouts.masterbooking')

@section('title')
    {{ trans('call_center_trans.questionnaire_details') }}
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
                <h4 class="mb-sm-0 font">{{ trans('call_center_trans.questionnaire_details') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('all_questionnaires.index') }}">{{ trans('call_center_trans.questionnaires_mangment') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.questionnaire_details') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- نموذج تعديل الاستبيان -->
    <form action="{{ route('all_questionnaires.update', ['all_questionnaire' => Crypt::encryptString($questionnaire->id)]) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- بيانات مخفية -->
        <input type="hidden" name="patient_id" id="patient_id" value="{{ $questionnaire->patient_id }}">
        <input type="hidden" name="visit_id" id="visit_id" value="{{ $questionnaire->patient_visits_id }}">
        <input type="hidden" id="questionnaire_id" value="{{ $questionnaire->questionnair }}">

        <!-- الأسئلة داخل Card واحد -->
        <div class="card">
            <div class="card-header text-center">
                <h5>{{ trans('call_center_trans.questionnaire_details') }}</h5>
            </div>
            <div class="card-body" id="questions-container">
                <!-- الأسئلة يتم تحميلها هنا عبر AJAX -->
            </div>
        </div>

        <!-- أزرار الحفظ والرجوع -->
        <div class="fixed-bottom bg-light p-3 text-end shadow border-top">
            <a href="{{ route('all_questionnaires.index') }}" class="btn btn-secondary me-2">
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
    $(document).ready(function () {
        var details = @json($details->keyBy('question_id') ?? []);
        var questionnaireId = $('#questionnaire_id').val();
        var patientId = $('#patient_id').val();
        var visitId = $('#visit_id').val();

        if (questionnaireId) {
            $.ajax({
                url: "{{ route('getQuestions') }}",
                type: "GET",
                data: {
                    questionnaire_id: questionnaireId,
                    patient_id: patientId,
                    visit_id: visitId
                },
                success: function (response) {
                    var html = '';

                    if (response.questions && response.questions.length > 0) {
                        response.questions.forEach(function (question, index) {
                            html += `<div class="mb-3 p-3 border rounded">
                                        <strong>${index + 1}. ${question.name_ar}</strong><br>`;

                            if (question.answer_id !== null) {
                                if (question.answer_values && question.answer_values.length > 0) {
                                    question.answer_values.forEach(function (answer) {
                                        var checked = (details[question.id] && details[question.id].answer_id == answer.id) ? 'checked' : '';
                                        html += `<div class="form-check">
                                                    <input class="form-check-input" type="radio" name="answers[${question.id}]" id="answer_${answer.id}" value="${answer.id}" ${checked}>
                                                    <label class="form-check-label" for="answer_${answer.id}">${answer.name_ar}</label>
                                                 </div>`;
                                    });
                                } else {
                                    html += '<p class="text-danger">{{ trans("call_center_trans.no_answers_available") }}</p>';
                                }

                                var noteVal = details[question.id] ? details[question.id].notes : '';
                                html += `<div class="form-group mt-2">
                                            <input type="text" name="notes[${question.id}]" class="form-control" placeholder="{{ trans("call_center_trans.enter_notes") }}" value="${noteVal || ''}">
                                         </div>`;
                            } else if (question.text == true || question.text == 1) {
                                var answerVal = details[question.id] ? details[question.id].text : '';
                                html += `<div class="form-group">
                                            <input type="text" name="answers[${question.id}]" class="form-control" placeholder="{{ trans("call_center_trans.enter_answer") }}" value="${answerVal || ''}">
                                         </div>`;
                            } else {
                                html += '<p class="text-warning">{{ trans("call_center_trans.no_answers_available") }}</p>';
                            }

                            html += `</div>`;
                        });
                    } else {
                        html = '<p class="text-danger text-center">{{ trans("call_center_trans.no_questions_available") }}</p>';
                    }
                    $('#questions-container').html(html);
                },
                error: function () {
                    Swal.fire({
                        title: "{{ trans('outpatient_trans.error_saving_reservation') }}",
                        icon: "error",
                        text: "{{ trans('outpatient_trans.please_try_again') }}",
                        showConfirmButton: true
                    });
                }
            });
        }

        // التحقق من الإدخال قبل الإرسال
        $('form').on('submit', function (e) {
            var hasEmpty = false;

            $('#questions-container input[type="radio"]').each(function () {
                var name = $(this).attr('name');
                if ($('input[name="' + name + '"]:checked').length === 0) {
                    hasEmpty = true;
                    return false;
                }
            });

            $('#questions-container input[type="text"]:not([name^="notes"])').each(function () {
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
