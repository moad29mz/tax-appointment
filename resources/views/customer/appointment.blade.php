<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز موعد -مصلحات الجبايات </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 30px 0;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #4a6491;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .required-docs {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-right: 5px solid #4a6491;
        }
        .time-slot {
            border: 2px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
            text-align: center;
        }
        .time-slot:hover {
            background-color: #e9ecef;
        }
        .time-slot.selected {
            background-color: #4a6491;
            color: white;
            border-color: #2c3e50;
        }
        .time-slot.booked {
            background-color: #dc3545;
            color: white;
            cursor: not-allowed;
        }
        .working-hours {
            background: #e8f4fd;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-custom {
            background: linear-gradient(to right, #2c3e50, #4a6491);
            color: white;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 8px;
            border: none;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            background: linear-gradient(to right, #4a6491, #2c3e50);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>مصلحات الجبايات وزان  </h1>
            <h3>إدارة حجز موعد</h3>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="form-container">
                    <h3 class="section-title">احجز موعدك الآن</h3>
                    <p class="text-muted mb-4">نظم وقتك وتجنب الانتظار في مصلحة الجباية.</p>
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('appointment.store') }}" id="appointmentForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">الاسم الشخصي *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       required value="{{ old('first_name') }}" placeholder="أدخل اسمك">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">النسب *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       required value="{{ old('last_name') }}" placeholder="أدخل نسبك">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cin" class="form-label">رقم البطاقة الوطنية (CIN) *</label>
                                <input type="text" class="form-control" id="cin" name="cin" 
                                       required value="{{ old('cin') }}" placeholder="مثال: AB123456">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       required value="{{ old('phone') }}" placeholder="مثال: 0612345678">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="appointment_type" class="form-label">سبب الزيارة *</label>
                                <select class="form-select" id="appointment_type" name="appointment_type" required>
                                    <option value="">اختر السبب</option>
                                    <option value="payment" {{ old('appointment_type') == 'payment' ? 'selected' : '' }}>
                                        أداء الضريبة 
                                    </option>
                                    <option value="consultation" {{ old('appointment_type') == 'consultation' ? 'selected' : '' }}>
                                        استفسار وتوجيه 
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="appointment_date" class="form-label">التاريخ *</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                       required value="{{ old('appointment_date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="working-hours">
                            <i class="fas fa-clock"></i> أوقات العمل: 09:00 - 16:30
                        </div>

                        <div class="mb-4">
                            <label class="form-label">اختر الساعة المتاحة *</label>
                            <div id="timeSlots" class="d-flex flex-wrap">
                                <!-- سيتم ملء الأوقات المتاحة بواسطة JavaScript -->
                            </div>
                            <input type="hidden" id="selected_time" name="appointment_time" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-custom btn-lg">
                                <i class="fas fa-calendar-check"></i> تأكيد حجز الموعد
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="required-docs mb-4">
                    <h4 class="section-title">ماذا يجب أن أحضر؟</h4>
                    <p>مرحباً بكم في مصلحة الجبايات. لضمان سير موعدكم الخاص بخدمة "أداء الضريبة" في أفضل الظروف، يرجى إحضار الوثائق الأساسية التالية:</p>
                    
                    <ul class="list-unstyled">
                        <li><strong>البطاقة الوطنية (CIN):</strong></li>
                        <li>✓ الأصلي ونسبة منها</li>
                        <li>✓ الإعلام بالضريبة (Avis d'imposition)</li>
                        <li>✓ شهادة الملكية أو عقد الكراء</li>
                        <li>✓ آخر وصل أداء (إن وجد)</li>
                    </ul>
                    
                    <p class="text-muted mt-3"><small>ملاحظة: قد تختلف بعض الوثائق حسب نوع الضريبة (سيارات، عقارات، ...)</small></p>
                </div>

                
            </div>
        </div>
    </div>

    <!-- Font Awesome للرموز -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // تحميل الأوقات المتاحة عند تحميل الصفحة
            loadAvailableTimes('{{ date("Y-m-d") }}');
            
            // تحميل الأوقات المتاحة عند تغيير التاريخ
            $('#appointment_date').change(function() {
                loadAvailableTimes($(this).val());
            });
            
            // معالجة إرسال النموذج
            $('#appointmentForm').submit(function(e) {
                if (!$('#selected_time').val()) {
                    e.preventDefault();
                    alert('يرجى اختيار وقت للموعد');
                }
            });
        });
        
        function loadAvailableTimes(date) {
            $('#timeSlots').html('<div class="text-center"><div class="spinner-border" role="status"></div><p class="mt-2">جاري تحميل الأوقات المتاحة...</p></div>');
            $('#selected_time').val('');
            
            $.get('/api/available-times', { date: date }, function(response) {
                $('#timeSlots').empty();
                
                if (response.times.length === 0) {
                    $('#timeSlots').html('<div class="alert alert-warning w-100 text-center">لا توجد أوقات متاحة لهذا التاريخ</div>');
                    return;
                }
                
                response.times.forEach(function(time) {
                    const timeSlot = $('<div>').addClass('time-slot col-5 col-md-3').text(time);
                    
                    timeSlot.click(function() {
                        $('.time-slot').removeClass('selected');
                        $(this).addClass('selected');
                        $('#selected_time').val(time);
                    });
                    
                    $('#timeSlots').append(timeSlot);
                });
            }).fail(function() {
                $('#timeSlots').html('<div class="alert alert-danger w-100 text-center">خطأ في تحميل الأوقات</div>');
            });
        }
    </script>
</body>
</html>