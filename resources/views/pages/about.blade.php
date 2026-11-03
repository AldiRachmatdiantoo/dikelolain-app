@extends('layouts.app')

@section('title', 'About Us - Dikelolain')

@php
if (!function_exists('getAboutContent')) {
    function getAboutContent($sections, $sectionKey, $contentKey, $default = '') {
        $section = collect($sections)->get($sectionKey);
        if (!$section || !isset($section->contents)) {
            return $default;
        }
        $content = $section->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }
}

// Page Header - uses about-header section
$headerTitle = getAboutContent($sections, 'about-header', 'about-header-1_title', 'About our <span>journey</span>');
$headerBreadcrumb = getAboutContent($sections, 'about-header', 'about-header-1_breadcrumb', 'About Us');


// About Us Section - uses about-us section with about-us-1_* keys (matching admin)
$aboutImg = getAboutContent($sections, 'about-us', 'about-us-1_image', 'images/about-img.jpg');
$aboutSubTitle = getAboutContent($sections, 'about-us', 'about-us-1_sub_title', 'about us');
$aboutMainTitle = getAboutContent($sections, 'about-us', 'about-us-1_main_title', 'A team of <span>creative thinkers</span>');
$aboutDesc = getAboutContent($sections, 'about-us', 'about-us-1_description', 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.');

// Get list items dynamically from database
$infoItems = [];
$defaultInfoItems = [
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
}

// Use defaults if no items in database
if (empty($infoItems)) {
    foreach ($defaultInfoItems as $idx => $item) {
        $infoItems[$idx] = $item;
    }
}
ksort($infoItems);
@endphp

@section('content')

    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{!! $headerBreadcrumb !!}</li>
    </x-page-header>
<div class="page-about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-us-img">
                        <figure class="image-anime reveal">
                            <img src="{{ asset($aboutImg) }}" alt="">
                        </figure>
                     </div>
                </div>

                <div class="col-lg-6">
                    <div class="about-us-content">
                        <div class="section-title">
                            <h3 class="wow fadeInUp">{{ $aboutSubTitle }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $aboutMainTitle !!}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $aboutDesc }}</p>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="about-us-info-list wow fadeInUp" data-wow-delay="0.4s">
                                    <ul>
                                        @foreach($infoItems as $item)
                                            @if(!empty($item))
                                                <li>{{ $item }}</li>
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
    <!-- Page About Us Section End -->

    <!-- CTA Section Start -->
    <div class="cta-section bg-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cta-box">
                        <div class="cta-counter-content-box">
                            <div class="cta-box-content wow fadeInUp">
                                @php
                                    $ctaDesc = getAboutContent($sections, 'cta', 'cta_text_content', 'We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.');
                                @endphp
                                <p>{{ $ctaDesc }}</p>
                            </div>
                            <div class="cta-counter-box">
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
                            <a href="{{ $ctaCircleLink }}"><img src="{{ asset($ctaCircleImg) }}" alt="Contact Circle"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CTA Section End -->

    <div class="our-team">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-8">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h3 class="wow fadeInUp">our team</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">The minds behind <span>the magic</span></h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-4">
                    <!-- Section Button Start -->
                    <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                        <a href="{{ route('team') }}" class="btn-default">all team members</a>
                    </div>
                    <!-- Section Button End -->
                </div>
            </div>

            <div class="row">
                @php
                    // Hitung jumlah member dari database
                    $teamCount = 0;
                    if (isset($sections['our-team'])) {
                        foreach ($sections['our-team']->contents as $content) {
                            if (preg_match('/^team-list-1_member_(\d+)_name$/', $content->key, $matches)) {
                                $teamCount = max($teamCount, (int)$matches[1] + 1);
                            }
                        }
                    }
                    // About page menampilkan maksimal 4 member
                    $displayCount = min($teamCount, 4);
                @endphp

                @if ($displayCount === 0)
                    <div class="col-12 text-center py-5">
                        <p style="color: #888;">No team members yet.</p>
                    </div>
                @endif

                @for ($index = 0; $index < $displayCount; $index++)
                    @php
                        $memberName = getAboutContent($sections, 'our-team', "team-list-1_member_{$index}_name", 'Team Member');
                        $memberRole = getAboutContent($sections, 'our-team', "team-list-1_member_{$index}_role", 'Position');
                        $memberImg = getAboutContent($sections, 'our-team', "team-list-1_member_{$index}_img", 'images/team-1.jpg');
                        $memberLink = route('pages.team.single', ['index' => $index]);
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="team-item wow fadeInUp" data-wow-delay="{{ 0.2 * $index }}s">
                            <div class="team-image">
                                <a href="{{ $memberLink }}" data-cursor-text="View">
                                    <figure class="image-anime">
                                        <img src="{{ asset($memberImg) }}" alt="{{ $memberName }}">
                                    </figure>
                                </a>
                            </div>
                            <div class="team-body">
                                <div class="team-content">
                                    <h3><a href="{{ $memberLink }}">{{ $memberName }}</a></h3>
                                </div>
                                <div class="team-social-list">
                                    <span>{{ $memberRole }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Our Achievements Section Start -->
    <div class="our-achievements">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="our-achievements-content">
                        <div class="section-title">
                            @php
                                $achievementsSubTitle = getAboutContent($sections, 'achievements', 'achievements_sub_title', 'our achievements');
                                $achievementsMainTitle = getAboutContent($sections, 'achievements', 'achievements_main_title', 'Proud moments & <span>milestones</span>');
                                $achievementsDescription = getAboutContent($sections, 'achievements', 'achievements_description', 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.');
                            @endphp
                            <h3 class="wow fadeInUp">{{ $achievementsSubTitle }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $achievementsMainTitle !!}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $achievementsDescription }}</p>
                        </div>
                        <div class="achievements-content-btn wow fadeInUp" data-wow-delay="0.4s">
                            @php
                                $achievementsBtnText = getAboutContent($sections, 'achievements', 'achievements_btn_text', 'let\'s get started !');
                                $achievementsBtnLink = getAboutContent($sections, 'achievements', 'achievements_btn_link', route('contact'));
                            @endphp
                            <a href="{{ $achievementsBtnLink }}" class="btn-default">{{ $achievementsBtnText }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="our-achievements-box wow fadeInUp">
                        @php
                            // Get achievements from database dynamically
                            $achievements = [];
                            $achievementsSection = collect($sections)->firstWhere('key', 'achievements');

                            if ($achievementsSection && isset($achievementsSection['contents'])) {
                                $achievementIds = [];
                                foreach ($achievementsSection['contents'] as $content) {
                                    if (preg_match('/achievement-list-1_ach_(.+)_name$/', $content['key'], $matches)) {
                                        $achievementIds[$matches[1]] = $content['value'];
                                    }
                                }
                                foreach ($achievementIds as $id => $name) {
                                    $achievements[] = ['id' => $id, 'name' => $name];
                                }
                            }

                            // Fallback to defaults if no data in database
                            if (empty($achievements)) {
                                $defaultAchievements = [
                                    ['id' => '0', 'year' => '2015 - 2016', 'name' => 'Best Design Award', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
                                    ['id' => '1', 'year' => '2016 - 2017', 'name' => 'Dribble Winner', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
                                    ['id' => '2', 'year' => '2017 - 2018', 'name' => 'Design Of The Year', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
                                    ['id' => '3', 'year' => '2017 - 2018', 'name' => 'Graphic Design Winner', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
                                    ['id' => '4', 'year' => '2018 - 2019', 'name' => 'Awward Winner', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
                                    ['id' => '5', 'year' => '2019 - 2020', 'name' => 'Best Jury Award', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
                                ];
                                $achievements = $defaultAchievements;
                            }
                        @endphp

                        @if(count($achievements) == 0)
                            <div class="text-center py-5" style="color: #888;">
                                <p>No achievements yet.</p>
                            </div>
                        @endif

                        @foreach ($achievements as $index => $achievement)
                            @php
                                $achId = $achievement['id'];
                                $achYear = getAboutContent($sections, 'achievements', "achievement-list-1_ach_{$achId}_year", $achievement['year'] ?? '2024 - 2025');
                                $achName = getAboutContent($sections, 'achievements', "achievement-list-1_ach_{$achId}_name", $achievement['name'] ?? 'Achievement');
                                $achDesc = getAboutContent($sections, 'achievements', "achievement-list-1_ach_{$achId}_desc", $achievement['desc'] ?? 'Achievement description.');
                            @endphp
                            <div class="achievements-item">
                                <h3>{{ $achYear }}</h3>
                                <h2>{{ $achName }}</h2>
                                <p>{{ $achDesc }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Our Achievements Section End -->

    <!-- Our Vision Mission Section Start -->
    <div class="our-vision-mission bg-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Vision Mission Box Start -->
                    <div class="vision-mission-box">
                        @php
                            // Vision Mission items configuration
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
                                        if (preg_match('/^vision-mission-1_' . $itemKey . '_list_(\d+)$/', $content->key, $matches)) {
                                            $listItems[(int)$matches[1]] = $content->value;
                                        }
                                    }
                                }

                                // Use defaults if no items in database
                                if (empty($listItems)) {
                                    foreach ($item['default_list'] as $idx => $listItem) {
                                        $listItems[$idx] = $listItem;
                                    }
                                }
                                ksort($listItems);
                            @endphp
                            <!-- Vision Mission Item Start -->
                            <div class="vision-mission-item">
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
                                        <h3 class="wow fadeInUp">{{ $subTitle }}</h3>
                                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $mainTitle !!}</h2>
                                        <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $description }}</p>
                                    </div>
                                    <!-- Section Title End -->

                                    <!-- Vision Mission List Start -->
                                    <div class="vision-mission-list wow fadeInUp" data-wow-delay="0.4s">
                                        <ul>
                                            @foreach($listItems as $listItem)
                                                @if(!empty($listItem))
                                                    <li>{{ $listItem }}</li>
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
                        <h3 class="wow fadeInUp">{{ $settings->get('faqs.header.breadcrumb') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('faqs.header.title') !!}</h2>
                    </div>
                    <!-- Section Title End -->
                </div>

                <div class="col-lg-3">
                    <!-- Section Button Start -->
                    <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
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
                                    $delayValue = $index * 0.2;
                                    $isFirst = $index === 0;
                                @endphp
                                <!-- FAQ Item Start -->
                                <div class="accordion-item wow fadeInUp" @if($delayValue > 0) data-wow-delay="{{ $delayValue }}s" @endif>
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
