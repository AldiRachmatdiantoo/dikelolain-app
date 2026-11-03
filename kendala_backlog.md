# Backlog Kendala & Error

**Pasangan dari:** [status_backlog.md](status_backlog.md)
**Dibuat:** 3 November 2026

> Berisi hal yang **tidak bisa diselesaikan tanpa pemilik** (akun, konten asli, keputusan hosting) dan **error penting** yang ditemukan saat eksekusi otomatis. Pekerjaan lain tetap jalan; item di sini menunggu tindakan kamu.

| # | Kendala | Jenis | Menghambat | Yang dibutuhkan | Status |
|---|---|---|---|---|---|
| KD-1 | Database dan hosting untuk rilis belum dipilih (K-3) | Keputusan | PBI-504 (deploy), PBI-507 (optimasi server) | Pilih DB (SQLite/MySQL/PostgreSQL) dan hosting (Azure App Service / lainnya) | ⏳ Menunggu pemilik |
| KD-2 | Konten asli belum ada: video hero, foto & link project, nama dan foto tim, logo klien, testimoni, harga paket, nomor WhatsApp, alamat & peta | Konten | PBI-502 | Konten dari pemilik/klien | ⏳ Menunggu pemilik |

## Catatan masa transisi

- **Selesai (PBI-403):** editor lama `/admin/pages/*` sudah dihapus. Semua konten diedit lewat **mode edit** di halaman publik: buka Dashboard atau menu "Edit Halaman" di sidebar admin, pilih halaman, lalu klik tombol ✎ di bagian yang ingin diubah. Isi yang dulu diubah lewat editor lama tidak ikut pindah; bila ada, masukkan ulang lewat mode edit.
- Sudah dipindah: **FAQ** (halaman `/faqs` dan section FAQ di About), **testimoni** (`/testimonials`), **logo klien** (Home, About), **tools** (Home, Services, Pricing, Testimonials), **langkah proses** (Home), **pencapaian** (Home, About), **angka CTA** (Home, About), **tim** (Home, About, Team, detail anggota), **layanan** (Home, Services, detail layanan), **project** (Home, Portfolio, slider eksperimen), **paket harga** (Pricing), **daftar dan teks About** (about us, visi, misi), **keuntungan Pricing**, **hero Home**, **halaman Contact**, **footer**, **header Testimonials**, dan **URL/tinggi Looker Studio** (menu Analytics). Semua halaman publik kini lepas dari editor lama.
