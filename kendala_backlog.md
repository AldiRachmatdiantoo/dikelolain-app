# Backlog Kendala & Error

**Pasangan dari:** [status_backlog.md](status_backlog.md)
**Dibuat:** 3 November 2026

> Berisi hal yang **tidak bisa diselesaikan tanpa pemilik** (akun, konten asli, keputusan hosting) dan **error penting** yang ditemukan saat eksekusi otomatis. Pekerjaan lain tetap jalan; item di sini menunggu tindakan kamu.

| # | Kendala | Jenis | Menghambat | Yang dibutuhkan | Status |
|---|---|---|---|---|---|
| KD-1 | Database dan hosting untuk rilis belum dipilih (K-3) | Keputusan | PBI-504 (deploy), PBI-507 (optimasi server) | Pilih hosting (Azure App Service / lainnya) dan DB, buat akunnya sendiri, lalu ikuti [DEPLOY.md](DEPLOY.md). SQLite dan MySQL 8 sudah lolos uji (PBI-517); PostgreSQL belum | ⏳ Menunggu pemilik |
| KD-2 | Konten asli belum ada: video hero dan gambar penggantinya untuk HP (PBI-515), foto & link project, nama dan foto tim, logo klien, testimoni, harga paket, nomor WhatsApp, alamat & peta | Konten | PBI-502 | Konten dari pemilik/klien. **Video hero** bawaan demo Rovex dipakai seterusnya dan kini disimpan di proyek, 3,0 MB (PBI-516). Template sudah dibeli, tapi belum dipastikan lisensinya mencakup video demo: sebelum situs online, cek apakah `rovex-video-1.mp4` ada di ZIP template, atau baca lisensinya. Video pengganti perlu dikompres dulu karena unggahan tidak dikompres otomatis; ganti lewat tombol "Gambar & video" di mode edit (unggah MP4/WebM, maks 50 MB). Foto yang diunggah otomatis diperkecil dan dijadikan WebP | ⏳ Menunggu pemilik |
| KD-3 | Form kontak: tetap membuka WhatsApp (sekarang), disimpan ke database (dibaca di panel admin), atau dikirim ke email? Email butuh akun SMTP (misalnya App Password Gmail) di `.env` | Keputusan + akun | PBI-509 | Pilih salah satu; bila email, siapkan alamat tujuan dan kredensial SMTP (diisi sendiri di `.env`, jangan dikirim lewat chat) | ✅ Dijawab 3 Nov 2026: **database + WhatsApp** (PBI-509 selesai). Notifikasi email bisa ditambah nanti bila akun SMTP sudah siap |

## Catatan masa transisi

- **Selesai (PBI-403):** editor lama `/admin/pages/*` sudah dihapus. Semua konten diedit lewat **mode edit** di halaman publik: buka Dashboard atau menu "Edit Halaman" di sidebar admin, pilih halaman, lalu klik tombol ✎ di bagian yang ingin diubah. Isi yang dulu diubah lewat editor lama tidak ikut pindah; bila ada, masukkan ulang lewat mode edit.
- **Tabel lama sudah di-drop (PBI-405).** Cadangan database lokal sebelum drop: `storage/app/private/backups/database-before-pbi405-2026-11-03.sqlite`, dan isi tiga tabel lama dalam bentuk JSON: `storage/app/private/backups/legacy-sections-2026-11-03.json` (10 section, 215 isi). Keduanya hanya ada di komputer ini (tidak masuk git). Boleh dihapus setelah kamu yakin tidak ada teks lama yang perlu disalin.
- Sudah dipindah: **FAQ** (halaman `/faqs` dan section FAQ di About), **testimoni** (`/testimonials`), **logo klien** (Home, About), **tools** (Home, Services, Pricing, Testimonials), **langkah proses** (Home), **pencapaian** (Home, About), **angka CTA** (Home, About), **tim** (Home, About, Team, detail anggota), **layanan** (Home, Services, detail layanan), **project** (Home, Portfolio, slider eksperimen), **paket harga** (Pricing), **daftar dan teks About** (about us, visi, misi), **keuntungan Pricing**, **hero Home**, **halaman Contact**, **footer**, **header Testimonials**, dan **URL/tinggi Looker Studio** (menu Analytics). Semua halaman publik kini lepas dari editor lama.
