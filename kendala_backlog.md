# Backlog Kendala & Error

**Pasangan dari:** [status_backlog.md](status_backlog.md)
**Dibuat:** 3 November 2026

> Berisi hal yang **tidak bisa diselesaikan tanpa pemilik** (akun, konten asli, keputusan hosting) dan **error penting** yang ditemukan saat eksekusi otomatis. Pekerjaan lain tetap jalan; item di sini menunggu tindakan kamu.

| # | Kendala | Jenis | Menghambat | Yang dibutuhkan | Status |
|---|---|---|---|---|---|
| KD-1 | Database dan hosting untuk rilis belum dipilih (K-3) | Keputusan | PBI-504 (deploy), PBI-507 (optimasi server) | Pilih DB (SQLite/MySQL/PostgreSQL) dan hosting (Azure App Service / lainnya) | ⏳ Menunggu pemilik |
| KD-2 | Konten asli belum ada: video hero, foto & link project, nama dan foto tim, logo klien, testimoni, harga paket, nomor WhatsApp, alamat & peta | Konten | PBI-502 | Konten dari pemilik/klien | ⏳ Menunggu pemilik |

## Catatan masa transisi

- Selama Epic 2–3, halaman editor lama (`/admin/pages/*`) masih menampilkan section yang **sudah dipindah** ke struktur baru. Mengedit section itu di editor lama tidak lagi berpengaruh ke website. Edit section yang sudah dipindah lewat **mode edit** di halaman publik. Editor lama dihapus di Epic 4.
- Sudah dipindah: **FAQ** (halaman `/faqs` dan section FAQ di About), **testimoni** (`/testimonials`), **logo klien** (Home, About), **tools** (Home, Services, Pricing, Testimonials), **langkah proses** (Home). `/admin/pages/faqs` sudah dihapus; menu "FAQs Page" di sidebar admin membuka mode edit `/faqs`.
