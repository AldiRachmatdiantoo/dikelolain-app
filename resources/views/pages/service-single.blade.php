@extends('layouts.sidebar')

@php
    // Helper function to get content from database
    if (!function_exists('getServiceContent')) {
        function getServiceContent($sections, $sectionKey, $contentKey, $default = '') {
            if (!isset($sections[$sectionKey])) {
                return $default;
            }
            $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
            return $content ? $content->value : $default;
        }
    }

    // Get service data
    $serviceTitle = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_title", 'Web Development');
    $serviceDesc = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_desc", 'We provide excellent service for your needs.');
    $headerBreadcrumb = getServiceContent($sections, 'services-header', 'services-header-1_breadcrumb', 'Services');


    // Service card image (used as fallback)
    $serviceImage = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_image", 'images/service-image-2.jpg');
    
    // Detail content - feature image with fallback to service card image
    $featureImage = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_feature_image", '');
    if (empty($featureImage)) {
        $featureImage = $serviceImage;
    }
    $desc1 = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_desc_1", 'Our web development services are designed to create dynamic, responsive, and user-friendly websites that drive engagement and deliver exceptional user experiences. From concept to launch, we work closely with you to design and develop websites tailored to your brand, ensuring they not only look stunning but also perform seamlessly across all devices.');
    $desc2 = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_desc_2", 'Our expertise spans a wide range of technologies, including custom coding, CMS platforms like WordPress, and e-commerce solutions such as Shopify and Magento. Whether you need a sleek business website, a robust e-commerce platform, or a fully customized web application, our team ensures your online presence is impactful, secure, and optimized for search engines.');

    // Benefits section
    $betit = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_betit", 'Benefits <span>web development</span>');
    $bedes = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_bedes", 'Web development enhances your online presence, boosts user engagement, and drives business growth with responsive, secure, and customized websites tailored to meet your goals.');

    // Get benefit items
    $benefitCount = (int)getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_benefit_count", '6');
    $benefits = [];
    for ($i = 1; $i <= $benefitCount; $i++) {
        $benefits[] = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_benefit_{$i}", '');
    }
    // Default benefits if empty
    if (empty(array_filter($benefits))) {
        $benefits = [
            'enhanced online presence',
            'secure platforms to protect data',
            'improved user experience (ux)',
            'seamless e-commerce integration',
            'mobile-friendly & responsive design',
            'higher user engagement & retention'
        ];
    }

    // FAQ section
    $faqSubTitle = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_subtitle", "FAQ's");
    $fatit = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_fatit", 'Find your <span>answers here</span>');

    // Get FAQ items
    $faqCount = (int)getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_count", '5');
    $faqs = [];
    for ($i = 1; $i <= $faqCount; $i++) {
        $faqs[] = [
            'question' => getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_{$i}_question", ''),
            'answer' => getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_{$i}_answer", '')
        ];
    }
    // Default FAQs if empty
    if (empty($faqs[0]['question'])) {
        $faqs = [
            ['question' => 'What services does your agency provide?', 'answer' => 'We specialize in including graphic design, branding, website design and development, UX/UI design, social media marketing, digital advertising, video production, and content creation.'],
            ['question' => 'Who are your typical clients?', 'answer' => 'We work with businesses of all sizes, from startups and small businesses to large enterprises. Our clients span industries such as technology, retail, hospitality, healthcare, and more.'],
            ['question' => 'How long does a typical project take?', 'answer' => 'The timeline depends on the scope and complexity of the project. Smaller projects may take 2-4 weeks, while larger ones can take several months. We\'ll provide an estimated timeline after discussing your needs.'],
            ['question' => 'What is your pricing structure?', 'answer' => 'We offer customized pricing based on the specific requirements of each project. After our initial consultation, we\'ll provide a detailed proposal with a breakdown of costs.'],
            ['question' => 'Why are UX and UI important?', 'answer' => 'User Experience (UX) design focuses on enhancing the overall experience users have when interacting with a product or service. It involves researching user needs, creating user personas, designing user journeys, and testing how easy or efficient the product is to use.']
        ];
    }

    // Sidebar CTA
    $ctaText = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_cta_text", 'We always available to discuss with you');
    $ctaEmail = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_cta_email", 'info@domain.com');
    $ctaBtnText = getServiceContent($sections, 'services', "services-list-1_service_{$serviceId}_cta_btn_text", 'contact us');

    // Get all services for sidebar category list
    $allServices = [];
    if (isset($sections['services'])) {
        foreach ($sections['services']->contents as $content) {
            if (preg_match('/services-list-1_service_(.+)_title$/', $content->key, $matches)) {
                $allServices[$matches[1]] = $content->value;
            }
        }
    }
    if (empty($allServices)) {
        $allServices = [
            '01' => 'UI/UX design',
            '02' => 'Web development',
            '03' => '3D designs',
            '04' => 'Digital marketing design',
            '05' => 'Logos & branding'
        ];
    }
@endphp

@section('title', $serviceTitle . ' - Dikelolain')

@section('page-header')
    <x-page-header title="{!! $serviceTitle !!}">
        <li class="breadcrumb-item"><a href="{{ route('services') }}">{{ $headerBreadcrumb }}</a></li>
        <li class="breadcrumb-item" aria-current="page">{{ strip_tags($serviceTitle) }}</li>
    </x-page-header>
@endsection

@section('page-wrapper-class', 'page-service-single')
@section('sidebar')

<div class="service-sidebar">
    <!-- Service Category List Start -->
    <div class="service-catagery-list wow fadeInUp">
        <h3>services category</h3>
        <ul>
            @foreach ($allServices as $sId => $sTitle)
                <li class="{{ $sId == $serviceId ? 'active' : '' }}">
                    <a href="{{ route('pages.service.single', ['serviceId' => $sId]) }}">{{ $sTitle }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <!-- Service Category List End -->

    <!-- Sidebar Cta Box Start -->
    <div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
        <div class="icon-box">
            <img src="{{ asset('images/icon-sidebar-cta.svg') }}" alt="">
        </div>
        <div class="cta-contact-content">
            <p>{{ $ctaText }}</p>
            <h3><a href="mailto:{{ $ctaEmail }}">{{ $ctaEmail }}</a></h3>
        </div>
        <div class="cta-contact-btn">
            <a href="{{ route('contact') }}" class="btn-default">{{ $ctaBtnText }}</a>
        </div>
    </div>
    <!-- Sidebar Cta Box End -->
</div>

@endsection

@section('main-content')

<div class="service-single-content">
    <!-- Service Feature Image Start -->
    <div class="service-feature-image">
        <figure class="image-anime reveal">
            <img src="{{ asset($featureImage) }}" alt="{{ strip_tags($serviceTitle) }}">
        </figure>
    </div>
    <!-- Service Feature Image End -->

    <!-- Service Entry Start -->
    <div class="service-entry">
        <p class="wow fadeInUp">{{ $desc1 }}</p>
        <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $desc2 }}</p>

        <!-- Service Benefits Start -->
        <div class="service-benefits">
            <h2 class="text-anime-style-2">{!! $betit !!}</h2>
            <p class="wow fadeInUp">{{ $bedes }}</p>

            <!-- Service Benefit List Start -->
            <div class="service-benefit-list wow fadeInUp" data-wow-delay="0.2s">
                <ul>
                    @foreach ($benefits as $benefit)
                        @if (!empty($benefit))
                            <li>{{ $benefit }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <!-- Service Benefit List End -->
        </div>
        <!-- Service Benefits End -->
    </div>
    <!-- Service Entry End -->

    <!-- Page Single Faqs Start -->
    <div class="page-single-faqs">
        <div class="section-title">
            <h3 class="wow fadeInUp">{{ $faqSubTitle }}</h3>
            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $fatit !!}</h2>
        </div>

        <div class="faq-accordion" id="accordion">
            @foreach ($faqs as $index => $faq)
                @if (!empty($faq['question']))
                    <div class="accordion-item wow fadeInUp" @if($index > 0) data-wow-delay="{{ 0.2 * $index }}s" @endif>
                        <h2 class="accordion-header" id="heading{{ $index + 1 }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index + 1 }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index + 1 }}">
                                {{ $faq['question'] }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index + 1 }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index + 1 }}" data-bs-parent="#accordion">
                            <div class="accordion-body">
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <!-- Page Single Faqs End -->
</div>

@endsection

@push('scripts')
@endpush
