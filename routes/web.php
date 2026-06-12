<?php

use App\Http\Controllers\AppointmentChatbotController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// DB::listen(function ($event) {
//     dump($event->sql);
// });

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit')->middleware('can:profile.manage');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('can:profile.manage');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('can:profile.manage');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index')->middleware('can:appointments.view.all,appointments.view.own');
    Route::get('/appointments/calendar', [AppointmentController::class, 'calendar'])->name('appointments.calendar');

    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/appointments/chatbot', [AppointmentChatbotController::class, 'index'])->name('appointments.chatbot')->middleware('can:appointments.chatbot.access');
    Route::post('/appointments/chatbot/sendMessage', [AppointmentChatbotController::class, 'sendMessage'])->name('appointments.chatbot.sendMessage')->middleware('can:appointments.chatbot.access');

    Route::post('/appointments/availableTimeCheck', [AppointmentController::class, 'availableTimeCheck'])->name('appointments.availableTimeCheck');
    Route::post('/appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');

    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    });
});

require __DIR__.'/auth.php';
