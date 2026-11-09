@extends('layouts.app')

@section('title', 'Dikelolain - Creative Agency')

@section('content')

    <!-- Hero Section Start -->
    <div class="hero">
        @php
            // The phone cut is made from the main video. If the owner replaces only the main video, phones keep the poster rather than show the old one.
            $mobileVideo = $settings->isDefault('hero.video') || !$settings->isDefault('hero.video_mobile') ? $settings->url('hero.video_mobile') : null;
        @endphp
        <div class="hero-bg-video">
            <video autoplay muted loop playsinline id="myVideo" @if ($settings->get('hero.poster')) poster="{{ $settings->url('hero.poster') }}" @endif @if ($mobileVideo) data-mobile-src="{{ $mobileVideo }}" @endif>
                {{-- Phones skip the full video and show the poster; function.js loads the small phone cut (data-mobile-src) once the page has loaded. --}}
                <source src="{{ $settings->url('hero.video') }}" type="video/mp4" media="(min-width: 768px)">
            </video>
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <x-vcms.settings :keys="['hero.title', 'hero.description', 'hero.button_text', 'hero.button_link']" label="Teks" class="hero-content">
                        <div class="section-title">
                            <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('hero.title') !!}</h1>
                            <p>{{ $settings->get('hero.description') }}</p>
                        </div>
                        <div class="hero-btn">
                            <a href="{{ $settings->get('hero.button_link') }}" class="btn-default">{{ $settings->get('hero.button_text') }}</a>
                        </div>
                    </x-vcms.settings>
                </div>

                <div class="col-lg-5">
                    {{-- The background video sits behind the content, so it is edited with the images. --}}
                    <x-vcms.settings :keys="['hero.image_1', 'hero.image_2', 'hero.image_3', 'hero.image_4', 'hero.image_5', 'hero.video', 'hero.video_mobile', 'hero.poster']" label="Gambar & video" class="hero-images">
                        @foreach ([[1, 2], [3], [4, 5]] as $box => $images)
                            <div class="{{ $box === 1 ? 'hero-image-box-2' : 'hero-image-box-1' }}">
                                @foreach ($images as $i)
                                    <div class="hero-img">
                                        <figure class="image-anime reveal">
                                            <x-img :src="$settings->url('hero.image_' . $i)" :sizes="$box === 1 ? '(min-width: 1200px) 512px, (min-width: 992px) 377px, (min-width: 768px) 506px, calc(100vw - 45px)' : '(min-width: 1200px) 246px, (min-width: 992px) 178px, (min-width: 768px) 243px, calc(50vw - 33px)'" alt="Hero Image {{ $i }}" />
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </x-vcms.settings>
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
                    <x-vcms.settings :keys="['services.button_text', 'services.button_link']" label="Tombol" class="section-btn">
                        <a href="{{ $settings->get('services.button_link') }}" class="btn-default">{{ $settings->get('services.button_text') }}</a>
                    </x-vcms.settings>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="our-services-boxes tab-content" id="servicesbox">
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
                                        <x-img :src="$service->image_url" sizes="(min-width: 1200px) 727px, (min-width: 992px) 533px, calc(100vw - 45px)" alt="{{ $service->name }}" loading="lazy" />
                                    </figure>
                                </div>
                                <div class="service-box-item">
                                    <div class="icon-box">
                                        <x-img :src="$service->icon_url" sizes="80px" alt="Service Icon" loading="lazy" />
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
                        <h3>{{ $settings->get('work.sub_title') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('work.title') !!}</h2>
                    </x-vcms.settings>
                </div>
                <div class="col-lg-5">
                    <x-vcms.settings :keys="['work.button_text', 'work.button_link']" label="Tombol" class="section-btn">
                        <a href="{{ $settings->get('work.button_link') }}" class="btn-default">{{ $settings->get('work.button_text') }}</a>
                    </x-vcms.settings>
                </div>
            </div>

            {{-- The first six videos, then the first six designs; order and add on the Portfolio page. --}}
            <div class="row">
                @forelse ($projects as $project)
                    <div class="col-lg-4 col-md-6">
                        @include('partials.work-item', ['movable' => false, 'ratioImage' => true])
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-dim">
                        <p>No projects yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <!-- Our Work Section End -->

    <div class="spacer-70"></div>

    <!-- How It Work Section Start-->
    <div class="how-it-work bg-section">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-8">
                    <x-vcms.settings :keys="['process.sub_title', 'process.title']" label="Judul" class="section-title">
                        <h3>{{ $settings->get('process.sub_title') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('process.title') !!}</h2>
                    </x-vcms.settings>
                </div>
                <div class="col-lg-4">
                    <x-vcms.settings :keys="['process.cta_image', 'process.cta_link']" label="Tombol" class="contact-now-circle">
                        <a href="{{ $settings->get('process.cta_link') }}"><x-img :src="$settings->url('process.cta_image')" sizes="144px" alt="Contact Circle" loading="lazy" /></a>
                    </x-vcms.settings>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-6">
                    <x-vcms.settings :keys="['process.image', 'process.circle_image']" label="Gambar" class="how-it-work-images">
                        <div class="how-work-image-circle">
                            <x-img :src="$settings->url('process.circle_image')" sizes="(min-width: 1400px) 600px, (min-width: 1200px) 527px, (min-width: 992px) 423px, (min-width: 768px) 600px, calc(100vw - 45px)" alt="Decorative Circle" loading="lazy" />
                        </div>
                        <div class="how-it-work-image">
                            <figure class="image-anime">
                                <x-img :src="$settings->url('process.image')" sizes="(min-width: 768px) 423px, 250px" alt="Work Image" loading="lazy" />
                            </figure>
                        </div>
                    </x-vcms.settings>
                </div>

                <div class="col-lg-6">
                    <div class="work-faq-accordion" id="workaccordion">
                        @foreach ($processSteps as $step)
                            <x-vcms.item resource="process-steps" :item="$step" :fields="$processStepFields" label="langkah"
                                class="work-accordion-item {{ $step->is_active ? '' : 'vcms-hidden' }}">
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
    <div class="spacer-80"></div>
    <!-- Client Logos Section Start -->
    @include('partials.client-logos')
    <div class="spacer-80"></div>

    <!-- Testimonials Section End -->

@endsection
