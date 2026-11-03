# Dikelolain

Website company profile agensi kreatif Dikelolain, dengan panel admin **VCMS** untuk mengedit konten langsung di tampilan halaman.

- **Arsitektur yang dituju:** [ARCHITECTURE.md](ARCHITECTURE.md)
- **Status pekerjaan:** [status_backlog.md](status_backlog.md)

## Stack

- Laravel 12, PHP 8.2+
- SQLite (lokal)
- Tampilan: template HTML (Bootstrap, jQuery, GSAP, Swiper) di `public/`

## Menjalankan di lokal

Kebutuhan: PHP 8.2+ dengan ekstensi `pdo_sqlite`, `sqlite3`, `fileinfo`, `mbstring`, `openssl`, `gd`, dan Composer 2. Di Windows, [Laragon](https://laragon.org) sudah menyediakan semuanya.

```bash
composer install
cp .env.example .env
# isi ADMIN_EMAIL dan ADMIN_PASSWORD di .env (opsional, lihat di bawah)
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka:

- Website: http://127.0.0.1:8000
- Admin: http://127.0.0.1:8000/admin/login

Akun admin dibuat oleh seeder dari `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` di `.env`. Kalau `ADMIN_PASSWORD` kosong, seeder membuat password acak dan menampilkannya **sekali** di terminal.

Upload di editor hanya menerima JPG, PNG, GIF, WebP (maks 10 MB), dan MP4/WebM (maks 50 MB).

Node/npm **belum diperlukan**: halaman belum memakai Vite. Vite baru dipakai untuk editor VCMS baru (lihat PBI-202 di backlog).

### Test

```bash
php artisan test
```

Memakai database SQLite in-memory dan data seeder, tidak menyentuh `database/database.sqlite`. Wajib hijau sebelum setiap commit.

### Mengulang database dari awal

```bash
php artisan migrate:fresh --seed
```

Perintah ini menghapus semua isi database lokal, termasuk perubahan yang dibuat lewat admin.

## Struktur singkat (kondisi sekarang)

| Bagian | Lokasi |
|---|---|
| Route | `routes/web.php` |
| Halaman publik | `resources/views/pages/` |
| Halaman editor admin | `resources/views/admin/pages/` |
| API simpan konten | `app/Http/Controllers/Admin/ContentController.php` |
| Editor VCMS (JS) | `public/js/admin-vcms.js` |
| Data awal | `database/seeders/SectionContentSeeder.php` |

Struktur ini sedang dirombak menjadi layered architecture. Bentuk akhirnya ada di [ARCHITECTURE.md](ARCHITECTURE.md).
