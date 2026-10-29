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
