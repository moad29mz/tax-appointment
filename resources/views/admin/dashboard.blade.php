<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة إدارة المواعيد - المديرية العامة للضرائب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        /* أنماط pagination */
        .pagination {
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination .page-item .page-link {
            color: #2c3e50;
            border-radius: 8px;
            margin: 0 2px;
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            transition: all 0.3s;
        }
        
        .pagination .page-item .page-link:hover {
            background-color: #4a6491;
            color: white;
            border-color: #4a6491;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            border-color: #4a6491;
            font-weight: bold;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .pagination .page-item .page-link:focus {
            box-shadow: 0 0 0 0.2rem rgba(74, 100, 145, 0.25);
        }
        
        /* معلومات الصفحة */
        .pagination-info {
            text-align: center;
            color: #6c757d;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .pagination-info i {
            color: #4a6491;
            margin-left: 5px;
        }
        
        /* اختيار عدد العناصر في الصفحة */
        .per-page-selector {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .per-page-selector select {
            width: auto;
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .per-page-selector select:focus {
            border-color: #4a6491;
            outline: none;
        }
        
        /* تحسينات الجدول */
        .table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(74, 100, 145, 0.05);
            cursor: pointer;
        }
        
        /* عرض الصفحات */
        .page-numbers {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        
        .page-numbers .btn-page {
            min-width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            background: white;
            color: #2c3e50;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .page-numbers .btn-page:hover {
            background: #4a6491;
            color: white;
            border-color: #4a6491;
        }
        
        .page-numbers .btn-page.active {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            color: white;
            border-color: #4a6491;
        }
        
        .page-numbers .btn-page:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                <i class="fas fa-calendar-alt"></i> لائحة المواعيد
            </a>
            <a href="{{ route('admin.statistics') }}" class="nav-link">
                <i class="fas fa-chart-bar"></i> الإحصائيات
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link">
                <i class="fas fa-users"></i> إدارة المستخدمين
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-link">
                <i class="fas fa-cog"></i> الإعدادات
            </a>
            <a href="{{ route('appointment.create') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> واجهة الزبائن
            </a>
        </nav>
        
        <div class="mt-5 text-center">
            <p class="text-muted">مرحباً بك، {{ session('admin_name', 'المسؤول') }}</p>
            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h2><i class="fas fa-chart-pie"></i> مواردي الضريبة - لائحة المواعيد اليومية</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fas fa-table"></i> جدول المواعيد</h5>
                    
                    <!-- اختيار عدد العناصر في الصفحة -->
                    <div class="per-page-selector">
                        <span class="text-muted"><i class="fas fa-sliders-h"></i> عرض:</span>
                        <select onchange="changePerPage(this.value)" class="form-select form-select-sm">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-muted">موعد في الصفحة</span>
                    </div>
                </div>
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
                            @forelse($appointments as $appointment)
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
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">لا توجد مواعيد حالياً</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination بتصميم Bootstrap -->
                @if($appointments->hasPages())
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            {{-- Previous Page Link --}}
                            @if($appointments->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $appointments->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach($appointments->getUrlRange(1, $appointments->lastPage()) as $page => $url)
                                @if($page == $appointments->currentPage())
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if($appointments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $appointments->nextPageUrl() }}" rel="next">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>

                    <!-- Pagination بديل (أرقام الصفحات) -->
                    {{-- <div class="page-numbers">
                        @if(!$appointments->onFirstPage())
                            <a href="{{ $appointments->previousPageUrl() }}" class="btn-page">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif

                        @for($i = 1; $i <= $appointments->lastPage(); $i++)
                            @if($i >= $appointments->currentPage() - 2 && $i <= $appointments->currentPage() + 2)
                                <a href="{{ $appointments->url($i) }}" 
                                   class="btn-page {{ $i == $appointments->currentPage() ? 'active' : '' }}">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        @if($appointments->hasMorePages())
                            <a href="{{ $appointments->nextPageUrl() }}" class="btn-page">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif
                    </div> --}}
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // دالة تغيير عدد العناصر في الصفحة
        function changePerPage(value) {
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', 1); // الرجوع للصفحة الأولى
            window.location.href = url.toString();
        }
        
        // إضافة تأثير hover للصفوف
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('select')) {
                    // يمكن إضافة تفاصيل الموعد هنا
                    console.log('Clicked row:', this);
                }
            });
        });
        
        // تحديث حالة select عند تغيير الحالة
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function() {
                this.style.opacity = '0.5';
            });
        });
        
        // إخفاء رسائل النجاح بعد 5 ثواني
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>