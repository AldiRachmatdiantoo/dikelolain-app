# Deploy Dikelolain

Ada dua cara. Yang dipakai sekarang adalah **versi statis**, karena situs ini untuk dipamerkan di LinkedIn dan adminnya hanya dipakai pemilik.

## Versi statis di Vercel

Situs disimpan sebagai file HTML, jadi tidak butuh server. Admin tetap di laptop.

1. Isi foto, teks, dan nomor WhatsApp asli lewat admin di `http://127.0.0.1:8000/admin/login`.
2. Buat akun Vercel, lalu login dari terminal:

```bash
npx vercel login
```

3. Ekspor dan unggah. Alamatnya harus sama persis dengan alamat di Vercel, karena link dan preview LinkedIn memakai alamat itu. Pada unggahan pertama alamatnya belum diketahui, jadi unggah sekali, catat alamatnya, lalu ulangi dengan alamat yang benar.

```bash
php artisan site:export https://dikelolain.vercel.app
```

```bash
npx vercel deploy dist --prod
```

4. Coba link-nya di [LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/) untuk melihat preview sebelum dibagikan.

Setiap isi berubah, edit di laptop lalu ulangi langkah 3.

Yang berbeda dari versi server: halaman admin tidak ikut online, dan pesan form kontak tidak tersimpan, tapi WhatsApp tetap terbuka. Paket gratis Vercel hanya untuk pemakaian pribadi non-komersial. Unggah sungguhan ke Vercel belum pernah dicoba; hasil ekspornya sudah diuji di server statis lokal.

## Versi server

Untuk hosting Linux dengan akses SSH, bila kelak admin perlu online. Langkahnya sudah digladikan di Apache lokal dengan SQLite dan MySQL; hasilnya ada di [status_backlog.md](status_backlog.md) (PBI-517).

### Syarat server

- PHP 8.2 atau lebih baru, dengan ekstensi `fileinfo`, `mbstring`, `openssl`, `gd`, dan `pdo_sqlite` atau `pdo_mysql`.
- Composer dan Git. Node 22 boleh tidak ada, tapi folder `public/build` lalu harus diunggah sendiri setelah `npm run build` di komputer Anda.
- **Document root mengarah ke folder `public/`**, bukan ke akar proyek. Kalau salah, file `.env` bisa terbaca orang.
- Apache dengan `mod_rewrite`, `mod_headers`, dan `mod_deflate`. Aturan kompresi dan cache browser sudah ada di `public/.htaccess`. Nginx tidak membaca file itu, jadi aturannya perlu ditulis ulang di konfigurasi server.
- `php.ini`:
  - `opcache.enable=1`
  - `upload_max_filesize=50M` dan `post_max_size=55M`, karena video boleh sampai 50 MB
- Folder `storage/` dan `bootstrap/cache/` bisa ditulis oleh web server.

### Rilis pertama

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

`db:seed` hanya untuk rilis pertama: perintah ini membuat akun admin dan isi demo. Kalau `ADMIN_PASSWORD` kosong, password acak tampil sekali di layar, jadi catat saat itu juga.

Terakhir, aktifkan sertifikat HTTPS dan pengalihan HTTP ke HTTPS di panel hosting.

### Rilis berikutnya

```bash
git pull
bash scripts/deploy.sh
```

Situs menampilkan halaman pemeliharaan selama migrasi berjalan, lalu kembali normal sendiri, juga bila skripnya gagal di tengah.

### Cek setelah online

1. `https://domain-anda.com/up` menjawab 200.
2. Halaman publik tampil lengkap, dan di konsol browser tidak ada aset yang diblokir (*mixed content*).
3. Login admin, buka mode edit, ganti satu teks, dan unggah satu gambar.
4. Kirim form kontak, lalu lihat pesannya di panel admin.
5. Pastikan lisensi video hero sudah dicek (lihat [kendala_backlog.md](kendala_backlog.md) KD-2).

### Yang perlu diingat

- **Jangan pernah menjalankan `migrate:fresh` di server.** Perintah itu menghapus semua isi situs.
- Cadangkan dua hal: database, dan folder `storage/app/public` yang berisi unggahan.
- Setelah mengubah `.env`, jalankan `php artisan optimize` agar nilainya terbaca.
- Hosting dengan proxy di depan situs (Azure, Cloudflare, load balancer) perlu `TRUSTED_PROXIES`. Tanpa itu semua pengunjung terhitung satu IP, sehingga batas 5 kali per menit di form kontak dan login terpakai bersama.
