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
    Route::get('/account', Modules\Setup\Livewire\SetupAccount::class)->name('setup.owner');
});
