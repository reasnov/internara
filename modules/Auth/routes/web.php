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

Route::prefix('auth')->group(function () {
    Route::get('login', Modules\Auth\Livewire\Login::class)
        ->middleware('guest')->name('login');

    Route::get('register', Modules\Auth\Livewire\Register::class)
        ->middleware('guest')->name('register');

    Route::get('email/verify/{id}/{hash}', Modules\Auth\Livewire\VerifyEmail::class)
        ->middleware(['auth', 'signed'])->name('verification.verify');

    Route::get('email/verify', Modules\Auth\Livewire\VerificationNotice::class)
        ->middleware('auth')->name('verification.notice');
});
