<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KonserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PromotorController;
use App\Models\Konser;
use App\Models\Promotor;
use Illuminate\Support\Facades\Route;

// Halaman publik
Route::get('/', function () {
    $jumlahKonser = Konser::count();
    $jumlahPromotor = Promotor::count();
    $totalSisa = Konser::sum('sisa');

    return view('home', compact('jumlahKonser', 'jumlahPromotor', 'totalSisa'));
})->name('home');

Route::view('/tentang', 'about')->name('tentang');
Route::view('/kontak', 'kontak')->name('kontak');

Route::get('/konser', [KonserController::class, 'index'])->name('konser.index');
Route::get('/konser/{id}', [KonserController::class, 'show'])
    ->whereNumber('id')->name('konser.detail');

// Pelanggan memesan tiket tanpa login
Route::get('/konser/{konser}/pesan', [PesananController::class, 'create'])
    ->whereNumber('konser')->name('pesanan.create');
Route::post('/konser/{konser}/pesan', [PesananController::class, 'store'])
    ->whereNumber('konser')->name('pesanan.store');

// Login dan logout admin
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:login'])->name('login.proses');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

// Halaman admin: wajib login
Route::middleware('auth')->group(function () {
    // Kelola konser
    Route::get('/konser/create', [KonserController::class, 'create'])
        ->name('konser.create');
    Route::post('/konser', [KonserController::class, 'store'])
        ->name('konser.store');
    Route::get('/konser/{id}/edit', [KonserController::class, 'edit'])
        ->whereNumber('id')->name('konser.edit');
    Route::put('/konser/{id}', [KonserController::class, 'update'])
        ->whereNumber('id')->name('konser.update');
    Route::delete('/konser/{id}', [KonserController::class, 'destroy'])
        ->whereNumber('id')->name('konser.destroy');

    // Kelola promotor
    Route::get('/promotor', [PromotorController::class, 'index'])->name('promotor.index');
    Route::get('/promotor/create', [PromotorController::class, 'create'])->name('promotor.create');
    Route::post('/promotor', [PromotorController::class, 'store'])->name('promotor.store');
    Route::get('/promotor/{id}/edit', [PromotorController::class, 'edit'])
        ->whereNumber('id')->name('promotor.edit');
    Route::put('/promotor/{id}', [PromotorController::class, 'update'])
        ->whereNumber('id')->name('promotor.update');
    Route::delete('/promotor/{id}', [PromotorController::class, 'destroy'])
        ->whereNumber('id')->name('promotor.destroy');

    // Kelola pesanan
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::patch('/pesanan/{pesanan}/bayar', [PesananController::class, 'bayar'])
        ->whereNumber('pesanan')->name('pesanan.bayar');
    Route::patch('/pesanan/{pesanan}/batal', [PesananController::class, 'batal'])
        ->whereNumber('pesanan')->name('pesanan.batal');

    // Ganti kata sandi admin
    Route::get('/akun/password', [AkunController::class, 'showPassword'])
        ->name('akun.password');
    Route::put('/akun/password', [AkunController::class, 'updatePassword'])
        ->name('akun.password.update');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
});
