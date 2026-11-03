@extends('layouts.app')

@section('title', 'Dikelolain - Pricing')

@php
    // Helper function to get content from database
    if (!function_exists('getPricingContent')) {
        function getPricingContent($sections, $sectionKey, $contentKey, $default = '') {
            $section = collect($sections)->firstWhere('key', $sectionKey);
            if (!$section || !isset($section['contents'])) {
                return $default;
            }
            $content = collect($section['contents'])->firstWhere('key', $contentKey);
            return $content ? $content['value'] : $default;
        }
    }

    // Page Header data
    $headerTitle = getPricingContent($sections, 'pricing-header', 'pricing-header-1_title', 'Pricing <span>plan</span>');
    $headerBreadcrumb = getPricingContent($sections, 'pricing-header', 'pricing-header-1_breadcrumb', 'Pricing');

    // Default plans structure
    $defaultPlans = [
        [
            'id' => 'basic',
            'name' => 'basic plan',
            'price' => 'Rp 300 Rb',
            'period' => '/per month',
            'highlighted' => false,
            'btn_text' => "let's get started",
            'btn_link' => '#',
            'features' => [
                'Desain Grafis (Feed, Reels, Story)',
                'Desain Interaktif',
                'Strategi Konten & Social Media Management'
            ]
        ],
        [
            'id' => 'standard',
            'name' => 'standard plan',
            'price' => 'Rp 500 Rb',
            'period' => '/per month',
            'highlighted' => true,
            'btn_text' => "let's get started",
            'btn_link' => '#',
            'features' => [
                'Konten Video & Foto untuk Instagram & TikTok',
                'Desain Grafis (Feed, Reels, Story)',
                'Strategi Konten & Social Media Management'
            ]
        ],
        [
            'id' => 'premium',
            'name' => 'premium plan',
            'price' => 'Rp 1 Jt',
            'period' => '/per month',
            'highlighted' => false,
            'btn_text' => "let's get started",
            'btn_link' => '#',
            'features' => [
                'Konten Video & Foto untuk Instagram & TikTok',
                'Desain Grafis (Feed, Reels, Story)',
                'Strategi Konten & Social Media Management',
                'Ads kreatif untuk Meta (Instagram & FB Ads)',
                'Paket manajemen konten full (1 bulan ke depan ready!)'
            ]
        ]
    ];

    // Benefits data
    $benefits = [];
    for ($i = 1; $i <= 3; $i++) {
        $defaultIcons = ['images/icon-pricing-benefit-1.svg', 'images/icon-pricing-benefit-2.svg', 'images/icon-pricing-benefit-3.svg'];
        $defaultTexts = ['Get 30 day free trial', 'No any hidden fees pay', 'You can cancel anytime'];
        $benefits[] = [
            'icon' => getPricingContent($sections, 'pricing', "pricing-benefits_benefit_{$i}_icon", $defaultIcons[$i-1]),
            'text' => getPricingContent($sections, 'pricing', "pricing-benefits_benefit_{$i}_text", $defaultTexts[$i-1])
        ];
    }
@endphp

@section('content')

    <!-- Page Header -->
    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>

    <!-- Page Pricing Start -->
    <div class="page-pricing">
        <div class="container">
            <div class="row">
                @foreach($defaultPlans as $index => $plan)
                    @php
                        $planId = $plan['id'];
                        $planName = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_name", $plan['name']);
                        $planPrice = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_price", $plan['price']);
                        $planPeriod = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_period", $plan['period']);
                        $planHighlighted = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_highlighted", $plan['highlighted'] ? '1' : '0');
                        $planBtnText = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_btn_text", $plan['btn_text']);
                        $planBtnLink = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_btn_link", $plan['btn_link']);

                        // Get features from database
                        $features = [];
                        for ($i = 1; $i <= 10; $i++) {
                            $feature = getPricingContent($sections, 'pricing', "pricing-plans_plan_{$planId}_feature_{$i}", '');
                            if (!empty($feature)) {
                                $features[] = $feature;
                            }
                        }
                        // Fallback to default features if empty
                        if (empty($features)) {
                            $features = $plan['features'];
                        }

                        $delayValue = $index * 0.2;
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <!-- Pricing Box Start -->
                        <div class="pricing-box {{ $planHighlighted == '1' ? 'highlighted-box' : '' }} wow fadeInUp" @if($delayValue > 0) data-wow-delay="{{ $delayValue }}s" @endif>
                            <!-- Pricing Header Start -->
                            <div class="pricing-header">
                                <h3>{{ $planName }}</h3>
                                <h2>{{ $planPrice }} <sub>{{ $planPeriod }}</sub></h2>
                            </div>
                            <!-- Pricing Header End -->

                            <!-- Pricing Box Body Start -->
                            <div class="pricing-body">
                                <!-- Pricing List Title Start -->
                                <div class="pricing-list-title">
                                    <h3>What included?</h3>
                                </div>
                                <!-- Pricing List Title End -->

                                <!-- Pricing List Start -->
                                <div class="pricing-list">
                                    <ul>
                                        @foreach($features as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Pricing List End -->
                            </div>
                            <!-- Pricing Box Body End -->

                            <!-- Pricing Button Start -->
                            <div class="pricing-btn">
                                <a href="{{ $planBtnLink }}" class="btn-default btn-highlighted">{{ $planBtnText }}</a>
                            </div>
                            <!-- Pricing Button End -->
                        </div>
                        <!-- Pricing Box End -->
                    </div>
                @endforeach

                <div class="col-lg-12">
                    <!-- Pricing Benefit List Start -->
                    <div class="pricing-benefit-list wow fadeInUp" data-wow-delay="0.6s">
                        <ul>
                            @foreach($benefits as $benefit)
                                <li><img src="{{ asset($benefit['icon']) }}" alt="">{{ $benefit['text'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Pricing Benefit List End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Pricing End -->

    <!-- CTA Section Start -->
    {{-- <div class="cta-section bg-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- CTA Box Start -->
                    <div class="cta-box">
                        <div class="cta-counter-content-box">
                            <!-- CTA Box Content Start -->
                            <div class="cta-box-content wow fadeInUp">
                                <p>We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.</p>
                            </div>
                            <!-- CTA Box Content End -->

                            <!-- CTA Counter Box Start -->
                            <div class="cta-counter-box">
                                <!-- CTA Counter Item Start -->
                                <div class="cta-counter-item">
                                    <h3><span class="counter">45</span>+</h3>
                                    <p>project completed</p>
                                </div>
                                <!-- CTA Counter Item End -->

                                <!-- CTA Counter Item Start -->
                                <div class="cta-counter-item">
                                    <h3><span class="counter">15</span>+</h3>
                                    <p>years of experience</p>
                                </div>
                                <!-- CTA Counter Item End -->

                                <!-- CTA Counter Item Start -->
                                <div class="cta-counter-item">
                                    <h3><span class="counter">100</span>%</h3>
                                    <p>client satisfaction</p>
                                </div>
                                <!-- CTA Counter Item End -->
                            </div>
                            <!-- CTA Counter Box End -->
                        </div>

                        <!-- CTA Contact Circle Start -->
                        <div class="cta-contact-circle">
                            <a href="contact.html"><img src="images/cta-contact-circle.png" alt=""></a>
                        </div>
                        <!-- CTA Contact Circle End -->
                    </div>
                    <!-- CTA Box End -->
                </div>
            </div>
        </div>
    </div> --}}
    <!-- CTA Section End -->

    <!-- Creative Tools Section Start -->
    <div class="creative-tools service-creative-tools">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-9">
                    <div class="section-title">
                        @php
                            $toolsSubTitle = getPricingContent($sections, 'creative-tools', 'tools_sub_title', 'creative toolkit');
                            $toolsMainTitle = getPricingContent($sections, 'creative-tools', 'tools_main_title', 'Our toolbox for <span>innovation</span>');
                        @endphp
                        <h3 class="wow fadeInUp">{{ $toolsSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $toolsMainTitle !!}</h2>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                        @php
                            $toolsBtnText = getPricingContent($sections, 'creative-tools', 'tools_btn_text', 'let\'s get started !');
                            $toolsBtnLink = getPricingContent($sections, 'creative-tools', 'tools_btn_link', route('contact'));
                        @endphp
                        <a href="{{ $toolsBtnLink }}" class="btn-default">{{ $toolsBtnText }}</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="creative-tools-box">
                        @php
                            // Get tools from database dynamically
                            $tools = [];
                            $toolsSection = collect($sections)->firstWhere('key', 'creative-tools');

                            if ($toolsSection && isset($toolsSection['contents'])) {
                                $toolIds = [];
                                foreach ($toolsSection['contents'] as $content) {
                                    if (preg_match('/tools-list-1_tool_(.+)_name$/', $content['key'], $matches)) {
                                        $toolIds[$matches[1]] = $content['value'];
                                    }
                                }
                                foreach ($toolIds as $id => $name) {
                                    $tools[] = ['id' => $id, 'name' => $name];
                                }
                            }

                            // Fallback to defaults if no data in database
                            if (empty($tools)) {
                                $defaultTools = [
                                    ['id' => '0', 'name' => 'figma', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-1.svg'],
                                    ['id' => '1', 'name' => 'framer', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-2.svg'],
                                    ['id' => '2', 'name' => 'illustrator', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-3.svg'],
                                    ['id' => '3', 'name' => 'adobe xd', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-4.svg'],
                                    ['id' => '4', 'name' => 'indesign', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-5.svg'],
                                    ['id' => '5', 'name' => 'photoshop', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-6.svg'],
                                    ['id' => '6', 'name' => 'lightroom', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-7.svg'],
                                    ['id' => '7', 'name' => 'incopy', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-8.svg'],
                                ];
                                $tools = $defaultTools;
                            }
                        @endphp

                        @foreach ($tools as $index => $tool)
                            @php
                                $toolId = $tool['id'];
                                $toolName = getPricingContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_name", $tool['name'] ?? 'Tool');
                                $toolCat = getPricingContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_cat", $tool['cat'] ?? 'Design tool');
                                $toolIcon = getPricingContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_icon", $tool['icon'] ?? 'images/icon-creative-tools-1.svg');
                            @endphp
                            <div class="creative-tool-item wow fadeInUp" data-wow-delay="{{ 0.2 * ($index % 4) }}s">
                                <div class="icon-box">
                                    <img src="{{ asset($toolIcon) }}" alt="{{ $toolName }} Icon">
                                </div>
                                <div class="creative-tool-item-content">
                                    <h3>{{ $toolName }}</h3>
                                    <p>{{ $toolCat }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@endpush
