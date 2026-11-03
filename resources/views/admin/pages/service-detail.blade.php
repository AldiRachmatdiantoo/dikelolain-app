@extends('admin.layouts.admin')

@section('title', 'Edit Service Detail')
@section('page-title', 'Service Detail - VCMS Editor')

@section('content')

@php
    function getContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }

    // Get service data
    $serviceTitle = getContent($sections, 'services', "services-list-1_service_{$serviceId}_title", 'Service Title');
    $serviceDesc = getContent($sections, 'services', "services-list-1_service_{$serviceId}_desc", '');
    $serviceImage = getContent($sections, 'services', "services-list-1_service_{$serviceId}_image", 'images/service-image-1.jpg');
    $serviceLink = getContent($sections, 'services', "services-list-1_service_{$serviceId}_link", '');

    // Detail content
    $featureImage = getContent($sections, 'services', "services-list-1_service_{$serviceId}_feature_image", '');
    $desc1 = getContent($sections, 'services', "services-list-1_service_{$serviceId}_desc_1", '');
    $desc2 = getContent($sections, 'services', "services-list-1_service_{$serviceId}_desc_2", '');

    // Benefits
    $betit = getContent($sections, 'services', "services-list-1_service_{$serviceId}_betit", '');
    $bedes = getContent($sections, 'services', "services-list-1_service_{$serviceId}_bedes", '');
    $benefitCount = (int)getContent($sections, 'services', "services-list-1_service_{$serviceId}_benefit_count", '0');
    $benefits = [];
    for ($i = 1; $i <= $benefitCount; $i++) {
        $benefits[$i] = getContent($sections, 'services', "services-list-1_service_{$serviceId}_benefit_{$i}", '');
    }

    // FAQs
    $faqSubTitle = getContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_subtitle", '');
    $fatit = getContent($sections, 'services', "services-list-1_service_{$serviceId}_fatit", '');
    $faqCount = (int)getContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_count", '0');
    $faqs = [];
    for ($i = 1; $i <= $faqCount; $i++) {
        $faqs[$i] = [
            'question' => getContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_{$i}_question", ''),
            'answer' => getContent($sections, 'services', "services-list-1_service_{$serviceId}_faq_{$i}_answer", '')
        ];
    }

    // CTA
    $ctaText = getContent($sections, 'services', "services-list-1_service_{$serviceId}_cta_text", '');
    $ctaEmail = getContent($sections, 'services', "services-list-1_service_{$serviceId}_cta_email", '');
    $ctaBtnText = getContent($sections, 'services', "services-list-1_service_{$serviceId}_cta_btn_text", '');
@endphp

<x-page-header title="Edit <span>{{ $serviceTitle }}</span>">
    <li class="breadcrumb-item"><a href="{{ route('admin.pages.services') }}">Services</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $serviceTitle }}</li>
</x-page-header>

<div class="service-detail-editor vcms-editable-section" data-section="services" data-section-id="2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('admin.pages.services') }}" class="btn" style="background: #252525; color: #e0e0e0; border: 1px solid #3a3a3a; padding: 10px 20px; border-radius: 8px;">
                        <i class="fa-solid fa-arrow-left"></i> Back to Services
                    </a>
                </div>

                <!-- Service Detail Form -->
                <div class="vcms-editable-item position-relative" data-edit-type="service-detail" data-item-id="services-list-1"
                     data-service-id="{{ $serviceId }}"
                     data-title="{{ $serviceTitle }}"
                     data-desc="{{ $serviceDesc }}"
                     data-image="{{ $serviceImage }}"
                     data-link="{{ $serviceLink }}"
                     data-feature-image="{{ $featureImage }}"
                     data-desc1="{{ $desc1 }}"
                     data-desc2="{{ $desc2 }}"
                     data-betit="{{ $betit }}"
                     data-bedes="{{ $bedes }}"
                     data-benefit-count="{{ $benefitCount }}"
                     @for ($b = 1; $b <= $benefitCount; $b++)
                     data-benefit-{{ $b }}="{{ $benefits[$b] ?? '' }}"
                     @endfor
                     data-faq-subtitle="{{ $faqSubTitle }}"
                     data-fatit="{{ $fatit }}"
                     data-faq-count="{{ $faqCount }}"
                     @for ($f = 1; $f <= $faqCount; $f++)
                     data-faq-{{ $f }}-question="{{ $faqs[$f]['question'] ?? '' }}"
                     data-faq-{{ $f }}-answer="{{ $faqs[$f]['answer'] ?? '' }}"
                     @endfor
                     data-cta-text="{{ $ctaText }}"
                     data-cta-email="{{ $ctaEmail }}"
                     data-cta-btn-text="{{ $ctaBtnText }}"
                >
                    <div class="vcms-edit-indicator" title="Edit Service Details" 
                         style="position: absolute; top: 15px; right: 15px; z-index: 100; 
                                background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); 
                                color: #161616; padding: 12px 20px; border-radius: 8px; 
                                cursor: pointer; font-weight: 600; font-size: 14px;
                                box-shadow: 0 4px 15px rgba(255, 204, 1, 0.4);
                                display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-pen"></i> Edit Service Details
                    </div>

                    <!-- Preview Card -->
                    <div class="service-preview" style="background: #1a1a1a; border-radius: 16px; overflow: hidden; border: 1px solid #3a3a3a;">
                        <!-- Header with Image -->
                        <div style="position: relative; height: 300px; overflow: hidden;">
                            <img src="{{ asset($serviceImage) }}" alt="{{ $serviceTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(transparent, rgba(0,0,0,0.9));">
                                <h2 style="color: #fff; font-size: 32px; margin: 0;">{{ $serviceTitle }}</h2>
                                <p style="color: #888; margin: 10px 0 0 0;">{{ $serviceDesc }}</p>
                            </div>
                        </div>

                        <!-- Content Preview -->
                        <div style="padding: 30px;">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-lg-8">
                                    <!-- Description -->
                                    @if($desc1 || $desc2)
                                    <div style="margin-bottom: 30px;">
                                        <h4 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-align-left"></i> Description</h4>
                                        @if($desc1)<p style="color: #ccc;">{{ Str::limit($desc1, 200) }}</p>@endif
                                        @if($desc2)<p style="color: #ccc;">{{ Str::limit($desc2, 200) }}</p>@endif
                                    </div>
                                    @endif

                                    <!-- Benefits Preview -->
                                    <div style="margin-bottom: 30px;">
                                        <h4 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-check-circle"></i> Benefits ({{ $benefitCount }} items)</h4>
                                        @if($benefitCount > 0)
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                @foreach($benefits as $benefit)
                                                    @if($benefit)
                                                    <span style="background: #252525; padding: 6px 12px; border-radius: 20px; font-size: 13px; color: #ccc;">{{ Str::limit($benefit, 30) }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <p style="color: #666; font-style: italic;">No benefits added yet. Click edit to add.</p>
                                        @endif
                                    </div>

                                    <!-- FAQs Preview -->
                                    <div>
                                        <h4 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-circle-question"></i> FAQs ({{ $faqCount }} items)</h4>
                                        @if($faqCount > 0)
                                            @foreach($faqs as $index => $faq)
                                                @if($faq['question'])
                                                <div style="background: #252525; padding: 15px; border-radius: 10px; margin-bottom: 10px;">
                                                    <strong style="color: #fff;">Q: {{ Str::limit($faq['question'], 80) }}</strong>
                                                    @if($faq['answer'])<p style="color: #888; font-size: 13px; margin: 8px 0 0 0;">A: {{ Str::limit($faq['answer'], 100) }}</p>@endif
                                                </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <p style="color: #666; font-style: italic;">No FAQs added yet. Click edit to add.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Right Column - Sidebar -->
                                <div class="col-lg-4">
                                    <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a;">
                                        <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-envelope"></i> Sidebar CTA</h5>
                                        @if($ctaText || $ctaEmail)
                                            <p style="color: #ccc;">{{ $ctaText ?: 'No CTA text' }}</p>
                                            <p style="color: #888; font-size: 13px;"><i class="fa-solid fa-envelope"></i> {{ $ctaEmail ?: 'No email' }}</p>
                                            <p style="color: #888; font-size: 13px;"><i class="fa-solid fa-mouse-pointer"></i> {{ $ctaBtnText ?: 'No button text' }}</p>
                                        @else
                                            <p style="color: #666; font-style: italic;">No CTA configured. Click edit to add.</p>
                                        @endif
                                    </div>

                                    <div style="background: #252525; padding: 20px; border-radius: 12px; border: 1px solid #3a3a3a; margin-top: 20px;">
                                        <h5 style="color: #ffcc01; margin-bottom: 15px;"><i class="fa-solid fa-link"></i> Service Link</h5>
                                        <p style="color: #888; font-size: 13px; word-break: break-all;">{{ $serviceLink ?: 'Default link' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/admin-vcms.js') }}"></script>
@endpush
