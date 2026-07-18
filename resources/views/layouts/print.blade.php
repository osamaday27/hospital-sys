<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'طباعة الفاتورة' }}</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { font-size: 14px; background: #fff; color: #000; }
        .invoice-header { text-align: center; margin-bottom: 25px; }
        .table th, .table td { padding: 6px; vertical-align: middle; }
        h4, h5 { margin: 0; padding: 0; }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .card, .report-container { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container my-3">
        {{ $slot }}
    </div>
</body>
</html>
