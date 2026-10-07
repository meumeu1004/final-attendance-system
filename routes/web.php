<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AuthController;

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

//signup
Route::get('/signup', [StudentController::class, 'showSignup'])
    ->name('signup');

Route::post('/signup', [StudentController::class, 'signup'])
    ->name('signup.store');

//forgot pass
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot-password');

Route::get('/change-password', function () {
    return view('change-password');
})->name('change-password');

//dashboard
Route::get('/student/home', [StudentController::class, 'dashboard'])
    ->name('student.dashboard');

Route::post('/student/attendance', [StudentController::class, 'storeAttendance'])
    ->name('student.attendance.store');