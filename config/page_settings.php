<?php

/*
|--------------------------------------------------------------------------
| Page settings registry
|--------------------------------------------------------------------------
|
| Every single value an admin can edit (titles, button texts, contact info...).
| The database only stores values that were changed; anything else falls back
| to the default here. Types: text, textarea, rich_text, url, email, image, video.
| Uploaded pictures are scaled down and saved as WebP unless 'original' => true.
| rich_text allows only <span>, <strong>, <em> and <br>.
|
*/

return [

    // About page: header, about us section, vision and mission (their bullet lists are page_list_items)
    'about.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'About our <span>journey</span>'],
    'about.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'About Us'],
    'about.image' => ['type' => 'image', 'label' => 'Gambar', 'default' => 'images/about-img.jpg'],
    'about.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'about us'],
    'about.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'A team of <span>creative thinkers</span>'],
    'about.description' => ['type' => 'textarea', 'label' => 'Deskripsi', 'default' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.'],
    'about.circle_image' => ['type' => 'image', 'label' => 'Gambar tombol bulat', 'default' => 'images/contact-circle-yellow.webp'],
    'about.circle_link' => ['type' => 'url', 'label' => 'Link tombol bulat', 'default' => '/contact'],
    'vision.image' => ['type' => 'image', 'label' => 'Gambar', 'default' => 'images/our-vision-img.jpg'],
    'vision.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our vision'],
    'vision.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Driving the Future of <span>Creativity</span>'],
    'vision.description' => ['type' => 'textarea', 'label' => 'Deskripsi', 'default' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.'],
    'mission.image' => ['type' => 'image', 'label' => 'Gambar', 'default' => 'images/our-mission-img.jpg'],
    'mission.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our mission'],
    'mission.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Bringing ideas to life through <span>creativity</span>'],
    'mission.description' => ['type' => 'textarea', 'label' => 'Deskripsi', 'default' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.'],

    // Achievements section (Home, About)
    'achievements.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'our achievements'],
    'achievements.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Proud moments & <span>milestones</span>'],
    'achievements.description' => ['type' => 'textarea', 'label' => 'Deskripsi', 'default' => "We're a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses."],
    'achievements.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => "let's get started !"],
    'achievements.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/contact'],

    // CTA section with counters (Home, About)
    'cta.text' => ['type' => 'textarea', 'label' => 'Teks', 'default' => 'We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.'],
    'cta.circle_image' => ['type' => 'image', 'label' => 'Gambar tombol bulat', 'default' => 'images/cta-contact-circle.webp'],
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
    'process.cta_image' => ['type' => 'image', 'label' => 'Gambar tombol bulat', 'default' => 'images/contact-circle-yellow.webp'],
    'process.cta_link' => ['type' => 'url', 'label' => 'Link tombol bulat', 'default' => '/contact'],
    'process.image' => ['type' => 'image', 'label' => 'Gambar utama', 'default' => 'images/how-it-work-image.jpg'],
    'process.circle_image' => ['type' => 'image', 'label' => 'Gambar lingkaran dekorasi', 'default' => 'images/how-work-image-circle.webp'],

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

    // Hero section (Home)
    'hero.video' => ['type' => 'video', 'label' => 'Video latar', 'default' => '/videos/hero.mp4'],
    'hero.poster' => ['type' => 'image', 'label' => 'Gambar pengganti video (tampil di HP)', 'default' => ''],
    'hero.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Where <span>creativity</span> meets strategy'],
    'hero.description' => ['type' => 'textarea', 'label' => 'Deskripsi', 'default' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.'],
    'hero.button_text' => ['type' => 'text', 'label' => 'Teks tombol', 'default' => 'view our portfolio'],
    'hero.button_link' => ['type' => 'url', 'label' => 'Link tombol', 'default' => '/portfolio'],
    'hero.image_1' => ['type' => 'image', 'label' => 'Gambar 1', 'default' => 'images/hero-img-1.jpg'],
    'hero.image_2' => ['type' => 'image', 'label' => 'Gambar 2', 'default' => 'images/hero-img-2.jpg'],
    'hero.image_3' => ['type' => 'image', 'label' => 'Gambar 3', 'default' => 'images/hero-img-3.jpg'],
    'hero.image_4' => ['type' => 'image', 'label' => 'Gambar 4', 'default' => 'images/hero-img-4.jpg'],
    'hero.image_5' => ['type' => 'image', 'label' => 'Gambar 5', 'default' => 'images/hero-img-5.jpg'],

    // Contact page (phone, email and address are also shown in the footer)
    'contact.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'Contact <span>us</span>'],
    'contact.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Contact'],
    'contact.sub_title' => ['type' => 'text', 'label' => 'Sub judul', 'default' => 'contact us'],
    'contact.title' => ['type' => 'rich_text', 'label' => 'Judul', 'default' => 'Let\'s start a <span>conversation</span>'],
    'contact.phone_icon' => ['type' => 'image', 'label' => 'Ikon telepon', 'default' => 'images/icon-phone.svg'],
    'contact.phone_label' => ['type' => 'text', 'label' => 'Label telepon', 'default' => 'phone number'],
    'contact.phone' => ['type' => 'text', 'label' => 'Nomor telepon', 'default' => '+987 654 3210'],
    'contact.email_icon' => ['type' => 'image', 'label' => 'Ikon email', 'default' => 'images/icon-mail.svg'],
    'contact.email_label' => ['type' => 'text', 'label' => 'Label email', 'default' => 'email us'],
    'contact.email' => ['type' => 'email', 'label' => 'Email', 'default' => 'info@domain.com'],
    'contact.address_icon' => ['type' => 'image', 'label' => 'Ikon alamat', 'default' => 'images/icon-location.svg'],
    'contact.address_label' => ['type' => 'text', 'label' => 'Label alamat', 'default' => 'address'],
    'contact.address' => ['type' => 'textarea', 'label' => 'Alamat', 'default' => '7676 Arden Avenue, Ginashire'],
    'contact.form_title' => ['type' => 'rich_text', 'label' => 'Judul form', 'default' => 'Get a <span>free advise</span>'],
    'contact.form_button' => ['type' => 'text', 'label' => 'Teks tombol kirim', 'default' => 'send message'],
    'contact.whatsapp' => ['type' => 'text', 'label' => 'Nomor WhatsApp tujuan form (angka saja, diawali 62)', 'default' => '6281234567890'],
    'contact.map_url' => ['type' => 'url', 'label' => 'Link embed Google Maps', 'default' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d96737.10562045308!2d-74.08535042841811!3d40.739265258395164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sin!4v1703158537552!5m2!1sen!2sin'],

    // Footer (every page)
    'footer.title' => ['type' => 'text', 'label' => 'Judul besar', 'default' => 'Let\'s Connect'],
    'footer.title_link' => ['type' => 'url', 'label' => 'Link judul besar', 'default' => '/contact'],
    'footer.logo' => ['type' => 'image', 'label' => 'Logo', 'default' => 'images/logo-dikelolain-full-hitam.webp'],
    'footer.description' => ['type' => 'textarea', 'label' => 'Teks', 'default' => 'Dikelolain is a creative agency for branding, websites and digital campaigns. We turn ideas into designs that help your business grow.'],
    'footer.facebook' => ['type' => 'url', 'label' => 'Link Facebook', 'default' => '#'],
    'footer.x' => ['type' => 'url', 'label' => 'Link X', 'default' => '#'],
    'footer.linkedin' => ['type' => 'url', 'label' => 'Link LinkedIn', 'default' => '#'],
    'footer.instagram' => ['type' => 'url', 'label' => 'Link Instagram', 'default' => '#'],
    'footer.quick_1_text' => ['type' => 'text', 'label' => 'Link cepat 1: teks', 'default' => 'home'],
    'footer.quick_1_url' => ['type' => 'url', 'label' => 'Link cepat 1: alamat', 'default' => '/'],
    'footer.quick_2_text' => ['type' => 'text', 'label' => 'Link cepat 2: teks', 'default' => 'about us'],
    'footer.quick_2_url' => ['type' => 'url', 'label' => 'Link cepat 2: alamat', 'default' => '/about'],
    'footer.quick_3_text' => ['type' => 'text', 'label' => 'Link cepat 3: teks', 'default' => 'team'],
    'footer.quick_3_url' => ['type' => 'url', 'label' => 'Link cepat 3: alamat', 'default' => '/team'],
    'footer.quick_4_text' => ['type' => 'text', 'label' => 'Link cepat 4: teks', 'default' => 'our portfolio'],
    'footer.quick_4_url' => ['type' => 'url', 'label' => 'Link cepat 4: alamat', 'default' => '/portfolio'],
    'footer.copyright' => ['type' => 'text', 'label' => 'Copyright', 'default' => 'Copyright © 2025 All Rights Reserved.'],

    // Testimonials page
    'testimonials.header.title' => ['type' => 'rich_text', 'label' => 'Judul header', 'default' => 'Testimonials'],
    'testimonials.header.breadcrumb' => ['type' => 'text', 'label' => 'Breadcrumb', 'default' => 'Testimonials'],

    // Admin dashboard: Looker Studio report
    'analytics.looker_url' => ['type' => 'url', 'label' => 'Link embed Looker Studio', 'default' => 'https://lookerstudio.google.com/embed/reporting/c13bd4e0-3d10-48f4-ad69-27e600a65c1a/page/GzLhF'],
    'analytics.looker_height' => ['type' => 'text', 'label' => 'Tinggi laporan (piksel, kosong = otomatis)', 'default' => ''],

    // SEO: search and social previews (edited from the admin bar in edit mode).
    // original: stored exactly as uploaded, since not every social network shows WebP previews.
    'seo.image' => ['type' => 'image', 'label' => 'Gambar pratinjau saat dibagikan', 'default' => 'images/logo-dikelolain-full-hitam.png', 'original' => true],
    'seo.home.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Home (Google, maks. ±160 karakter)', 'default' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.'],
    'seo.about.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman About (Google, maks. ±160 karakter)', 'default' => 'Get to know Dikelolain: a team of creative thinkers building brands, websites and digital campaigns.'],
    'seo.services.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Services (Google, maks. ±160 karakter)', 'default' => 'Branding, UI/UX design, web development, 3D design and digital marketing services from Dikelolain.'],
    'seo.portfolio.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Portfolio (Google, maks. ±160 karakter)', 'default' => 'Selected video and design projects by Dikelolain.'],
    'seo.pricing.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Pricing (Google, maks. ±160 karakter)', 'default' => 'Simple, transparent pricing plans for design and digital services at Dikelolain.'],
    'seo.faqs.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman FAQs (Google, maks. ±160 karakter)', 'default' => 'Answers to common questions about working with Dikelolain.'],
    'seo.team.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Team (Google, maks. ±160 karakter)', 'default' => 'Meet the Dikelolain team behind every project.'],
    'seo.testimonials.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Testimonials (Google, maks. ±160 karakter)', 'default' => 'What clients say about working with Dikelolain.'],
    'seo.contact.description' => ['type' => 'textarea', 'label' => 'Deskripsi halaman Contact (Google, maks. ±160 karakter)', 'default' => 'Contact Dikelolain by phone, email or WhatsApp to start your project.'],

];
