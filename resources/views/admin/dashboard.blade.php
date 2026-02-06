<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة إدارة المواعيد - المديرية العامة للضرائب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            background: linear-gradient(180deg, #2c3e50, #1a2530);
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            padding-top: 20px;
        }
        .main-content {
            margin-right: 250px;
            padding: 20px;
        }
        .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #4a6491;
            color: white;
        }
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            text-align: center;
        }
        .stats-card.pending { background: linear-gradient(135deg, #ff9966, #ff5e62); }
        .stats-card.processed { background: linear-gradient(135deg, #36d1dc, #5b86e5); }
        .stats-card.today { background: linear-gradient(135deg, #00b09b, #96c93d); }
        .stats-card.total { background: linear-gradient(135deg, #7b4397, #dc2430); }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
        }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-processed { background-color: #28a745; color: white; }
        .badge-cancelled { background-color: #dc3545; color: white; }
        .header {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4>مصلحة الجبايات</h4>
            <p class="text-muted">لوحة الإدارة</p>
        </div>
        
        <nav class="nav flex-column">
            <a href="#" class="nav-link active">
                <i class="fas fa-calendar-alt"></i> لائحة المواعيد
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-chart-bar"></i> الإحصائيات
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-users"></i> إدارة المستخدمين
            </a>
            <a href="#" class="nav-link">
                <i class="fas fa-cog"></i> الإعدادات
            </a>
            <a href="{{ route('appointment.create') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> واجهة الزبائن
            </a>
        </nav>
        
        <div class="mt-5 text-center">
            <p class="text-muted">مرحباً بك، المسؤول</p>
            <a href="#" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h2><i class="fas fa-chart-pie"></i> مواردي الضريبة - لائحة المواعيد اليومية</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card pending">
                    <h3><i class="fas fa-clock"></i> {{ $stats['pending'] }}</h3>
                    <p>في الانتظار</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card processed">
                    <h3><i class="fas fa-check-circle"></i> {{ $stats['processed'] }}</h3>
                    <p>تمت المعالجة</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card today">
                    <h3><i class="fas fa-calendar-day"></i> {{ $stats['today'] }}</h3>
                    <p>مواعيد اليوم</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card total">
                    <h3><i class="fas fa-list-alt"></i> {{ $stats['total'] }}</h3>
                    <p>إجمالي المواعيد</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-table"></i> جدول المواعيد</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم الكامل</th>
                                <th>رقم البطاقة</th>
                                <th>الهاتف</th>
                                <th>نوع الخدمة</th>
                                <th>التاريخ</th>
                                <th>الوقت</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->id }}</td>
                                    <td>{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                                    <td>{{ $appointment->cin }}</td>
                                    <td>{{ $appointment->phone }}</td>
                                    <td>
                                        @if($appointment->appointment_type == 'payment')
                                            <span class="badge bg-primary">أداء الضريبة</span>
                                        @else
                                            <span class="badge bg-info">استفسار وتوجيه</span>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->appointment_date->format('d/m/Y') }}</td>
                                    <td>{{ $appointment->appointment_time }}</td>
                                    <td>
                                        @if($appointment->status == 'pending')
                                            <span class="status-badge badge-pending">في الانتظار</span>
                                        @elseif($appointment->status == 'processed')
                                            <span class="status-badge badge-processed">تمت المعالجة</span>
                                        @else
                                            <span class="status-badge badge-cancelled">ملغى</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.updateStatus', $appointment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                                <option value="processed" {{ $appointment->status == 'processed' ? 'selected' : '' }}>تمت المعالجة</option>
                                                <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                                            </select>
                                        </form>
                                        <button class="btn btn-sm btn-outline-secondary mt-1" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $appointment->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $appointments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>