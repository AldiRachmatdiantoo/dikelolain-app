# Arsitektur Dikelolain (target)

**Status pekerjaan:** [status_backlog.md](status_backlog.md)
**Dibuat:** 3 November 2026

Dokumen ini menjelaskan **bentuk akhir** yang dituju dan **aturan main** yang diikuti setiap perubahan. Kondisi kode saat ini dan alasan perombakan ada di [status_backlog.md](status_backlog.md#kondisi-awal-3-november-2026).

---

## 1. Prinsip

1. **Edit inline (VCMS) tetap menjadi inti produk.** Admin mengedit konten langsung di tampilan halaman, bukan lewat form terpisah.
2. **Satu view, dua mode.** Halaman publik dan halaman edit memakai file Blade yang sama. Tidak ada salinan halaman khusus admin.
3. **Data punya bentuk.** Setiap jenis konten berbentuk daftar punya tabel sendiri. Key–value hanya untuk nilai tunggal (`page_settings`).
4. **Berlapis, satu arah.** Route → Controller → Service → Repository → Model. Tidak ada lapisan yang melompati lapisan di bawahnya.
5. **Dirombak per entitas.** Website tetap jalan di setiap langkah. Kode lama untuk satu entitas dihapus begitu entitas itu selesai dipindah.

---

## 2. Lapisan

```
HTTP request
   │
   ▼
Route ──► Controller ──► FormRequest (validasi)
              │
              ▼
           Service  ──►  Repository (interface)  ──►  Eloquent Model  ──►  DB
              │
              ▼
   View / API Resource  ──►  HTML / JSON
```

| Lapisan | Tanggung jawab | Tidak boleh |
|---|---|---|
| **Route** (`routes/web.php`) | Memetakan URL ke controller | Berisi closure dengan logika atau query |
| **Controller** | Menerima request, memanggil satu method service, mengembalikan view/JSON | Query ke model, logika bisnis |
| **FormRequest** | Validasi dan otorisasi input | – |
| **Service** | Aturan bisnis: urutan, aktif/nonaktif, upload gambar, transaksi | Tahu soal `Request`/`Response` HTTP |
| **Repository** | Satu-satunya tempat yang tahu cara mengambil/menyimpan data | Aturan bisnis |
| **Model** | Relasi, cast, scope (`ordered`, `active`) | Logika tampilan |
| **View (Blade)** | Menampilkan data yang sudah siap | `@php` berisi query, regex, atau fungsi global |
| **API Resource** | Bentuk JSON keluaran | – |

**Repository:** dipakai untuk semua entitas supaya polanya seragam. Interface di `Repositories/Contracts`, implementasi Eloquent di `Repositories/Eloquent`, binding di `RepositoryServiceProvider`. *(Masih bisa dilonggarkan untuk entitas sederhana; lihat keputusan K-2 di backlog.)*

**Halaman gabungan:** halaman yang memakai banyak entitas (Home, About, dll) punya page service sendiri, misalnya `HomePageService::data()`, yang mengumpulkan semua data untuk halaman itu. Controller halaman cukup memanggil satu method.

---

## 3. Struktur folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Pages/                 HomePageController, FaqPageController, ...
│   │   └── Admin/
│   │       ├── Api/               FaqController, TestimonialController, SettingController, ...
│   │       ├── AuthController.php
│   │       └── ProfileController.php
│   ├── Requests/Admin/            StoreFaqRequest, UpdateFaqRequest, MoveRequest, UpdateSettingRequest, ...
│   └── Resources/                 FaqResource, ...
├── Services/
│   ├── Pages/                     HomePageService, AboutPageService, ...
│   ├── FaqService.php
│   ├── SettingService.php         nilai tunggal (page_settings)
│   └── ImageUploadService.php
├── Repositories/
│   ├── Contracts/                 FaqRepositoryInterface, PageSettingRepositoryInterface, ...
│   └── Eloquent/                  EloquentFaqRepository, ...
├── Models/                        Faq, Testimonial, TeamMember, ..., PageSetting
├── Rules/MediaUpload.php          validasi upload dari isi file
├── Support/                       RichText (sanitasi), Media (path → URL)
├── Vcms/
│   ├── EditMode.php               status mode edit di sesi
│   ├── FieldSet.php               mengisi definisi field dengan nilai
│   └── Fields/                    definisi field yang bisa diedit per entitas (FaqFields, ...)
└── Providers/RepositoryServiceProvider.php

config/page_settings.php           daftar semua nilai tunggal: tipe, label, default

resources/
├── views/
│   ├── pages/                     satu file per halaman publik (dipakai juga untuk mode edit)
│   ├── components/vcms/           komponen anonim: settings, item, add
│   ├── vcms/admin-bar.blade.php   bilah admin + modal editor
│   └── admin/                     hanya login, dashboard, analytics, profil
├── css/vcms.css                   gaya editor
└── js/vcms/editor.js              editor generik (Alpine.js); Vite hanya membundel editor ini
```

---

## 4. Model data

### 4.1 Daftar → tabel sendiri

Semua tabel daftar punya `id`, `sort_order` (int), `is_active` (bool, default true), `timestamps`.

| Tabel | Kolom utama | Dipakai di |
|---|---|---|
| `faqs` | `category` (general/technical/project/support), `question`, `answer` | FAQs, About (4 teratas) |
| `testimonials` | `name`, `role`, `company`, `quote`, `photo_path`, `rating` | Home, Testimonials |
| `client_logos` | `name`, `logo_path` | Home, Testimonials |
| `tools` | `name`, `category`, `icon_path` | Home (creative tools) |
| `process_steps` | `title`, `description` | Home (how it works) |
| `achievements` | `period`, `title`, `description` | Home |
| `counters` | `number`, `suffix`, `label` | Home, CTA |
| `team_members` | `slug`, `name`, `role`, `photo_path`, `bio`, `email`, `phone`, link sosial | Home, About, Team |
| `team_member_skills` | `team_member_id`, `name`, `percent` | Team detail |
| `team_member_expertises` | `team_member_id`, `name` | Team detail |
| `services` | `slug`, `number`, `name`, `summary`, `description`, `image_path` | Home, Services |
| `service_benefits` | `service_id`, `text` | Service detail |
| `service_process_steps` | `service_id`, `title`, `description` | Service detail |
| `service_faqs` | `service_id`, `question`, `answer` | Service detail |
| `projects` | `slug`, `title`, `category`, `media_type` (image/video), `media_url`, `thumbnail_path`, `description`, `client`, `link` | Home (our work), Portfolio |
| `pricing_plans` | `name`, `price`, `period`, `description`, `is_featured`, `button_text`, `button_link` | Pricing |
| `pricing_features` | `pricing_plan_id`, `text` | Pricing |
| `page_list_items` | `page`, `group` (about_list, vision, mission, pricing_benefits), `text`, `icon_path` | About, Pricing |

> Kolom di atas adalah **rancangan**. Saat mengerjakan tiap entitas, kolomnya dicocokkan dulu dengan field yang benar-benar dipakai di seeder dan view lama.

### 4.2 Nilai tunggal → `page_settings`

| Kolom | Isi |
|---|---|
| `key` | unik, berpola `halaman.bagian.field`, contoh `home.hero.title`, `contact.whatsapp_number`, `analytics.looker_url` |
| `value` | isi; `null` berarti memakai default |

Tipe, label, dan default **tidak** disimpan di database, tapi di `config/page_settings.php`:

```php
'faqs.cta.email' => ['type' => 'email', 'label' => 'Email kontak', 'default' => 'info@domainname.com'],
```

- Tabel hanya berisi nilai yang pernah diubah admin, jadi tidak perlu seeder. Menambah setting baru = satu baris di config.
- Tipe: `text`, `textarea`, `rich_text`, `url`, `email`, `image`, `video`.
- Mengosongkan nilai mengembalikannya ke default.
- Key memakai titik, jadi dicari di array config (`config('page_settings')[$key]`), bukan lewat dot notation.
- `SettingService::get()` memuat semua nilai sekali per request.

Dipakai untuk: judul dan deskripsi hero, header setiap halaman, judul section, CTA, info kontak, peta, footer, URL Looker Studio.

`rich_text` hanya menerima tag yang diizinkan (`span`, `strong`, `em`, `br`) tanpa atribut, dibersihkan saat disimpan dan saat ditampilkan (`App\Support\RichText`). Semua tipe lain ditampilkan dengan `{{ }}` (di-escape).

### 4.3 Gambar & video

- Disimpan di disk `public`, folder per entitas: `storage/app/public/uploads/{folder}/{nama-acak}.{ext}`; perlu `php artisan storage:link`.
- Kolom menyimpan path yang siap untuk `asset()`: `storage/uploads/faqs/abc123.webp`. Gambar bawaan template tetap `images/...`. URL dibuat oleh `App\Support\Media::url()` (nilai yang sudah berupa `http(s)://` dipakai apa adanya).
- Validasi di rule `App\Rules\MediaUpload`: tipe dideteksi dari **isi file**, JPG/PNG/GIF/WebP maks. 10 MB, MP4/WebM maks. 50 MB, SVG ditolak (K-10).
- `ImageUploadService` memberi nama acak dengan ekstensi dari isi file, dan hanya menghapus file di bawah `storage/uploads/` saat diganti.
- Video boleh berupa URL atau file MP4/WebM.
- Tabel `media` dan media library tidak dipakai lagi.

### 4.4 Tabel yang tetap

`users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.

### 4.5 Tabel yang dihapus di akhir

`sections`, `section_contents`, `media`.

### 4.6 Index

Index di project ini terutama untuk **keabsahan data** (unik, relasi), baru kemudian kecepatan. Datanya kecil, jadi jangan meng-index kolom yang tidak dipakai query.

| Jenis | Kolom | Cara di migration |
|---|---|---|
| Primary key | `id` semua tabel | `$table->id()` |
| Unique | `page_settings.key`, `services.slug`, `projects.slug`, `team_members.slug` | `->unique()` |
| Foreign key | kolom `*_id` di tabel anak | `->foreignId('service_id')->constrained()->cascadeOnDelete()` **plus** `->index()` eksplisit, karena SQLite dan PostgreSQL tidak membuat index FK otomatis |
| Daftar | `(is_active, sort_order)` di setiap tabel daftar | `$table->index(['is_active', 'sort_order'])` |
| Daftar per kategori/grup | `faqs (category, is_active, sort_order)`, `projects (category, is_active, sort_order)`, `page_list_items (page, group, sort_order)` | index gabungan |
| Tabel anak | `(parent_id, sort_order)` | index gabungan (menggantikan index FK tunggal) |

Query tidak boleh memakai `LIKE '%...'` dengan wildcard di depan, karena index tidak akan terpakai.

---

## 5. Kontrak API admin

Semua endpoint di bawah `/admin/api`, middleware `web` + `auth` (sesi login + CSRF token). Tidak memakai Sanctum/token karena admin dan website satu domain.

### 5.1 Pola per entitas

| Aksi | Endpoint | Body | Respons sukses |
|---|---|---|---|
| Daftar | `GET /admin/api/{resource}` | – | `200 { "data": [...] }` |
| Tambah | `POST /admin/api/{resource}` | field entitas (multipart bila ada gambar) | `201 { "data": {...} }` |
| Ubah sebagian | `PATCH /admin/api/{resource}/{id}` | hanya field yang diubah | `200 { "data": {...} }` |
| Hapus | `DELETE /admin/api/{resource}/{id}` | – | `204` |
| Geser urutan | `POST /admin/api/{resource}/{id}/move` | `{ "direction": "up" }` atau `"down"` | `204` |

Geser bertukar posisi dengan item tetangga dalam kelompok yang sama (misalnya FAQ dalam satu kategori). Item di ujung tidak bergeser dan tetap `204`.

Sub-daftar memakai rute bersarang, misalnya `POST /admin/api/services/{service}/benefits`, `PATCH /admin/api/pricing-plans/{plan}/features/{feature}`.

Upload gambar dikirim sebagai field file (`image`, `photo`, `logo`, ...) pada `POST`/`PATCH` milik entitasnya. Karena browser tidak mengirim file lewat `PATCH`, kirim `POST` dengan `_method=PATCH`.

### 5.2 Nilai tunggal

| Aksi | Endpoint | Body |
|---|---|---|
| Ambil | `GET /admin/api/settings/{key}` | – |
| Ubah | `PATCH /admin/api/settings/{key}` | `{ "value": "..." }` atau file `file` |

Keduanya membalas `200 { "data": { "name", "label", "type", "value", "url"? } }`; key yang tidak ada di registry → `404`. Aturan validasi mengikuti tipe setting.

### 5.3 Error

| Kondisi | Respons |
|---|---|
| Validasi gagal | `422 { "message": "...", "errors": { "field": ["..."] } }` (bawaan Laravel) |
| Belum login | `401` |
| Data tidak ada | `404` |
| Error server | `500 { "message": "Terjadi kesalahan, coba lagi." }`; detail hanya di log, tidak pernah `$e->getMessage()` ke klien |

---

## 6. VCMS (editor inline)

### 6.1 Mode edit

- Admin yang login melihat **bilah admin** melayang di bawah setiap halaman publik: "Edit halaman ini", Dashboard, Keluar.
- `GET /admin/edit-mode?to=/faqs` menyalakan mode edit lalu membuka halaman itu; `POST /admin/edit-mode/off` mematikannya. `to` hanya menerima path di situs ini (bukan `//...`, URL lain, atau backslash).
- Status disimpan di sesi (`App\Vcms\EditMode`), dan hanya berlaku bila sedang login. Blade: `@vcmsediting ... @endvcmsediting`.
- Pengunjung dan admin dengan mode edit mati melihat HTML yang sama, tanpa atribut atau script editor. Aset editor (`@vite`) hanya dimuat dalam mode edit.
- Panel `/admin` hanya berisi: login, dashboard, analytics, profil, dan tautan "Edit halaman X" yang membuka halaman publik dalam mode edit.

### 6.2 Komponen Blade

```blade
{{-- sekelompok nilai tunggal dengan satu tombol edit --}}
<x-vcms.settings :keys="['faqs.cta.text', 'faqs.cta.email']" label="Kotak kontak" class="sidebar-cta-box">
    <p>{{ $settings->get('faqs.cta.text') }}</p>
</x-vcms.settings>

{{-- satu item daftar: edit, naik, turun, hapus --}}
@foreach ($faqs as $faq)
    <x-vcms.item resource="faqs" :item="$faq" :fields="$faqFields" label="FAQ" class="accordion-item">
        <h3>{{ $faq->question }}</h3>
    </x-vcms.item>
@endforeach

{{-- tombol tambah (hanya muncul di mode edit) --}}
<x-vcms.add resource="faqs" :fields="$faqFields" label="FAQ" :defaults="['category' => 'general']" />
```

- `settings` dan `item` selalu merender elemennya (`tag`, default `div`, dengan atribut dan class yang diberikan), jadi markup template tidak berubah. Dalam mode edit ditambah class `vcms-zone`/`vcms-item`, tombol, dan `data-vcms` berisi JSON `{ kind, title, endpoint, fields }`.
- `fields` berisi definisi field yang sudah diberi nilai (`App\Vcms\FieldSet::withValues()`).

### 6.3 Definisi field

Setiap entitas punya kelas di `app/Vcms/Fields` yang mendeskripsikan field yang bisa diedit:

```php
class FaqFields
{
    public static function definition(): array
    {
        return [
            ['name' => 'category', 'type' => 'select', 'label' => 'Kategori', 'options' => [...]],
            ['name' => 'question', 'type' => 'text', 'label' => 'Pertanyaan'],
            ['name' => 'answer', 'type' => 'textarea', 'label' => 'Jawaban'],
        ];
    }
}
```

### 6.4 Editor JS

- Satu editor generik dengan **Alpine.js** + `fetch`, di `resources/js/vcms/`, dibundel Vite dan hanya dimuat saat mode edit.
- Editor membaca `data-vcms`, membuat form dari definisi field (text, textarea, rich_text, select, image, video, url, email, number, toggle), mengirim ke endpoint, lalu **memuat ulang halaman**. Error 422 ditampilkan di bawah field-nya.
- Settings: satu `PATCH` per field yang berubah. Item: satu `POST`/`PATCH` berisi semua field; gambar hanya dikirim bila dipilih file baru.
- Klik tombol editor ditangkap di fase capture, jadi tidak ikut membuka accordion, slider, atau link template.
- Tidak ada pengetahuan khusus per entitas di JS. Menambah entitas baru = definisi field + endpoint, tanpa mengubah JS.
- Tampilan modal mengikuti gaya `admin-vcms.css` yang sekarang supaya terasa sama bagi admin.
- Website publik tetap memakai jQuery dan plugin template. Editor baru tidak memakai jQuery.

---

## 7. Pola per entitas (checklist)

Setiap entitas dianggap selesai kalau semua poin ini terpenuhi:

1. Migration tabel (dan tabel anak bila ada).
2. Model dengan cast dan scope `ordered()`/`active()`.
3. Repository interface + implementasi Eloquent + binding.
4. Service.
5. FormRequest untuk tambah dan ubah (geser memakai `MoveRequest` bersama).
6. Controller API admin + API Resource.
7. Definisi field VCMS.
8. View publik memakai data dari service dan komponen `<x-vcms.*>`.
9. Seeder data demo dalam format baru.
10. Test: feature test endpoint (wajib login, 422, tambah/ubah/hapus/urutkan) + halaman publik tampil.
11. Key lama entitas ini **tidak disebut lagi** di view, `admin-vcms.js`, `ContentController`, maupun seeder lama, dan barisnya di `section_contents` sudah dihapus.

---

## 8. Test

- PHPUnit, database SQLite in-memory (`phpunit.xml`).
- **Smoke test:** semua halaman publik dan admin mengembalikan 200 (atau redirect login untuk admin tanpa sesi).
- **Feature test** per endpoint API.
- **Unit test** untuk service yang punya logika (urutan, upload, sanitasi rich text).
- Test wajib hijau sebelum setiap commit perombakan.

---

## 9. Konvensi

- Nama tabel jamak snake_case, model tunggal PascalCase, route resource kebab-case (`pricing-plans`).
- Kolom urutan selalu `sort_order`, status tampil selalu `is_active`.
- Pesan untuk admin berbahasa Indonesia. Nama kode berbahasa Inggris.
- Tidak ada fungsi global di view. Helper tampilan ditaruh di komponen Blade atau accessor model.
- Semua output Blade memakai `{{ }}`, kecuali `rich_text` yang sudah dibersihkan.

---

## 10. Performa & cache

Urutan prioritas bila website terasa lambat:

1. **Query per halaman:** setiap halaman hanya mengambil data yang dipakainya, dengan eager loading (`with()`) untuk relasi. Tidak ada lagi "muat semua section".
2. **Gambar & video:** WebP, ukuran sesuai tampilan, `loading="lazy"` untuk gambar di bawah layar, video disimpan sendiri.
3. **Optimasi rilis:** `php artisan optimize` dan OPcache.
4. **Cache data halaman** bila 1–3 belum cukup.

Aturan cache:

- **Tanpa Redis.** Driver `database` (default `.env.example`) atau `file` sudah cukup untuk website ini. Redis baru dipertimbangkan bila aplikasi berjalan di beberapa server, ada antrean berat, atau trafik sangat tinggi.
- Selalu lewat facade `Cache`, jadi pindah driver cukup dengan mengubah `CACHE_STORE`, tanpa mengubah kode.
- Key eksplisit per halaman (`page.home`, `page.faqs`, ...). **Jangan pakai `Cache::tags()`** karena tidak didukung driver `database`/`file`.
- Page service yang menyimpan cache, dan service entitas yang menghapusnya setelah tambah, ubah, hapus, atau urutkan:

```php
// HomePageService
return Cache::rememberForever('page.home', fn () => [...]);

// FaqService::update(), setelah data tersimpan
Cache::forget('page.faqs');
```
