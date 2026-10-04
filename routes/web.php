<?php

use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('/laporan/cetak', [LaporanController::class, 'export'])->name('laporan.cetak');
Route::get('/laporan/bulan', [LaporanController::class, 'bulanTersedia']);
