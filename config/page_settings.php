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

    // CTA section with counters (Home, About)
    'cta.text' => ['type' => 'textarea', 'label' => 'Teks', 'default' => 'We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.'],
    'cta.circle_image' => ['type' => 'image', 'label' => 'Gambar tombol bulat', 'default' => 'images/cta-contact-circle.png'],
    'cta.circle_link' => ['type' => 'url', 'label' => 'Link tombol bulat', 'default' => '/contact'],

    // Client logos section (Home, About)
    'clients.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'testimonials'],
    'clients.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Trusted by <span>Teams at</span>'],
    'clients.box_title' => ['type' => 'text', 'label' => 'Judul kotak logo', 'default' => 'Our Clients'],

    // Services section (Home), Services page and service detail pages
    'services.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our Services'],
    'services.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Our innovative <span>services</span>'],
    'services.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => 'view all services'],
    'services.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/services'],
    'services.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'Explore our <span>services</span>'],
    'services.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Services'],
    'services.sidebar_title' => ['type' => 'text', 'label' => 'Judul daftar layanan (sidebar)', 'default' => 'services category'],
    'services.faq.sub_title' => ['type' => 'text', 'label' => 'Sub judul FAQ', 'default' => "FAQ's"],
    'services.faq.title' => ['type' => 'rich_text', 'label' => 'Judul FAQ', 'default' => 'Find your <span>answers here</span>'],
    'services.cta.text' => ['type' => 'textarea', 'label' => 'Teks kotak kontak', 'default' => 'We always available to discuss with you'],
    'services.cta.email' => ['type' => 'email', 'label' => 'Email kontak', 'default' => 'info@domain.com'],
    'services.cta.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => 'contact us'],

    // Team section (Home, About) and Team page
    'team.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our team'],
    'team.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'The Minds Behind <span>the Magic</span>'],
    'team.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => 'all team members'],
    'team.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/team'],
    'team.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'The minds behind <span>the magic</span>'],
    'team.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Our Team'],

    // Our work section (Home), Portfolio page and the horizontal slider experiment
    'work.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our work'],
    'work.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Our latest <span>Projects</span>'],
    'work.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => 'view all portfolio'],
    'work.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/portfolio'],
    'portfolio.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'Our <span>portfolio</span>'],
    'portfolio.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Portfolio'],
    'portfolio.experiment_breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb halaman slider', 'default' => 'Portfolio Experiment'],

    // Pricing page
    'pricing.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'Pricing <span>plan</span>'],
    'pricing.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Pricing'],
    'pricing.list_title' => ['type' => 'text', 'label' => 'Judul daftar fitur', 'default' => 'What included?'],

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
