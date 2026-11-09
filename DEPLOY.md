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

Sejak PBI-521, gambar mendapat salinan kecil untuk `srcset`. Gambar baru membuatnya sendiri saat diunggah. Untuk gambar yang diunggah sebelumnya, jalankan sekali `php artisan media:variants`; perintah ini aman diulang. Di hosting tanpa SSH, ikuti [Memperbarui kode](#memperbarui-kode-tanpa-ssh).

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

Paket ini tidak punya SSH dan Composer, jadi semua yang biasanya dikerjakan `scripts/deploy.sh` disiapkan di komputer sendiri. Folder dan database web lama tidak disentuh sampai langkah pengalihan, jadi web bisa dikembalikan kapan saja.

**Susunan web lama** (dicek 9 Nov 2026): proyek di `~/dikelolain-app`, dan `public_html` berisi salinan isi folder `public/`. `index.php` di `public_html` sama dengan `public/index.php`, hanya tiga path-nya diubah menjadi `../dikelolain-app/`. `public_html/storage` adalah symlink ke `dikelolain-app/storage/app/public`, yang dibuat lewat `link.php`. Versi baru memakai susunan yang sama dengan folder `~/dikelolain-v2`.

**Di komputer sendiri.** Hasilnya disimpan di `storage/app/private/deploy/`, tidak masuk git, dan jangan dibagikan.

1. Konversi database. Ekspor database lama dari phpMyAdmin (Export → Quick → SQL), lalu unduh folder `dikelolain-app/storage/app/public/media` (Compress → ZIP, lalu hapus ZIP itu dari server). Muat SQL ke MySQL lokal yang baru, salin `media` ke `storage/app/public/media`, arahkan koneksi lokal ke database itu, lalu jalankan `php artisan legacy:report` dan `php artisan migrate --force`. Ekspor hasilnya tanpa isi tabel `sessions` ke `dikelolain-baru.sql` (`mysqldump --no-tablespaces --single-transaction`, tanpa `--databases`). Server memakai MariaDB 11.8, jadi pastikan dump tidak memuat collation `utf8mb4_0900_*`; tabel Laravel memakai `utf8mb4_unicode_ci`.
2. `dikelolain-v2-kode.zip`: hasil `git archive`, ditambah `composer install --no-dev --optimize-autoloader`, folder `public/build` hasil `npm run build`, dan `.env` produksi berisi `APP_KEY` baru. Isian `DB_*` di `.env` dibiarkan kosong untuk diisi di server. Tanpa `tests` dan dokumen internal.
3. `public_html_baru.zip`: isi folder `public/`, dengan tiga path di `index.php` diubah menjadi `../dikelolain-v2/`, ditambah `buat-link-storage.php` (pengganti `php artisan storage:link`).
4. `uploads-legacy.zip`: foto WebP hasil konversi (`uploads/legacy`).
5. `cek-v2.php`: file sementara untuk mengecek versi baru sebelum pengalihan.

**Di hPanel**

6. Buat database **baru** (Databases → Management). Catat sendiri nama database, user, dan password-nya.
7. Unggah `dikelolain-v2-kode.zip` ke home (bukan ke `public_html`), Extract, lalu isi tiga baris `DB_*` di `dikelolain-v2/.env`.
8. Salin folder `dikelolain-app/storage/app/public/media` ke `dikelolain-v2/storage/app/public/`. Lalu unggah `uploads-legacy.zip` ke folder itu dan Extract.
9. Di phpMyAdmin database baru, impor `dikelolain-baru.sql`.
10. **Cek sebelum pengalihan.** Unggah `cek-v2.php` ke `public_html`, lalu buka `https://dikelolain.com/cek-v2.php?cek=server`; semua baris harus `[OK]`. Halaman versi baru bisa dibuka lewat `?p=/about`, `?p=/pages/team/hasan`, dan seterusnya. Tampilannya polos karena CSS-nya belum ada di `public_html`, jadi yang dicek isinya. Web lama tetap berjalan normal selama pengecekan.
11. Unggah `public_html_baru.zip` ke home, lalu Extract menjadi folder `public_html_baru`.
12. **Pengalihan.** Jika isi web lama diubah setelah ekspor, ulangi langkah 1, 8, dan 9 dulu. Lalu:
    - Buat folder `public_html_lama` di home, kemudian pindahkan **semua** isi `public_html` ke sana (`.htaccess`, `storage`, `link.php`, `robots.txt`, dan lainnya). `robots.txt` lama tidak boleh tertinggal, karena versi baru membuat `/robots.txt` sendiri. Hapus `cek-v2.php`.
    - Pindahkan semua isi `public_html_baru` ke `public_html`.
    - Buka `https://dikelolain.com/buat-link-storage.php` sekali, lalu hapus file itu.
13. Jalankan [Cek setelah online](#cek-setelah-online). **Untuk kembali ke versi lama**, pindahkan isi `public_html` kembali ke `public_html_baru`, lalu pindahkan isi `public_html_lama` ke `public_html`. Web lama tampil lagi dengan database lamanya.
14. Setelah stabil: ganti password admin, lalu pilih PHP 8.3 dan aktifkan OPcache (Advanced → PHP Configuration). Folder `dikelolain-app`, `public_html_lama`, dan database lama baru dihapus setelah beberapa minggu tanpa masalah.

### Memperbarui kode (tanpa SSH)

Dipakai pertama kali untuk PBI-521. Perubahan kode yang tidak menyentuh database cukup dipasang dengan dua file, dari `storage/app/private/deploy/` (tidak masuk git):

- **`pembaruan.zip`** berisi file yang berubah sejak versi di server, dalam dua folder:
  - `dikelolain-v2/...` dengan path yang sama seperti di proyek;
  - `public_html/...` untuk salinan file `public/`. Layout membaca `filemtime(public_path(...))` dari `dikelolain-v2/public`, sedangkan browser mengunduh dari `public_html`, jadi file `public/` harus ada di kedua tempat.

  Daftar filenya: `git diff --name-only <commit di server> -- app resources/views public config routes`, ditambah file baru yang belum ada di server. Setelah `npm run build:site`, ikutkan `public/webfonts/site/*`.
- **`pasang-pembaruan.php`** menampilkan error sebagai teks (bukan halaman 500), dan hanya menghapus dirinya bila ada baris yang diawali "SELESAI:". Ia memeriksa isi ZIP (hanya dua folder itu), menyalin setiap file lama yang akan tertimpa ke `dikelolain-v2/storage/app/private/cadangan-pembaruan/<waktu>/`, memasang filenya, lalu menghapus ZIP. Setelah itu ia menjalankan `view:clear`, membangun ulang cache config bila ada (agar setting baru terbaca), dan `media:variants` (paling lama 20 detik sekali buka), lalu menghapus dirinya sendiri setelah selesai.

Langkahnya:

1. Unggah kedua file ke `public_html`.
2. Buka `https://dikelolain.com/pasang-pembaruan.php`. Muat ulang halaman sampai muncul "Semua langkah selesai".
3. Jalankan [Cek setelah online](#cek-setelah-online).

**Untuk kembali**, salin isi folder cadangan ke tempat asalnya: `dikelolain-v2/...` ke `~/dikelolain-v2`, dan `public_html/...` ke `public_html`.

Perubahan yang butuh migration belum bisa lewat cara ini, karena perlu konversi database seperti langkah 1.

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
