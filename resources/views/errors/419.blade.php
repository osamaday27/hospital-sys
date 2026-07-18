{{-- resources/views/errors/419.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>انتهت الجلسة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Cairo', sans-serif;
        }
        .error-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .error-icon {
            font-size: 5rem;
            color: #ffc107;
        }
        .btn-refresh {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            ⚠️
        </div>
        <h1 class="display-5 mb-3">انتهت صلاحية الجلسة</h1>
        <p class="lead mb-4">
            لأسباب تتعلق بالأمان، انتهت جلستك. برجاء تحديث الصفحة ومتابعة العمل.
        </p>
        <a href="{{ url()->current() }}" class="btn btn-warning btn-refresh">
            🔄 تحديث الصفحة
        </a>
    </div>
</body>
</html>
