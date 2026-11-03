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
│   ├── Requests/Admin/            StoreFaqRequest, UpdateFaqRequest, ReorderRequest, ...
│   └── Resources/                 FaqResource, ...
├── Services/
│   ├── Pages/                     HomePageService, AboutPageService, ...
│   ├── FaqService.php
│   └── ImageUploadService.php
├── Repositories/
│   ├── Contracts/                 FaqRepositoryInterface, ...
│   └── Eloquent/                  EloquentFaqRepository, ...
├── Models/                        Faq, Testimonial, TeamMember, ..., PageSetting
├── Vcms/
│   └── Fields/                    definisi field yang bisa diedit per entitas (FaqFields, ...)
├── View/Components/Vcms/          Text, Item, ItemList, Image
└── Providers/RepositoryServiceProvider.php

resources/
├── views/
│   ├── pages/                     satu file per halaman publik (dipakai juga untuk mode edit)
│   ├── components/vcms/           komponen editable
│   └── admin/                     hanya login, dashboard, analytics, profil
└── js/vcms/                       editor generik (Alpine.js), dibundel Vite khusus admin
```

---

## 4. Model data

### 4.1 Daftar → tabel sendiri

Semua tabel daftar punya `id`, `sort_order` (int), `is_active` (bool, default true), `timestamps`.

| Tabel | Kolom utama | Dipakai di |
|---|---|---|
| `faqs` | `category` (general/technical/project/support/pricing), `question`, `answer` | FAQs, Pricing |
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
| `type` | `text`, `textarea`, `rich_text`, `image`, `video`, `url` |
| `value` | isi |

Dipakai untuk: judul dan deskripsi hero, header setiap halaman, judul section, CTA, info kontak, peta, footer, URL Looker Studio.

`rich_text` hanya menerima tag yang diizinkan (`span`, `strong`, `em`, `br`) dan dibersihkan saat disimpan. Semua tipe lain ditampilkan dengan `{{ }}` (di-escape).

### 4.3 Gambar & video

- Disimpan di disk `public`, folder per entitas: `storage/app/public/{tabel}/{nama-acak}.{ext}`; perlu `php artisan storage:link`.
- Kolom menyimpan path relatif (`faqs/abc123.webp`). URL dibuat oleh model (accessor `image_url`).
- `ImageUploadService` memvalidasi **isi file** (mime sebenarnya, bukan ekstensi dari pengguna), membatasi ukuran, memberi nama acak dengan ekstensi dari mime, dan menghapus file lama saat diganti.
- Video berupa URL (YouTube/MP4 eksternal), tidak di-upload.
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
| Ubah urutan | `POST /admin/api/{resource}/reorder` | `{ "ids": [3, 1, 2] }` | `204` |

Sub-daftar memakai rute bersarang, misalnya `POST /admin/api/services/{service}/benefits`, `PATCH /admin/api/pricing-plans/{plan}/features/{feature}`.

Upload gambar dikirim sebagai field file (`image`, `photo`, `logo`, ...) pada `POST`/`PATCH` milik entitasnya. Karena browser tidak mengirim file lewat `PATCH`, kirim `POST` dengan `_method=PATCH`.

### 5.2 Nilai tunggal

| Aksi | Endpoint | Body |
|---|---|---|
| Ambil | `GET /admin/api/settings/{key}` | – |
| Ubah | `PATCH /admin/api/settings/{key}` | `{ "value": "..." }` atau file `file` |

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

- Admin yang login membuka halaman publik biasa (`/`, `/faqs`, ...) lalu menyalakan **mode edit** lewat bilah admin di atas halaman. Status mode edit disimpan di sesi.
- Pengunjung dan admin dengan mode edit mati melihat HTML yang persis sama, tanpa atribut atau script editor.
- Panel `/admin` hanya berisi: login, dashboard, analytics, profil, dan tautan "Edit halaman X" yang membuka halaman publik dalam mode edit.

### 6.2 Komponen Blade

```blade
{{-- nilai tunggal --}}
<x-vcms.text key="home.hero.title" :value="$hero['title']" tag="h1" />

{{-- daftar: tombol tambah + urutkan --}}
<x-vcms.list resource="faqs" :fields="App\Vcms\Fields\FaqFields::class">
    @foreach ($faqs as $faq)
        {{-- satu item: tombol edit + hapus --}}
        <x-vcms.item resource="faqs" :model="$faq">
            <h3>{{ $faq->question }}</h3>
            <p>{{ $faq->answer }}</p>
        </x-vcms.item>
    @endforeach
</x-vcms.list>
```

Dalam mode edit, komponen menambahkan atribut `data-vcms-*`: endpoint, id, dan definisi field (JSON). Di luar mode edit, komponen hanya merender isinya.

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
- Editor membaca `data-vcms-*`, membuat form dari definisi field (text, textarea, rich_text, select, image, url, number, toggle), mengirim ke endpoint, lalu memperbarui tampilan.
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
5. FormRequest untuk tambah, ubah, dan urutkan.
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
