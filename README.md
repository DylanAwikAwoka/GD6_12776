# GD6_12776

Repository praktikum Modul 6 Laravel Fundamental dengan studi kasus TiketWar.

## Struktur Repository

- `README.md` berisi catatan praktikum dan jawaban latihan.
- `tiketwar/` berisi proyek Laravel.

## Menjalankan Aplikasi

```powershell
cd tiketwar
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

## Jawaban Latihan Debugging

1. PHP lama masih terpakai karena terminal belum dibuka ulang atau PATH masih menunjuk PHP lama. Tutup dan buka terminal baru, periksa `where php`, letakkan PHP baru di urutan pertama PATH, lalu periksa dengan `php -v`.

## Catatan artisan serve dan composer run dev

Modul ini cukup memakai php artisan serve karena CSS dan gambar dipanggil langsung dari folder public lewat asset(), Bootstrap diambil dari CDN, dan tidak ada file yang perlu dikompilasi sehingga Vite tidak dibutuhkan.

## Versi Tools

- PHP: `8.3.36` (CLI, output `php -v`).
- Composer: `2.10.3` (output `composer -V`).
- Laravel Installer: `5.32.0` (output `laravel --version`).
- Laravel Framework: `13.39.0` (output `php artisan --version`).

2. `php artisan` tidak ditemukan karena terminal berada di folder repository, bukan `tiketwar`. Jalankan `cd tiketwar`.
3. `composer run dev` menjalankan server Laravel, queue, dan Vite/npm. Untuk modul ini gunakan `php artisan serve` karena aset dibaca dari `public` dan tidak perlu dikompilasi.

4. Ribuan file di Git muncul karena `vendor` tidak diabaikan oleh `.gitignore`. Pastikan baris `/vendor` ada dan, bila sudah masuk staging, keluarkan dengan `git rm -r --cached tiketwar/vendor`.
5. Parse error terjadi karena perintah `return` tidak diakhiri titik koma. Tambahkan `;`.
6. `Undefined variable $id` terjadi karena fungsi route tidak menerima parameter. Gunakan `function ($id)`.

7. View `konser` tidak ditemukan karena nama file view harus berakhiran `.blade.php`. Gunakan `konser.blade.php` di `resources/views`.
8. Variabel `$daftarKonser` tidak masuk ke scope closure route. Tambahkan `use ($daftarKonser)` pada fungsi route.
9. Error Blade yang terjadi karena blok kondisi tidak ditutup dapat diperbaiki dengan menambahkan `@endif` setelah `@else`.

10. View layout tidak ditemukan karena nama folder yang dipakai harus sama. Gunakan folder `layouts` untuk `@extends('layouts.main')`.

11. Route `[tentang]` tidak ditemukan karena route belum diberi nama. Tambahkan `->name('tentang')` pada route tersebut.

12. Halaman detail menampilkan error untuk ID yang tidak ada karena belum ada pemeriksaan kunci array. Tambahkan `abort_unless(isset($daftarKonser[$id]), 404)` sebelum memakai data.
