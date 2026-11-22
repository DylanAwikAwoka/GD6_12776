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

## Jawaban Latihan Debugging (Modul 7)

1. **Total diambil dari form.** Kesalahannya: nilai `total` dipercaya dari `<input type="hidden" name="total">` (`$request->total`), padahal isi form bisa diubah lewat DevTools sebelum dikirim. Bahayanya, pembeli bisa mengubah `total` menjadi 1 sehingga 5 tiket terbayar seharga 1 tiket (kerugian finansial dan data laporan tidak akurat). Perbaikannya: hapus input `total` dari form, lalu hitung di server dengan `'total' => $terkunci->harga * $data['jumlah']` (harga diambil dari data konser, bukan dari request).
2. **Pengecekan stok tanpa kunci.** Kesalahannya: `$konser->sisa` dibaca **di luar** transaksi dan tanpa `lockForUpdate()`, sehingga dua pembeli bisa membaca `sisa` yang sama sebelum salah satunya menyimpan. Bahayanya terjadi oversell: tiket terjual dua kali dan `sisa` bisa menjadi minus. Perbaikannya: bungkus alur simpan dalam `DB::transaction()`, baca ulang konser dengan `Konser::lockForUpdate()->findOrFail($konser->id)` **di dalam** transaksi, cek `$data['jumlah'] > $terkunci->sisa` dari baris yang sudah terkunci, baru `decrement('sisa', ...)`.

## Jawaban Latihan Debugging (Modul 8: API dan Autentikasi)

### Debugging 1: Pesanan dari semua isian

Kesalahannya, `Pesanan::create($request->all())` meneruskan seluruh isi body JSON ke mass assignment. Karena `$fillable` pada model `Pesanan` memuat `konser_id`, `harga_satuan`, `total`, dan `status`, klien bisa mengirim nilai-nilai itu sendiri. Pada contoh, pesanan tersimpan dengan `konser_id` 1, `harga_satuan` 1, `total` 1, dan `status` `lunas`, sehingga penyusup mendapat tiket hampir gratis dan statusnya langsung lunas. Ini berbahaya karena aturan bisnis (harga, total, status, dan stok) diambil dari klien, bukan dihitung server. Perbaikannya: validasi hanya `nama_pemesan`, `email`, dan `jumlah`; ambil konser dari route; lalu hitung `harga_satuan`, `total`, dan `status` di server seperti pada `Api\PesananController@store`. Jangan pernah memakai `$request->all()` untuk membuat data.

### Debugging 2: Postman menerima HTML

Penyebabnya, request tidak membawa header `Accept: application/json`, sehingga Laravel menganggap kliennya browser. Middleware `auth` lalu mengalihkan pengunjung yang belum login ke route bernama `login`; sebelum route login dibuat hasilnya error `Route [login] not defined`, dan sesudah route login dibuat hasilnya halaman HTML login dengan status 200. Perbaikannya: kirim header `Accept: application/json` pada setiap request API. Dengan header itu `$request->expectsJson()` bernilai benar, sehingga middleware mengembalikan 401 JSON `{"message":"Unauthenticated."}` dan tidak mengalihkan ke halaman login.

### Debugging 3: Login yang selalu 419

Form itu tidak memiliki `@csrf`, sehingga tidak membawa token CSRF yang diperiksa Laravel pada setiap POST. Server menolak permintaan dengan 419 Page Expired. Perbaikannya: tambahkan `@csrf` di dalam `<form>`. Mematikan pemeriksaan CSRF (misalnya mengecualikan route login dari middleware `VerifyCsrfToken`) bukan perbaikan, karena justru membuka celah CSRF: situs lain bisa mengirim form login atas nama pengunjung, dan perlindungan terhadap permintaan lintas situs hilang.

### Debugging 4: Login buatan sendiri

Kesalahannya ada beberapa. Pertama, kata sandi dibandingkan dengan `!==` terhadap kolom `password`, padahal kolom itu menyimpan hash; perbandingan teks biasa tidak akan cocok dengan hash, dan kalau bisa cocok berarti kata sandi disimpan sebagai teks biasa. Kedua, tidak ada `Auth::attempt`, sehingga verifikasi hash dan pencatatan session tidak dilakukan dengan benar. Ketiga, pesan "Email tidak terdaftar" dan "Kata sandi salah" berbeda, sehingga penyerang bisa menebak email mana yang terdaftar (user enumeration). Keempat, tidak ada `session()->regenerate()` setelah login, sehingga rawan session fixation, dan tidak ada `redirect()->intended()` serta validasi input. Perbaikannya: gunakan `Auth::attempt($credentials)` dengan pesan gagal yang sama, lalu `session()->regenerate()` dan `redirect()->intended(route('pesanan.index'))` seperti pada `AuthController@login`.

### Debugging 5: Tombol hilang, halaman tetap terbuka

Penyebabnya, `@auth` hanya mengatur tampilan: tombol tidak digambar untuk pengunjung, tetapi route `/konser/create` dan `POST /konser` tetap terbuka karena tidak memakai middleware `auth`. Siapa pun bisa mengetik alamatnya atau mengirim POST langsung dan menambah konser. `@auth` bukan perlindungan, melainkan kenyamanan tampilan. Perbaikannya: masukkan route admin ke dalam `Route::middleware('auth')->group(function () { ... })` pada `routes/web.php`, lalu cek dengan `php artisan route:list -v --path=konser` bahwa `konser.create`, `konser.store`, `konser.edit`, `konser.update`, dan `konser.destroy` memiliki middleware `Illuminate\Auth\Middleware\Authenticate`, sedangkan `konser.index` dan `konser.detail` tidak.

## Audit Keamanan 10 Butir (Tugas Rumah: Guided 2)

Branch: `feature/audit-keamanan`. Pembatas percobaan login didefinisikan di `app/Providers/AppServiceProvider.php` dengan nama `login`:

```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
});
```

Limiter itu dipasang pada dua pintu login: `POST /login` memakai `['guest', 'throttle:login']` dan `POST /api/login` memakai `throttle:login`. Menggabungkan email dan IP berarti satu email tidak ikut terkunci hanya karena orang lain salah mengetik dari alamat berbeda.

| # | Butir | Hasil | Bukti ringkas |
|---|-------|-------|---------------|
| 1 | Halaman admin wajib login, dicek di server | Lulus | Saat logout, `/pesanan`, `/laporan`, `/konser/create`, dan `/konser/1/edit` semuanya `302` ke `/login`. |
| 2 | Semua aksi pengubah data dilindungi, bukan hanya tombolnya | Lulus | `php artisan route:list -v` menunjukkan `konser.store/update/destroy`, `promotor.store/update/destroy`, `pesanan.bayar/batal`, dan `pesanan.store` (web) memakai `Illuminate\Auth\Middleware\Authenticate`. `POST /api/konser/{konser}/pesanan` sengaja tetap publik karena itu alur beli tiket pelanggan; endpoint admin API memakai `Authenticate:sanctum`. |
| 3 | Mode debug mati di produksi (`APP_DEBUG=false`, `APP_ENV=production`) | Lulus | Route sementara `/uji-error` menghasilkan 500. Dengan `APP_DEBUG=true` halaman memuat `Exception`, pesan `uji`, dan stack trace; dengan `APP_DEBUG=false` hanya tampil halaman `Server Error` generik. Route sementara sudah dihapus; nilai `.env` lokal dikembalikan seperti semula. |
| 4 | `.env` tidak masuk Git dan tidak bisa diakses publik | Lulus | `git ls-files` tidak menemukan `.env` maupun `vendor/`; `GET /.env` menjawab `404`. Kredensial admin hanya ada di `.env` lokal dan `.env.example` berisi nama kunci tanpa nilai rahasia. |
| 5 | Kata sandi disimpan sebagai hash | Lulus | Kolom `password` pada tabel `users` diawali `$2y$12$`, bukan teks biasa. Hash itu berasal dari cast `password => hashed` pada model `User`. |
| 6 | HTTPS saat produksi | Lulus (catatan) | Saat hosting, HTTPS wajib agar kredensial login, cookie session, dan token Bearer tidak terkirim sebagai teks terbuka; `php artisan serve` memakai HTTP hanya untuk pengembangan lokal. |
| 7 | Mencegah injection dan XSS | Lulus | Nama konser sementara diubah menjadi `<script>alert(1)</script>`. Halaman `/konser` menampilkannya sebagai `&lt;script&gt;alert(1)&lt;/script&gt;` dan tidak ada tag skrip mentah. Query memakai Eloquent/parameter binding dan model memakai `$fillable`; nama konser sudah dikembalikan. |
| 8 | Validasi dan aturan bisnis di server | Lulus | `POST /api/konser/1/pesanan` dengan body tambahan `total: 1`, `status: "lunas"`, dan `harga_satuan: 1` tetap menghasilkan `harga_satuan` dan `total` 1500000 serta `status` `menunggu_bayar`; stok berkurang 22 menjadi 21. |
| 9 | Login tahan tebakan | Lulus | Pesan gagal web dan API sama untuk email tidak terdaftar maupun kata sandi salah; `AuthController@login` memanggil `session()->regenerate()`. Percobaan ke-1 sampai ke-5 salah menghasilkan `401` (API) / `302` kembali ke form (web), percobaan ke-6 menghasilkan `429 Too Many Attempts` pada kedua pintu. |
| 10 | CSRF aktif di form web dan CORS dibatasi | Sebagian: CSRF Lulus, CORS Tidak | Menghapus `@csrf` sementara dari form login membuat POST tanpa `_token` ditolak `419`; token sudah dipasang kembali. Namun `GET /api/konser` dengan `Origin: https://example.com` masih membalas `Access-Control-Allow-Origin: *` (default Laravel), jadi CORS belum dibatasi ke origin dikenal dan perlu diatur di modul Integrasi. |

Catatan: uji butir 3, 7, dan 10 memakai perubahan sementara. Sebelum commit, route `/uji-error` dihapus, nama konser dikembalikan, `@csrf` dipasang lagi, dan tidak ada perubahan `APP_DEBUG` yang ikut ke repository.

## Jawaban Unguided Modul 8

Semua soal dikerjakan di branch `unguided/modul8`. Branch ini sengaja dipush dan dibuka Pull Request-nya, tetapi tidak di-merge ke `main`.

### Soal 1: Filter Konser Berdasarkan Kota

`GET /api/konser` sekarang menerima `?kota=...` opsional. Tanpa parameter tetap tampil semua konser; kota yang tidak ada menghasilkan `"data": []`, bukan error.

**Pertanyaan — mengapa aman dari SQL injection?** Karena filter ditulis lewat Eloquent/Query Builder (`where('kota', $kota)`), nilai dari `$request->query('kota')` dikirim sebagai parameter terikat (prepared statement), bukan digabungkan ke dalam string SQL. Nilai tersebut juga hanya dipakai sebagai pembanding kolom, sehingga karakter khusus tidak pernah diartikan sebagai perintah SQL.

**Hasil uji:**

- `GET /api/konser` → 200, 5 konser (Bandung, Yogyakarta, Jakarta, Bali).
- `GET /api/konser?kota=Bandung` → 200, 1 konser, semua ber-kota Bandung.
- `GET /api/konser?kota=KotaTakAda` → 200, `"data": []`.

### Soal 2: Token dengan Hak Akses Terbatas

Token dari `POST /api/login` dibuat dengan `createToken('postman', ['pesanan:baca'])`. Route `GET /api/admin/pesanan` berada di dalam grup `auth:sanctum` dan ditambah `abilities:pesanan:baca`. Alias middleware `abilities` dan `ability` didaftarkan di `bootstrap/app.php` memakai `CheckAbilities` dan `CheckForAnyAbility` dari Sanctum.

**Pertanyaan — apa bedanya 401 dan 403?** `401 Unauthorized` berarti klien belum terbukti identitasnya: token tidak dikirim, salah, sudah dicabut, atau kedaluwarsa, sehingga `auth:sanctum` menolak. `403 Forbidden` berarti klien sudah dikenali (token sah) tetapi tidak punya hak untuk aksi itu, sehingga `abilities` yang menolak. Pada uji ini, permintaan tanpa token menghasilkan 401; token pembanding ber-ability `konser:baca` menghasilkan 403; token dari login admin yang ber-ability `pesanan:baca` menghasilkan 200.

**Hasil uji:**

- `POST /api/login` → 200, token dengan ability `pesanan:baca`.
- `GET /api/admin/pesanan` dengan token itu → 200, berisi daftar pesanan.
- Token pembanding dari tinker (`createToken('lain', ['konser:baca'])`) → 403.
- `GET /api/admin/pesanan` tanpa token → 401 `Unauthenticated.`.

### Soal 3: Token Kedaluwarsa

`TokenController@store` sekarang memakai argumen ketiga `createToken`, yaitu `now()->addHour()`, sehingga token dari `POST /api/login` berlaku satu jam. Ability tetap `['pesanan:baca']`, dan kolom `expires_at` pada tabel `personal_access_tokens` terisi.

**Pertanyaan — mengapa token yang tidak pernah kedaluwarsa berbahaya bila bocor?** Karena token berlaku selamanya sampai dicabut secara manual. Bila token bocor, penyerang bisa memakainya kapan saja untuk mengakses data admin, dan korban baru aman setelah token tersebut ditemukan lalu dicabut. Kedaluwarsa membatasi jendela penyalahgunaan; kredensial juga tetap perlu diganti bila dugaan kebocoran terjadi.

**Hasil uji:** Untuk mempercepat pengujian, masa berlaku sementara diubah menjadi `now()->addSeconds(20)`, lalu dikembalikan ke `addHour()` sebelum commit.

- Token 20 detik langsung dipakai → 200.
- Token yang sama setelah 25 detik → 401 `Unauthenticated.`.
- Setelah dikembalikan ke `addHour()`, login ulang → 200 dan `GET /api/admin/pesanan` → 200.

### Soal 4: Ganti Kata Sandi Admin

Ditambahkan `AkunController` dengan `showPassword()` dan `updatePassword()`, dua route di dalam grup `auth` (`GET /akun/password` bernama `akun.password` dan `PUT /akun/password` bernama `akun.password.update`), view `akun/password.blade.php`, serta tautan "Ganti Password" di navbar. Validasi memakai aturan `current_password` dan `password => required|string|min:8|confirmed`; kata sandi baru disimpan lewat `$request->user()->update(['password' => $data['password']])` dan otomatis di-hash oleh cast `hashed` pada model `User`.

**Pertanyaan — dampak bila `php artisan migrate:fresh --seed` dijalankan lagi setelah kata sandi diganti?** `ADMIN_PASSWORD` di `.env` menjadi usang. `AdminSeeder` memakai `config('admin.password')` (sumbernya `.env`) dan `updateOrCreate`, sehingga seeding ulang akan menimpa hash kata sandi baru dengan kata sandi lama dari `.env`. Artinya kata sandi baru yang diganti lewat form hilang setelah reseed; agar tetap dipakai, nilai `ADMIN_PASSWORD` di `.env` lokal juga harus diperbarui (tanpa pernah di-commit).

**Hasil uji:**

- Kata sandi lama salah → ditolak dengan pesan validasi.
- Konfirmasi tidak cocok → ditolak dengan pesan `confirmed`.
- Kata sandi baru kurang dari 8 karakter → ditolak dengan pesan panjang minimum.
- Kasus berhasil → 302 ke `/pesanan` dan pesan "Kata sandi berhasil diganti." tampil; logout lalu login dengan kata sandi baru berhasil, login dengan kata sandi lama gagal.
- Database dikembalikan dengan `migrate:fresh --seed` setelah uji agar kata sandi di `.env` lagi yang berlaku.

### Soal 5: API CRUD Konser untuk Admin

Ditambahkan `store()`, `update()`, dan `destroy()` pada `Api\KonserController` plus tiga route di dalam grup `auth:sanctum`. Aturan validasi disatukan di method privat `aturan()` dan dipakai ulang untuk tambah dan ubah, termasuk `exists:App\Models\Promotor,id`. Hanya data hasil `validate()` yang disimpan; `destroy()` menolak 409 bila konser masih memiliki pesanan.

**Pertanyaan — mengapa hapus memakai 204 dan mengapa konser berpesanan tidak boleh dihapus?** `204 No Content` berarti operasi berhasil tetapi tidak ada badan respons yang perlu dikirim; mengirim 200 dengan JSON kosong tidak memberi informasi tambahan. Konser yang sudah punya pesanan tidak boleh dihapus karena pesanan, pembayaran, dan laporan merujuk pada `konser_id` itu: penghapusan akan membuat riwayat transaksi kehilangan konteks atau menjadi data yatim. Lebih aman menolak penghapusan (atau menandai konser tidak tersedia) sampai pesanannya diselesaikan.

**Hasil uji:**

- `POST /api/konser` dengan data valid → 201 dan JSON konser baru (id 6).
- `POST` dengan `promotor_id: 9999` → 422.
- `PUT /api/konser/6` → 200, nama/kota/harga/sisa/tanggal berubah.
- `DELETE /api/konser/6` (tanpa pesanan) → 204 tanpa isi.
- `DELETE /api/konser/1` (masih punya pesanan) → 409 dengan pesan yang jelas.
- `POST`, `PUT`, dan `DELETE` tanpa token → 401.

### Soal 6: API Bayar dan Batal Pesanan untuk Admin

Ditambahkan `bayar()` dan `batal()` pada `Api\PesananController` dengan route `PATCH /api/pesanan/{pesanan}/bayar` dan `/batal` di dalam grup `auth:sanctum`. Keduanya berjalan dalam `DB::transaction()`, mengunci baris pesanan dengan `lockForUpdate()`, dan hanya memproses status `menunggu_bayar`. Pembatalan juga mengunci konser dan mengembalikan stok dengan `increment('sisa', $terkunci->jumlah)`. Status yang tidak sesuai ditolak 409.

**Pertanyaan — mengapa status dicek di dalam transaksi setelah `lockForUpdate`?** Karena pengecekan sebelum penguncian rentan race condition: dua permintaan bersamaan bisa sama-sama membaca status `menunggu_bayar`, lalu keduanya melanjutkan. Salah satunya bisa membayar atau membatalkan pesanan yang sudah diubah, dan pada pembatalan stok bisa dikembalikan dua kali. Dengan mengunci baris lebih dulu lalu mengecek status di dalam transaksi, permintaan kedua baru melihat hasil permintaan pertama setelah lock dilepas, sehingga aturan status dan stok tetap konsisten.

**Hasil uji:**

- Sisa konser 5 awalnya 57.
- Buat pesanan (jumlah 1) → 201, sisa menjadi 56. `PATCH .../bayar` → 200 dengan status `lunas`; `PATCH .../batal` pada pesanan itu → 409.
- Buat pesanan kedua → 201, sisa 55. `PATCH .../batal` → 200 status `batal` dan sisa kembali 56; `PATCH .../bayar` pada pesanan batal → 409.
- `PATCH .../bayar` tanpa token → 401.

### Soal 7: API Laporan Ringkasan untuk Admin

Dibuat `Api\LaporanController` (di-import dengan alias `ApiLaporanController` agar tidak bentrok dengan `LaporanController` web) dan route `GET /api/admin/laporan` di dalam grup `auth:sanctum`. Respons memakai `count()`, `sum('total')` hanya untuk pesanan `lunas`, dan agregasi `selectRaw`/`groupBy`/`pluck` untuk `per_status`. Nilai `sum` dikonversi ke `(int)`.

**Pertanyaan — mengapa menghitung di database lebih baik?** Karena database sudah dioptimalkan untuk agregasi: ia memindai baris yang relevan (dan bisa memakai indeks) lalu mengirim hanya beberapa angka ke aplikasi. Mengambil semua pesanan lalu menjumlahkannya di PHP memakan memori, bandwidth, dan waktu bertambah seiring jumlah data, serta logika perhitungan tersebar di kode aplikasi.

**Hasil uji:**

- `GET /api/admin/laporan` dengan token → 200:
  `{"data":{"jumlah_pesanan":8,"total_pendapatan":5800000,"per_status":{"batal":2,"lunas":4,"menunggu_bayar":2}}}`.
- `GET /api/admin/laporan` tanpa token → 401.
- Dashboard web `/laporan` pada saat yang sama menampilkan total 8, pendapatan Rp5.800.000, menunggu 2, dan batal 2 — angkanya cocok.


