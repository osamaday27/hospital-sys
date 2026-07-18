<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ trans('acknowledgments.outsourced_employee_orientation.title') }}</title>
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
        }
        table th {
            background: #f0f0f0;
            text-align: center;
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
    </style>
    <script>
        function printDocument() { window.print(); }
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="printDocument()">طباعة / Print</button>

    <div class="title">{!! trans('acknowledgments.outsourced_employee_orientation.content.main_title') !!}</div>

    <table>
        <tr>
            <td>{{ trans('acknowledgments.outsourced_employee_orientation.content.job_title_label') }} <strong>{{ $employee->emp_pos->name_ar ?? '________________' }}</strong></td>
            <td>{{ trans('acknowledgments.outsourced_employee_orientation.content.name_label') }} <strong>{{ $employee->full_name ?? '________________' }}</strong></td>
        </tr>
        <tr>
            <td>{{ trans('acknowledgments.outsourced_employee_orientation.content.department_label') }} <strong>{{ $employee->emp_dep->name_ar ?? '________________' }}</strong></td>
            <td>{{ trans('acknowledgments.outsourced_employee_orientation.content.date_label') }} _____________</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">{{ trans('acknowledgments.outsourced_employee_orientation.content.table_headers.check') }}</th>
                <th style="width:45%;">{{ trans('acknowledgments.outsourced_employee_orientation.content.table_headers.items_english') }}</th>
                <th style="width:50%;">{{ trans('acknowledgments.outsourced_employee_orientation.content.table_headers.items_arabic') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach(trans('acknowledgments.outsourced_employee_orientation.content.orientation_topics') as $topic)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>{{ $topic['english'] }}</td>
                    <td>{{ $topic['arabic'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section">
        <p>{!! trans('acknowledgments.outsourced_employee_orientation.content.acknowledgment_text') !!}</p>
    </div>

    <div class="signature">
        <p>{{ trans('acknowledgments.outsourced_employee_orientation.content.employee_signature_label') }} ____________________ &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.outsourced_employee_orientation.content.employee_signature_arabic') }}</p>
        <p>{{ trans('acknowledgments.outsourced_employee_orientation.content.date_label') }} ____________________ &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.outsourced_employee_orientation.content.date_arabic') }}</p>
        <p>{{ trans('acknowledgments.outsourced_employee_orientation.content.hr_department_head_signature_label') }} ____________________ &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.outsourced_employee_orientation.content.hr_department_head_signature_arabic') }}</p>
        <p>{{ trans('acknowledgments.outsourced_employee_orientation.content.date_label') }} ____________________ &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.outsourced_employee_orientation.content.date_arabic') }}</p>
    </div>
</body>
</html>
