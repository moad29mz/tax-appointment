<?php

// use App\Http\Controllers\AppointmentController;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect()->route('appointment.create');
// });

// // واجهة الزبائن
// Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
// Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
// Route::get('/appointment/success', [AppointmentController::class, 'success'])->name('appointment.success');
// Route::get('/api/available-times', [AppointmentController::class, 'getAvailableTimes'])->name('appointment.availableTimes');

// // واجهة الإدارة
// Route::prefix('admin')->group(function () {
//     Route::get('/dashboard', [AppointmentController::class, 'admin'])->name('admin.dashboard');
//     Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('admin.updateStatus');
    
//     Route::get('/statistics', [AppointmentController::class, 'statistics'])->name('admin.statistics');
    
//     Route::get('/users', [AppointmentController::class, 'users'])->name('admin.users');
//     Route::post('/users', [AppointmentController::class, 'storeUser'])->name('admin.users.store');
//     Route::post('/users/{id}', [AppointmentController::class, 'updateUser'])->name('admin.users.update');
//     Route::delete('/users/{id}', [AppointmentController::class, 'deleteUser'])->name('admin.users.delete');
    
//     Route::get('/settings', [AppointmentController::class, 'settings'])->name('admin.settings');
//     Route::post('/settings', [AppointmentController::class, 'updateSettings'])->name('admin.settings.update');
    
//     // إضافة مسار تسجيل الخروج
//     Route::post('/logout', function() {
//         session()->forget('admin_user');
//         return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
//     })->name('admin.logout');
// });



// Route::prefix('admin')->group(function () {
//     // صفحة تسجيل الدخول
//     Route::get('/login', function() {
//         return view('admin.auth.login');
//     })->name('admin.login');
    
//     // معالجة تسجيل الدخول
//     Route::post('/login', function() {
//         // هنا سيتم معالجة تسجيل الدخول
//         return redirect()->route('admin.dashboard');
//     })->name('admin.login.submit');
    
//     // تسجيل الخروج
//     Route::post('/logout', function() {
//         session()->forget('admin_user');
//         return redirect()->route('admin.login');
//     })->name('admin.logout');
// });


// // البريد: admin@tax.gov

// // كلمة المرور: admin123


use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

// الصفحة الرئيسية
Route::get('/', function () {
    return redirect()->route('appointment.create');
});

// واجهة الزبائن
Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/appointment/success', [AppointmentController::class, 'success'])->name('appointment.success');
Route::get('/api/available-times', [AppointmentController::class, 'getAvailableTimes'])->name('appointment.availableTimes');

// ========== صفحة دخول الإدارة ==========
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (Illuminate\Http\Request $request) {
    if ($request->email == 'admin@tax.gov' && $request->password == 'admin123') {
        session(['admin_logged_in' => true, 'admin_name' => 'المسؤول الرئيسي']);
        return redirect('/admin/dashboard');
    }
    return back()->with('error', 'خطأ في الدخول');
})->name('admin.login.submit');

Route::post('/admin/logout', function () {
    session()->forget('admin_logged_in');
    return redirect('/admin/login');
})->name('admin.logout');

// ========== صفحات الإدارة (حماية بسيطة) ==========
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->admin();
    })->name('admin.dashboard');
    
    Route::post('/appointments/{id}/status', function ($id, Illuminate\Http\Request $request) {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->updateStatus($request, $id);
    })->name('admin.updateStatus');
    
    Route::get('/statistics', function () {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->statistics();
    })->name('admin.statistics');
    
    Route::get('/users', function () {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->users();
    })->name('admin.users');
    
    Route::post('/users', function (Illuminate\Http\Request $request) {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->storeUser($request);
    })->name('admin.users.store');
    
    Route::post('/users/{id}', function ($id, Illuminate\Http\Request $request) {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->updateUser($request, $id);
    })->name('admin.users.update');
    
    Route::delete('/users/{id}', function ($id) {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->deleteUser($id);
    })->name('admin.users.delete');
    
    Route::get('/settings', function () {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->settings();
    })->name('admin.settings');
    
    Route::post('/settings', function (Illuminate\Http\Request $request) {
        if (!session('admin_logged_in')) {
            return redirect('/admin/login');
        }
        return app(AppointmentController::class)->updateSettings($request);
    })->name('admin.settings.update');
});