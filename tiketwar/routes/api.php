<?php

use App\Http\Controllers\Api\KonserController;
use App\Http\Controllers\Api\LaporanController as ApiLaporanController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API publik TiketWar (modul API dan Autentikasi, kelas)
Route::get('/konser', [KonserController::class, 'index'])
    ->name('api.konser.index');
Route::get('/konser/{konser}', [KonserController::class, 'show'])
    ->whereNumber('konser')->name('api.konser.show');
Route::post('/konser/{konser}/pesanan', [PesananController::class, 'store'])
    ->whereNumber('konser')->name('api.pesanan.store');

// Token admin (Tugas Rumah: Sanctum dan Audit Keamanan)
Route::post('/login', [TokenController::class, 'store'])
    ->middleware('throttle:login')->name('api.login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [TokenController::class, 'destroy'])
        ->name('api.logout');
    Route::get('/admin/pesanan', [PesananController::class, 'index'])
        ->middleware('abilities:pesanan:baca')
        ->name('api.admin.pesanan.index');
    Route::get('/admin/laporan', [ApiLaporanController::class, 'index'])
        ->name('api.admin.laporan.index');

    // Kelola konser untuk admin
    Route::post('/konser', [KonserController::class, 'store'])
        ->name('api.konser.store');
    Route::put('/konser/{konser}', [KonserController::class, 'update'])
        ->whereNumber('konser')->name('api.konser.update');
    Route::delete('/konser/{konser}', [KonserController::class, 'destroy'])
        ->whereNumber('konser')->name('api.konser.destroy');

    // Kelola status pesanan untuk admin
    Route::patch('/pesanan/{pesanan}/bayar', [PesananController::class, 'bayar'])
        ->whereNumber('pesanan')->name('api.pesanan.bayar');
    Route::patch('/pesanan/{pesanan}/batal', [PesananController::class, 'batal'])
        ->whereNumber('pesanan')->name('api.pesanan.batal');
});
