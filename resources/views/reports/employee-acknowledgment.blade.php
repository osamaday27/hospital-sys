<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إقرار الاطلاع على دليل السياسات والإجراءات</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 20px;
            line-height: 1.8;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            margin: 20px 0;
            line-height: 2;
        }
        .employee-info {
            margin: 30px 0;
            border: 1px solid #000;
            padding: 20px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-box {
            border: 1px solid #000;
            width: 200px;
            height: 80px;
            margin: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .field {
            display: inline-block;
            margin: 0 10px;
            min-width: 100px;
            border-bottom: 1px solid #000;
            padding: 5px;
        }
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>إقرار الاطلاع على دليل السياسات والإجراءات</h1>
    </div>

    <div class="content">
        <p>
            أقر وأتعهد أنا <span class="underline">{{ $employee->full_name ?? $employee->name ?? '________________' }}</span>
            الموقع أدناه وأعمل بوظيفة <span class="underline">{{ $employee->job->name_ar ?? '________________' }}</span>
        </p>

        <p>
            بأنني اطلعت على دليل السياسات والإجراءات الخاصة بمركز دنيا العيون لجراحات العيون المتخصصة، 
            وقد تم توضيح وشرح جميع النقاط لي، حيث يعتبر هذا الدليل من شروط والتزامات العمل ومكمل للعقد الذي وقعت عليه.
        </p>

        <p>
            وهذا إقرار مني بذلك،،،،
        </p>

        <p>
            المقر بما فيه:
        </p>
    </div>

    <div class="employee-info">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border: 1px solid #000; width: 25%; font-weight: bold;">الاسم:</td>
                <td style="padding: 10px; border: 1px solid #000; width: 25%;">{{ $employee->full_name ?? $employee->name ?? '________________' }}</td>
                <td style="padding: 10px; border: 1px solid #000; width: 25%; font-weight: bold;">الكود:</td>
                <td style="padding: 10px; border: 1px solid #000; width: 25%;">{{ $employee->employee_code ?? '________________' }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #000; font-weight: bold;">القسم:</td>
                <td style="padding: 10px; border: 1px solid #000;">{{ $employee->department->name_ar ?? '________________' }}</td>
                <td style="padding: 10px; border: 1px solid #000; font-weight: bold;">الوظيفة:</td>
                <td style="padding: 10px; border: 1px solid #000;">{{ $employee->job->name_ar ?? '________________' }}</td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        <div>
            <p><strong>التوقيع:</strong></p>
            <div class="signature-box"></div>
        </div>
        <div>
            <p><strong>التاريخ:</strong></p>
            <div class="signature-box">{{ date('Y-m-d') }}</div>
        </div>
    </div>

    <div style="margin-top: 50px; text-align: center;">
        <p><strong>ختم المؤسسة</strong></p>
        <div style="border: 2px solid #000; width: 150px; height: 100px; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
            <span>ختم المؤسسة</span>
        </div>
    </div>
</body>
</html>
