<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

//index
Route::get('/', function () {
    return view('index');
})->name('home');

//login
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

//logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

//signup
Route::get('/signup', [StudentController::class, 'showSignup'])
    ->name('signup');

Route::post('/signup', [StudentController::class, 'signup'])
    ->name('signup.store');

//forgot pass
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot-password');

Route::post('/forgot-password', [AuthController::class, 'verifyPasswordReset'])
    ->name('password.verify');

Route::get('/change-password', function () {
    if (!session()->has('password_reset_student_id')) {
        return redirect()->route('forgot-password');
    }

    return view('change-password');
})->name('change-password');

Route::post('/change-password', [AuthController::class, 'updatePassword'])
    ->name('password.update');

//student
Route::middleware('user.type:student')->group(function () {
    Route::get('/student/home', [StudentController::class, 'dashboard'])
        ->name('student.dashboard');

    Route::post('/student/attendance', [StudentController::class, 'storeAttendance'])
        ->name('student.attendance.store');
});

//admin
Route::middleware('user.type:admin')->group(function () {
    Route::get('/professor/home', [AdminController::class, 'dashboard'])
        ->name('professor.dashboard');

    Route::post('/professor/session', [AdminController::class, 'createSession'])
        ->name('professor.session.create');

    Route::post('/professor/session/{sessionId}/close', [AdminController::class, 'closeSession'])
        ->name('professor.session.close');
});