@extends('layouts.app')

@section('title', 'Dikelolain - Creative Agency')

@section('content')

@php
    // Helper function to get content from database
    if (!function_exists('getContent')) {
        function getContent($sections, $sectionKey, $contentKey, $default = '')
        {
            if (!isset($sections[$sectionKey])) {
                return $default;
            }
            $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
            $value = $content ? $content->value : $default;
    
            if (
                strpos($contentKey, 'image') !== false ||
                strpos($contentKey, 'video') !== false ||
                strpos($contentKey, 'icon') !== false
            ) {
                if (!preg_match('/^(http|https):\/\//i', $value)) {
                    return $value;
                }
            }
            return $value;
        }
    }
@endphp

    <!-- Hero Section Start -->
    <div class="hero">
        <div class="hero-bg-video">
            @php
                $videoSrc = getContent(
                    $sections,
                    'hero',
                    'hero-video_video_url',
                    'https://demo.awaikenthemes.com/assets/videos/rovex-video-1.mp4',
                );
            @endphp
            <video autoplay muted loop id="myVideo">
                <source src="{{ asset($videoSrc) }}" type="video/mp4">
            </video>
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-content">
                        <div class="section-title">
                            @php
                                $heroTitle = getContent($sections, 'hero', 'hero-content-1_hero_title', 'Where <span>creativity</span> meets strategy');
                                $heroDesc = getContent($sections, 'hero', 'hero-content-1_hero_description', 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.');
                                $heroBtn = getContent($sections, 'hero', 'hero-content-1_btn_text', 'view our portfolio');
                                $heroLink = getContent($sections, 'hero', 'hero-content-1_btn_link', route('portfolio'));
                            @endphp
                            <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $heroTitle !!}</h1>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $heroDesc }}</p>
                        </div>
                        <div class="hero-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a href="{{ $heroLink }}" class="btn-default">{{ $heroBtn }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-images">
                        @php
                            $img1 = getContent($sections, 'hero', 'hero-images-1_image_1', 'images/hero-img-1.jpg');
                            $img2 = getContent($sections, 'hero', 'hero-images-1_image_2', 'images/hero-img-2.jpg');
                            $img3 = getContent($sections, 'hero', 'hero-images-1_image_3', 'images/hero-img-3.jpg');
                            $img4 = getContent($sections, 'hero', 'hero-images-1_image_4', 'images/hero-img-4.jpg');
                            $img5 = getContent($sections, 'hero', 'hero-images-1_image_5', 'images/hero-img-5.jpg');
                        @endphp
                        <div class="hero-image-box-1">
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($img1) }}" alt="Hero Image 1">
                                </figure>
                            </div>
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($img2) }}" alt="Hero Image 2">
                                </figure>
                            </div>
                        </div>
                        <div class="hero-image-box-2">
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($img3) }}" alt="Hero Image 3">
                                </figure>
                            </div>
                        </div>
                        <div class="hero-image-box-1">
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($img4) }}" alt="Hero Image 4">
                                </figure>
                            </div>
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($img5) }}" alt="Hero Image 5">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Hero Section End -->


    <!-- Our Services Section Start -->
    <div class="our-services">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-6">
                    <x-vcms.settings :keys="['services.sub_title', 'services.title']" label="Judul" class="section-title">
                        <h3>{{ $settings->get('services.sub_title') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('services.title') !!}</h2>
                    </x-vcms.settings>
                </div>
                <div class="col-lg-6">
                    <x-vcms.settings :keys="['services.button_text', 'services.button_link']" label="Tombol" class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                        <a href="{{ $settings->get('services.button_link') }}" class="btn-default">{{ $settings->get('services.button_text') }}</a>
                    </x-vcms.settings>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="our-services-boxes tab-content wow fadeInUp" data-wow-delay="0.25s" id="servicesbox">
                        <div class="our-services-nav">
                            <ul class="nav nav-tabs" id="servicestab" role="tablist">
                                @foreach ($services as $service)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="service-{{ $service->id }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#service-{{ $service->id }}" type="button" role="tab"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span> {{ $service->name }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @foreach ($services as $service)
                            <x-vcms.item resource="services" :item="$service" :fields="$serviceFields" label="layanan"
                                class="our-service-box tab-pane fade {{ $loop->first ? 'show active' : '' }} {{ $service->is_active ? '' : 'vcms-hidden' }}"
                                id="service-{{ $service->id }}" role="tabpanel">
                                <div class="service-box-image">
                                    <figure>
                                        <img src="{{ $service->image_url }}" alt="{{ $service->name }}">
                                    </figure>
                                </div>
                                <div class="service-box-item">
                                    <div class="icon-box">
                                        <img src="{{ $service->icon_url }}" alt="Service Icon">
                                    </div>
                                    <div class="service-box-item-content">
                                        <h3><a href="{{ route('pages.service.single', $service->slug) }}">{{ $service->name }}</a></h3>
                                        <p>{{ $service->summary }}</p>
                                    </div>
                                    <div class="service-box-item-btn">
                                        <a href="{{ route('pages.service.single', $service->slug) }}" class="readmore-btn">read more</a>
                                    </div>
                                </div>
                            </x-vcms.item>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Services Section End -->

    <!-- Our Work Section Start -->
    <div class="our-work bg-section">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-7">
                    <x-vcms.settings :keys="['work.sub_title', 'work.title']" label="Judul" class="section-title">
                        <h3 class="wow fadeInUp">{{ $settings->get('work.sub_title') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('work.title') !!}</h2>
                    </x-vcms.settings>
                </div>
                <div class="col-lg-5">
                    <x-vcms.settings :keys="['work.button_text', 'work.button_link']" label="Tombol" class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                        <a href="{{ $settings->get('work.button_link') }}" class="btn-default">{{ $settings->get('work.button_text') }}</a>
                    </x-vcms.settings>
                </div>
            </div>

            {{-- The first six videos, then the first six designs; order and add on the Portfolio page. --}}
            <div class="row">
                @forelse ($projects as $project)
                    <div class="col-lg-4 col-md-6">
                        @include('partials.work-item', ['delay' => 0.2 * ($loop->index % 3), 'movable' => false, 'ratioImage' => true])
                    </div>
                @empty
                    <div class="col-12 text-center py-5" style="color: #888;">
                        <p>No projects yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Our Work Section End -->

    <div style="height: 70px;"></div>

    <!-- How It Work Section Start-->
    <div class="how-it-work bg-section">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-8">
                    <x-vcms.settings :keys="['process.sub_title', 'process.title']" label="Judul" class="section-title">
                        <h3 class="wow fadeInUp">{{ $settings->get('process.sub_title') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('process.title') !!}</h2>
                    </x-vcms.settings>
                </div>
                <div class="col-lg-4">
                    <x-vcms.settings :keys="['process.cta_image', 'process.cta_link']" label="Tombol" class="contact-now-circle">
                        <a href="{{ $settings->get('process.cta_link') }}"><img src="{{ $settings->url('process.cta_image') }}" alt="Contact Circle"></a>
                    </x-vcms.settings>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-6">
                    <x-vcms.settings :keys="['process.image', 'process.circle_image']" label="Gambar" class="how-it-work-images">
                        <div class="how-work-image-circle">
                            <img src="{{ $settings->url('process.circle_image') }}" alt="Decorative Circle">
                        </div>
                        <div class="how-it-work-image">
                            <figure class="image-anime">
                                <img src="{{ $settings->url('process.image') }}" alt="Work Image">
                            </figure>
                        </div>
                    </x-vcms.settings>
                </div>

                <div class="col-lg-6">
                    <div class="work-faq-accordion" id="workaccordion">
                        @foreach ($processSteps as $step)
                            <x-vcms.item resource="process-steps" :item="$step" :fields="$processStepFields" label="langkah"
                                class="work-accordion-item wow fadeInUp {{ $step->is_active ? '' : 'vcms-hidden' }}" data-wow-delay="{{ 0.2 * $loop->index }}s">
                                <h2 class="accordion-header" id="process-heading-{{ $step->id }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#process-{{ $step->id }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="process-{{ $step->id }}">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}. {{ $step->title }}
                                    </button>
                                </h2>
                                <div id="process-{{ $step->id }}"
                                    class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                    aria-labelledby="process-heading-{{ $step->id }}" data-bs-parent="#workaccordion">
                                    <div class="accordion-body">
                                        <p>{{ $step->description }}</p>
                                    </div>
                                </div>
                            </x-vcms.item>
                        @endforeach
                    </div>

                    <x-vcms.add resource="process-steps" :fields="$processStepFields" label="langkah" />
                </div>
            </div>
        </div>
    </div>
    <!-- How It Work Section End-->

    <!-- Our Team Section Start -->
    @include('partials.team')
    <!-- Our Team Section End -->

    <!-- Creative Tools Section Start -->
    @include('partials.creative-tools')
    <!-- Creative Tools Section End -->

    <!-- Our Achievements Section Start -->
    @include('partials.achievements')
    <!-- Our Achievements Section End -->

    <!-- CTA Section Start -->
    @include('partials.cta')
    <!-- CTA Section End -->
    <div style="height: 80px;"></div>
    <!-- Client Logos Section Start -->
    @include('partials.client-logos')
    <div style="height: 80px;"></div>

    <!-- Testimonials Section End -->

@endsection
