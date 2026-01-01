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
    Route::get('login', Modules\User\Livewire\Auth\Login::class)
        ->middleware('guest')->name('login');

    Route::get('register', Modules\User\Livewire\Auth\Register::class)
        ->middleware('guest')->name('register');
});
