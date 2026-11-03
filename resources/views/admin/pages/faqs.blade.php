@extends('admin.layouts.admin')

@section('title', 'Edit FAQs Page')
@section('page-title', 'FAQs Page - VCMS Editor')

@php
    $previewUrl = route('faqs');
@endphp

@section('content')

@php
if (!function_exists('getContent')) {
    function getContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }
}

// Get FAQ items from database
$faqCategories = [
    ['id' => 'general', 'title' => 'FAQ Umum'],
    ['id' => 'technical', 'title' => 'Pertanyaan Teknis & Desain'],
    ['id' => 'project', 'title' => 'Pertanyaan Seputar Proyek'],
    ['id' => 'support', 'title' => 'Bantuan Klien'],
];

// Get dynamic FAQs
$dynamicFaqs = [];
$faqSection = $sections['faqs'] ?? null;
if ($faqSection && $faqSection->contents) {
    $faqIds = [];
    foreach ($faqSection->contents as $content) {
        if (preg_match('/faq-list-1_faq_(.+)_question$/', $content->key, $matches)) {
            $faqIds[$matches[1]] = $content->value;
        }
    }
    foreach ($faqIds as $id => $question) {
        $dynamicFaqs[] = ['id' => $id, 'question' => $question];
    }
}

// Page Header data
$headerTitle = getContent($sections, 'faqs-header', 'faqs-header-1_title', 'FAQs');
$headerBreadcrumb = getContent($sections, 'faqs-header', 'faqs-header-1_breadcrumb', 'FAQs');
@endphp

<!-- Page Header Section -->
<div class="page-header bg-section vcms-editable-section" data-section="faqs-header" data-item-id="faqs-header-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="page-header">
            <i class="fa-solid fa-pen"></i> Edit Header
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="faqs-header-1"
                    data-title="{{ $headerTitle }}"
                    data-breadcrumb="{{ $headerBreadcrumb }}">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $headerTitle !!}</h1>
                    <nav class="wow fadeInUp" data-wow-delay="0.25s">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQs Content Section -->
<div class="page-faqs vcms-editable-section" data-section="faqs" data-item-id="faqs-section">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="faqs-section">
            <i class="fa-solid fa-pen"></i> Edit FAQs
        </button>
    </div>

    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="faq-sidebar">
                    <!-- FAQ Category List -->
                    <div class="faq-catagery-list vcms-editable-item" data-edit-type="faq-categories" data-item-id="faq-categories">
                        <ul>
                            @foreach($faqCategories as $cat)
                                @php
                                    $catTitle = getContent($sections, 'faqs', "faq-category_{$cat['id']}_title", $cat['title']);
                                @endphp
                                <li><a href="#{{ $cat['id'] }}">{{ $catTitle }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Sidebar CTA Box -->
                    <div class="sidebar-cta-box vcms-editable-item" data-edit-type="sidebar-cta" data-item-id="faq-sidebar-cta">
                        <div class="vcms-edit-indicator-small"><i class="fa-solid fa-pen"></i></div>
                        @php
                            $ctaText = getContent($sections, 'faqs', 'faq-sidebar-cta_text', 'Kami selalu siap berdiskusi dengan Anda.');
                            $ctaEmail = getContent($sections, 'faqs', 'faq-sidebar-cta_email', 'info@domainname.com');
                            $ctaBtnText = getContent($sections, 'faqs', 'faq-sidebar-cta_btn_text', 'Hubungi Kami Sekarang');
                        @endphp
                        <div class="icon-box">
                            <img src="{{ asset('images/icon-sidebar-cta.svg') }}" alt="">
                        </div>
                        <div class="cta-contact-content">
                            <p>{{ $ctaText }}</p>
                            <h3>{{ $ctaEmail }}</h3>
                        </div>
                        <div class="cta-contact-btn">
                            <a href="{{ route('contact') }}" class="btn-default">{{ $ctaBtnText }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Content -->
            <div class="col-lg-8">
                <div class="page-faqs-catagery">
                    @foreach($faqCategories as $catIndex => $cat)
                        @php
                            $catTitle = getContent($sections, 'faqs', "faq-category_{$cat['id']}_title", $cat['title']);
                        @endphp
                        <div class="page-faq-accordion" id="{{ $cat['id'] }}">
                            <div class="section-title">
                                <h2>{{ $catTitle }}</h2>
                            </div>

                            <div class="faq-accordion" id="accordion-{{ $cat['id'] }}">
                                @php
                                    // Get FAQs for this category
                                    $categoryFaqs = [];
                                    if ($faqSection && $faqSection->contents) {
                                        foreach ($faqSection->contents as $content) {
                                            if (preg_match('/faq-list-1_' . $cat['id'] . '_faq_(.+)_question$/', $content->key, $matches)) {
                                                $faqId = $matches[1];
                                                $answer = getContent($sections, 'faqs', "faq-list-1_{$cat['id']}_faq_{$faqId}_answer", '');
                                                $categoryFaqs[] = [
                                                    'id' => $faqId,
                                                    'question' => $content->value,
                                                    'answer' => $answer
                                                ];
                                            }
                                        }
                                    }

                                @endphp

                                @foreach($categoryFaqs as $faqIndex => $faq)
                                    <div class="accordion-item" data-faq-id="{{ $faq['id'] }}">
                                        <h2 class="accordion-header" id="heading-{{ $cat['id'] }}-{{ $faq['id'] }}">
                                            <button class="accordion-button {{ $faqIndex > 0 ? 'collapsed' : '' }}" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $cat['id'] }}-{{ $faq['id'] }}"
                                                aria-expanded="{{ $faqIndex == 0 ? 'true' : 'false' }}">
                                                {{ $faq['question'] }}
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $cat['id'] }}-{{ $faq['id'] }}"
                                            class="accordion-collapse collapse {{ $faqIndex == 0 ? 'show' : '' }}"
                                            data-bs-parent="#accordion-{{ $cat['id'] }}">
                                            <div class="accordion-body">
                                                <p>{{ $faq['answer'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
