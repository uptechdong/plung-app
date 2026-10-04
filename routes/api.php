<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::match(['put', 'post'], '/profile', [AuthController::class, 'updateProfile']);

    Route::get('/dashboard/warga', [DashboardController::class, 'warga']);
    Route::get('/dashboard/pengurus', [DashboardController::class, 'pengurus']);

    Route::get('/setoran', [SetoranController::class, 'index']);
    Route::post('/setoran', [SetoranController::class, 'store']);
    Route::put('/setoran/{setoran}/verify', [SetoranController::class, 'verify']);
    Route::get('/riwayat', [SetoranController::class, 'riwayat']);

    Route::get('/warga', [WargaController::class, 'index']);
    Route::post('/warga', [WargaController::class, 'store']);
    Route::put('/warga/{user}', [WargaController::class, 'update']);
    Route::delete('/warga/{user}', [WargaController::class, 'destroy']);

    Route::get('/rewards', [RewardController::class, 'index']);
    Route::post('/rewards', [RewardController::class, 'store']);
    Route::put('/rewards/{reward}', [RewardController::class, 'update']);
    Route::delete('/rewards/{reward}', [RewardController::class, 'destroy']);
    Route::post('/rewards/{reward}/tukar', [RewardController::class, 'tukar']);

    Route::get('/kategori-sampah', [SetoranController::class, 'kategori']);

    Route::get('/verifikasi/setoran', [VerifikasiController::class, 'setoranPending']);
    Route::put('/verifikasi/setoran/{setoran}', [VerifikasiController::class, 'setoranVerifikasi']);
    Route::get('/verifikasi/kupon', [VerifikasiController::class, 'kupon']);
    Route::put('/verifikasi/kupon/{penukaran}', [VerifikasiController::class, 'kuponToggle']);

    Route::get('/laporan/bulan', [LaporanController::class, 'bulanTersedia']);
    Route::get('/laporan/export', [LaporanController::class, 'export']);
});
