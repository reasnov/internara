<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Livewire\AdminDashboard;
use Modules\Dashboard\Livewire\StudentDashboard;
use Modules\Dashboard\Livewire\TeacherDashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    // Student Dashboard
    Route::get('/dashboard', StudentDashboard::class)
        ->middleware(['dashboard.redirect', 'role:student'])
        ->name('dashboard');

    // Teacher Dashboard
    Route::get('/dashboard/teacher', TeacherDashboard::class)
        ->middleware('role:teacher')
        ->name('dashboard.teacher');

    // Admin & Owner Dashboard
    Route::get('/dashboard/admin', AdminDashboard::class)
        ->middleware('role:admin|owner')
        ->name('dashboard.admin');
});
