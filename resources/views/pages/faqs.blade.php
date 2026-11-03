@extends('layouts.sidebar')

@section('title', 'Dikelolain - FAQs')

@php
    // Helper function to get content from database
    if (!function_exists('getFaqContent')) {
        function getFaqContent($sections, $sectionKey, $contentKey, $default = '') {
            $section = collect($sections)->firstWhere('key', $sectionKey);
            if (!$section || !isset($section['contents'])) {
                return $default;
            }
            $content = collect($section['contents'])->firstWhere('key', $contentKey);
            return $content ? $content['value'] : $default;
        }
    }

    // Page Header data
    $headerTitle = getFaqContent($sections, 'faqs-header', 'faqs-header-1_title', 'FAQs');
    $headerBreadcrumb = getFaqContent($sections, 'faqs-header', 'faqs-header-1_breadcrumb', 'FAQs');

    // FAQ Categories with default data
    $faqCategories = [
        [
            'id' => 'general',
            'anchor' => 'general_information',
            'default_title' => 'FAQ Umum',
            'default_faqs' => [
                ['question' => 'Layanan apa saja yang Dikelolain Creative Agency sediakan?', 'answer' => 'Dikelolain Creative Agency berspesialisasi dalam manajemen media sosial dan konten kreatif yang komprehensif untuk Bisnis, UMKM, dan UKM. Layanan utama kami mencakup pembuatan konten video dan foto yang menarik khusus untuk Instagram & TikTok, desain grafis kreatif untuk kebutuhan Feed, Reels, dan Story, pengembangan strategi konten & manajemen media sosial yang terarah, hingga pengelolaan iklan kreatif di platform Meta (Instagram & FB Ads). Kami juga menawarkan Paket Manajemen Konten Full di mana semua konten untuk kebutuhan satu bulan ke depan sudah kami siapkan secara lengkap.'],
                ['question' => 'Siapa saja klien tipikal Dikelolain Creative Agency?', 'answer' => 'Klien tipikal kami adalah Bisnis, UMKM, UKM, dan Perusahaan di seluruh Nusantara yang ingin memperkuat kehadiran online dan mengembangkan bisnis mereka melalui strategi media sosial dan konten yang efektif.'],
                ['question' => 'Berapa lama waktu yang dibutuhkan untuk sebuah proyek?', 'answer' => 'Durasi proyek sangat bervariasi tergantung pada jenis layanan dan kompleksitasnya. Untuk Paket Manajemen Konten Full, kami menyiapkan semua konten untuk kebutuhan 1 bulan ke depan. Untuk layanan lain seperti desain grafis tunggal atau kampanye iklan khusus, waktu pengerjaan akan disesuaikan berdasarkan kesepakatan.'],
                ['question' => 'Bagaimana struktur harga layanan Dikelolain?', 'answer' => 'Kami menawarkan berbagai paket layanan yang disesuaikan dengan kebutuhan dan anggaran klien, mulai dari paket pembuatan konten bulanan hingga manajemen media sosial penuh. Untuk informasi lebih detail mengenai harga, silakan hubungi tim kami untuk konsultasi gratis.'],
                ['question' => 'Apakah saya bisa melihat contoh hasil kerja Dikelolain (portofolio)?', 'answer' => 'Tentu! Anda bisa melihat beberapa portofolio kami di akun Instagram resmi kami @dikelolain atau dengan menghubungi kami untuk studi kasus yang lebih spesifik.'],
            ]
        ],
        [
            'id' => 'technical',
            'anchor' => 'technical_and_design',
            'default_title' => 'Pertanyaan Teknis & Desain',
            'default_faqs' => [
                ['question' => 'Format file apa saja yang Anda sediakan untuk desain akhir?', 'answer' => 'Kami menyediakan desain dalam berbagai format sesuai kebutuhan platform dan penggunaan Anda. Untuk gambar, kami biasanya menyediakan file PNG atau JPEG berkualitas tinggi. Sedangkan untuk video, format yang kami gunakan adalah MP4 atau format lain yang sesuai dengan platform seperti Instagram dan TikTok.'],
                ['question' => 'Apakah desain Anda disesuaikan untuk tampilan seluler (mobile-friendly)?', 'answer' => 'Tentu saja. Semua desain dan konten video yang kami buat dioptimalkan secara khusus agar terlihat menarik dan efektif saat diakses melalui perangkat seluler, terutama di platform Instagram dan TikTok.'],
            ]
        ],
        [
            'id' => 'project',
            'anchor' => 'project_management',
            'default_title' => 'FAQ Manajemen Proyek',
            'default_faqs' => [
                ['question' => 'Bagaimana Anda mengelola proyek untuk memastikan pengiriman tepat waktu?', 'answer' => 'Kami menggunakan alur kerja yang terstruktur dan sistem manajemen proyek internal untuk memantau setiap tahapan. Dengan penjadwalan yang jelas, penugasan yang terorganisir, dan komunikasi berkala dengan klien, kami berkomitmen untuk memastikan semua konten dan proyek selesai serta dikirimkan tepat waktu.'],
                ['question' => 'Bagaimana saya akan terlibat dalam proyek?', 'answer' => 'Keterlibatan Anda sangat penting bagi kami. Anda akan terlibat dalam proses persetujuan ide awal, review draf konten atau desain, dan memberikan masukan di setiap tahapan penting. Kami akan menjaga komunikasi yang terbuka melalui WhatsApp, email, ataupun meeting online untuk memastikan proyek sesuai dengan ekspektasi Anda.'],
                ['question' => 'Siapa yang akan menjadi narahubung utama saya?', 'answer' => 'Anda akan memiliki seorang Project Manager atau Account Executive yang ditunjuk sebagai narahubung utama Anda. Beliau akan bertanggung jawab untuk mengoordinasikan proyek Anda dan memastikan semua pertanyaan atau kebutuhan Anda terpenuhi dengan baik.'],
            ]
        ],
        [
            'id' => 'support',
            'anchor' => 'client_support',
            'default_title' => 'Bantuan Klien',
            'default_faqs' => [
                ['question' => 'Apakah Anda menawarkan dukungan berkelanjutan setelah proyek selesai?', 'answer' => 'Ya, kami menyediakan dukungan berkelanjutan, terutama untuk layanan manajemen media sosial dan kampanye iklan. Bentuk dukungan dapat meliputi monitoring performa, penyesuaian strategi konten, analisis metrik, hingga sesi konsultasi berkala. Paket dukungan kami dapat disesuaikan berdasarkan kebutuhan spesifik Anda.'],
                ['question' => 'Bagaimana cara saya menghubungi tim dukungan Anda?', 'answer' => 'Anda dapat menghubungi tim dukungan kami melalui whatsapp atau melalui narahubung utama proyek Anda selama jam kerja kami. Kami siap membantu Anda.'],
                ['question' => 'Berapa waktu respons untuk pertanyaan dukungan?', 'answer' => 'Kami berusaha untuk merespons semua pertanyaan dukungan dalam waktu 1x24 jam kerja setelah menerima pesan Anda. Untuk masalah yang bersifat sangat mendesak, kami akan memberikan prioritas dan respons secepat mungkin.'],
            ]
        ],
    ];

    // Sidebar CTA data
    $ctaText = getFaqContent($sections, 'faqs', 'faq-sidebar-cta_text', 'Kami selalu siap berdiskusi dengan Anda.');
    $ctaEmail = getFaqContent($sections, 'faqs', 'faq-sidebar-cta_email', 'info@domainname.com');
    $ctaBtnText = getFaqContent($sections, 'faqs', 'faq-sidebar-cta_btn_text', 'Hubungi Kami Sekarang');
@endphp

@section('page-header')
    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>
@endsection

@section('page-wrapper-class', 'page-faqs')
@section('sidebar')
    <div class="faq-sidebar">
        <!-- Faq Category List Start -->
        <div class="faq-catagery-list wow fadeInUp">
            <ul>
                @foreach($faqCategories as $cat)
                    @php
                        $catTitle = getFaqContent($sections, 'faqs', "faq-category_{$cat['id']}_title", $cat['default_title']);
                    @endphp
                    <li><a href="#{{ $cat['anchor'] }}">{{ $catTitle }}</a></li>
                @endforeach
            </ul>
        </div>
        <!-- Faq Category List End -->

        <!-- Sidebar Cta Box Start -->
        <div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
            <div class="icon-box">
                <img src="{{ asset('images/icon-sidebar-cta.svg') }}" alt="">
            </div>
            <div class="cta-contact-content">
                <p>{{ $ctaText }}</p>
                <h3><a href="mailto:{{ $ctaEmail }}">{{ $ctaEmail }}</a></h3>
            </div>
            <div class="cta-contact-btn">
                <a href="{{ route('contact') }}" class="btn-default">{{ $ctaBtnText }}</a>
            </div>
        </div>
        <!-- Sidebar Cta Box End -->
    </div>
@endsection

@section('main-content')
    <!-- Page Faqs Category Start -->
    <div class="page-faqs-catagery">
        @php $globalFaqIndex = 0; @endphp
        @foreach($faqCategories as $catIndex => $cat)
            @php
                $catTitle = getFaqContent($sections, 'faqs', "faq-category_{$cat['id']}_title", $cat['default_title']);
                
                // Get FAQs from database for this category
                $categoryFaqs = [];
                $faqSection = collect($sections)->firstWhere('key', 'faqs');
                if ($faqSection && isset($faqSection['contents'])) {
                    foreach ($faqSection['contents'] as $content) {
                        if (preg_match('/faq-list-1_' . $cat['id'] . '_faq_(.+)_question$/', $content['key'], $matches)) {
                            $faqId = $matches[1];
                            $answer = getFaqContent($sections, 'faqs', "faq-list-1_{$cat['id']}_faq_{$faqId}_answer", '');
                            $categoryFaqs[] = [
                                'id' => $faqId,
                                'question' => $content['value'],
                                'answer' => $answer
                            ];
                        }
                    }
                }
                
                // Use default FAQs if no data in database
                if (empty($categoryFaqs)) {
                    foreach ($cat['default_faqs'] as $idx => $defaultFaq) {
                        $categoryFaqs[] = [
                            'id' => $idx + 1,
                            'question' => $defaultFaq['question'],
                            'answer' => $defaultFaq['answer']
                        ];
                    }
                }
            @endphp

            <!-- FAQs section start -->
            <div class="page-faq-accordion" id="{{ $cat['anchor'] }}">
                <!-- Section Title Start -->
                <div class="section-title">
                    <h2 class="text-anime-style-2" data-cursor="-opaque">{{ $catTitle }}</h2>
                </div>
                <!-- Section Title End -->

                <!-- FAQ Accordion Start -->
                <div class="faq-accordion" id="accordion{{ $catIndex }}">
                    @foreach($categoryFaqs as $faqIndex => $faq)
                        @php
                            $globalFaqIndex++;
                            $delayValue = $faqIndex * 0.2;
                        @endphp
                        <!-- FAQ Item Start -->
                        <div class="accordion-item wow fadeInUp" @if($delayValue > 0) data-wow-delay="{{ $delayValue }}s" @endif>
                            <h2 class="accordion-header" id="heading{{ $globalFaqIndex }}">
                                <button class="accordion-button {{ $faqIndex > 0 ? 'collapsed' : '' }}" type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $globalFaqIndex }}" 
                                    aria-expanded="{{ $faqIndex == 0 ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ $globalFaqIndex }}">
                                    {{ $faq['question'] }}
                                </button>
                            </h2>
                            <div id="collapse{{ $globalFaqIndex }}" 
                                class="accordion-collapse collapse {{ $faqIndex == 0 ? 'show' : '' }}" 
                                aria-labelledby="heading{{ $globalFaqIndex }}" 
                                data-bs-parent="#accordion{{ $catIndex }}">
                                <div class="accordion-body">
                                    <p>{{ $faq['answer'] }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item End -->
                    @endforeach
                </div>
                <!-- FAQ Accordion End -->
            </div>
            <!-- FAQs section End -->
        @endforeach
    </div>
    <!-- Page Faqs Category End -->
@endsection

@push('scripts')
@endpush
