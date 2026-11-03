@extends('layouts.app')

@php
// Helper function to get content from database
function getContentSingle($sections, $sectionKey, $contentKey, $default = '') {
    if (!isset($sections[$sectionKey])) return $default;
    $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
    return $content ? $content->value : $default;
}

// Detect which key prefix is used (backward compatibility)
$keyPrefix = 'team-list-1'; // default prefix
if (isset($sections['our-team'])) {
    foreach ($sections['our-team']->contents as $content) {
        if (preg_match('/^team-list-1_member_(\d+)_name$/', $content->key)) {
            $keyPrefix = 'team-list-1';
            break;
        } elseif (preg_match('/^default_member_(\d+)_name$/', $content->key)) {
            $keyPrefix = 'default';
            break;
        }
    }
}

// Get member data from database
$memberName = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_name", 'Team Member');
$memberRole = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_role", 'Position');
$memberImg = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_img", 'images/team-1.jpg');
$memberExperience = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_experience", '5 years');

// About
$memberAbout = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_about", "With years of experience in design and creativity, {$memberName} leads the team with a passion for crafting innovative and impactful visual solutions.");

// Skills
$memberSkillsDesc = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_skills_desc", "{$memberName} excels in creative problem-solving, user-centered design, branding, and UI/UX development.");
$skillsCount = (int) getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_skills_count", '1');
$memberSkillsList = [];
for ($i = 1; $i <= $skillsCount; $i++) {
    $memberSkillsList[] = [
        'title' => getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_skill_{$i}_title", ''),
        'percent' => getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_skill_{$i}_percent", '80')
    ];
}

// Expertise
$memberExpertiseDesc = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_expertise_desc", "{$memberName} is a seasoned professional with a keen eye for aesthetics and a deep understanding of creative strategies.");
$expertiseCount = (int) getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_expertise_count", '4');
$memberExpertiseList = [];
for ($i = 1; $i <= $expertiseCount; $i++) {
    $memberExpertiseList[] = getContentSingle($sections, 'our-team', "{$keyPrefix}_member_{$index}_expertise_{$i}", '');
}

// Split name for header styling (first word normal, rest in span)
$nameParts = explode(' ', $memberName, 2);
$firstName = $nameParts[0];
$lastName = isset($nameParts[1]) ? $nameParts[1] : '';
@endphp

@section('title', $memberName . ' - Our Team - Dikelolain')

@section('content')

    <x-page-header title="{{ $firstName }} <span>{{ $lastName }}</span>">
        <li class="breadcrumb-item"><a href="{{ route('team') }}">our team</a></li>
        <li class="breadcrumb-item active" arial-current="page">{{ $memberName }}</li>
    </x-page-header>

<div class="page-team-single">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Team Single Box Start -->
                    <div class="team-single-box">
                        <!-- Team About Box Start -->
                        <div class="team-about-box">
                            <!-- Team Single Image Start -->
                            <div class="team-single-image">
                                <figure class="image-anime reveal">
                                    <img src="{{ asset($memberImg) }}" alt="{{ $memberName }}">
                                </figure>
                            </div>
                            <!-- Team Single Image End -->

                            <!-- Team About Contact Start -->
                            <div class="team-about-content">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">About <span>me</span></h2>
                                    <p class="wow fadeInUp">{{ $memberAbout }}</p>
                                </div>
                                <!-- Section Title End -->

                                <!-- Team Contact List Start -->
                                <div class="team-contact-list">
                                    <!-- Team Member Box Start -->
                                    <div class="team-contact-box wow fadeInUp" data-wow-delay="0.2s">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-team-contact-1.svg') }}" alt="">
                                        </div>

                                        <div class="team-contact-content">
                                            <h3>position</h3>
                                            <p>{{ $memberRole }}</p>
                                        </div>
                                    </div>
                                    <!-- Team Member Box End -->

                                    <!-- Team Member Box Start -->
                                    <div class="team-contact-box wow fadeInUp" data-wow-delay="0.6s">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-team-contact-3.svg') }}" alt="">
                                        </div>

                                        <div class="team-contact-content">
                                            <h3>experience</h3>
                                            <p>{{ $memberExperience }}</p>
                                        </div>
                                    </div>
                                    <!-- Team Member Box End -->
                                </div>
                                <!-- Team Contact List End -->
                            </div>
                            <!-- Team About Contact End -->
                        </div>
                        <!-- Team About Box End -->

                        <!-- Team Expertise Skills Start -->
                        <div class="team-expertise-skills">
                            <!-- Team Expertise Boox Start -->
                            <div class="team-expertise-box">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">My <span>expertise</span></h2>
                                    <p class="wow fadeInUp">{{ $memberExpertiseDesc }}</p>
                                </div>
                                <!-- Section Title End -->

                                <!-- Team Expertise List Start -->
                                <div class="team-expertise-list wow fadeInUp" data-wow-delay="0.2s">
                                    <ul>
                                        @foreach($memberExpertiseList as $expertise)
                                            <li>{{ !empty($expertise) ? $expertise : 'blank' }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Team Expertise List End -->
                            </div>
                            <!-- Team Expertise Skills End -->

                            <!-- Team Skills Box Start -->
                            <div class="team-skills-box">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">My <span>skills</span></h2>
                                    <p class="wow fadeInUp">{{ $memberSkillsDesc }}</p>
                                </div>
                                <!-- Section Title End -->

                                @foreach($memberSkillsList as $skill)
                                    @if(!empty($skill['title']))
                                        <!-- Skills Progress Bar Start -->
                                        <div class="skills-progress-bar">
                                            <!-- Skill Item Start -->
                                            <div class="skillbar" data-percent="{{ $skill['percent'] }}%">
                                                <div class="skill-data">
                                                    <div class="skill-title">{{ strtolower($skill['title']) }}</div>
                                                    <div class="skill-no">{{ $skill['percent'] }}%</div>
                                                </div>
                                                <div class="skill-progress">
                                                    <div class="count-bar"></div>
                                                </div>
                                            </div>
                                            <!-- Skill Item End -->
                                        </div>
                                        <!-- Skills Progress Bar End -->
                                    @endif
                                @endforeach
                            </div>
                            <!-- Team Skills Box End -->
                        </div>
                        <!-- Team Expertise Skills End -->
                    </div>
                    <!-- Team Single Box End -->
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .team-contact-content p {
        word-break: break-all;
    }
</style>
@endpush
