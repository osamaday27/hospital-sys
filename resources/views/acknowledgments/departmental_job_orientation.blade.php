<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ trans('acknowledgments.departmental_job_orientation.title') }}</title>
    <style>
        @page {
            margin: 40px;
            size: A4;
        }
        body {
            font-family: "Tahoma", "Arial Unicode MS", Arial, sans-serif;
            line-height: 1.8;
            margin: 40px;
            text-align: right;
            direction: rtl;
            font-size: 14px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .section {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        table td, table th {
            border: 1px solid #000;
            padding: 6px;
        }
        ul {
            list-style-type: none;
            padding-right: 0;
        }
        ul li {
            margin-bottom: 5px;
        }
        .signature {
            margin-top: 50px;
        }
        .signature p {
            margin: 10px 0;
        }
        .footer {
            margin-top: 40px;
            text-align: left;
            font-size: 12px;
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

    <div class="title">{!! trans('acknowledgments.departmental_job_orientation.content.main_title') !!}</div>

    <div class="section">
        <table>
            <tr>
                <td>{{ trans('acknowledgments.departmental_job_orientation.content.job_title_label') }} <strong>{{ $employee->emp_pos->name_ar ?? '________________' }}</strong></td>
                <td>{{ trans('acknowledgments.departmental_job_orientation.content.employee_number_label') }} <strong>{{ $employee->employee_code ?? '________________' }}</strong></td>
                <td>{{ trans('acknowledgments.departmental_job_orientation.content.name_label') }} <strong>{{ $employee->full_name ?? '________________' }}</strong></td>
            </tr>
            <tr>
                <td>{{ trans('acknowledgments.departmental_job_orientation.content.department_label') }} <strong>{{ $employee->emp_dep->name_ar ?? '________________' }}</strong></td>
                <td>{{ trans('acknowledgments.departmental_job_orientation.content.orientation_date_label') }} _____________</td>
                <td>{{ trans('acknowledgments.departmental_job_orientation.content.appointment_date_label') }} _____________</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p>{{ trans('acknowledgments.departmental_job_orientation.content.employee_type_title') }}</p>
        <p>
            @foreach(trans('acknowledgments.departmental_job_orientation.content.employee_types') as $type)
                <input type="checkbox"> {{ $type }} &nbsp;&nbsp;
            @endforeach
        </p>
    </div>

    <div class="section">
        <h4>{{ trans('acknowledgments.departmental_job_orientation.content.departmental_topics_title') }}</h4>
        <ul>
            @foreach(trans('acknowledgments.departmental_job_orientation.content.departmental_topics') as $index => $topic)
                <li><input type="checkbox"> {{ $index + 1 }}. {{ $topic }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h4>{{ trans('acknowledgments.departmental_job_orientation.content.job_topics_title') }}</h4>
        <ul>
            @foreach(trans('acknowledgments.departmental_job_orientation.content.job_topics') as $index => $topic)
                <li><input type="checkbox"> {{ $index + 13 }}. {{ $topic }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h4>{{ trans('acknowledgments.departmental_job_orientation.content.other_topics_title') }}</h4>
        <ul>
            <li><input type="checkbox"> 20.</li>
            <li><input type="checkbox"> 21.</li>
            <li><input type="checkbox"> 22.</li>
            <li><input type="checkbox"> 23.</li>
        </ul>
    </div>

    <div class="section">
        <p>{{ trans('acknowledgments.departmental_job_orientation.content.acknowledgment_text') }}</p>
    </div>

    <div class="signature">
        <p>{{ trans('acknowledgments.departmental_job_orientation.content.signature_label') }} ____________________ &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.departmental_job_orientation.content.date_label') }} ____________________</p>
        <p>{{ trans('acknowledgments.departmental_job_orientation.content.supervisor_signature_label') }} ____________________ &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.departmental_job_orientation.content.supervisor_date_label') }} ____________________</p>
    </div>
</body>
</html>
