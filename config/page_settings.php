<?php

/*
|--------------------------------------------------------------------------
| Page settings registry
|--------------------------------------------------------------------------
|
| Every single value an admin can edit (titles, button texts, contact info...).
| The database only stores values that were changed; anything else falls back
| to the default here. Types: text, textarea, rich_text, url, email, image, video.
| rich_text allows only <span>, <strong>, <em> and <br>.
|
*/

return [

    // Achievements section (Home, About)
    'achievements.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our achievements'],
    'achievements.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Proud moments & <span>milestones</span>'],
    'achievements.description' => ['type' => 'textarea', 'label' => 'Deskripsi', 'default' => "We're a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses."],
    'achievements.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => "let's get started !"],
    'achievements.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/contact'],

    // Client logos section (Home, About)
    'clients.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'testimonials'],
    'clients.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Trusted by <span>Teams at</span>'],
    'clients.box_title' => ['type' => 'text', 'label' => 'Judul kotak logo', 'default' => 'Our Clients'],

    // Creative tools section (Home, Services, Pricing, Testimonials)
    'tools.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'creative toolkit'],
    'tools.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Our toolbox for <span>innovation</span>'],
    'tools.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => "let's get started !"],
    'tools.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/contact'],

    // How we work section (Home)
    'process.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'how we work'],
    'process.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Our methodology for <span>success</span>'],
    'process.cta_image' => ['type' => 'image', 'label' => 'Gambar tombol bulat', 'default' => 'images/contact-circle-yellow.png'],
    'process.cta_link' => ['type' => 'url', 'label' => 'Link tombol bulat', 'default' => '/contact'],
    'process.image' => ['type' => 'image', 'label' => 'Gambar utama', 'default' => 'images/how-it-work-image.jpg'],
    'process.circle_image' => ['type' => 'image', 'label' => 'Gambar lingkaran dekorasi', 'default' => 'images/how-work-image-circle.png'],

    // FAQs page
    'faqs.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'FAQs'],
    'faqs.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'FAQs'],
    'faqs.category.general' => ['type' => 'text', 'label' => 'Judul kategori umum', 'default' => 'FAQ Umum'],
    'faqs.category.technical' => ['type' => 'text', 'label' => 'Judul kategori teknis & desain', 'default' => 'Pertanyaan Teknis & Desain'],
    'faqs.category.project' => ['type' => 'text', 'label' => 'Judul kategori manajemen proyek', 'default' => 'FAQ Manajemen Proyek'],
    'faqs.category.support' => ['type' => 'text', 'label' => 'Judul kategori bantuan klien', 'default' => 'Bantuan Klien'],
    'faqs.cta.text' => ['type' => 'textarea', 'label' => 'Teks kotak kontak', 'default' => 'Kami selalu siap berdiskusi dengan Anda.'],
    'faqs.cta.email' => ['type' => 'email', 'label' => 'Email kontak', 'default' => 'info@domainname.com'],
    'faqs.cta.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => 'Hubungi Kami Sekarang'],

];
