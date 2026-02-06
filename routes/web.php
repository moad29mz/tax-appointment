<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('appointment.create');
});

// واجهة الزبائن
Route::get('/appointment', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/appointment', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/appointment/success', [AppointmentController::class, 'success'])->name('appointment.success');
Route::get('/api/available-times', [AppointmentController::class, 'getAvailableTimes'])->name('appointment.availableTimes');

// واجهة الإدارة
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AppointmentController::class, 'admin'])->name('admin.dashboard');
    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('admin.updateStatus');
    
    Route::get('/statistics', [AppointmentController::class, 'statistics'])->name('admin.statistics');
    
    Route::get('/users', [AppointmentController::class, 'users'])->name('admin.users');
    Route::post('/users', [AppointmentController::class, 'storeUser'])->name('admin.users.store');
    Route::post('/users/{id}', [AppointmentController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AppointmentController::class, 'deleteUser'])->name('admin.users.delete');
    
    Route::get('/settings', [AppointmentController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AppointmentController::class, 'updateSettings'])->name('admin.settings.update');
    
    // إضافة مسار تسجيل الخروج
    Route::post('/logout', function() {
        session()->forget('admin_user');
        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    })->name('admin.logout');
});