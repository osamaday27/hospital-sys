<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>اقرار الاطلاع على دليل السياسات والاجراءات</title>
    <style>
        @page {
            margin: 50px;
            size: A4;
        }
        body {
            font-family: "Tahoma", "Arial Unicode MS", Arial, sans-serif;
            line-height: 2;
            margin: 50px;
            text-align: right;
            direction: rtl;
            font-size: 14px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 40px;
        }
        .section {
            margin: 20px 0;
        }
        .signature {
            margin-top: 80px;
        }
        .footer {
            margin-top: 60px;
            text-align: left;
            font-size: 12px;
        }
        
        /* Print specific styles */
        @media print {
            body {
                margin: 0;
                padding: 50px;
            }
            .no-print {
                display: none;
            }
        }
        
        /* Button for printing */
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
            .print-button {
                display: none;
            }
        }
    </style>
    <script>
        function printDocument() {
            window.print();
        }
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="printDocument()">طباعة / Print</button>

    <div class="title">{{ trans('acknowledgments.policy_acknowledgment.title') }}</div>

    <div class="section">
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.declaration', [
            'name' => $employee->full_name ?? '________________',
            'position' => $employee->emp_pos->name_ar ?? '________________'
        ]) }}</p>
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.acknowledgment') }}</p>
    </div>

    <div class="section">
        {{ trans('acknowledgments.policy_acknowledgment.content.confirmation') }}
    </div>

    <div class="section">
        <p><strong>{{ trans('acknowledgments.policy_acknowledgment.content.details_title') }}</strong></p>
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.name_label') }} <strong>{{ $employee->full_name ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.code_label') }} <strong>{{ $employee->employee_code ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.department_label') }} <strong>{{ $employee->emp_dep->name_ar ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.position_label') }} <strong>{{ $employee->emp_pos->name_ar ?? '________________' }}</strong></p>
    </div>

    <div class="signature">
        <p>{{ trans('acknowledgments.policy_acknowledgment.content.signature_label') }} _________________________</p>
    </div>

    <div class="footer">
        {{ trans('acknowledgments.policy_acknowledgment.content.form_number') }} &nbsp;&nbsp;&nbsp; {{ trans('acknowledgments.policy_acknowledgment.content.page_info') }}
    </div>

</body>
</html>
