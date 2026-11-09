# Deploy Dikelolain

Panduan untuk hosting Linux dengan akses SSH. Langkahnya sudah digladikan di Apache lokal dengan SQLite dan MySQL; hasilnya ada di [status_backlog.md](status_backlog.md) (PBI-517). Untuk paket tanpa SSH seperti Hostinger Single, lihat [bagian Hostinger](#hostinger-single-web-hosting-tanpa-ssh). Untuk mengganti web lama tanpa kehilangan isinya, lihat [Pindah dari web lama](#pindah-dari-web-lama-isi-ikut-pindah).

## Syarat server

- PHP 8.2 atau lebih baru, dengan ekstensi `fileinfo`, `mbstring`, `openssl`, `gd`, dan `pdo_sqlite` atau `pdo_mysql`.
- Composer dan Git. Node 22 boleh tidak ada, tapi folder `public/build` lalu harus diunggah sendiri setelah `npm run build` di komputer Anda.
- **Document root mengarah ke folder `public/`**, bukan ke akar proyek. Kalau salah, file `.env` bisa terbaca orang.
- Apache dengan `mod_rewrite`, `mod_headers`, dan `mod_deflate`. Aturan kompresi dan cache browser sudah ada di `public/.htaccess`. Nginx tidak membaca file itu, jadi aturannya perlu ditulis ulang di konfigurasi server.
- `php.ini`:
  - `opcache.enable=1`
  - `upload_max_filesize=50M` dan `post_max_size=55M`, karena video boleh sampai 50 MB
- Folder `storage/` dan `bootstrap/cache/` bisa ditulis oleh web server.

## Rilis pertama

```bash
git clone <alamat-repo> dikelolain
cd dikelolain
cp .env.production.example .env
```

Isi `.env`, minimal `APP_URL` dan `ADMIN_EMAIL`. Lalu:

```bash
bash scripts/deploy.sh
```

```bash
php artisan db:seed --force
```

`db:seed` hanya untuk rilis pertama di database kosong: perintah ini membuat akun admin dan isi demo. Kalau `ADMIN_PASSWORD` kosong, password acak tampil sekali di layar, jadi catat saat itu juga. **Jangan jalankan `db:seed` di database web lama**; lihat [Pindah dari web lama](#pindah-dari-web-lama-isi-ikut-pindah).

Terakhir, aktifkan sertifikat HTTPS dan pengalihan HTTP ke HTTPS di panel hosting.

## Rilis berikutnya

```bash
git pull
bash scripts/deploy.sh
```

Situs menampilkan halaman pemeliharaan selama migrasi berjalan, lalu kembali normal sendiri, juga bila skripnya gagal di tengah.

## Pindah dari web lama (isi ikut pindah)

Database web lama menyimpan isi halaman di tabel `sections` dan `section_contents`. Migration `2026_11_03_000014` (PBI-520) melakukan ini sebelum menghapus tabel lama:

1. Menyalin isi tiga tabel lama ke `storage/app/private/backups/legacy-content-<waktu>.json`.
2. Memindahkan isinya ke tabel baru, sehingga setiap halaman menampilkan isi yang sama dengan web lama.
3. Mengubah foto unggahan JPG/PNG ke WebP: sisi terpanjang maksimal 2000 px, kualitas 82, disimpan di `storage/app/public/uploads/legacy/`. Foto aslinya tidak dihapus. Foto yang filenya tidak ada, atau yang versi WebP-nya tidak lebih kecil, dipakai apa adanya.
4. Menulis laporannya di `storage/app/private/backups/legacy-import-<waktu>.txt`.

Akun admin lama (tabel `users`) ikut terbawa, jadi `db:seed` tidak dijalankan. Yang perlu disiapkan:

- Folder `storage/app/public/media` dari web lama sudah ada di proyek sebelum `migrate`, karena WebP dibuat dari file di situ.
- Jalankan `php artisan legacy:report` dulu. Perintah ini menampilkan rencana pemindahan tanpa menulis apa pun. Bagian "Kunci yang tidak dikenali" seharusnya berisi "tidak ada".
- Sengaja tidak dipindah (tercatat di laporan): teks dan link layanan di footer (kini otomatis dari empat layanan pertama), telepon, email, dan alamat footer (kini sama dengan halaman Contact), testimoni tanpa nama dan butir keahlian tim yang kosong (di web lama tidak tampil atau tampil sebagai "blank"), serta video hero dari server demo template (diganti salinannya di proyek).
- Alamat detail berubah: `/pages/team/0` menjadi `/pages/team/<slug-nama>`, dan `/pages/service/new_...` menjadi `/pages/service/<slug-nama>`.

## Hostinger Single Web Hosting (tanpa SSH)

Paket ini tidak punya SSH dan Composer, jadi database diubah di komputer sendiri, lalu hasilnya diunggah. Folder dan database web lama tidak disentuh sampai langkah pengalihan, jadi web bisa dikembalikan kapan saja.

**Di komputer sendiri**

1. **Tepat sebelum pengalihan** (isi web lama masih bisa diubah sampai saat itu): ekspor database lama dari phpMyAdmin (Export → Quick → SQL). Lalu di File Manager, buka `storage/app/public` proyek lama, Compress folder `media` ke ZIP, unduh, dan hapus ZIP itu dari server.
2. Muat SQL itu ke database MySQL lokal yang baru, salin isi ZIP ke `storage/app/public/media`, dan arahkan koneksi database lokal ke database itu. Jalankan `php artisan legacy:report`, lalu `php artisan migrate --force`, dan baca laporannya.
3. Ekspor hasilnya: `mysqldump --no-tablespaces --single-transaction <nama-db> > dikelolain-baru.sql` (tanpa `--databases`). Server Hostinger memakai MariaDB 11.8. Dump dari MySQL 8 lokal bisa diimpor ke sana selama tidak ada collation `utf8mb4_0900_*`: tabel Laravel memakai `utf8mb4_unicode_ci`, dan ini sudah dicek pada hasil konversi 9 Nov 2026.
4. Siapkan kode: `composer install --no-dev --optimize-autoloader` dan `npm run build`. Buat ZIP proyek berisi `vendor/`, `public/build/`, dan `storage/app/public/` (media lama dan `uploads/legacy`). Jangan ikutkan `.git`, `node_modules`, `tests`, `.env`, `storage/app/private`, dan isi `storage/logs` serta `storage/framework/*`. Foldernya tetap harus ada.
5. Buat `APP_KEY` dengan `php artisan key:generate --show`.

**Di hPanel**

6. Buat database **baru** (Databases → Management). Catat nama database, user, dan password-nya sendiri. Impor `dikelolain-baru.sql` lewat phpMyAdmin database baru itu.
7. Unggah ZIP kode ke folder baru di home, misalnya `dikelolain-v2`, lalu Extract. Buat `.env` dari `.env.production.example`, lalu isi `APP_KEY`, `APP_URL=https://dikelolain.com`, `DB_CONNECTION=mysql`, dan `DB_*` database baru.
8. Pilih PHP 8.3 dan aktifkan OPcache (Advanced → PHP Configuration).
9. **Pengalihan.** Cadangkan isi `public_html` (Compress → ZIP). Lalu isi `public_html` dengan isi folder `public/` versi baru, dan ubah tiga baris di `public_html/index.php` supaya menunjuk ke folder proyek:

   ```php
   if (file_exists($maintenance = __DIR__.'/../dikelolain-v2/storage/framework/maintenance.php')) {
   require __DIR__.'/../dikelolain-v2/vendor/autoload.php';
   $app = require_once __DIR__.'/../dikelolain-v2/bootstrap/app.php';
   $app->usePublicPath(__DIR__);   // tambahkan di bawah baris $app
   ```

   `public_html/storage` harus menjadi symlink ke `dikelolain-v2/storage/app/public`. Tanpa SSH, symlink dibuat sekali lewat Cron Jobs hPanel dengan perintah `ln -sfn` (alamat lengkap dari home), lalu cron itu dihapus. Sebelum mengubah apa pun, cek dulu cara web lama menyambungkan `public_html` dan `storage`, lalu ikuti cara yang sama.
10. Jalankan [Cek setelah online](#cek-setelah-online). Kalau ada yang salah, kembalikan isi `public_html` dari ZIP cadangan, dan web lama tampil lagi dengan database lamanya.
11. Ganti password admin setelah rilis.

## Cek setelah online

1. `https://domain-anda.com/up` menjawab 200.
2. Halaman publik tampil lengkap, dan di konsol browser tidak ada aset yang diblokir (*mixed content*).
3. Login admin, buka mode edit, ganti satu teks, dan unggah satu gambar.
4. Kirim form kontak, lalu lihat pesannya di panel admin.
5. Pastikan lisensi video hero sudah dicek (lihat [kendala_backlog.md](kendala_backlog.md) KD-2).

## Yang perlu diingat

- **Jangan pernah menjalankan `migrate:fresh` di server.** Perintah itu menghapus semua isi situs.
- Cadangkan dua hal: database, dan folder `storage/app/public` yang berisi unggahan.
- Setelah mengubah `.env`, jalankan `php artisan optimize` agar nilainya terbaca.
- Hosting dengan proxy di depan situs (Azure, Cloudflare, load balancer) perlu `TRUSTED_PROXIES`. Tanpa itu semua pengunjung terhitung satu IP, sehingga batas 5 kali per menit di form kontak dan login terpakai bersama.
