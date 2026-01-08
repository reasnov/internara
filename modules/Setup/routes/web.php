<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('setup')->group(function () {
    Route::get('/', fn () => redirect()->route('setup.welcome'))->name('setup');
    Route::get('/welcome', Modules\Setup\Livewire\SetupWelcome::class)->name('setup.welcome');
    Route::get('/account', Modules\Setup\Livewire\SetupAccount::class)->name('setup.account');
    Route::get('/school', Modules\Setup\Livewire\SetupSchool::class)->name('setup.school');
    Route::get('/department', Modules\Setup\Livewire\SetupDepartment::class)->name('setup.department');
    Route::get('/internship', Modules\Setup\Livewire\SetupInternship::class)->name('setup.internship');
    Route::get('/complete', Modules\Setup\Livewire\SetupComplete::class)->name('setup.complete');
});
