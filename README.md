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

13. CSS tidak muncul di halaman detail karena alamat relatif dihitung dari URL halaman saat ini. Gunakan `{{ asset('css/style.css') }}` untuk menunjuk ke `public/css/style.css`.


## Cara Menjalankan Proyek TiketWar

1. Nyalakan Apache dan MySQL di XAMPP, lalu buat database `tiketwar` di phpMyAdmin.
2. Dari folder repository, masuk ke folder proyek: `cd tiketwar`
3. `composer install`
4. Salin `.env.example` menjadi `.env` (macOS/Linux: `cp .env.example .env`, Windows: `copy .env.example .env`).
5. Ubah bagian DB_ di `.env` menjadi `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=tiketwar`, `DB_USERNAME=root`, dan `DB_PASSWORD` dikosongkan, lalu `php artisan key:generate`
6. `php artisan migrate:fresh --seed`
7. `php artisan serve`, lalu buka `http://127.0.0.1:8000/konser`

## Jawaban Latihan Debugging (Modul 6)

1. Koneksi ditolak: pesan `SQLSTATE[HY000] [2002] Connection refused` muncul karena Laravel tidak bisa terhubung ke server MySQL. Dua kemungkinan penyebabnya: (a) MySQL di XAMPP belum menyala atau berjalan di port yang berbeda dari `DB_PORT`, diperiksa lewat XAMPP Control Panel dan `netstat -ano | findstr :3306`; (b) isi `.env` salah atau belum terbaca (misalnya `DB_CONNECTION` masih sqlite, atau host, nama database, username salah), diperbaiki dengan memeriksa blok `DB_` lalu menjalankan `php artisan config:clear`.
2. Model belum punya `$fillable`: `Konser::create([...])` memakai mass assignment, dan Laravel menolaknya dengan `MassAssignmentException` kalau model belum mendaftarkan kolom yang boleh diisi. Perbaikannya mengisi `protected $fillable = [...]` dengan kolom yang boleh diisi. Kalau `$fillable` sudah ada tetapi kolom `tanggal` tidak ditulis, tidak ada error: kolom itu dibuang diam-diam, sehingga data tidak lengkap atau database menolak karena kolom wajib kosong.
3. 419 Page Expired: form tidak memiliki `@csrf`. Laravel menolak kiriman POST yang tidak membawa token CSRF, sehingga muncul 419 dan bukan pesan validasi. Perbaikannya menambahkan `@csrf` tepat di bawah tag `<form>`.
4. 405 Method Not Allowed: form tidak memiliki `@method('PUT')`. Form HTML hanya mengenal GET dan POST, jadi tanpa `@method('PUT')` form mengirim POST, sedangkan route-nya `Route::put`. Perbaikannya menambahkan `@method('PUT')` di bawah `@csrf`.

## Jawaban Pertanyaan Seeder

Menjalankan `php artisan migrate:fresh --seed` berulang kali tidak menambah jumlah baris (tetap 3 promotor dan 5 konser). `migrate:fresh` menghapus semua tabel beserta datanya terlebih dahulu, lalu membuat ulang tabel dari migration, baru seeder mengisi data awal. Jadi setiap eksekusi dimulai dari tabel kosong dan hasilnya selalu sama, bukan menumpuk di atas data lama.