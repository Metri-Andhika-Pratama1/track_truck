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
Route::post('login', [LoginController::class, 'login'])->name('login');

// Route untuk logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Route untuk mencetak perjalanan
Route::get('/perjalanan/print/{id}', [PerjalananController::class, 'printPerjalanan'])->name('perjalanan.print');

// Route untuk menampilkan detail perjalanan
Route::get('/perjalanan/{id}', [PerjalananController::class, 'show'])->name('perjalanan.show');

// Route untuk mendapatkan data real-time perjalanan
Route::post('/perjalanan/update/{id}', [PerjalananController::class, 'updateRealTimeData'])->name('perjalanan.updateRealTimeData');

Route::get('/perjalanan/{id}/real-time-location', [PerjalananController::class, 'getRealTimeLocation']);
Route::get('/perjalanan/{id}/real-time-fuel-level', [PerjalananController::class, 'getRealTimeFuelLevel']);

// Middleware auth untuk grup rute yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () { 
    Route::resource('details', DetailPerjalananController::class);
    Route::resource('truk', TrukController::class);
    Route::resource('gudang', GudangController::class);
    Route::resource('perjalanan', PerjalananController::class);
    Route::resource('supir', SupirController::class);
});
