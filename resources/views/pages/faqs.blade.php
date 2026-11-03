@extends('layouts.sidebar')

@section('title', 'FAQs - Dikelolain')

@section('page-header')
    <x-vcms.settings :keys="['faqs.header.title', 'faqs.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('faqs.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('faqs.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>
@endsection

@section('page-wrapper-class', 'page-faqs')
@section('sidebar')
    <div class="faq-sidebar">
        <!-- Faq Category List Start -->
        <x-vcms.settings :keys="['faqs.category.general', 'faqs.category.technical', 'faqs.category.project', 'faqs.category.support']" label="Judul kategori" class="faq-catagery-list">
            <ul>
                @foreach ($categories as $category)
                    <li><a href="#{{ $category['anchor'] }}">{{ $category['title'] }}</a></li>
                @endforeach
            </ul>
        </x-vcms.settings>
        <!-- Faq Category List End -->

        <!-- Sidebar Cta Box Start -->
        <x-vcms.settings :keys="['faqs.cta.text', 'faqs.cta.email', 'faqs.cta.button_text']" label="Kotak kontak" class="sidebar-cta-box">
            <div class="icon-box">
                <img src="{{ asset('images/icon-sidebar-cta.svg') }}" alt="">
            </div>
            <div class="cta-contact-content">
                <p>{{ $settings->get('faqs.cta.text') }}</p>
                <h3><a href="mailto:{{ $settings->get('faqs.cta.email') }}">{{ $settings->get('faqs.cta.email') }}</a></h3>
            </div>
            <div class="cta-contact-btn">
                <a href="{{ route('contact') }}" class="btn-default">{{ $settings->get('faqs.cta.button_text') }}</a>
            </div>
        </x-vcms.settings>
        <!-- Sidebar Cta Box End -->
    </div>
@endsection

@section('main-content')
    <!-- Page Faqs Category Start -->
    <div class="page-faqs-catagery">
        @foreach ($categories as $category)
            <!-- FAQs section start -->
            <div class="page-faq-accordion" id="{{ $category['anchor'] }}">
                <!-- Section Title Start -->
                <div class="section-title">
                    <h2 class="text-anime-style-2" data-cursor="-opaque">{{ $category['title'] }}</h2>
                </div>
                <!-- Section Title End -->

                <!-- FAQ Accordion Start -->
                <div class="faq-accordion" id="accordion-{{ $category['key'] }}">
                    @foreach ($category['faqs'] as $faq)
                        <!-- FAQ Item Start -->
                        <x-vcms.item resource="faqs" :item="$faq" :fields="$faqFields" label="FAQ"
                            class="accordion-item {{ $faq->is_active ? '' : 'vcms-hidden' }}">
                            <h2 class="accordion-header" id="heading-faq-{{ $faq->id }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse-faq-{{ $faq->id }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-controls="collapse-faq-{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="collapse-faq-{{ $faq->id }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                aria-labelledby="heading-faq-{{ $faq->id }}"
                                data-bs-parent="#accordion-{{ $category['key'] }}">
                                <div class="accordion-body">
                                    <p>{{ $faq->answer }}</p>
                                </div>
                            </div>
                        </x-vcms.item>
                        <!-- FAQ Item End -->
                    @endforeach
                </div>
                <!-- FAQ Accordion End -->

                <x-vcms.add resource="faqs" :fields="$faqFields" label="FAQ" :defaults="['category' => $category['key']]" />
            </div>
            <!-- FAQs section End -->
        @endforeach
    </div>
    <!-- Page Faqs Category End -->
@endsection
