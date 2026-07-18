<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ trans('acknowledgments.training_needs_assessment.title') }}</title>
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
            font-size: 13px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 25px;
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
            text-align: center;
        }
        table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .signature {
            margin-top: 40px;
        }
        .signature p {
            margin: 10px 0;
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
        .header-info {
            margin-bottom: 20px;
        }
        .header-info p {
            margin: 5px 0;
        }
    </style>
    <script>
        function printDocument() { window.print(); }
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="printDocument()">طباعة / Print</button>

    <div class="title">{!! trans('acknowledgments.training_needs_assessment.content.main_title') !!}</div>

    <div class="header-info">
        <p>{{ trans('acknowledgments.training_needs_assessment.content.department_label') }} <strong>{{ $employee->emp_dep->name_ar ?? '________________' }}</strong> {{ trans('acknowledgments.training_needs_assessment.content.date_label') }}  /  /</p>
        <p>{{ trans('acknowledgments.training_needs_assessment.content.to_hr_manager_label') }} ..............................................................................</p>
        <p><strong>{{ trans('acknowledgments.training_needs_assessment.content.signature_labels.name') }} {{ $employee->full_name ?? '________________' }} - {{ trans('acknowledgments.training_needs_assessment.content.signature_labels.job_title') }} {{ $employee->emp_pos->name_ar ?? '________________' }} - الكود: {{ $employee->employee_code ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.training_needs_assessment.content.please_review_label') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">{{ trans('acknowledgments.training_needs_assessment.content.table_headers.number') }}</th>
                <th style="width:25%;">{!! trans('acknowledgments.training_needs_assessment.content.table_headers.training_course') !!}</th>
                <th style="width:15%;">{!! trans('acknowledgments.training_needs_assessment.content.table_headers.candidates_number') !!}</th>
                <th style="width:15%;">{!! trans('acknowledgments.training_needs_assessment.content.table_headers.job_title') !!}</th>
                <th style="width:20%;">{!! trans('acknowledgments.training_needs_assessment.content.table_headers.dept_unit') !!}</th>
                <th style="width:20%;">{!! trans('acknowledgments.training_needs_assessment.content.table_headers.comments') !!}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="signature">
        <p>{{ trans('acknowledgments.training_needs_assessment.content.signature_labels.name') }} <strong>{{ $employee->full_name ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.training_needs_assessment.content.signature_labels.job_title') }} <strong>{{ $employee->emp_pos->name_ar ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.training_needs_assessment.content.signature_labels.signature') }} ..............................................................................</p>
    </div>
</body>
</html>
