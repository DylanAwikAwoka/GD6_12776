<?php

use Illuminate\Support\Facades\Route;

$daftarKonser = [
    1 => ['nama' => 'Coldplay - Music of the Spheres', 'kota' => 'Jakarta', 'harga' => 1500000, 'sisa' => 25, 'tanggal' => '2027-03-15'],
    2 => ['nama' => 'NCT Dream - The Dream Show', 'kota' => 'Jakarta', 'harga' => 1200000, 'sisa' => 0, 'tanggal' => '2027-04-12'],
    3 => ['nama' => 'Dewa 19 - Pesta Rakyat', 'kota' => 'Yogyakarta', 'harga' => 450000, 'sisa' => 120, 'tanggal' => '2027-05-20'],
    4 => ['nama' => 'Sheila on 7 - Tunggu Aku di', 'kota' => 'Surabaya', 'harga' => 650000, 'sisa' => 8, 'tanggal' => '2027-06-18'],
    5 => ['nama' => 'Tulus - Tur Manusia', 'kota' => 'Bandung', 'harga' => 550000, 'sisa' => 40, 'tanggal' => '2027-07-25'],
];

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/konser', function () use ($daftarKonser) {
    return view('konser', compact('daftarKonser'));
})->name('konser.index');

Route::get('/konser/{id}', function ($id) use ($daftarKonser) {
    abort_unless(isset($daftarKonser[$id]), 404);
    return view('konser-detail', [
        'id' => $id,
        'konser' => $daftarKonser[$id],
    ]);
})->whereNumber('id')->name('konser.detail');

Route::view('/tentang', 'about')->name('tentang');
