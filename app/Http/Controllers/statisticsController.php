<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class statisticsController extends Controller
{
    public function statistics()
{
    // الإحصائيات الشهرية
    $monthlyStats = Appointment::select(
            DB::raw('MONTH(appointment_date) as month'),
            DB::raw('YEAR(appointment_date) as year'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "processed" THEN 1 ELSE 0 END) as processed'),
            DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
        )
        ->whereYear('appointment_date', date('Y'))
        ->groupBy(DB::raw('YEAR(appointment_date)'), DB::raw('MONTH(appointment_date)'))
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

    // الإحصائيات حسب نوع الخدمة
    $serviceStats = Appointment::select(
            'appointment_type',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('appointment_type')
        ->get();

    // الإحصائيات اليومية للأسبوع الحالي
    $weekDays = [];
    $startOfWeek = Carbon::now()->startOfWeek();
    
    for ($i = 0; $i < 7; $i++) {
        $date = $startOfWeek->copy()->addDays($i);
        $appointmentsCount = Appointment::whereDate('appointment_date', $date)->count();
        $processedCount = Appointment::whereDate('appointment_date', $date)
            ->where('status', 'processed')
            ->count();
            
        $weekDays[$date->format('Y-m-d')] = [
            'day_name' => $date->isoFormat('dddd'),
            'appointments' => $appointmentsCount,
            'processed' => $processedCount,
        ];
    }

    // أكثر المواعيد ازدحاماً
    $busiestTimes = Appointment::select(
            DB::raw('HOUR(appointment_time) as hour'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy(DB::raw('HOUR(appointment_time)'))
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();

    return view('admin.statistics', compact('monthlyStats', 'serviceStats', 'weekDays', 'busiestTimes'));
}
}
