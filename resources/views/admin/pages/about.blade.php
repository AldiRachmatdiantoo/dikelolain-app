@extends('admin.layouts.admin')

@section('title', 'Edit About Page')
@section('page-title', 'About Page - VCMS Editor')

@php
    $previewUrl = route('about');
@endphp

@section('content')

@php
if (!function_exists('getAboutContent')) {
    function getAboutContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }
}

$headerTitle = getAboutContent($sections, 'about-header', 'about-header-1_title', 'About our <span>journey</span>');
$headerBreadcrumb = getAboutContent($sections, 'about-header', 'about-header-1_breadcrumb', 'about us');
@endphp

<!-- Page Header Section -->
<div class="page-header bg-section vcms-editable-section" data-section="about-header" data-item-id="about-header-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="page-header">
            <i class="fa-solid fa-pen"></i> Edit Header
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="about-header-1"
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

<!-- About Us Section -->
<div class="page-about-us vcms-editable-section" data-section="about-us" data-item-id="about-us-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="about-section">
            <i class="fa-solid fa-pen"></i> Edit About Section
        </button>
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                @php
                    $aboutImg = getAboutContent($sections, 'about-us', 'about-us-1_image', 'images/about-img.jpg');
                @endphp
                <div class="about-us-img">
                    <figure class="image-anime reveal">
                        <img src="{{ asset($aboutImg) }}" alt="" id="about-main-image">
                    </figure>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="about-us-content">
                    <div class="section-title">
                        @php
                            $aboutSubTitle = getAboutContent($sections, 'about-us', 'about-us-1_sub_title', 'about us');
                            $aboutMainTitle = getAboutContent($sections, 'about-us', 'about-us-1_main_title', 'A team of <span>creative thinkers</span>');
                            $aboutDesc = getAboutContent($sections, 'about-us', 'about-us-1_description', 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.');
                        @endphp
                        <h3>{{ $aboutSubTitle }}</h3>
                        <h2>{!! $aboutMainTitle !!}</h2>
                        <p>{{ $aboutDesc }}</p>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="about-us-info-list">
                                @php
                                    // Get list items dynamically from database
                                    $infoItems = [];
                                    $defaultItems = [
                                        'Creativity and Innovation',
                                        'Client-Centricity',
                                        'Integrity and Transparency',
                                        'Excellence and Quality',
                                    ];
                                    
                                    if (isset($sections['about-us'])) {
                                        foreach ($sections['about-us']->contents as $content) {
                                            if (preg_match('/^about-us-1_list_item_(\d+)$/', $content->key, $matches)) {
                                                $infoItems[(int)$matches[1]] = $content->value;
                                            }
                                        }
                                        ksort($infoItems);
                                    }
                                    
                                    // Use defaults if no items in database
                                    if (empty($infoItems)) {
                                        $infoItems = $defaultItems;
                                    } else {
                                        $infoItems = array_values($infoItems);
                                    }
                                @endphp
                                <ul>
                                    @foreach($infoItems as $index => $item)
                                        @if(!empty($item))
                                            <li data-list-index="{{ $index }}">{{ $item }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="about-contact-now">
                                <a href="{{ route('contact') }}"><img src="{{ asset('images/contact-circle.png') }}" alt=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section (sama dengan Home Page) -->
<div class="cta-section bg-section vcms-editable-section" data-section="cta" data-item-id="cta-section">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="cta">
            <i class="fa-solid fa-pen"></i> Edit CTA Section
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="cta-box">
                    <div class="cta-counter-content-box">
                        <div class="cta-box-content vcms-editable-item" data-edit-type="rich-text" data-item-id="cta">
                            <div class="vcms-edit-indicator-small" title="Edit Description">
                                <i class="fa-solid fa-file-alt"></i>
                            </div>
                            @php
                                $ctaDesc = getAboutContent($sections, 'cta', 'cta_text_content', 'We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.');
                            @endphp
                            <p>{{ $ctaDesc }}</p>
                        </div>
                        <div class="cta-counter-box vcms-editable-item" data-edit-type="counter-list" data-item-id="cta-counters">
                            <div class="vcms-edit-indicator-small" title="Manage Counters">
                                <i class="fa-solid fa-list-ol"></i>
                            </div>
                            @php
                                $defaultCounters = [
                                    ['num' => '45', 'sym' => '+', 'label' => 'project completed'],
                                    ['num' => '15', 'sym' => '+', 'label' => 'years of experience'],
                                    ['num' => '100', 'sym' => '%', 'label' => 'client satisfaction'],
                                ];
                            @endphp
                            @foreach ($defaultCounters as $index => $counter)
                                @php
                                    $countNum = getAboutContent($sections, 'cta', "cta-counters_cnt_{$index}_num", $counter['num']);
                                    $countSym = getAboutContent($sections, 'cta', "cta-counters_cnt_{$index}_sym", $counter['sym']);
                                    $countLabel = getAboutContent($sections, 'cta', "cta-counters_cnt_{$index}_label", $counter['label']);
                                @endphp
                                <div class="cta-counter-item">
                                    <h3><span class="counter">{{ $countNum }}</span>{{ $countSym }}</h3>
                                    <p>{{ $countLabel }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="cta-contact-circle">
                        @php
                            $ctaCircleImg = getAboutContent($sections, 'cta', 'cta-circle-img_image_file', 'images/cta-contact-circle.png');
                            $ctaCircleLink = getAboutContent($sections, 'cta', 'cta-circle-img_link_url', route('contact'));
                        @endphp
                        <a href="{{ $ctaCircleLink }}"><img src="{{ asset($ctaCircleImg) }}" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="height: 80px;"></div>
<!-- Our Vision Mission Section -->
<div class="our-vision-mission bg-section vcms-editable-section" data-section="vision-mission" data-item-id="vision-mission-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="vision-mission">
            <i class="fa-solid fa-pen"></i> Edit Vision & Mission
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Vision Mission Box Start -->
                <div class="vision-mission-box">
                    @php
                        $visionMissionItems = [
                            [
                                'key' => 'vision',
                                'default_sub_title' => 'our vision',
                                'default_title' => 'Driving the Future of <span>Creativity</span>',
                                'default_desc' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.',
                                'default_image' => 'images/our-vision-img.jpg',
                                'default_list' => ['Creativity and Innovation', 'Client-Centricity', 'Integrity and Transparency', 'Excellence and Quality'],
                            ],
                            [
                                'key' => 'mission',
                                'default_sub_title' => 'our mission',
                                'default_title' => 'Bringing ideas to life through <span>creativity</span>',
                                'default_desc' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.',
                                'default_image' => 'images/our-mission-img.jpg',
                                'default_list' => ['Creativity and Innovation', 'Client-Centricity', 'Integrity and Transparency', 'Excellence and Quality'],
                            ],
                        ];
                    @endphp

                    @foreach($visionMissionItems as $item)
                        @php
                            $itemKey = $item['key'];
                            $subTitle = getAboutContent($sections, 'vision-mission', "vision-mission-1_{$itemKey}_sub_title", $item['default_sub_title']);
                            $mainTitle = getAboutContent($sections, 'vision-mission', "vision-mission-1_{$itemKey}_title", $item['default_title']);
                            $description = getAboutContent($sections, 'vision-mission', "vision-mission-1_{$itemKey}_description", $item['default_desc']);
                            $image = getAboutContent($sections, 'vision-mission', "vision-mission-1_{$itemKey}_image", $item['default_image']);

                            // Get list items dynamically from database
                            $listItems = [];
                            if (isset($sections['vision-mission'])) {
                                foreach ($sections['vision-mission']->contents as $content) {
                                    // Match pattern: vision-mission-1_vision_list_0, vision-mission-1_mission_list_1, etc.
                                    if (preg_match('/^vision-mission-1_' . $itemKey . '_list_(\d+)$/', $content->key, $matches)) {
                                        $listItems[(int)$matches[1]] = $content->value;
                                    }
                                }
                                ksort($listItems);
                            }
                            
                            // Use defaults if no items in database
                            if (empty($listItems)) {
                                $listItems = $item['default_list'];
                            } else {
                                $listItems = array_values($listItems);
                            }
                        @endphp
                        <!-- Vision Mission Item Start -->
                        <div class="vision-mission-item" data-type="{{ $itemKey }}">
                            <!-- Vision Mission image Start -->
                            <div class="vision-mission-image">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($image) }}" alt="{{ $itemKey }}">
                                </figure>
                            </div>
                            <!-- Vision Mission image End -->

                            <!-- Vision Mission Content Start -->
                            <div class="vision-mission-content">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h3>{{ $subTitle }}</h3>
                                    <h2 data-cursor="-opaque">{!! $mainTitle !!}</h2>
                                    <p>{{ $description }}</p>
                                </div>
                                <!-- Section Title End -->

                                <!-- Vision Mission List Start -->
                                <div class="vision-mission-list" data-list-type="{{ $itemKey }}">
                                    <ul>
                                        @foreach($listItems as $index => $listItem)
                                            @if(!empty($listItem))
                                                <li data-index="{{ $index }}">{{ $listItem }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Vision Mission List End -->
                            </div>
                            <!-- Vision Mission Content End -->
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

<!-- Team Section (Preview Only) -->
<div class="our-team" data-section="our-team">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-lg-8">
                <div class="section-title">
                    @php
                        $teamSubTitle = getAboutContent($sections, 'our-team', 'team_sub_title', 'our team');
                        $teamMainTitle = getAboutContent($sections, 'our-team', 'team_main_title', 'The minds behind <span>the magic</span>');
                    @endphp
                    <h3>{{ $teamSubTitle }}</h3>
                    <h2>{!! $teamMainTitle !!}</h2>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="section-btn">
                    @php
                        $teamBtnText = getAboutContent($sections, 'our-team', 'team_btn_text', 'all team members');
                        $teamBtnLink = getAboutContent($sections, 'our-team', 'team_btn_link', route('team'));
                    @endphp
                    <a href="{{ $teamBtnLink }}" class="btn-default">{{ $teamBtnText }}</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center py-4" style="background: rgba(255, 204, 1, 0.1); border-radius: 12px; border: 1px dashed #ffcc01;">
                <p style="color: #ffcc01; margin: 0;">
                    <i class="fa-solid fa-info-circle" style="margin-right: 8px;"></i>
                    Team members dapat diedit di halaman <a href="{{ route('admin.pages.team') }}" style="color: #fff; text-decoration: underline;">Team Page</a>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
