@extends('admin.layouts.admin')

@section('title', 'Edit Pricing Page')
@section('page-title', 'Pricing Page - VCMS Editor')

@php
    $previewUrl = route('pricing');
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

// Page Header data
$headerTitle = getContent($sections, 'pricing-header', 'pricing-header-1_title', 'Pricing <span>plan</span>');
$headerBreadcrumb = getContent($sections, 'pricing-header', 'pricing-header-1_breadcrumb', 'Pricing');

$defaultPlans = [
    [
        'id' => 'basic',
        'name' => 'basic plan',
        'price' => 'Rp 300 Rb',
        'period' => '/per month',
        'highlighted' => false,
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
        'features' => [
            'Konten Video & Foto untuk Instagram & TikTok',
            'Desain Grafis (Feed, Reels, Story)',
            'Strategi Konten & Social Media Management',
            'Ads kreatif untuk Meta (Instagram & FB Ads)',
            'Paket manajemen konten full (1 bulan ke depan ready!)'
        ]
    ]
];
@endphp

<!-- Page Header Section -->
<div class="page-header bg-section vcms-editable-section" data-section="pricing-header" data-item-id="pricing-header-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="page-header">
            <i class="fa-solid fa-pen"></i> Edit Header
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="pricing-header-1"
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

<div style="height: 80px"></div>

<!-- Pricing Section -->
<div class="vcms-editable-section" data-section="pricing" data-item-id="pricing-plans">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="pricing-section">
            <i class="fa-solid fa-pen"></i> Edit Pricing Plans
        </button>
    </div>
    <div style="height: 80px;"></div>
    <div class="container">
        <div class="row">
            @foreach($defaultPlans as $index => $plan)
                @php
                    $planId = $plan['id'];
                    $planName = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_name", $plan['name']);
                    $planPrice = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_price", $plan['price']);
                    $planPeriod = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_period", $plan['period']);
                    $planHighlighted = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_highlighted", $plan['highlighted'] ? '1' : '0');
                    $planBtnText = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_btn_text", "let's get started");
                    $planBtnLink = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_btn_link", '#');

                    // Get features
                    $features = [];
                    for ($i = 1; $i <= 10; $i++) {
                        $feature = getContent($sections, 'pricing', "pricing-plans_plan_{$planId}_feature_{$i}", '');
                        if (!empty($feature)) {
                            $features[] = $feature;
                        }
                    }
                    if (empty($features)) {
                        $features = $plan['features'];
                    }
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-box {{ $planHighlighted == '1' ? 'highlighted-box' : '' }} vcms-editable-item"
                         data-edit-type="pricing-plan" data-item-id="pricing-plan-{{ $planId }}">
                        <div class="vcms-edit-indicator-small"><i class="fa-solid fa-pen"></i></div>

                        <div class="pricing-header">
                            <h3>{{ $planName }}</h3>
                            <h2>{{ $planPrice }} <sub>{{ $planPeriod }}</sub></h2>
                        </div>

                        <div class="pricing-body">
                            <div class="pricing-list-title">
                                <h3>What included?</h3>
                            </div>
                            <div class="pricing-list">
                                <ul>
                                    @foreach($features as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="pricing-btn">
                            <a href="{{ $planBtnLink }}" class="btn-default btn-highlighted">{{ $planBtnText }}</a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Benefits Section -->
            <div class="col-lg-12">
                <div class="pricing-benefit-list vcms-editable-item" data-edit-type="pricing-benefits" data-item-id="pricing-benefits">
                    <div class="vcms-edit-indicator-small"><i class="fa-solid fa-pen"></i></div>
                    @php
                        $benefit1Icon = getContent($sections, 'pricing', 'pricing-benefits_benefit_1_icon', 'images/icon-pricing-benefit-1.svg');
                        $benefit1Text = getContent($sections, 'pricing', 'pricing-benefits_benefit_1_text', 'Get 30 day free trial');
                        $benefit2Icon = getContent($sections, 'pricing', 'pricing-benefits_benefit_2_icon', 'images/icon-pricing-benefit-2.svg');
                        $benefit2Text = getContent($sections, 'pricing', 'pricing-benefits_benefit_2_text', 'No any hidden fees pay');
                        $benefit3Icon = getContent($sections, 'pricing', 'pricing-benefits_benefit_3_icon', 'images/icon-pricing-benefit-3.svg');
                        $benefit3Text = getContent($sections, 'pricing', 'pricing-benefits_benefit_3_text', 'You can cancel anytime');
                    @endphp
                    <ul>
                        <li><img src="{{ asset($benefit1Icon) }}" alt="">{{ $benefit1Text }}</li>
                        <li><img src="{{ asset($benefit2Icon) }}" alt="">{{ $benefit2Text }}</li>
                        <li><img src="{{ asset($benefit3Icon) }}" alt="">{{ $benefit3Text }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="height: 80px"></div>


@endsection
