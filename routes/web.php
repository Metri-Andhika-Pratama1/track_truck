<?php

use App\Http\Controllers\DetailPerjalananController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerjalananController;
use App\Http\Controllers\SupirController;
use App\Http\Controllers\TrukController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



// Route untuk menampilkan halaman beranda



Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Route untuk menampilkan formulir login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

// Route untuk menangani proses login
// routes/web.php
Route::post('login', [LoginController::class, 'login'])->name('login');

// Route untuk logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


// Middleware auth untuk grup rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () { 
    Route::resource('details', DetailPerjalananController::class);
    Route::resource('truk', TrukController::class);
    Route::resource('gudang', GudangController::class);
    Route::resource('perjalanan', PerjalananController::class);
    Route::resource('supir', SupirController::class);
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
