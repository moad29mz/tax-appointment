@extends('layouts.admin')

@section('title', 'الإحصائيات - المديرية العامة للضرائب')

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            text-align: center;
            height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 400px;
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .progress {
            height: 8px;
            margin-top: 5px;
        }
        .progress-bar {
            background-color: #4a6491;
        }
    </style>
@endpush

@section('content')
    <div class="header">
        <h2><i class="fas fa-chart-pie"></i> الإحصائيات والتقارير</h2>
        <p class="mb-0">تحليل شامل لأداء نظام حجز المواعيد</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #36d1dc, #5b86e5);">
                @php
                    $totalAppointments = $monthlyStats->sum('total') ?? 0;
                @endphp
                <h3><i class="fas fa-calendar-check"></i> {{ $totalAppointments }}</h3>
                <p>إجمالي المواعيد هذا العام</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #00b09b, #96c93d);">
                @php
                    $processedAppointments = $monthlyStats->sum('processed') ?? 0;
                @endphp
                <h3><i class="fas fa-check-circle"></i> {{ $processedAppointments }}</h3>
                <p>مواعيد تمت معالجتها</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ff9966, #ff5e62);">
                @php
                    $pendingAppointments = $monthlyStats->sum('pending') ?? 0;
                @endphp
                <h3><i class="fas fa-clock"></i> {{ $pendingAppointments }}</h3>
                <p>مواعيد في الانتظار</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #7b4397, #dc2430);">
                @php
                    $total = $monthlyStats->sum('total') ?? 0;
                    $processed = $monthlyStats->sum('processed') ?? 0;
                    $percentage = $total > 0 ? round(($processed / $total) * 100, 1) : 0;
                @endphp
                <h3><i class="fas fa-percentage"></i> {{ $percentage }}%</h3>
                <p>نسبة الإنجاز</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="chart-container">
                <h4 class="mb-4"><i class="fas fa-chart-line"></i> الإحصائيات الشهرية لهذا العام</h4>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container">
                <h4 class="mb-4"><i class="fas fa-chart-pie"></i> توزيع الخدمات</h4>
                <canvas id="serviceChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="chart-container">
                <h4 class="mb-4"><i class="fas fa-calendar-week"></i> نشاط هذا الأسبوع</h4>
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-container">
                <h4 class="mb-4"><i class="fas fa-clock"></i> أكثر الأوقات ازدحاماً</h4>
                <div style="height: 300px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الساعة</th>
                                <th>عدد المواعيد</th>
                                <th>النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $busiestTotal = $busiestTimes->sum('total') ?? 0;
                            @endphp
                            @if($busiestTimes && $busiestTimes->count() > 0)
                                @foreach($busiestTimes as $time)
                                    <tr>
                                        <td>{{ sprintf('%02d:00', $time->hour) }}</td>
                                        <td>{{ $time->total }}</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ $busiestTotal > 0 ? ($time->total / $busiestTotal) * 100 : 0 }}%"></div>
                                            </div>
                                            {{ $busiestTotal > 0 ? round(($time->total / $busiestTotal) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive mt-4">
        <h4 class="mb-4"><i class="fas fa-table"></i> تفاصيل الإحصائيات الشهرية</h4>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>الشهر</th>
                    <th>إجمالي المواعيد</th>
                    <th>تمت المعالجة</th>
                    <th>في الانتظار</th>
                    <th>نسبة الإنجاز</th>
                    <th>متوسط يومي</th>
                </tr>
            </thead>
            <tbody>
                @if($monthlyStats && $monthlyStats->count() > 0)
                    @foreach($monthlyStats as $stat)
                        @php
                            $monthName = [
                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                            ];
                            $daysInMonth = $stat->month ? cal_days_in_month(CAL_GREGORIAN, $stat->month, $stat->year) : 30;
                            $completionPercentage = $stat->total > 0 ? ($stat->processed / $stat->total) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ isset($monthName[$stat->month]) ? $monthName[$stat->month] : 'غير معروف' }} {{ $stat->year }}</td>
                            <td>{{ $stat->total }}</td>
                            <td>{{ $stat->processed }}</td>
                            <td>{{ $stat->pending }}</td>
                            <td>
                                <span class="badge bg-{{ $completionPercentage >= 80 ? 'success' : ($completionPercentage >= 50 ? 'warning' : 'danger') }}">
                                    {{ round($completionPercentage, 1) }}%
                                </span>
                            </td>
                            <td>{{ round($stat->total / $daysInMonth, 1) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">لا توجد بيانات</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>

        @php
            $monthLabels = [];
            $monthTotals = [];
            $monthProcessed = [];
            
            if($monthlyStats && $monthlyStats->count() > 0) {
                $monthName = [
                    1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                    5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                    9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                ];
                
                foreach($monthlyStats as $stat) {
                    $monthLabels[] = ($monthName[$stat->month] ?? 'غير معروف') . ' ' . $stat->year;
                    $monthTotals[] = $stat->total;
                    $monthProcessed[] = $stat->processed;
                }
            }
            
            $paymentCount = $serviceStats ? ($serviceStats->firstWhere('appointment_type', 'payment')->total ?? 0) : 0;
            $consultationCount = $serviceStats ? ($serviceStats->firstWhere('appointment_type', 'consultation')->total ?? 0) : 0;
            
            $weekLabels = [];
            $weekAppointments = [];
            $weekProcessed = [];
            
            if($weekDays && count($weekDays) > 0) {
                foreach($weekDays as $day) {
                    $weekLabels[] = $day['day_name'];
                    $weekAppointments[] = $day['appointments'];
                    $weekProcessed[] = $day['processed'];
                }
            }
        @endphp
        
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'إجمالي المواعيد',
                    data: @json($monthTotals),
                    borderColor: '#36d1dc',
                    backgroundColor: 'rgba(54, 209, 220, 0.1)',
                    fill: true,
                    tension: 0.3
                }, {
                    label: 'تمت المعالجة',
                    data: @json($monthProcessed),
                    borderColor: '#00b09b',
                    backgroundColor: 'rgba(0, 176, 155, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        const serviceChart = new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['أداء الرسوم', 'استفسار وتوجيه'],
                datasets: [{
                    data: [{{ $paymentCount }}, {{ $consultationCount }}],
                    backgroundColor: ['#4a6491', '#2c3e50'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.raw / total) * 100);
                                label += context.raw + ' موعد (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
                }
            }
        });

        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyChart = new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: @json($weekLabels),
                datasets: [{
                    label: 'إجمالي المواعيد',
                    data: @json($weekAppointments),
                    backgroundColor: '#4a6491',
                    borderColor: '#2c3e50',
                    borderWidth: 1
                }, {
                    label: 'تمت المعالجة',
                    data: @json($weekProcessed),
                    backgroundColor: '#00b09b',
                    borderColor: '#008f7b',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endpush