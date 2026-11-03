@extends('layouts.app')

@section('title', 'About Us - Dikelolain')

@section('content')

    <x-vcms.settings :keys="['about.header.title', 'about.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('about.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('about.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>
<div class="page-about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <x-vcms.settings :keys="['about.image']" label="Gambar" class="about-us-img">
                        <figure class="image-anime reveal">
                            <img src="{{ $settings->url('about.image') }}" alt="">
                        </figure>
                    </x-vcms.settings>
                </div>

                <div class="col-lg-6">
                    <div class="about-us-content">
                        <x-vcms.settings :keys="['about.sub_title', 'about.title', 'about.description']" label="Judul" class="section-title">
                            <h3>{{ $settings->get('about.sub_title') }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('about.title') !!}</h2>
                            <p>{{ $settings->get('about.description') }}</p>
                        </x-vcms.settings>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="about-us-info-list">
                                    @include('partials.page-list', ['list' => 'about'])
                                </div>
                            </div>
                            <div class="col-md-6">
                                <x-vcms.settings :keys="['about.circle_image', 'about.circle_link']" label="Tombol" class="about-contact-now">
                                    <a href="{{ $settings->get('about.circle_link') }}"><img src="{{ $settings->url('about.circle_image') }}" alt="" loading="lazy"></a>
                                </x-vcms.settings>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page About Us Section End -->

    <!-- CTA Section Start -->
    @include('partials.cta')
    <!-- CTA Section End -->

    <!-- Our Team Section Start -->
    @include('partials.team')
    <!-- Our Team Section End -->

    <!-- Our Achievements Section Start -->
    @include('partials.achievements')
    <!-- Our Achievements Section End -->

    <!-- Our Vision Mission Section Start -->
    <div class="our-vision-mission bg-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Vision Mission Box Start -->
                    <div class="vision-mission-box">
                        @foreach (['vision', 'mission'] as $list)
                            <!-- Vision Mission Item Start -->
                            <div class="vision-mission-item">
                                <x-vcms.settings :keys="[$list . '.image']" label="Gambar" class="vision-mission-image">
                                    <figure class="image-anime reveal">
                                        <img src="{{ $settings->url($list . '.image') }}" alt="{{ $list }}" loading="lazy">
                                    </figure>
                                </x-vcms.settings>

                                <div class="vision-mission-content">
                                    <x-vcms.settings :keys="[$list . '.sub_title', $list . '.title', $list . '.description']" label="Judul" class="section-title">
                                        <h3>{{ $settings->get($list . '.sub_title') }}</h3>
                                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get($list . '.title') !!}</h2>
                                        <p>{{ $settings->get($list . '.description') }}</p>
                                    </x-vcms.settings>

                                    <div class="vision-mission-list">
                                        @include('partials.page-list')
                                    </div>
                                </div>
                            </div>
                            <!-- Vision Mission Item End -->
                        @endforeach
                    </div>
                    <!-- Vision Mission Box End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Vision Mission Section End -->

    <div style="height: 70px;"></div>
    <!-- Client Logos Section Start -->
    @include('partials.client-logos')
    <!-- Our Testimonial Section End -->

    <!-- Our Faqs Section Start -->
    <div class="our-faqs">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-9">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3>{{ $settings->get('faqs.header.breadcrumb') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('faqs.header.title') !!}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-3">
                    <!-- Section Button Start -->
                    <div class="section-btn">
                        <a href="{{ route('faqs') }}" class="btn-default">view all FAQ</a>
                    </div>
                    <!-- Section Button End -->
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-12">
                    <!-- Our FAQ Section Start -->
                    <div class="our-faq-section">
                        <!-- FAQ Accordion Start -->
                        <div class="faq-accordion" id="accordion">
                            @foreach ($faqs as $index => $faq)
                                @php
                                    $isFirst = $index === 0;
                                @endphp
                                <!-- FAQ Item Start -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-about-faq-{{ $faq->id }}">
                                        <button class="accordion-button {{ !$isFirst ? 'collapsed' : '' }}" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapse-about-faq-{{ $faq->id }}"
                                            aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                            aria-controls="collapse-about-faq-{{ $faq->id }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse-about-faq-{{ $faq->id }}"
                                        class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                                        aria-labelledby="heading-about-faq-{{ $faq->id }}"
                                        data-bs-parent="#accordion">
                                        <div class="accordion-body">
                                            <p>{{ $faq->answer }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- FAQ Item End -->
                            @endforeach
                        </div>
                        <!-- FAQ Accordion End -->
                    </div>
                    <!-- Our FAQ Section End -->
                </div>

            </div>
        </div>
    </div>
    @endsection
