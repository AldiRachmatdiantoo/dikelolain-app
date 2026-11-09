# Ringkasan Proyek Dikelolain

**Kondisi per:** 9 November 2026, setelah rilis (PBI-504)
**Hasil test:** 464 test lolos, 1.962 assertion (`php artisan test`)

> Dokumen internal. Isinya menyebut kelemahan versi lama dan rencana rilis, jadi jangan dibagikan ke publik.
>
> Dokumen ini adalah potret kondisi. Status harian tetap dicatat di [status_backlog.md](status_backlog.md), kendala di [kendala_backlog.md](kendala_backlog.md), dan aturan teknis di [ARCHITECTURE.md](ARCHITECTURE.md).

## Kondisi singkat

- **Refactor selesai di sisi kode.** 62 dari 66 PBI selesai, dan seluruh Epic 0–4 tuntas.
- **Sudah online sejak 9 Nov 2026 (PBI-504).** dikelolain.com di Hostinger kini menjalankan versi ini, dengan izin dosen. Web lama disimpan sebagai jalan kembali.
- **Isi web resmi ikut pindah** lewat migration (PBI-520), dan foto unggahannya dijadikan WebP.
- **PageSpeed (PBI-521, 522):** mobile naik dari 78 ke **92** (CLS 0,292 → 0, LCP 2,7 s, Aksesibilitas 99, SEO 100), desktop **98**, diukur 9 Nov 2026 setelah dipasang di Hostinger. Video hero kembali diputar di HP dengan potongan kecil 880 KB.
- **Tugas pemilik setelah rilis (KD-7):** coba mode edit, ganti password admin, hapus folder sementara.
- **Dua PBI menunggu pemilik:** konten asli (502) dan pengaturan server (507).

---

## 1. Gambaran proyek

Dikelolain adalah website company profile agensi kreatif, dengan **VCMS**: cara mengelola konten langsung di tampilan halaman, bukan lewat form terpisah. Kepanjangan VCMS tidak tercatat di repo.

### Asal repo

| Hal | Keterangan |
|---|---|
| Repo asal | `abahdzanil/dikelolain-app` |
| Repo refactor | `AldiRachmatdiantoo/dikelolain-app` (private, branch `main`) |
| Pengerjaan versi lama | 10 November 2025 sampai 16 Januari 2026, oleh `adzanilrachmadhip`, `AdliXSec`, dan `AldiRachmatdiantoo` |
| Pengerjaan refactor | 3–5 November 2026 |
| Titik awal refactor | Commit `02fa1fd`, berisi snapshot branch `back-end` repo asal |
| Riwayat lama | Branch lokal `arsip/*`, tidak di-push |

### Stack

| Bagian | Teknologi |
|---|---|
| Backend | Laravel 12.71, PHP 8.2+ (lokal 8.3.29) |
| Database | SQLite di lokal; MySQL 8 lolos gladi; PostgreSQL belum diuji |
| Website publik | Template HTML dengan Bootstrap, jQuery, GSAP, Swiper; digabung esbuild dan dipangkas PurgeCSS |
| Editor VCMS | Alpine.js, dibundel Vite, hanya dimuat saat mode edit |
| Test | PHPUnit, SQLite in-memory |
| Lokal | Laragon di Windows, Node 22 |

---

## 2. Fitur

### Website publik

- **Halaman:** Home, About, Services, Portfolio, Portfolio Experiment (slider horizontal), Pricing, FAQs, Team, Testimonials, Contact, dan 404.
- **Halaman detail:** `/pages/service/{slug}` dan `/pages/team/{slug}`.
- **Form kontak:** pesan disimpan ke database, lalu pengunjung diarahkan ke WhatsApp. Batasnya 5 kiriman per menit, dan ada kolom jebakan untuk bot.
- **SEO:** meta description, Open Graph, canonical, `/sitemap.xml`, dan `/robots.txt` dinamis.
- **Video YouTube:** tampil sebagai thumbnail, dan pemutarnya baru dimuat saat diklik.
- **Video hero:** hanya diunduh di lebar 768 px ke atas.

### Panel admin

- Login dengan batas 5 percobaan per menit.
- Dashboard berisi tautan untuk mengedit 9 halaman.
- Pesan Masuk, dengan tanda dibaca dan tombol balas lewat WhatsApp.
- Analytics lewat iframe Looker Studio.
- Pengaturan profil dan ganti password.

### VCMS

1. Admin yang login melihat bilah admin di halaman publik, lalu memilih **Edit halaman ini**.
2. Setiap bagian yang bisa diedit mendapat tombol edit.
3. Klik tombol membuka modal berisi field untuk bagian itu.
4. Setelah disimpan, halaman dimuat ulang dengan isi baru.

Admin bisa mengubah teks, gambar, video, link, dan SEO. Item daftar bisa ditambah, dihapus, digeser, dan disembunyikan. Gambar JPG, PNG, dan WebP yang diunggah diperkecil ke maksimal 2000 px dan disimpan sebagai WebP.

---

## 3. Arsitektur

Detail lengkapnya ada di [ARCHITECTURE.md](ARCHITECTURE.md).

### Lapisan

```text
Route -> Controller -> FormRequest -> Service -> Repository -> Model -> Database
```

Setiap lapisan punya satu tugas, dan alurnya satu arah. Controller tidak menulis query, dan view tidak berisi logika data.

### Prinsip VCMS

- **Satu view, dua mode.** Halaman publik dan halaman edit memakai file Blade yang sama.
- **Pengunjung tetap bersih.** Atribut dan script editor hanya muncul saat mode edit aktif.
- **Editor generik.** `resources/js/vcms/editor.js` membuat form dari definisi field, tanpa logika khusus per jenis konten.
- **Menambah jenis konten** cukup dengan definisi field dan endpoint, tanpa mengubah JavaScript.

### Model data

| Jenis | Tempat | Contoh |
|---|---|---|
| Nilai tunggal | Tabel `page_settings`; tipe, label, dan default di `config/page_settings.php` (133 key) | Judul hero, kontak, SEO |
| Daftar | 17 jenis daftar dengan tabel dan endpoint sendiri | FAQ, tim, layanan, project, harga |
| Pesan | Tabel `contact_messages` | Isi form Contact |

### Angka kode saat ini

| Hal | Jumlah |
|---|---|
| Route | 119 |
| Tabel database | 28, termasuk tabel bawaan Laravel |
| Migration | 22 |
| Model | 20 |
| File test | 49 |
| Controller terbesar | 70 baris |
| Editor VCMS | 169 baris |

### Lokasi penting

| Bagian | Lokasi |
|---|---|
| Route | `routes/web.php` |
| Halaman publik | `resources/views/pages/` |
| Section bersama | `resources/views/partials/` |
| Komponen VCMS | `resources/views/components/vcms/` |
| Definisi field | `app/Vcms/Fields/` |
| Logika halaman | `app/Services/Pages/` |
| Kerangka daftar | `ListService`, `EloquentListRepository`, `ListController` |
| Daftar nilai tunggal | `config/page_settings.php` |
| Build website publik | `scripts/build-site.mjs`, `resources/site-assets.json` |
| Rilis | `scripts/deploy.sh`, `.env.production.example`, [DEPLOY.md](DEPLOY.md) |

---

## 4. Yang sudah dikerjakan

| Epic | Isi | Selesai |
|---|---|---|
| 0 | Persiapan, smoke test, dokumen arsitektur | 5/5 |
| 1 | Hapus kode mati, amankan upload, perbaiki error | 11/11 |
| 2 | Fondasi VCMS baru dan FAQ | 10/10 |
| 3 | Pindahkan semua konten ke tabel sendiri | 13/13 |
| 4 | Hapus editor dan tabel lama | 6/6 |
| 5 | SEO, form kontak, performa, persiapan rilis, pembaruan dependency | 13/18 |

### Sebelum dan sesudah

| Ukuran | Sebelum | Sesudah |
|---|---|---|
| Request halaman Home | 59 | 30 |
| Video hero | 12,3 MB, ikut terunduh di HP | 3,0 MB, tidak diunduh di HP |
| Font ikon per halaman | 254 KB | ±5 KB |
| CSS publik | 416 KB | 163 KB |
| Pemutar YouTube saat halaman dibuka | 6 | 0 |
| Controller terbesar | 994 baris | 70 baris |
| JavaScript editor | 6.015 baris | 169 baris |
| Test otomatis | 2 | 451 |

Angka di atas diukur di lingkungan lokal. Kenaikan kecepatan lokal 2,4 detik ke 0,27 detik **jangan dipakai sebagai hasil refactor**, karena penyebabnya alamat `127.0.0.1` dan OPcache.

### Gladi rilis (PBI-517)

- **Lolos di Apache lokal mode produksi:** semua halaman, 404, kompresi, login, upload gambar, form kontak, HTTPS di balik proxy simulasi, dan pemulihan saat migrasi gagal.
- **Lolos di MySQL 8.0.30:** migrasi, seeder, halaman, dan seluruh test.
- **Bug yang ditemukan dan diperbaiki:** `db:seed` gagal pada instalasi tanpa paket dev.

---

## 5. Yang belum selesai

| PBI | Pekerjaan | Status | Menunggu | Langkah berikutnya |
|---|---|---|---|---|
| 502 | Ganti konten demo dengan konten asli | ⏸️ | Konten dari pemilik atau klien | Lihat daftar di bawah |
| 507 | Optimasi server | ⏸️ | Pemilik di hPanel | Pilih PHP 8.3 dan aktifkan OPcache. Aturan `.htaccess` sudah jalan di LiteSpeed (brotli aktif) |
| 505 | Fitur baru | ❌ | Belum diisi | Tentukan kalau ada kebutuhan baru |
| 508 | Cache data halaman | ❌ | Tidak perlu saat ini | Ukur ulang bila database terpisah dari server web |

PBI-518 (ekspor statis untuk Vercel) dibatalkan. Kodenya masih ada di commit `f7af7c5` dan tidak dihitung dalam 66 PBI.

### Konten yang masih dibutuhkan (KD-2)

- [x] Gambar pengganti video untuk HP (`hero.poster`). Diisi 9 Nov 2026 dengan frame pertama video (WebP 71 KB).
- [ ] Foto, video, dan link project
- [ ] Nama dan foto tim
- [ ] Logo klien
- [ ] Testimoni
- [ ] Harga paket
- [ ] Nomor WhatsApp. Nilai sekarang masih contoh, `6281234567890`
- [ ] Email, alamat, dan peta. Email default masih `info@domainname.com`
- [ ] Deskripsi SEO dan gambar pratinjau

### Keputusan yang masih terbuka

| Keputusan | Kenapa penting |
|---|---|
| Pengalihan alamat detail lama (KD-6) | Link lama `/pages/team/0` dan sejenisnya kini 404 |
| Apakah refactor digabung ke repo asal? | Perlu persetujuan tim |

---

## 6. Yang harus diperhatikan

### 6.1 Konten web resmi ✅ (ditangani 9 Nov 2026, PBI-520)

Asumsi K-1 ternyata keliru. dikelolain.com berjalan di Hostinger dengan versi lama, dan isi aslinya tersimpan di database MariaDB serta folder `storage/app/public/media`. Repo asal tidak punya commit setelah 16 Januari 2026, jadi perbedaannya hanya isi database, bukan kode.

Migration `2026_11_03_000014` kini menyalin isi tabel lama ke JSON, memindahkannya ke tabel baru, dan mengubah foto unggahan ke WebP sebelum tabel lama dihapus. Hasilnya sudah dibandingkan dengan salinan database asli: semua halaman sama, kecuali perubahan yang disengaja. Rinciannya ada di [status_backlog.md](status_backlog.md) (PBI-520), dan langkahnya di [DEPLOY.md](DEPLOY.md#pindah-dari-web-lama-isi-ikut-pindah).

Yang tetap berlaku:

1. Biarkan `public_html_lama`, folder `dikelolain-app`, dan database lama di Hostinger beberapa minggu sebagai jalan kembali.
2. Ganti password admin (KD-7).

### 6.2 Dependency

Dependency PHP sudah diperbarui di PBI-519. `composer audit` turun dari 55 catatan di 15 paket menjadi **0**, dan `npm audit` juga 0.

- Laravel kini 12.71.0. Laravel 13 sudah ada, tapi pindah versi mayor adalah pekerjaan terpisah.
- Paket produksi tetap berjalan di PHP 8.2. Hanya `laravel/pint` (paket dev) yang kini butuh PHP 8.3, jadi pengembangan lokal perlu PHP 8.3.
- Hasil audit berubah seiring waktu, jadi jalankan `composer audit` lagi tepat sebelum rilis.

### 6.3 Keamanan versi lama

- Celah PBI-104 sampai 106 sudah ditutup di repo ini, tapi hasilnya belum dirilis. Kalau web resmi masih memakai kode lama, celah itu mungkin masih ada.
- **Jangan membahas celah ini di tempat publik**, termasuk LinkedIn.
- Password admin lama tercatat di riwayat git, di seeder commit `02fa1fd`. Kalau password itu masih dipakai di web resmi, segera ganti.
- Password jangan pernah ditulis ke dokumen, chat, atau commit.

### 6.4 Izin dan lisensi

- **Tim:** proyek ini milik bersama. Bicarakan dengan Adzanil dan tim sebelum merilis, menggabungkan kode, atau memposting hasilnya.
- **Video hero:** `public/videos/hero.mp4` berasal dari demo template Rovex. Template sudah dibeli, tapi lisensi videonya belum dipastikan. Cek ini sebelum rilis, tanpa mengganti videonya kecuali diputuskan pemilik.
- **Kredit template:** kepala `custom.css` sengaja dibiarkan karena lisensi.
- **Konten demo:** foto dan teks template jangan dipakai sebagai portofolio asli.

### 6.5 Rilis dan server

- **Document root harus ke `public/`.** Kalau salah, `.env` bisa terbaca orang.
- **Jangan pernah `migrate:fresh` di server.** Perintah itu menghapus semua isi situs. Pakai `php artisan migrate --force`.
- **`db:seed` hanya untuk rilis pertama.**
- **Server butuh disk yang menetap.** Unggahan disimpan di `storage/app/public`, dan SQLite juga berupa file.
- **Git tidak membawa konten.** `database/*.sqlite`, `storage/app/public/*`, `public/build`, dan `.env` tidak ikut ke GitHub.
- **Cadangkan dua hal:** database dan `storage/app/public`. Saat ini belum ada cadangan otomatis.
- **Di balik proxy,** isi `TRUSTED_PROXIES`. Tanpa itu semua pengunjung terhitung satu IP, sehingga batas login dan form kontak terpakai bersama.
- **Nginx dan Caddy tidak membaca `.htaccess`,** jadi aturan kompresi dan cache perlu ditulis ulang.
- **Setelah mengubah `.env`,** jalankan `php artisan optimize`.

### 6.6 Belum pernah diuji

- Server Linux sungguhan
- Sertifikat HTTPS sungguhan
- Nginx dan PostgreSQL
- Tampilan di browser saat berjalan di Apache
- Mengedit teks lewat mode edit di Apache
- Trafik nyata dari pengunjung

### 6.7 Aturan saat mengubah kode

- **Setelah mengubah view apa pun,** jalankan `npm run build:site`. `SiteAssetsTest` akan gagal kalau lupa.
- **Tulis nama kelas CSS secara utuh.** Kelas yang dirangkai bisa terbuang oleh PurgeCSS. Kelas plugin yang dibuat saat jalan masuk daftar `runtimeClasses`.
- **Ikon baru** butuh `npm run build:site`, karena font ikon dipangkas.
- **Jangan menaruh CSS di view atau aset dari CDN.** `ViewHygieneTest` menolaknya. Google Fonts adalah satu-satunya pengecualian di layout publik.
- **Ikuti pola berlapis.** Entitas baru meniru Testimonial; checklist-nya ada di [ARCHITECTURE §7](ARCHITECTURE.md#7-pola-per-entitas-checklist).
- **Jalankan `php artisan test` sebelum setiap commit.**

### 6.8 Aturan Git

- **Jangan pernah `git push --all`.** Branch `arsip/*` tidak boleh naik ke GitHub. Pakai `git push origin main`.
- Commit atas nama pemilik repo, tanpa trailer atau penyebutan AI.
- `CLAUDE.md` dan `.claude/` hanya ada di lokal.
- Folder `linkedin/` dan `ss/` belum masuk git. Hindari `git add -A` kalau keduanya tidak ingin ikut.

### 6.9 Keterbatasan fitur

| Keterbatasan | Dampak |
|---|---|
| Tidak ada fitur lupa password | Admin yang lupa password harus direset lewat server |
| Satu tingkat admin, tanpa peran | Semua admin punya akses yang sama |
| Tidak ada draft, riwayat, atau undo di VCMS | Perubahan langsung tayang saat disimpan |
| Form kontak tidak mengirim email | Pesan harus dicek di panel admin |
| Upload SVG ditolak | Logo perlu PNG atau WebP (K-10) |
| Unggahan video tidak dikompres | Video perlu dikompres sebelum diunggah |
| Belum ada CI | Test hanya berjalan kalau dijalankan manual |
| Belum ada test browser di repo | Tampilan selama ini dicek manual |

### 6.10 Dokumen yang sudah usang

- **[README.md](README.md), bagian "Struktur singkat":** masih menyebut `ContentController`, `admin-vcms.js`, dan `SectionContentSeeder`, padahal semuanya sudah dihapus.
- **[ARCHITECTURE.md](ARCHITECTURE.md) §4.6:** indeks `projects` dan `page_list_items` belum sama dengan migration. Kolom `page_list_items` yang dipakai adalah `list_key`.
- **[ARCHITECTURE.md](ARCHITECTURE.md) §8:** menyebut unit test untuk service, padahal `tests/Unit` hanya berisi `RichTextTest`.
- **`.env` lokal:** `APP_LOCALE` masih `en`, sedangkan `.env.example` memakai `id`.

### 6.11 File yang hanya ada di komputer ini

| File | Isi | Catatan |
|---|---|---|
| `database/database.sqlite` | Database lokal | Masih berisi data demo |
| `storage/app/private/backups/` | Cadangan sebelum tabel lama dihapus, dan cadangan serta laporan import web live | Boleh dihapus kalau tidak ada teks lama yang perlu disalin |
| `storage/app/private/live/` | Dump database web live dan `media.zip` | Berisi hash password admin dan data sesi. Jangan dibagikan |
| `linkedin/` | Carousel VCMS (PPTX, PDF, gambar) | Materi postingan LinkedIn |
| `ss/` | Screenshot web resmi dan VCMS | Bahan carousel |
| `.env` | Konfigurasi lokal | Jangan di-commit |

---

## 7. Urutan kerja yang disarankan

1. **Sepakati arah dengan tim.** Tentukan apakah refactor ini akan dirilis, dan apakah kodenya digabung ke repo asal.
2. **Rapikan dokumen usang** di poin 6.10.
3. **Amankan web resmi.** Cadangkan database dan unggahan, lalu ganti password lama bila masih dipakai.
4. ~~Tentukan cara pindah konten dan tinjau ulang K-1.~~ Selesai 9 Nov 2026 (PBI-520).
5. **Lengkapi konten dan cek lisensi video.**
6. ~~Pilih hosting, coba, lalu rilis ke web resmi.~~ Selesai 9 Nov 2026 (PBI-504, Hostinger).
7. **Kerjakan KD-7 dan KD-5** setelah rilis, lalu PBI-507 di hPanel.

---

## 8. Perintah penting

```bash
php artisan test
```

```bash
npm run build:site
```

```bash
npm run build
```

```bash
php artisan migrate --force
```

```bash
git push origin main
```

**Server lokal:** pakai konfigurasi `dikelolain` di `.claude/launch.json`, lalu buka `http://127.0.0.1:8000`. Jangan pakai `localhost`, karena lambat di komputer ini.

**Akun admin:** dibuat oleh seeder dari `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` di `.env`.

---

## 9. Peta dokumen

| Dokumen | Isi |
|---|---|
| [status_backlog.md](status_backlog.md) | Status semua PBI, keputusan, dan riwayat |
| [kendala_backlog.md](kendala_backlog.md) | Hal yang menunggu pemilik |
| [ARCHITECTURE.md](ARCHITECTURE.md) | Arsitektur dan aturan teknis |
| [DEPLOY.md](DEPLOY.md) | Langkah rilis ke server |
| [README.md](README.md) | Cara menjalankan proyek |
