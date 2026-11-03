@extends('layouts.app')

@php
    // Header styling: first word normal, the rest highlighted.
    [$firstName, $lastName] = array_pad(explode(' ', $member->name, 2), 2, '');
@endphp

@section('title', $member->name . ' - Our Team - Dikelolain')
@section('meta_description', Str::limit(strip_tags($member->about ?: $member->role), 160))

@section('content')

    <x-page-header :title="e($firstName) . ' <span>' . e($lastName) . '</span>'">
        <li class="breadcrumb-item"><a href="{{ route('team') }}">our team</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $member->name }}</li>
    </x-page-header>

<div class="page-team-single">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Team Single Box Start -->
                    <div class="team-single-box">
                        <!-- Team About Box Start -->
                        <x-vcms.item resource="team-members" :item="$member" :fields="$memberFields" label="anggota tim"
                            :movable="false" :deletable="false" class="team-about-box {{ $member->is_active ? '' : 'vcms-hidden' }}">
                            <!-- Team Single Image Start -->
                            <div class="team-single-image">
                                <figure class="image-anime reveal">
                                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}">
                                </figure>
                            </div>
                            <!-- Team Single Image End -->

                            <!-- Team About Contact Start -->
                            <div class="team-about-content">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">About <span>me</span></h2>
                                    <p>{{ $member->about }}</p>
                                </div>
                                <!-- Section Title End -->

                                <!-- Team Contact List Start -->
                                <div class="team-contact-list">
                                    <!-- Team Member Box Start -->
                                    <div class="team-contact-box">
                                        <div class="icon-box">
                                            <img src="{{ asset('images/icon-team-contact-1.svg') }}" alt="">
                                        </div>

                                        <div class="team-contact-content">
                                            <h3>position</h3>
                                            <p>{{ $member->role }}</p>
                                        </div>
                                    </div>
                                    <!-- Team Member Box End -->

                                    @if ($member->experience)
                                        <!-- Team Member Box Start -->
                                        <div class="team-contact-box">
                                            <div class="icon-box">
                                                <img src="{{ asset('images/icon-team-contact-3.svg') }}" alt="">
                                            </div>

                                            <div class="team-contact-content">
                                                <h3>experience</h3>
                                                <p>{{ $member->experience }}</p>
                                            </div>
                                        </div>
                                        <!-- Team Member Box End -->
                                    @endif
                                </div>
                                <!-- Team Contact List End -->
                            </div>
                            <!-- Team About Contact End -->
                        </x-vcms.item>
                        <!-- Team About Box End -->

                        <!-- Team Expertise Skills Start -->
                        <div class="team-expertise-skills">
                            <!-- Team Expertise Boox Start -->
                            <div class="team-expertise-box">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">My <span>expertise</span></h2>
                                    <p>{{ $member->expertise_intro }}</p>
                                </div>
                                <!-- Section Title End -->

                                <!-- Team Expertise List Start -->
                                <div class="team-expertise-list">
                                    <ul>
                                        @foreach ($member->expertises as $expertise)
                                            <x-vcms.item tag="li" resource="team-member-expertises" :item="$expertise" :fields="$expertiseFields" label="keahlian"
                                                class="{{ $expertise->is_active ? '' : 'vcms-hidden' }}">{{ $expertise->name }}</x-vcms.item>
                                        @endforeach
                                    </ul>
                                </div>
                                <!-- Team Expertise List End -->

                                <x-vcms.add resource="team-member-expertises" :fields="$expertiseFields" label="keahlian" :defaults="['team_member_id' => $member->id]" />
                            </div>
                            <!-- Team Expertise Skills End -->

                            <!-- Team Skills Box Start -->
                            <div class="team-skills-box">
                                <!-- Section Title Start -->
                                <div class="section-title">
                                    <h2 class="text-anime-style-2" data-cursor="-opaque">My <span>skills</span></h2>
                                    <p>{{ $member->skills_intro }}</p>
                                </div>
                                <!-- Section Title End -->

                                @foreach ($member->skills as $skill)
                                    <!-- Skills Progress Bar Start -->
                                    <x-vcms.item resource="team-member-skills" :item="$skill" :fields="$skillFields" label="skill"
                                        class="skills-progress-bar {{ $skill->is_active ? '' : 'vcms-hidden' }}">
                                        <!-- Skill Item Start -->
                                        <div class="skillbar" data-percent="{{ $skill->percent }}%">
                                            <div class="skill-data">
                                                <div class="skill-title">{{ $skill->name }}</div>
                                                <div class="skill-no">{{ $skill->percent }}%</div>
                                            </div>
                                            <div class="skill-progress">
                                                <div class="count-bar"></div>
                                            </div>
                                        </div>
                                        <!-- Skill Item End -->
                                    </x-vcms.item>
                                    <!-- Skills Progress Bar End -->
                                @endforeach

                                <x-vcms.add resource="team-member-skills" :fields="$skillFields" label="skill" :defaults="['team_member_id' => $member->id, 'percent' => 80]" />
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

