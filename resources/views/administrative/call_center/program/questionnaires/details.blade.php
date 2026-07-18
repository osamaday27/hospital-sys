
@extends('layouts.masterbooking')

@section('title')
    {{ trans('call_center_trans.questionnaire_status') }}
@stop

<style>
    .fixed-bottom {
        position: fixed !important;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
    }

    .status-new {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .status-under-review {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .status-closed {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    #saveStatus {
        font-size: 1.1rem;
        padding: 10px 20px;
    }
</style>

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font">{{ trans('call_center_trans.questionnaire_status') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('all_questionnaires.index') }}">{{ trans('call_center_trans.questionnaires_mangment') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('call_center_trans.questionnaire_status') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

     <!-- اختيار حالة الاستبيان -->
     <div class="card mb-4">
        <div class="card-body">
            <label for="status" class="form-label"><strong>{{ trans('call_center_trans.questionnaire_status') }}</strong></label>
            <div class="d-flex align-items-center">
                <input type="hidden" name="patient_id" id="patient_id" value="{{ $questionnaire->patient_id }}">
                <input type="hidden" name="visit_id" id="visit_id" value="{{ $questionnaire->patient_visits_id }}">
                <input type="hidden" id="questionnaire_id" value="{{ $questionnaire->questionnair }}">

                <select id="status" class="form-select w-auto me-3">
                    <option value="new" class="status-new" {{ $questionnaire->status == 'new' ? 'selected' : '' }}>
                        {{ trans('call_center_trans.new') }}
                    </option>
                    <option value="under_review" class="status-under-review" {{ $questionnaire->status == 'under_review' ? 'selected' : '' }}>
                        {{ trans('call_center_trans.under_review') }}
                    </option>
                    <option value="closed" class="status-closed" {{ $questionnaire->status == 'closed' ? 'selected' : '' }}>
                        {{ trans('call_center_trans.closed') }}
                    </option>
                </select>
                <button type="button" id="saveStatus" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ trans('general.save') }}
                </button>
            </div>
        </div>
    </div>


    <!-- عرض الأسئلة داخل Card واحد -->
    <div class="card">
        <div class="card-header">
            <strong>{{ trans('call_center_trans.questionnaire_questions') }}</strong>
        </div>
        <div class="card-body" id="questions-container">
            <p class="text-center text-muted">{{ trans('call_center_trans.loading_questions') }}</p>
        </div>
    </div>

    <!-- زر الرجوع -->
    <div class="fixed-bottom bg-light p-3 text-end shadow border-top">
        <a href="{{ route('all_questionnaires.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> {{ trans('general.back') }}
        </a>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        var details = @json($details->keyBy('question_id') ?? []);
        var questionnaireId = $('#questionnaire_id').val();

        if (questionnaireId) {
            $.ajax({
                url: "{{ route('getQuestions') }}",
                type: "GET",
                data: { questionnaire_id: questionnaireId },
                success: function (response) {
                    var html = '';

                    if (response.questions && response.questions.length > 0) {
                        response.questions.forEach(function (question, index) {
                            var selectedAnswer = details[question.id] ? details[question.id].answer_id : null;
                            var noteVal = details[question.id] ? details[question.id].notes : '';
                            var answerVal = details[question.id] ? details[question.id].text : '';

                            html += `<div class="mb-3">
                                        <strong>${index + 1}. ${question.name_ar}</strong>
                                        <div>`;

                            if (question.answer_values && question.answer_values.length > 0) {
                                question.answer_values.forEach(function (answer) {
                                    var checked = (selectedAnswer == answer.id) ? 'checked' : '';
                                    html += `<div class="form-check">
                                                <input class="form-check-input" type="radio" name="answers[${question.id}]" id="answer_${answer.id}" value="${answer.id}" ${checked} disabled>
                                                <label class="form-check-label" for="answer_${answer.id}">${answer.name_ar}</label>
                                             </div>`;
                                });
                            } else if (question.text == true || question.text == 1) {
                                html += `<div class="form-group">
                                            <input type="text" name="answers[${question.id}]" class="form-control" value="${answerVal || ''}" readonly>
                                         </div>`;
                            }

                            if (noteVal) {
                                html += `<p class="text-muted"><strong>{{ trans("call_center_trans.notes") }}:</strong> ${noteVal}</p>`;
                            }

                            html += `</div></div>`;
                        });
                    } else {
                        html = '<p class="text-danger">{{ trans("call_center_trans.no_questions_available") }}</p>';
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

        // حفظ حالة الاستبيان عند الضغط على زر "حفظ الحالة"
        $('#saveStatus').on('click', function () {
            var newStatus = $('#status').val();
            var requestData = {
                _token: "{{ csrf_token() }}",
                status: newStatus
            };

            $.ajax({
                url: "{{ route('updateQuestionnaireStatus', ['id' => $questionnaire->id]) }}",
                type: "POST",
                data: requestData,
                success: function () {
                    Swal.fire({
                        icon: "success",
                        text: "{{ trans('call_center_trans.status_updated') }}",
                        showConfirmButton: true
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        text: "{{ trans('call_center_trans.error_updating_status') }}",
                        showConfirmButton: true
                    });
                }
            });
        });
    });
</script>
