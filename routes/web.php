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

// API للحصول على الأوقات المتاحة
Route::get('/api/available-times', [AppointmentController::class, 'getAvailableTimes'])->name('appointment.availableTimes');

// واجهة الإدارة
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AppointmentController::class, 'admin'])->name('admin.dashboard');
    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('admin.updateStatus');
});