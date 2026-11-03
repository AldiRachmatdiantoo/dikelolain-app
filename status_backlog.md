# Status Backlog Dikelolain

**Acuan arsitektur:** [ARCHITECTURE.md](ARCHITECTURE.md)
**Repo:** `AldiRachmatdiantoo/dikelolain-app` (private, branch `main`)
**Tanggal cek:** 3 November 2026

> **Semua pekerjaan yang belum dilakukan ada di file ini.** Error dan kendala yang butuh tindakan pemilik ada di [kendala_backlog.md](kendala_backlog.md). Mulai dari [Keputusan yang dibutuhkan](#keputusan-yang-dibutuhkan), lalu [Status semua PBI](#status-semua-pbi). Alasan perombakan ada di [Kondisi awal](#kondisi-awal-3-november-2026).

---

## Ringkasan

| Status | Jumlah | PBI |
|---|---|---|
| ✅ Selesai | 16 | **001–005** (seluruh Epic 0), **101–111** (seluruh Epic 1) |
| ⏳ Sedang dikerjakan | 0 | – |
| ⏸️ Menunggu keputusan | 2 | **504**, **507** (hosting, lihat [kendala_backlog.md](kendala_backlog.md) KD-1) |
| ❌ Belum | 35 | Epic 2–5 |

Total **53 PBI** dalam 6 epic:

| Epic | Isi | PBI | Selesai |
|---|---|---|---|
| 0 | Persiapan | 001–005 | 5/5 ✅ |
| 1 | Beres-beres tanpa risiko | 101–111 | 11/11 ✅ |
| 2 | Fondasi VCMS baru + FAQ | 201–210 | 0/10 |
| 3 | Migrasi entitas | 301–313 | 0/13 |
| 4 | Pembersihan akhir | 401–406 | 0/6 |
| 5 | Setelah rombak (fitur & rilis) | 501–508 | 0/8 |

**Urutan wajib:** Epic 0 → 1 → 2 → 3 → 4. Fitur baru (Epic 5) baru dikerjakan setelah Epic 4, kecuali yang tidak menyentuh model konten (lihat [PBI-501](#epic-5-setelah-rombak-fitur--rilis)).

---

## Keputusan yang dibutuhkan

| # | Keputusan | Pengaruh | Default bila tidak diputuskan | Status |
|---|---|---|---|---|
| K-1 | Apakah ada database online berisi **konten asli klien**? | Kalau ada: setiap entitas butuh command migrasi data + backup. Kalau tidak: cukup seeder baru dan `migrate:fresh --seed` | Dianggap **tidak ada**; semua isi sekarang data demo template | ✅ Default dipakai 3 Nov 2026 (eksekusi otomatis): tidak ada, cukup seeder baru |
| K-2 | Repository **wajib untuk semua entitas**, atau boleh dilewati untuk entitas sederhana? (misalnya ada syarat tugas kuliah) | Jumlah file per entitas | Wajib untuk semua (pola seragam) | ✅ Default dipakai 3 Nov 2026: wajib untuk semua |
| K-3 | Database dan hosting saat rilis (SQLite / MySQL / PostgreSQL; Azure / lainnya) | Konfigurasi, tidak memengaruhi migration | SQLite untuk lokal, diputuskan di Epic 5 | ❓ Belum dijawab, dicatat di [kendala_backlog.md](kendala_backlog.md) |
| K-4 | Mode edit di **halaman publik** dengan bilah admin (usulan di [ARCHITECTURE.md §6.1](ARCHITECTURE.md#61-mode-edit)), bukan lagi `/admin/pages/*` | Bentuk Epic 2 | Mengikuti usulan | ✅ Default dipakai 3 Nov 2026: mode edit di halaman publik |
| K-5 | Tetap edit inline (VCMS) | – | – | ✅ Diputuskan 3 Nov 2026: **ya**, inti produk |
| K-6 | Editor baru tanpa jQuery (Alpine.js + Vite); website publik tetap jQuery | – | – | ✅ Disepakati 3 Nov 2026 |
| K-7 | Index database: unique, foreign key, dan index gabungan untuk query daftar; tidak meng-index semua kolom | Aturan setiap migration | – | ✅ Disepakati 3 Nov 2026, aturan di [ARCHITECTURE §4.6](ARCHITECTURE.md#46-index) |
| K-8 | **Tanpa Redis.** Kalau perlu cache, pakai facade `Cache` dengan driver `database`/`file` dan key eksplisit (tanpa `Cache::tags()`); pindah ke Redis nanti cukup ubah `CACHE_STORE` | Infrastruktur rilis | – | ✅ Disepakati 3 Nov 2026, aturan di [ARCHITECTURE §10](ARCHITECTURE.md#10-performa--cache) |
| K-9 | Halaman `/portfolio-experiment` (slider horizontal, ditautkan dari banner "Try Our New Horizontal Slider!" di Portfolio): pertahankan sebagai alternatif, jadikan tampilan utama, atau hapus? | PBI-309 (satu atau dua view portofolio) | Dipertahankan apa adanya | ✅ Default dipakai 3 Nov 2026: dipertahankan, ikut memakai data baru |
| K-10 | Upload SVG ditolak (SVG bisa berisi script). Logo/ikon SVG bawaan template di `public/images` tetap tampil | Admin harus upload logo sebagai PNG/WebP | – | ✅ Diterapkan 3 Nov 2026 (PBI-104); bisa ditinjau ulang bila logo klien wajib SVG (perlu sanitasi SVG) |

---

## Status semua PBI

Legenda: ✅ selesai · ⏳ sedang dikerjakan · ❌ belum · ⏸️ menunggu keputusan

### Epic 0: Persiapan

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 001 | Project jalan di lokal (`composer install`, `.env`, `migrate --seed`, `storage:link`) dan langkahnya ditulis di README | ✅ | 3 Nov 2026, lihat [Hasil PBI-001](#hasil-pbi-001-3-november-2026). npm belum diperlukan (Vite hanya dirujuk `welcome.blade.php`) |
| 002 | Smoke test: semua halaman publik 200, semua halaman admin 200 saat login / redirect saat belum login | ✅ | 3 Nov 2026: `tests/Feature/SmokeTest.php`, **47 test hijau** (±8 dtk), lihat [Hasil PBI-002](#hasil-pbi-002-3-november-2026). Jalankan: `php artisan test` |
| 003 | `ARCHITECTURE.md`: target arsitektur, model data, kontrak API, VCMS, konvensi | ✅ | 3 Nov 2026 |
| 004 | Status backlog (file ini) | ✅ | 3 Nov 2026 |
| 005 | Jawab keputusan K-1 sampai K-4 dan K-9 | ✅ | 3 Nov 2026: K-1, K-2, K-4, K-9 memakai default atas instruksi eksekusi otomatis; K-3 (hosting) dicatat di [kendala_backlog.md](kendala_backlog.md) |

### Epic 1: Beres-beres tanpa risiko

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 101 | Hapus controller dan model yang tidak dipakai | ✅ | 3 Nov 2026: `ContentController` (root), `SectionController`, `MediaController`, `Admin/ProjectController`, model `Home`, `HomeController::getContent` dihapus. Peringatan PSR-4 saat `composer install` ikut hilang |
| 102 | Hapus route ke view yang tidak ada | ✅ | 3 Nov 2026: `/admin/portfolio/*`, `/admin/services/*`, `/admin/team/*`, `/admin/testimonials/*`, `/admin/settings/general`, `/admin/media-library` dihapus (sebelumnya 500, sekarang 404). Sisa 40 route |
| 103 | Hapus view tak terpakai | ✅ | 3 Nov 2026: `welcome.blade.php`, `admin/pages/service.blade.php` (kosong) dihapus. **Tidak dihapus:** `portfolio-experiment` (ditautkan dari halaman Portfolio, lihat K-9) dan `layouts/sidebar.blade.php` (dipakai FAQs dan detail layanan) |
| 104 | Upload: validasi berdasarkan isi file | ✅ | 3 Nov 2026: rule `App\Rules\MediaUpload` di ketiga endpoint upload (termasuk `batchUpdate` yang sebelumnya tanpa validasi). Hanya JPG/PNG/GIF/WebP (maks 10 MB) dan MP4/WebM (maks 50 MB), dinilai dari isi file; SVG ditolak (K-10); ekstensi file tersimpan diambil dari isi. Editor menampilkan alasan penolakan. Test: `MediaUploadTest` (6) |
| 105 | Seeder admin dari `.env` | ✅ | 3 Nov 2026: `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` lewat `config('app.admin')`; tanpa `ADMIN_PASSWORD` seeder membuat password acak dan menampilkannya sekali. Test: `AdminSeederTest` (2). **Database lokal yang sudah ada tidak berubah**; jalankan `migrate:fresh --seed` untuk memakai akun baru |
| 106 | Respons error API tidak lagi menyertakan `$e->getMessage()` | ✅ | 3 Nov 2026: 6 respons di `Admin/ContentController` diganti pesan umum berbahasa Indonesia; detail dicatat ke log (`report($e)` ditambahkan di 3 tempat yang belum mencatat) |
| 107 | `.env.example` | ✅ | 3 Nov 2026: `APP_NAME=Dikelolain`, `APP_URL=http://localhost:8000`, `APP_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`, variabel `ADMIN_*` |
| 108 | `portfolio-single.blade.php` tidak terjangkau | ✅ | 3 Nov 2026: view dihapus. Klik project membuka modal; `data-link` tidak pernah dipakai modal, jadi `/portfolio/single` di seeder tidak pernah dikunjungi. Halaman detail project (bila diinginkan) dibuat di PBI-309 |
| 109 | Halaman 404 dikirim dengan status 200 | ✅ | 3 Nov 2026: `Route::fallback` kini mengirim status 404; smoke test memeriksa status |
| 110 | Error JS `magiccursor.js:104` | ✅ | 3 Nov 2026: terjadi saat halaman dimuat dengan lebar viewport 0 (tab/iframe tersembunyi): `-window.innerWidth` bernilai 0 sehingga jatuh ke `this.pos` yang belum ada. Diberi posisi awal |
| 111 | Fungsi global `getContent()` dideklarasikan tanpa `function_exists` di 6 view → fatal "Cannot redeclare" bila dua halaman dirender dalam satu proses PHP (test, Octane) | ✅ | 3 Nov 2026, ditemukan oleh PBI-002. Dibungkus `function_exists` seperti view lain; isi semua versi identik secara perilaku. Fungsi ini tetap dihapus di PBI-404 |

### Epic 2: Fondasi VCMS baru + FAQ

Entitas pertama sekaligus pembuktian pola. Setelah epic ini, entitas lain tinggal mengikuti.

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 201 | Kerangka lapisan: folder `Services`, `Repositories/{Contracts,Eloquent}`, `RepositoryServiceProvider`, base handler error JSON | ❌ | [ARCHITECTURE §2–3](ARCHITECTURE.md#2-lapisan) |
| 202 | Aktifkan Vite untuk admin + pasang Alpine.js | ❌ | Vite sudah terpasang tapi belum dipakai |
| 203 | Mode edit: bilah admin di halaman publik, toggle di sesi, script editor hanya dimuat saat mode edit | ❌ | K-4 diputuskan |
| 204 | Komponen Blade `<x-vcms.text>`, `<x-vcms.list>`, `<x-vcms.item>`, `<x-vcms.image>` | ❌ | |
| 205 | Editor JS generik: modal, tipe field (text, textarea, rich_text, select, image, url, number, toggle), tambah, ubah, hapus, urutkan | ❌ | Gaya modal mengikuti `admin-vcms.css` |
| 206 | `ImageUploadService` | ❌ | Menggantikan `uploadMedia` lama |
| 207 | Tabel `page_settings` + `SettingService` + endpoint `settings/{key}` + sanitasi `rich_text` | ❌ | Dibutuhkan untuk header halaman FAQ |
| 208 | **FAQ**: tabel `faqs`, model, repository, service, request, controller API, resource, definisi field, seeder | ❌ | Kategori: general, technical, project, support |
| 209 | Halaman `/faqs` memakai data baru + komponen VCMS; header FAQ pindah ke `page_settings` | ❌ | |
| 210 | Test FAQ + hapus key FAQ lama dari `admin-vcms.js`, `batchUpdate`, `admin/pages/faqs.blade.php` | ❌ | Definition of done: [ARCHITECTURE §7](ARCHITECTURE.md#7-pola-per-entitas-checklist) |

### Epic 3: Migrasi entitas

Urutan dari yang paling sederhana. Setiap PBI mengikuti checklist [ARCHITECTURE §7](ARCHITECTURE.md#7-pola-per-entitas-checklist).

| PBI | Entitas | Tabel | Section lama | Halaman | Status |
|---|---|---|---|---|---|
| 301 | Testimonial | `testimonials` | `testimonials` (testimonial-list) | Home, Testimonials | ❌ |
| 302 | Logo klien | `client_logos` | `trusted-clients`, client-logos, client-logo-slider | Home, Testimonials | ❌ |
| 303 | Tools | `tools` | `creative-tools` (tools-list) | Home | ❌ |
| 304 | Langkah proses | `process_steps` | `how-it-works` (process-list) | Home | ❌ |
| 305 | Pencapaian | `achievements` | `achievements` (achievement-list) | Home | ❌ |
| 306 | Counter | `counters` | `cta` (counter-list) | Home, About | ❌ |
| 307 | Tim | `team_members`, `team_member_skills`, `team_member_expertises` | `our-team` (team-list) | Home, About, Team, Team detail | ❌ |
| 308 | Layanan | `services`, `service_benefits`, `service_process_steps`, `service_faqs` | `services` (service-list, service-detail) | Home, Services, Service detail | ❌ |
| 309 | Project / portofolio | `projects` | `our-work` (project-list, portfolio-item) | Home, Portfolio | ❌ |
| 310 | Paket harga | `pricing_plans`, `pricing_features` | `pricing` (pricing-section) | Pricing | ❌ |
| 311 | Daftar halaman | `page_list_items` | `about-us` (list item), `vision-mission`, pricing-benefits | About, Pricing | ❌ |
| 312 | Nilai tunggal sisanya | `page_settings` | hero, header semua halaman, judul section, CTA, `contact`, `contact-map`, `footer`, analytics | Semua | ❌ |
| 313 | Controller per halaman + page service; ganti semua route closure | – | – | Semua | ❌ |

### Epic 4: Pembersihan akhir

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 401 | Hapus `Admin/ContentController` (`batchUpdate` dkk) dan route `/admin/api/content/*` | ❌ | Setelah semua entitas pindah |
| 402 | Hapus `public/js/admin-vcms.js` dan CSS yang tidak dipakai lagi | ❌ | |
| 403 | Hapus semua `resources/views/admin/pages/*` kecuali analytics dan profil | ❌ | |
| 404 | Hapus semua fungsi `getContent()` / `get*Content()` di view | ❌ | |
| 405 | Migration drop `section_contents`, `sections`, `media`; hapus model dan `SectionContentSeeder` | ❌ | |
| 406 | Test lengkap hijau + cek manual semua halaman dalam mode edit dan mode publik | ❌ | |

### Epic 5: Setelah rombak (fitur & rilis)

Belum dirinci. Diisi setelah Epic 4.

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 501 | Fitur yang **tidak** menyentuh model konten (boleh dikerjakan kapan saja): form kontak disimpan ke DB / dikirim email, meta SEO + sitemap | ❌ | |
| 506 | Gambar & video: kompres 103 gambar `public/images` ke WebP, `loading="lazy"` di bawah layar, video hero disimpan sendiri (sekarang dari server demo Awaiken) | ❌ | Dampak kecepatan terbesar; boleh dikerjakan kapan saja |
| 507 | Optimasi rilis: `php artisan optimize`, OPcache di server | ⏸️ | Menunggu K-3 |
| 508 | Cache data per halaman (`Cache::rememberForever('page.home')`, dihapus saat admin menyimpan) | ❌ | Hanya bila PBI-313 + 506 + 507 belum cukup; tanpa Redis (K-8) |
| 502 | Ganti konten demo template dengan konten asli (video Awaiken, link YouTube acak, award 2015–2020, logo klien contoh, nama tim) | ❌ | Butuh konten dari pemilik |
| 503 | Branding: `<html lang="id">`, meta author/description, judul halaman | ❌ | `lang="zxx"` dan author "Awaiken" masih dari template |
| 504 | Deploy | ⏸️ | Menunggu K-3 |
| 505 | Fitur baru (diisi kemudian) | ❌ | |

---

## Hasil PBI-001 (3 November 2026)

Dijalankan di Windows dengan Laragon: PHP 8.3.29, Composer 2.9.3, SQLite. `composer install`, `migrate --seed` (7 migration + `SectionContentSeeder`), dan `storage:link` berhasil.

**Halaman publik** (dicek di browser, `php artisan serve`):

| Halaman | Status |
|---|---|
| `/`, `/about`, `/services`, `/portfolio`, `/portfolio-experiment`, `/pricing`, `/faqs`, `/team`, `/testimonials`, `/contact` | 200 |
| `/pages/service/01`, `/pages/team/0` | 200 |
| URL yang tidak ada (`/portfolio/single`, `/halaman-tidak-ada`) | 200 berisi halaman 404 → **PBI-109** |

**Admin** (login dengan akun dari seeder):

| Halaman | Status |
|---|---|
| `/admin/dashboard`, `/admin/pages/{analytics,home,about,services,portfolio,team,pricing,contact,faqs,testimonials}`, `/admin/pages/service/01`, `/admin/profile-detail`, `/admin/reset-password` | 200 |
| `/admin/api/content/hero`, `/admin/api/content/media-list` | 200 |
| `/admin/dashboard` tanpa login | redirect ke login |
| `/admin/portfolio`, `/admin/services`, `/admin/team`, `/admin/testimonials`, `/admin/settings/general`, `/admin/media-library` | **500** (view tidak ada) → PBI-102 |

**Temuan lain:** error JS `magiccursor.js` di semua halaman (dimuat oleh layout publik dan admin) → **PBI-110**; peringatan PSR-4 `SectionController` saat `composer install` → dicatat di PBI-101.

---

## Hasil PBI-002 (3 November 2026)

`tests/Feature/SmokeTest.php` menggantikan `tests/Feature/ExampleTest.php` bawaan Laravel (yang gagal karena tidak menyiapkan database). Setiap test memakai SQLite in-memory baru + `DatabaseSeeder` (data demo).

| Kelompok | Jumlah | Isi |
|---|---|---|
| Halaman publik | 13 | 10 halaman utama, detail layanan, detail tim, login admin → 200 |
| Halaman 404 | 1 | URL tak dikenal memakai view `pages.404` (status belum diperiksa; menunggu PBI-109) |
| Admin tanpa login | 14 | Semua halaman admin → redirect ke `/admin/login` |
| Admin dengan login | 14 | Dashboard, analytics, 11 editor halaman, profil, ganti password → 200 |
| API konten | 2 | `GET /admin/api/content/hero` dan `media-list` → 200 |
| Login | 2 | Login + logout berhasil; password salah ditolak |

Total 46 test di `SmokeTest` + 1 `tests/Unit/ExampleTest` = **47 hijau, 73 assertion, ±8 detik**.

**Ditemukan saat menulis test:** PBI-111 (fatal "Cannot redeclare getContent()"), langsung diperbaiki. Enam route admin yang mati (PBI-102) sengaja tidak dimasukkan karena memang error 500; route itu akan dihapus.

**Aturan:** `php artisan test` wajib hijau sebelum setiap commit perombakan. Setiap halaman atau endpoint baru ditambahkan ke test.

---

## Hasil Epic 1 (3 November 2026)

Semua 11 PBI selesai. `php artisan test`: **55 hijau, 93 assertion, ±13 detik** (`SmokeTest` 46, `MediaUploadTest` 6, `AdminSeederTest` 2, `Unit\ExampleTest` 1).

Dicek di browser setelah perubahan: URL tak dikenal → 404 dengan halaman 404; route admin yang dihapus → 404 (sebelumnya 500); `/portfolio-experiment` tetap 200; kursor `magiccursor` terpasang tanpa error.

**Perubahan test:** `SmokeTest` kini menjalankan seeder di `setUp()` setiap test. Dengan SQLite in-memory, `$seed = true` hanya men-seed saat migrasi pertama dalam satu proses, sehingga hasilnya bergantung urutan test begitu ada kelas test lain.

**Yang perlu kamu lakukan di lokal (opsional):** isi `ADMIN_EMAIL`/`ADMIN_PASSWORD` di `.env`, lalu `php artisan migrate:fresh --seed`. Tanpa itu, database lokal tetap memakai akun admin lama.

---

## Kondisi awal (3 November 2026)

Ringkasan hasil membaca kode di commit `02fa1fd` (isi sama dengan branch `back-end` repo asal, 16 Jan 2026).

### Stack
- Laravel 12, PHP 8.2, SQLite (default `.env.example`).
- Tampilan dari template HTML Awaiken: Bootstrap, jQuery 3.7, GSAP, Swiper, isotope, magnific-popup, dll, langsung dari `public/`. Vite dan Tailwind terpasang tapi tidak dipakai.

### Halaman
- **Publik:** Home, About, Services (+ `/pages/service/{id}`), Portfolio, Pricing, FAQs, Team (+ `/pages/team/{index}`), Testimonials, Contact, 404. Form kontak membuka WhatsApp (nomor dari admin), tidak disimpan.
- **Admin:** login, profil (nama, email, password), Analytics (iframe Looker Studio), dan VCMS editor untuk 10 halaman.

### Masalah utama

| # | Masalah | Lokasi | Ditangani di |
|---|---|---|---|
| 1 | **Semua konten di satu tabel key–value.** Struktur daftar disembunyikan di nama key (`faq-list-1_faq_123_question`, `pricing-plans_plan_basic_feature_1`). Baca pakai regex/`LIKE`, nomor urut digeser manual, beberapa daftar "hapus semua lalu tulis ulang" | `section_contents` | Epic 2–3 |
| 2 | **`batchUpdate` ~700 baris** dengan cabang `delete_*` per jenis data | `app/Http/Controllers/Admin/ContentController.php` | Epic 2–4 |
| 3 | **Setiap halaman dibuat dua kali**: versi publik dan versi admin | `resources/views/pages/*` vs `resources/views/admin/pages/*` | Epic 2–4 |
| 4 | **`admin-vcms.js` 6.000 baris**: satu objek, 103 method, ±50 pembuat form HTML manual, `switch` 52 kasus, `saveChanges` 575 baris, 13 `setTimeout`, 34 `innerHTML`/`.html()` | `public/js/admin-vcms.js` | Epic 2–4 |
| 5 | **Logika di view**: blok `@php` dengan regex, fungsi global `getContent()` disalin di ±15 file | `resources/views/**` | Epic 2–4 |
| 6 | **Semua route berupa closure** yang memuat semua section + konten + media di setiap halaman | `routes/web.php` | PBI-313 |
| 7 | Kode mati dan route ke view yang tidak ada | lihat PBI-101–103 | Epic 1 |
| 8 | Upload tanpa validasi tipe file, password admin seeder lemah, pesan exception bocor ke klien | lihat PBI-104–106 | Epic 1 |
| 9 | Tidak ada test selain contoh bawaan | `tests/` | PBI-002, setiap entitas |

### Yang sudah baik dan dipertahankan
- Laravel sebagai framework, auth admin dengan middleware `guest`/`auth`, validasi ganti password dan profil.
- Konsep VCMS (edit inline) dan tampilan website publik.
- Gaya modal editor (`admin-vcms.css`) sebagai acuan tampilan editor baru.

---

## Riwayat

| Tanggal | Perubahan |
|---|---|
| 3 Nov 2026 | Repo dipisah dari `abahdzanil/dikelolain-app`: isi branch `back-end` dijadikan satu commit di `main` (`02fa1fd`); riwayat lama disimpan lokal di branch `arsip/*` (tidak di-push) |
| 3 Nov 2026 | Audit kode, `ARCHITECTURE.md` dan status backlog dibuat (PBI-003, PBI-004) |
| 3 Nov 2026 | Keputusan index (K-7) dan tanpa Redis (K-8) dicatat; PBI-506–508 (performa) ditambahkan |
| 3 Nov 2026 | PBI-001 selesai: project jalan di lokal, README ditulis ulang; temuan baru PBI-109, PBI-110 |
| 3 Nov 2026 | PBI-002 selesai: smoke test 47 hijau; PBI-111 ditemukan dan diperbaiki |
| 3 Nov 2026 | Epic 1 selesai (PBI-101–110): kode mati dihapus, upload aman, admin dari `.env`, error tidak bocor, 404 benar, `magiccursor` diperbaiki; keputusan baru K-9, K-10 |
