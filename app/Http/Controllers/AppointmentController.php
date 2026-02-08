<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        // تحديد مدة الخدمة
        $service_duration = $request->appointment_type == 'payment' ? '15 دقيقة' : '20 دقيقة';

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
            'service_duration' => $service_duration,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
        ]);

        return redirect()->route('appointment.success')->with([
            'appointment_type' => $request->appointment_type,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);
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
    // الحصول على الأوقات المتاحة ليوم محدد
public function getAvailableTimes(Request $request)
{
    $request->validate([
        'date' => 'required|date',
    ]);

    $date = $request->date;
    
    // الحصول على أوقات العمل من الإعدادات
    $startTime = \App\Models\Setting::getValue('working_hours_start', '09:00');
    $endTime = \App\Models\Setting::getValue('working_hours_end', '16:30');
    
    // تحويل أوقات العمل إلى ساعات
    $startHour = (int) explode(':', $startTime)[0];
    $endHour = (int) explode(':', $endTime)[0];
    $endMinute = (int) explode(':', $endTime)[1];
    
    // إنشاء قائمة بجميع الأوقات المتاحة
    $allTimes = [];
    for ($hour = $startHour; $hour <= $endHour; $hour++) {
        // إذا كانت هذه هي الساعة الأخيرة، نتحقق من الدقائق
        if ($hour == $endHour && $endMinute < 30) {
            $allTimes[] = sprintf('%02d:00', $hour);
        } else if ($hour == $endHour && $endMinute >= 30) {
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
        'times' => array_values($availableTimes),
        'working_hours' => $startTime . ' - ' . $endTime
    ]);
}

    // إحصائيات مفصلة - هذه هي الدالة المفقودة
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

    // إدارة المستخدمين
    public function users()
    {
        $users = AdminUser::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    // حفظ مستخدم جديد
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,manager,employee',
        ]);

        AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.users')->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    // تحديث المستخدم
    public function updateUser(Request $request, $id)
    {
        $user = AdminUser::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:admin_users,email,' . $id,
            'role' => 'required|in:admin,manager,employee',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    // حذف المستخدم
    public function deleteUser($id)
    {
        $user = AdminUser::findOrFail($id);
        
        // منع حذف آخر مدير
        if ($user->role == 'admin' && AdminUser::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users')->with('error', 'لا يمكن حذف آخر مدير في النظام.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'تم حذف المستخدم بنجاح.');
    }

    // صفحة الإعدادات
    // صفحة الإعدادات
public function settings()
{
    $settings = \App\Models\Setting::getAllGrouped();
    return view('admin.settings', compact('settings'));
}

// تحديث الإعدادات
public function updateSettings(Request $request)
{
    try {
        // حفظ الإعدادات العامة
        $this->saveSetting($request, 'organization_name');
        $this->saveSetting($request, 'municipality_name');
        
        // حفظ معلومات الاتصال
        $this->saveSetting($request, 'official_email');
        $this->saveSetting($request, 'phone_number');
        $this->saveSetting($request, 'address');
        
        // حفظ الإعدادات النظامية
        $this->saveSetting($request, 'language');
        $this->saveSetting($request, 'working_hours_start');
        $this->saveSetting($request, 'working_hours_end');
        
        // معالجة رفع الشعار
        if ($request->hasFile('logo')) {
            $this->handleLogoUpload($request);
        }
        
        return redirect()->route('admin.settings')->with('success', 'تم تحديث الإعدادات بنجاح.');
        
    } catch (\Exception $e) {
        return redirect()->route('admin.settings')->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage());
    }
}

// دالة مساعدة لحفظ الإعدادات
private function saveSetting(Request $request, $key)
{
    if ($request->has($key)) {
        \App\Models\Setting::setValue($key, $request->input($key));
    }
}

// دالة معالجة رفع الشعار
private function handleLogoUpload(Request $request)
{
    $request->validate([
        'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
    
    if ($request->hasFile('logo')) {
        $image = $request->file('logo');
        $imageName = 'logo_' . time() . '.' . $image->getClientOriginalExtension();
        
        // حفظ الصورة في المجلد العام
        $image->move(public_path('uploads/logo'), $imageName);
        
        // حفظ المسار في قاعدة البيانات
        \App\Models\Setting::setValue('logo', 'uploads/logo/' . $imageName);
    }
}

    // تحديث الإعدادات

    

    
    
}