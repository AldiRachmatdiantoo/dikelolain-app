# Status Backlog Dikelolain

**Acuan arsitektur:** [ARCHITECTURE.md](ARCHITECTURE.md)
**Repo:** `AldiRachmatdiantoo/dikelolain-app` (private, branch `main`)
**Tanggal cek:** 3 November 2026

> **Semua pekerjaan yang belum dilakukan ada di file ini.** Mulai dari [Keputusan yang dibutuhkan](#keputusan-yang-dibutuhkan), lalu [Status semua PBI](#status-semua-pbi). Alasan perombakan ada di [Kondisi awal](#kondisi-awal-3-november-2026).

---

## Ringkasan

| Status | Jumlah | PBI |
|---|---|---|
| ✅ Selesai | 2 | **003** (ARCHITECTURE.md), **004** (status backlog) |
| ⏳ Sedang dikerjakan | 0 | – |
| ⏸️ Menunggu keputusan | 3 | **005** (K-1 s.d. K-4), **203** (K-4), **504** (K-3) |
| ❌ Belum | 42 | Semua PBI lain |

Total **47 PBI** dalam 6 epic:

| Epic | Isi | PBI | Selesai |
|---|---|---|---|
| 0 | Persiapan | 001–005 | 2/5 |
| 1 | Beres-beres tanpa risiko | 101–108 | 0/8 |
| 2 | Fondasi VCMS baru + FAQ | 201–210 | 0/10 |
| 3 | Migrasi entitas | 301–313 | 0/13 |
| 4 | Pembersihan akhir | 401–406 | 0/6 |
| 5 | Setelah rombak (fitur & rilis) | 501–505 | 0/5 |

**Urutan wajib:** Epic 0 → 1 → 2 → 3 → 4. Fitur baru (Epic 5) baru dikerjakan setelah Epic 4, kecuali yang tidak menyentuh model konten (lihat [PBI-501](#epic-5-setelah-rombak-fitur--rilis)).

---

## Keputusan yang dibutuhkan

| # | Keputusan | Pengaruh | Default bila tidak diputuskan | Status |
|---|---|---|---|---|
| K-1 | Apakah ada database online berisi **konten asli klien**? | Kalau ada: setiap entitas butuh command migrasi data + backup. Kalau tidak: cukup seeder baru dan `migrate:fresh --seed` | Dianggap **tidak ada**; semua isi sekarang data demo template | ❓ Belum dijawab |
| K-2 | Repository **wajib untuk semua entitas**, atau boleh dilewati untuk entitas sederhana? (misalnya ada syarat tugas kuliah) | Jumlah file per entitas | Wajib untuk semua (pola seragam) | ❓ Belum dijawab |
| K-3 | Database dan hosting saat rilis (SQLite / MySQL / PostgreSQL; Azure / lainnya) | Konfigurasi, tidak memengaruhi migration | SQLite untuk lokal, diputuskan di Epic 5 | ❓ Belum dijawab |
| K-4 | Mode edit di **halaman publik** dengan bilah admin (usulan di [ARCHITECTURE.md §6.1](ARCHITECTURE.md#61-mode-edit)), bukan lagi `/admin/pages/*` | Bentuk Epic 2 | Mengikuti usulan | ❓ Perlu konfirmasi |
| K-5 | Tetap edit inline (VCMS) | – | – | ✅ Diputuskan 3 Nov 2026: **ya**, inti produk |
| K-6 | Editor baru tanpa jQuery (Alpine.js + Vite); website publik tetap jQuery | – | – | ✅ Disepakati 3 Nov 2026 |

---

## Status semua PBI

Legenda: ✅ selesai · ⏳ sedang dikerjakan · ❌ belum · ⏸️ menunggu keputusan

### Epic 0: Persiapan

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 001 | Project jalan di lokal (`composer install`, `.env`, `migrate --seed`, `storage:link`, `npm install`) dan langkahnya ditulis di README | ❌ | README sekarang masih bawaan Laravel |
| 002 | Smoke test: semua halaman publik 200, semua halaman admin 200 saat login / redirect saat belum login | ❌ | Jaring pengaman sebelum mengubah apa pun |
| 003 | `ARCHITECTURE.md`: target arsitektur, model data, kontrak API, VCMS, konvensi | ✅ | 3 Nov 2026 |
| 004 | Status backlog (file ini) | ✅ | 3 Nov 2026 |
| 005 | Jawab keputusan K-1 sampai K-4 | ⏸️ | Lihat [Keputusan yang dibutuhkan](#keputusan-yang-dibutuhkan) |

### Epic 1: Beres-beres tanpa risiko

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 101 | Hapus controller dan model yang tidak dipakai: `ContentController` (root), `SectionController`, `MediaController`, `Admin/ProjectController`, model `Home`, `HomeController::getContent` | ❌ | Tidak dirujuk route mana pun |
| 102 | Hapus route ke view yang tidak ada: `/admin/portfolio/*`, `/admin/services/*`, `/admin/team/*`, `/admin/testimonials/*`, `/admin/settings/general`, `/admin/media-library` | ❌ | Semuanya error 500 kalau dibuka |
| 103 | Hapus view tak terpakai: `welcome.blade.php`, `admin/pages/service.blade.php` (kosong), `portfolio-experiment` (+ route), `layouts/sidebar.blade.php` bila tak dirujuk | ❌ | Cek dulu dengan grep |
| 104 | Upload: validasi mime sebenarnya (gambar/video), ekstensi dari mime, bukan dari nama file pengguna | ❌ | Sekarang file apa pun (termasuk `.php`) bisa masuk ke folder publik |
| 105 | Seeder admin: password dari `.env` (`ADMIN_EMAIL`, `ADMIN_PASSWORD`), bukan password lemah yang ditulis di kode | ❌ | |
| 106 | Respons error API tidak lagi menyertakan `$e->getMessage()` | ❌ | Bocor detail internal |
| 107 | `.env.example`: `APP_NAME=Dikelolain`, `APP_LOCALE=id`, variabel admin | ❌ | |
| 108 | Link project di seeder mengarah ke `/portfolio/single` yang tidak punya route (jatuh ke 404); `portfolio-single.blade.php` tidak dipakai | ❌ | Putuskan: buat route detail project atau hapus view |

### Epic 2: Fondasi VCMS baru + FAQ

Entitas pertama sekaligus pembuktian pola. Setelah epic ini, entitas lain tinggal mengikuti.

| PBI | Judul | Status | Catatan |
|---|---|---|---|
| 201 | Kerangka lapisan: folder `Services`, `Repositories/{Contracts,Eloquent}`, `RepositoryServiceProvider`, base handler error JSON | ❌ | [ARCHITECTURE §2–3](ARCHITECTURE.md#2-lapisan) |
| 202 | Aktifkan Vite untuk admin + pasang Alpine.js | ❌ | Vite sudah terpasang tapi belum dipakai |
| 203 | Mode edit: bilah admin di halaman publik, toggle di sesi, script editor hanya dimuat saat mode edit | ⏸️ | Menunggu K-4 |
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
| 501 | Fitur yang **tidak** menyentuh model konten (boleh dikerjakan kapan saja): form kontak disimpan ke DB / dikirim email, meta SEO + sitemap, optimasi gambar | ❌ | |
| 502 | Ganti konten demo template dengan konten asli (video Awaiken, link YouTube acak, award 2015–2020, logo klien contoh, nama tim) | ❌ | Butuh konten dari pemilik |
| 503 | Branding: `<html lang="id">`, meta author/description, judul halaman | ❌ | `lang="zxx"` dan author "Awaiken" masih dari template |
| 504 | Deploy | ⏸️ | Menunggu K-3 |
| 505 | Fitur baru (diisi kemudian) | ❌ | |

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
