# Backlog Kendala & Error

**Pasangan dari:** [status_backlog.md](status_backlog.md)
**Dibuat:** 3 November 2026

> Berisi hal yang **tidak bisa diselesaikan tanpa pemilik** (akun, konten asli, keputusan hosting) dan **error penting** yang ditemukan saat eksekusi otomatis. Pekerjaan lain tetap jalan; item di sini menunggu tindakan kamu.

| # | Kendala | Jenis | Menghambat | Yang dibutuhkan | Status |
|---|---|---|---|---|---|
| KD-1 | Database dan hosting untuk rilis belum dipilih (K-3) | Keputusan | PBI-504 (deploy), PBI-507 (optimasi server) | Pilih hosting (Azure App Service / lainnya) dan DB, buat akunnya sendiri, lalu ikuti [DEPLOY.md](DEPLOY.md). SQLite dan MySQL 8 sudah lolos uji (PBI-517); PostgreSQL belum | ✅ Dijawab 9 Nov 2026: **Hostinger Single Web Hosting** milik dosen, tempat web lama berjalan (MariaDB 11.8, LiteSpeed, PHP 8.2, tanpa SSH dan Composer). Pemilik masuk hPanel sendiri; jangan pernah meminta atau mengetik password. Sisa untuk PBI-507: pilih PHP 8.3 dan aktifkan OPcache di hPanel |
| KD-2 | Konten asli belum ada: video hero dan gambar penggantinya untuk HP (PBI-515), foto & link project, nama dan foto tim, logo klien, testimoni, harga paket, nomor WhatsApp, alamat & peta | Konten | PBI-502 | Konten dari pemilik/klien. **Video hero** bawaan demo Rovex dipakai seterusnya dan kini disimpan di proyek, 3,0 MB (PBI-516). Template sudah dibeli, tapi belum dipastikan lisensinya mencakup video demo: sebelum situs online, cek apakah `rovex-video-1.mp4` ada di ZIP template, atau baca lisensinya. Video pengganti perlu dikompres dulu karena unggahan tidak dikompres otomatis; ganti lewat tombol "Gambar & video" di mode edit (unggah MP4/WebM, maks 50 MB). Foto yang diunggah otomatis diperkecil dan dijadikan WebP | ⏳ Menunggu pemilik |
| KD-3 | Form kontak: tetap membuka WhatsApp (sekarang), disimpan ke database (dibaca di panel admin), atau dikirim ke email? Email butuh akun SMTP (misalnya App Password Gmail) di `.env` | Keputusan + akun | PBI-509 | Pilih salah satu; bila email, siapkan alamat tujuan dan kredensial SMTP (diisi sendiri di `.env`, jangan dikirim lewat chat) | ✅ Dijawab 3 Nov 2026: **database + WhatsApp** (PBI-509 selesai). Notifikasi email bisa ditambah nanti bila akun SMTP sudah siap |
| KD-4 | Banner "Try Our New Horizontal Slider!" di Portfolio dihapus dosen langsung di server web lama (tidak ada di git). Di versi ini banner masih tampil (K-9) | Keputusan | Tidak menghambat deploy | Pilih: sembunyikan banner seperti di web live, atau biarkan | ❓ Default: dibiarkan seperti versi ini (K-9) sampai pemilik memutuskan |
| KD-5 | Isi asli yang perlu dirapikan pemilik setelah rilis, lewat mode edit: testimoni uji berisi "hai", anggota tim urutan ke-8 tanpa nama, telepon/email/alamat di halaman Contact masih contoh template (`+987 654 3210`, `info@domain.com`), dan FAQ layanan masih contoh bawaan | Konten | Tidak menghambat deploy | Rapikan lewat mode edit setelah rilis | ⏳ Menunggu pemilik |
| KD-6 | Alamat detail lama (`/pages/team/0`, `/pages/service/new_...`) kini memakai slug, jadi link lama yang tersimpan di luar (Google, chat) menjadi 404 | Keputusan | Tidak menghambat deploy | Bila perlu, buat pengalihan 301 dari alamat lama ke slug baru | ❓ Default: tidak dialihkan (situs kecil, sitemap baru memuat alamat baru) |

## Catatan masa transisi

- **Selesai (PBI-403):** editor lama `/admin/pages/*` sudah dihapus. Semua konten diedit lewat **mode edit** di halaman publik: buka Dashboard atau menu "Edit Halaman" di sidebar admin, pilih halaman, lalu klik tombol ✎ di bagian yang ingin diubah. Isi yang dulu diubah lewat editor lama tidak ikut pindah; bila ada, masukkan ulang lewat mode edit.
- **Tabel lama sudah di-drop (PBI-405).** Cadangan database lokal sebelum drop: `storage/app/private/backups/database-before-pbi405-2026-11-03.sqlite`, dan isi tiga tabel lama dalam bentuk JSON: `storage/app/private/backups/legacy-sections-2026-11-03.json` (10 section, 215 isi). Keduanya hanya ada di komputer ini (tidak masuk git). Boleh dihapus setelah kamu yakin tidak ada teks lama yang perlu disalin.
- Sudah dipindah: **FAQ** (halaman `/faqs` dan section FAQ di About), **testimoni** (`/testimonials`), **logo klien** (Home, About), **tools** (Home, Services, Pricing, Testimonials), **langkah proses** (Home), **pencapaian** (Home, About), **angka CTA** (Home, About), **tim** (Home, About, Team, detail anggota), **layanan** (Home, Services, detail layanan), **project** (Home, Portfolio, slider eksperimen), **paket harga** (Pricing), **daftar dan teks About** (about us, visi, misi), **keuntungan Pricing**, **hero Home**, **halaman Contact**, **footer**, **header Testimonials**, dan **URL/tinggi Looker Studio** (menu Analytics). Semua halaman publik kini lepas dari editor lama.

## Data web lama (PBI-520, 9 Nov 2026)

- Hanya ada di komputer ini, **jangan masuk git** dan jangan dibagikan: dump database berisi hash password admin dan data sesi. Lokasinya `storage/app/private/live/`: `u592845481_dikelolain.sql` (89 KB, diekspor 9 Nov 2026 pukul 01.36 waktu server; perubahan isi terakhir 5 Nov 2026) dan `media.zip` (41,6 MB).
- Isi web live masih bisa diubah sampai pengalihan, jadi **ekspor ulang database dan folder `media` tepat sebelum pengalihan**, lalu ulangi konversinya.
- Folder dan database web lama di Hostinger dibiarkan sampai versi baru terbukti jalan, sebagai jalan kembali.
- Setelah rilis, ganti password admin.
