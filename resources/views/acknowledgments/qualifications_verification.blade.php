<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ trans('acknowledgments.qualifications_verification_request.title') }}</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        .signature {
            margin-top: 60px;
        }
        .footer {
            margin-top: 60px;
            text-align: left;
            font-size: 12px;
        }

        @media print {
            body {
                margin: 0;
                padding: 50px;
            }
            .no-print {
                display: none;
            }
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

    <div class="title">{{ trans('acknowledgments.qualifications_verification_request.title') }}</div>

    <div class="section">
        <p><strong>{{ trans('acknowledgments.qualifications_verification_request.content.qualification_types_title') }}</strong></p>
        <ul>
            @foreach(trans('acknowledgments.qualifications_verification_request.content.qualification_types') as $type)
                <li>{{ $type }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.to_label') }} ______________________</p>
        <p>______________________</p>
    </div>

    <div class="section">
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.greeting') }}</p>
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.request_text', ['name' => $employee->full_name ?? '________________']) }}</p>
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.thanks') }}</p>
        <p><strong>{{ trans('acknowledgments.qualifications_verification_request.content.hr_manager') }}</strong></p>
    </div>

    <div class="section">
        <h4>{{ trans('acknowledgments.qualifications_verification_request.content.employee_data_title') }}</h4>
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.name_label') }} <strong>{{ $employee->full_name ?? '________________' }}</strong></p>
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.position_applied_label') }} <strong>{{ $employee->emp_pos->name_ar ?? '________________' }}</strong></p>

        <table>
            <thead>
                <tr>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verification_table.qualification_required') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verification_table.date') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verification_table.data_to_verify') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verification_table.correct') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verification_table.incorrect') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>_________________________</td>
                    <td>_______</td>
                    <td>_________________________</td>
                    <td>⬜</td>
                    <td>⬜</td>
                </tr>
            </tbody>
        </table>

        <p><strong>{{ trans('acknowledgments.qualifications_verification_request.content.additional_info_title') }}</strong></p>
        <p>____________________________________________________________</p>
    </div>

    <div class="section">
        <h4>{{ trans('acknowledgments.qualifications_verification_request.content.verifier_data_title') }}</h4>
        <table>
            <thead>
                <tr>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verifier_table.name') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verifier_table.position') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verifier_table.signature') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verifier_table.date') }}</th>
                    <th>{{ trans('acknowledgments.qualifications_verification_request.content.verifier_table.institution_seal') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>_________</td>
                    <td>_________</td>
                    <td>_________</td>
                    <td>_________</td>
                    <td>_________</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h4>{{ trans('acknowledgments.qualifications_verification_request.content.consent_title') }}</h4>
        <p>{{ trans('acknowledgments.qualifications_verification_request.content.consent_text', ['name' => $employee->full_name ?? '________________']) }}</p>
    </div>

    <div class="section">
        <p><strong>{{ trans('acknowledgments.qualifications_verification_request.content.acknowledgment_title') }}</strong></p>
        <p>{!! trans('acknowledgments.qualifications_verification_request.content.signature_date') !!}</p>
    </div>
</body>
</html>
