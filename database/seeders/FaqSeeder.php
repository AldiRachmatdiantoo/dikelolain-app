<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Demo FAQs (previously hardcoded in pages/faqs.blade.php).
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            'general' => [
                ['Layanan apa saja yang Dikelolain Creative Agency sediakan?', 'Dikelolain Creative Agency berspesialisasi dalam manajemen media sosial dan konten kreatif yang komprehensif untuk Bisnis, UMKM, dan UKM. Layanan utama kami mencakup pembuatan konten video dan foto yang menarik khusus untuk Instagram & TikTok, desain grafis kreatif untuk kebutuhan Feed, Reels, dan Story, pengembangan strategi konten & manajemen media sosial yang terarah, hingga pengelolaan iklan kreatif di platform Meta (Instagram & FB Ads). Kami juga menawarkan Paket Manajemen Konten Full di mana semua konten untuk kebutuhan satu bulan ke depan sudah kami siapkan secara lengkap.'],
                ['Siapa saja klien tipikal Dikelolain Creative Agency?', 'Klien tipikal kami adalah Bisnis, UMKM, UKM, dan Perusahaan di seluruh Nusantara yang ingin memperkuat kehadiran online dan mengembangkan bisnis mereka melalui strategi media sosial dan konten yang efektif.'],
                ['Berapa lama waktu yang dibutuhkan untuk sebuah proyek?', 'Durasi proyek sangat bervariasi tergantung pada jenis layanan dan kompleksitasnya. Untuk Paket Manajemen Konten Full, kami menyiapkan semua konten untuk kebutuhan 1 bulan ke depan. Untuk layanan lain seperti desain grafis tunggal atau kampanye iklan khusus, waktu pengerjaan akan disesuaikan berdasarkan kesepakatan.'],
                ['Bagaimana struktur harga layanan Dikelolain?', 'Kami menawarkan berbagai paket layanan yang disesuaikan dengan kebutuhan dan anggaran klien, mulai dari paket pembuatan konten bulanan hingga manajemen media sosial penuh. Untuk informasi lebih detail mengenai harga, silakan hubungi tim kami untuk konsultasi gratis.'],
                ['Apakah saya bisa melihat contoh hasil kerja Dikelolain (portofolio)?', 'Tentu! Anda bisa melihat beberapa portofolio kami di akun Instagram resmi kami @dikelolain atau dengan menghubungi kami untuk studi kasus yang lebih spesifik.'],
            ],
            'technical' => [
                ['Format file apa saja yang Anda sediakan untuk desain akhir?', 'Kami menyediakan desain dalam berbagai format sesuai kebutuhan platform dan penggunaan Anda. Untuk gambar, kami biasanya menyediakan file PNG atau JPEG berkualitas tinggi. Sedangkan untuk video, format yang kami gunakan adalah MP4 atau format lain yang sesuai dengan platform seperti Instagram dan TikTok.'],
                ['Apakah desain Anda disesuaikan untuk tampilan seluler (mobile-friendly)?', 'Tentu saja. Semua desain dan konten video yang kami buat dioptimalkan secara khusus agar terlihat menarik dan efektif saat diakses melalui perangkat seluler, terutama di platform Instagram dan TikTok.'],
            ],
            'project' => [
                ['Bagaimana Anda mengelola proyek untuk memastikan pengiriman tepat waktu?', 'Kami menggunakan alur kerja yang terstruktur dan sistem manajemen proyek internal untuk memantau setiap tahapan. Dengan penjadwalan yang jelas, penugasan yang terorganisir, dan komunikasi berkala dengan klien, kami berkomitmen untuk memastikan semua konten dan proyek selesai serta dikirimkan tepat waktu.'],
                ['Bagaimana saya akan terlibat dalam proyek?', 'Keterlibatan Anda sangat penting bagi kami. Anda akan terlibat dalam proses persetujuan ide awal, review draf konten atau desain, dan memberikan masukan di setiap tahapan penting. Kami akan menjaga komunikasi yang terbuka melalui WhatsApp, email, ataupun meeting online untuk memastikan proyek sesuai dengan ekspektasi Anda.'],
                ['Siapa yang akan menjadi narahubung utama saya?', 'Anda akan memiliki seorang Project Manager atau Account Executive yang ditunjuk sebagai narahubung utama Anda. Beliau akan bertanggung jawab untuk mengoordinasikan proyek Anda dan memastikan semua pertanyaan atau kebutuhan Anda terpenuhi dengan baik.'],
            ],
            'support' => [
                ['Apakah Anda menawarkan dukungan berkelanjutan setelah proyek selesai?', 'Ya, kami menyediakan dukungan berkelanjutan, terutama untuk layanan manajemen media sosial dan kampanye iklan. Bentuk dukungan dapat meliputi monitoring performa, penyesuaian strategi konten, analisis metrik, hingga sesi konsultasi berkala. Paket dukungan kami dapat disesuaikan berdasarkan kebutuhan spesifik Anda.'],
                ['Bagaimana cara saya menghubungi tim dukungan Anda?', 'Anda dapat menghubungi tim dukungan kami melalui whatsapp atau melalui narahubung utama proyek Anda selama jam kerja kami. Kami siap membantu Anda.'],
                ['Berapa waktu respons untuk pertanyaan dukungan?', 'Kami berusaha untuk merespons semua pertanyaan dukungan dalam waktu 1x24 jam kerja setelah menerima pesan Anda. Untuk masalah yang bersifat sangat mendesak, kami akan memberikan prioritas dan respons secepat mungkin.'],
            ],
        ];

        foreach ($faqs as $category => $items) {
            foreach ($items as $index => [$question, $answer]) {
                Faq::create([
                    'category' => $category,
                    'question' => $question,
                    'answer' => $answer,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}
