<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ trans('acknowledgments.annual_training_plan.title') }}</title>
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
            background-color: #fff;
            color: #000;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }
        .period {
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            table-layout: fixed;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }
        table th {
            background: #e8f4f8;
            font-weight: bold;
            color: #2c3e50;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f0f7ff;
        }
        .program-name {
            text-align: right;
            padding-right: 10px;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-box p {
            margin: 15px 0;
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
        .month-header {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            font-size: 12px;
        }
        .target-group {
            font-size: 12px;
            line-height: 1.4;
        }
        .program-cell {
            text-align: right;
            padding-right: 8px;
        }
        .serial-no {
            font-weight: bold;
        }
    </style>
    <script>
        function printDocument() { window.print(); }
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="printDocument()">طباعة / Print</button>

    <div class="title">{!! trans('acknowledgments.annual_training_plan.content.main_title') !!}</div>

    <div class="period">{{ trans('acknowledgments.annual_training_plan.content.period') }}</div>

    <table>
        <thead>
            <tr>
                @foreach(trans('acknowledgments.annual_training_plan.content.months') as $month)
                    <th style="width: 5%;" class="month-header">{{ $month }}</th>
                @endforeach
                <th style="width: 12%;">{{ trans('acknowledgments.annual_training_plan.content.table_headers.target_group') }}</th>
                <th style="width: 28%;">{{ trans('acknowledgments.annual_training_plan.content.table_headers.program_name') }}</th>
                <th style="width: 3%;">{{ trans('acknowledgments.annual_training_plan.content.table_headers.serial_no') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach(trans('acknowledgments.annual_training_plan.content.training_programs') as $index => $program)
                <tr>
                    @for($i = 0; $i < 12; $i++)
                        <td></td>
                    @endfor
                    <td class="target-group">{!! $program['target_group'] !!}</td>
                    <td class="program-cell"><strong>{{ $program['program_name'] }}</strong></td>
                    <td class="serial-no">{{ $index + 1 }}</td>
                </tr>
            @endforeach
            @for($i = 0; $i < 7; $i++)
                <tr>
                    @for($j = 0; $j < 12; $j++)
                        <td></td>
                    @endfor
                    <td class="target-group"></td>
                    <td class="program-cell"><strong>{{ trans('acknowledgments.annual_training_plan.content.other_programs')[0] }}</strong></td>
                    <td class="serial-no">{{ count(trans('acknowledgments.annual_training_plan.content.training_programs')) + $i + 1 }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="signature">
        <div class="signature-box">
            <p>{{ trans('acknowledgments.annual_training_plan.content.signature_labels.executive_director') }}</p>
            <p>{{ trans('acknowledgments.annual_training_plan.content.signature_labels.name') }} _________________________</p>
            <p>{{ trans('acknowledgments.annual_training_plan.content.signature_labels.signature') }} _________________________</p>
        </div>
        <div class="signature-box">
            <p>{{ trans('acknowledgments.annual_training_plan.content.signature_labels.hr_manager') }}</p>
            <p>{{ trans('acknowledgments.annual_training_plan.content.signature_labels.name') }} _________________________</p>
            <p>{{ trans('acknowledgments.annual_training_plan.content.signature_labels.signature') }} _________________________</p>
        </div>
    </div>
</body>
</html>
