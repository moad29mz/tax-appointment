<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // عرض نموذج الحجز للزبائن
    public function create()
    {
        return view('customer.appointment');
    }

    // تخزين الموعد الجديد
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'cin' => 'required|string|max:20|unique:appointments',
            'phone' => 'required|string|max:20',
            'appointment_type' => 'required|in:payment,consultation',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        // التحقق من أن الموعد غير محجوز مسبقاً
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'processed'])
            ->exists();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'هذا الموعد محجوز بالفعل، يرجى اختيار وقت آخر.']);
        }

        Appointment::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'cin' => $request->cin,
            'phone' => $request->phone,
            'appointment_type' => $request->appointment_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
        ]);

        return redirect()->route('appointment.success');
    }

    // عرض صفحة النجاح
    public function success()
    {
        return view('customer.success');
    }

    // عرض لوحة الإدارة
    public function admin()
    {
        $today = Carbon::today()->toDateString();
        
        $stats = [
            'pending' => Appointment::where('status', 'pending')->count(),
            'processed' => Appointment::where('status', 'processed')->count(),
            'today' => Appointment::whereDate('appointment_date', $today)->count(),
            'total' => Appointment::count(),
        ];

        $appointments = Appointment::orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'appointments'));
    }

    // تحديث حالة الموعد
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processed,cancelled',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'تم تحديث حالة الموعد بنجاح.');
    }

    // الحصول على الأوقات المتاحة ليوم محدد
    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;
        
        // الأوقات المتاحة من الساعة 9:00 إلى 16:30
        $allTimes = [];
        for ($hour = 9; $hour <= 16; $hour++) {
            // ساعة العمل تنتهي عند 16:30
            if ($hour == 16) {
                $allTimes[] = sprintf('%02d:00', $hour);
                $allTimes[] = sprintf('%02d:30', $hour);
            } else {
                $allTimes[] = sprintf('%02d:00', $hour);
                $allTimes[] = sprintf('%02d:30', $hour);
            }
        }

        // الأوقات المحجوزة لهذا اليوم
        $bookedTimes = Appointment::where('appointment_date', $date)
            ->whereIn('status', ['pending', 'processed'])
            ->pluck('appointment_time')
            ->toArray();

        // الأوقات المتاحة
        $availableTimes = array_diff($allTimes, $bookedTimes);

        return response()->json([
            'times' => array_values($availableTimes)
        ]);
    }
}