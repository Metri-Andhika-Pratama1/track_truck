<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetailPerjalananController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sensor-data', [DetailPerjalananController::class, 'receiveFromSensor']);

Route::get('/detail-perjalanan/{id}', [DetailPerjalananController::class, 'show'])->name('details.show');
Route::get('/api/sensor-data/latest/{id}', [DetailPerjalananController::class, 'getLatestByPerjalananId']);
