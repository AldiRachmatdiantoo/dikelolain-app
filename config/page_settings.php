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
