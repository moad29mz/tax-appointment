<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم حجز الموعد بنجاح</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .success-container {
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .success-icon {
            color: #28a745;
            font-size: 80px;
            margin-bottom: 30px;
        }
        .confirmation-box {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #28a745;
            margin: 30px 0;
            text-align: right;
        }
        .btn-custom {
            background: linear-gradient(to right, #2c3e50, #4a6491);
            color: white;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s;
            margin: 10px;
        }
        .btn-custom:hover {
            background: linear-gradient(to right, #4a6491, #2c3e50);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="text-success">تم تأكيد حجز الموعد بنجاح!</h1>
        
        <div class="confirmation-box">
            <h4>تفاصيل الموعد</h4>
            <p><strong>نوع الخدمة:</strong> 
                @if(session('appointment_type') == 'payment')
                    أداء الضريبة (15 دقيقة)
                @else
                    استفسار وتوجيه (20 دقيقة)
                @endif
            </p>
            <p><strong>التاريخ:</strong> {{ session('appointment_date') }}</p>
            <p><strong>الوقت:</strong> {{ session('appointment_time') }}</p>
            <hr>
            <p class="mb-0"><strong>ملاحظة:</strong> يرجى الحضور قبل 10 دقائق من الموعد مع إحضار جميع الوثائق المطلوبة</p>
        </div>
        
        
        <div class="mt-4">
            <a href="{{ route('appointment.create') }}" class="btn btn-custom">
                <i class="fas fa-calendar-plus"></i> حجز موعد جديد
            </a>
            <a href="/" class="btn btn-outline-secondary">
                <i class="fas fa-home"></i> الصفحة الرئيسية
            </a>
        </div>
       
    </div>
    
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
