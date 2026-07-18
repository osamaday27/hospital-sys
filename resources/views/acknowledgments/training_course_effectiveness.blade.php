<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ trans('acknowledgments.training_course_effectiveness.title') }}</title>
    <style>
        @page {
            margin: 40px;
            size: A4;
        }
        body {
            font-family: "Tahoma", "Arial Unicode MS", Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
            text-align: right;
            direction: rtl;
            font-size: 14px;
            background-color: #fff;
            color: #000;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }
        .form-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-section h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            text-decoration: underline;
        }
        .form-group input[type="text"] {
            width: 70%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .options-group {
            margin: 15px 0;
        }
        .option-item {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .option-item input {
            margin-left: 10px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-box {
            width: 45%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f0f7ff;
        }
        .signature-box h4 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .signature-line {
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
        }
        .signature-line span {
            width: 45%;
        }
        .re-evaluation {
            margin-top: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff8e1;
        }
        .re-evaluation h3 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .print-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        @media print {
            .print-button { display: none; }
        }
    </style>
    <script>
        function printDocument() { window.print(); }
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="printDocument()">طباعة / Print</button>

    <div class="title">{!! trans('acknowledgments.training_course_effectiveness.content.main_title') !!}</div>

    <div class="form-section">
        <div class="form-group">
            <label for="employeeName">{{ trans('acknowledgments.training_course_effectiveness.content.form_labels.employee_name') }}</label>
            <input type="text" id="employeeName" value="{{ $employee->full_name ?? '' }}" placeholder="...................................................">
        </div>
        
        <div class="form-group">
            <label for="jobTitle">{{ trans('acknowledgments.training_course_effectiveness.content.form_labels.job_title') }}</label>
            <input type="text" id="jobTitle" value="{{ $employee->emp_pos->name_ar ?? '' }}" placeholder="...............................................................">
        </div>
        
        <div class="form-group">
            <label for="department">{{ trans('acknowledgments.training_course_effectiveness.content.form_labels.department') }}</label>
            <input type="text" id="department" value="{{ $employee->emp_dep->name_ar ?? '' }}" placeholder="...............................................................">
        </div>
        
        <div class="form-group">
            <label for="trainingProgram">{{ trans('acknowledgments.training_course_effectiveness.content.form_labels.training_program') }}</label>
            <input type="text" id="trainingProgram" placeholder=".........................................................">
        </div>
        
        <div class="form-group">
            <label for="date">{{ trans('acknowledgments.training_course_effectiveness.content.form_labels.date') }}</label>
            <input type="text" id="date" placeholder="..............................">
        </div>
    </div>

    <div class="form-section">
        <h3>{{ trans('acknowledgments.training_course_effectiveness.content.sections.training_result.title') }}</h3>
        <div class="options-group">
            @foreach(trans('acknowledgments.training_course_effectiveness.content.sections.training_result.options') as $key => $option)
                <div class="option-item">
                    <input type="radio" id="{{ $key }}" name="trainingResult">
                    <label for="{{ $key }}">{{ $option }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <h3>{{ trans('acknowledgments.training_course_effectiveness.content.sections.performance_impact.title') }}</h3>
        <div class="options-group">
            @foreach(trans('acknowledgments.training_course_effectiveness.content.sections.performance_impact.options') as $key => $option)
                <div class="option-item">
                    <input type="radio" id="{{ $key }}" name="performanceImpact">
                    <label for="{{ $key }}">{{ $option }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <h3>{{ trans('acknowledgments.training_course_effectiveness.content.sections.recommendation.title') }}</h3>
        <div class="options-group">
            @foreach(trans('acknowledgments.training_course_effectiveness.content.sections.recommendation.options') as $key => $option)
                <div class="option-item">
                    <input type="radio" id="{{ $key }}" name="recommendation">
                    <label for="{{ $key }}">{{ $option }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <h4>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.direct_supervisor') }}</h4>
            <div class="signature-line">
                <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.name') }} ...................................</span>
            </div>
            <div class="signature-line">
                <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.signature') }} ...................................</span>
            </div>
            <div class="signature-line">
                <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.date') }} / /</span>
            </div>
        </div>
        
        <div class="signature-box">
            <h4>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.hr_manager') }}</h4>
            <div class="signature-line">
                <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.name') }} ...................................</span>
            </div>
            <div class="signature-line">
                <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.signature') }} ...................................</span>
            </div>
            <div class="signature-line">
                <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.date') }} / /</span>
            </div>
        </div>
    </div>

    <div class="re-evaluation">
        <h3>{{ trans('acknowledgments.training_course_effectiveness.content.sections.reevaluation.title') }}</h3>
        <div class="options-group">
            @foreach(trans('acknowledgments.training_course_effectiveness.content.sections.reevaluation.options') as $key => $option)
                <div class="option-item">
                    <input type="radio" id="{{ $key }}" name="reevaluation">
                    <label for="{{ $key }}">{{ $option }}</label>
                </div>
            @endforeach
        </div>
        
        <div class="signature-section">
            <div class="signature-box">
                <h4>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.direct_supervisor') }}</h4>
                <div class="signature-line">
                    <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.name') }} ...................................</span>
                </div>
                <div class="signature-line">
                    <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.signature') }} ...................................</span>
                </div>
                <div class="signature-line">
                    <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.date') }} / /</span>
                </div>
            </div>
            
            <div class="signature-box">
                <h4>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.hr_manager') }}</h4>
                <div class="signature-line">
                    <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.name') }} ...................................</span>
                </div>
                <div class="signature-line">
                    <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.signature') }} ...................................</span>
                </div>
                <div class="signature-line">
                    <span>{{ trans('acknowledgments.training_course_effectiveness.content.signature_labels.date') }} / /</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
