@extends('layouts.sidebar')

@section('title', $service->name . ' - Dikelolain')

@section('page-header')
    <x-page-header :title="e($service->name)">
        <li class="breadcrumb-item"><a href="{{ route('services') }}">{{ $settings->get('services.header.breadcrumb') }}</a></li>
        <li class="breadcrumb-item" aria-current="page">{{ $service->name }}</li>
    </x-page-header>
@endsection

@section('page-wrapper-class', 'page-service-single')
@section('sidebar')

<div class="service-sidebar">
    <!-- Service Category List Start -->
    <div class="service-catagery-list wow fadeInUp">
        <x-vcms.settings :keys="['services.sidebar_title']" label="Judul" tag="h3">{{ $settings->get('services.sidebar_title') }}</x-vcms.settings>
        <ul>
            @foreach ($allServices as $item)
                <li class="{{ $item->is($service) ? 'active' : '' }}">
                    <a href="{{ route('pages.service.single', $item->slug) }}">{{ $item->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <!-- Service Category List End -->

    <!-- Sidebar Cta Box Start -->
    <x-vcms.settings :keys="['services.cta.text', 'services.cta.email', 'services.cta.button_text']" label="Kotak kontak" class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
        <div class="icon-box">
            <img src="{{ asset('images/icon-sidebar-cta.svg') }}" alt="">
        </div>
        <div class="cta-contact-content">
            <p>{{ $settings->get('services.cta.text') }}</p>
            <h3><a href="mailto:{{ $settings->get('services.cta.email') }}">{{ $settings->get('services.cta.email') }}</a></h3>
        </div>
        <div class="cta-contact-btn">
            <a href="{{ route('contact') }}" class="btn-default">{{ $settings->get('services.cta.button_text') }}</a>
        </div>
    </x-vcms.settings>
    <!-- Sidebar Cta Box End -->
</div>

@endsection

@section('main-content')

<div class="service-single-content">
    <!-- Service Feature Image Start -->
    <x-vcms.item resource="services" :item="$service" :fields="$serviceFields" label="detail layanan"
        :movable="false" :deletable="false" class="service-feature-image {{ $service->is_active ? '' : 'vcms-hidden' }}">
        <figure class="image-anime reveal">
            <img src="{{ $service->feature_image_url }}" alt="{{ $service->name }}">
        </figure>
    </x-vcms.item>
    <!-- Service Feature Image End -->

    <!-- Service Entry Start -->
    <div class="service-entry">
        @foreach ($service->paragraphs() as $paragraph)
            <p class="wow fadeInUp" @if (!$loop->first) data-wow-delay="0.2s" @endif>{{ $paragraph }}</p>
        @endforeach

        <!-- Service Benefits Start -->
        <div class="service-benefits">
            <h2 class="text-anime-style-2">{!! \App\Support\RichText::clean((string) $service->benefits_title) !!}</h2>
            <p class="wow fadeInUp">{{ $service->benefits_intro }}</p>

            <!-- Service Benefit List Start -->
            <div class="service-benefit-list wow fadeInUp" data-wow-delay="0.2s">
                <ul>
                    @foreach ($service->benefits as $benefit)
                        <x-vcms.item tag="li" resource="service-benefits" :item="$benefit" :fields="$benefitFields" label="manfaat"
                            class="{{ $benefit->is_active ? '' : 'vcms-hidden' }}">{{ $benefit->text }}</x-vcms.item>
                    @endforeach
                </ul>
            </div>
            <!-- Service Benefit List End -->

            <x-vcms.add resource="service-benefits" :fields="$benefitFields" label="manfaat" :defaults="['service_id' => $service->id]" />
        </div>
        <!-- Service Benefits End -->
    </div>
    <!-- Service Entry End -->

    <!-- Page Single Faqs Start -->
    <div class="page-single-faqs">
        <x-vcms.settings :keys="['services.faq.sub_title', 'services.faq.title']" label="Judul FAQ" class="section-title">
            <h3 class="wow fadeInUp">{{ $settings->get('services.faq.sub_title') }}</h3>
            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('services.faq.title') !!}</h2>
        </x-vcms.settings>

        <div class="faq-accordion" id="accordion">
            @foreach ($service->faqs as $faq)
                <x-vcms.item resource="service-faqs" :item="$faq" :fields="$faqFields" label="FAQ"
                    class="accordion-item wow fadeInUp {{ $faq->is_active ? '' : 'vcms-hidden' }}" :data-wow-delay="$loop->first ? null : (0.2 * $loop->index) . 's'">
                    <h2 class="accordion-header" id="service-faq-heading-{{ $faq->id }}">
                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#service-faq-{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="service-faq-{{ $faq->id }}">
                            {{ $faq->question }}
                        </button>
                    </h2>
                    <div id="service-faq-{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="service-faq-heading-{{ $faq->id }}" data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <p>{{ $faq->answer }}</p>
                        </div>
                    </div>
                </x-vcms.item>
            @endforeach
        </div>

        <x-vcms.add resource="service-faqs" :fields="$faqFields" label="FAQ" :defaults="['service_id' => $service->id]" />
    </div>
    <!-- Page Single Faqs End -->
</div>

@endsection
