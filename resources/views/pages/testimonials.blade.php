@extends('layouts.app')

@section('title', 'Testimonials - Dikelolain')

@php
    // Helper function to get content from database
    if (!function_exists('getTestimonialContent')) {
        function getTestimonialContent($sections, $sectionKey, $contentKey, $default = '') {
            $section = collect($sections)->firstWhere('key', $sectionKey);
            if (!$section || !isset($section['contents'])) {
                return $default;
            }
            $content = collect($section['contents'])->firstWhere('key', $contentKey);
            return $content ? $content['value'] : $default;
        }
    }

    // Page Header data
    $headerTitle = getTestimonialContent($sections, 'testimonials-header', 'testimonials-header-1_title', 'Testimonials');
    $headerBreadcrumb = getTestimonialContent($sections, 'testimonials-header', 'testimonials-header-1_breadcrumb', 'Testimonials');
@endphp

@section('content')

    <!-- Page Header -->
    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>

    <!-- Page Testimonials Start -->
    <div class="page-testimonial">
        <div class="container">
            <div class="row">
                @foreach ($testimonials as $testimonial)
                    <div class="col-lg-4 col-md-6">
                        <!-- Testimonial Item Start -->
                        <x-vcms.item resource="testimonials" :item="$testimonial" :fields="$testimonialFields" label="testimoni"
                            class="testimonial-item wow fadeInUp {{ $testimonial->is_active ? '' : 'vcms-hidden' }}"
                            :data-wow-delay="$loop->index ? ($loop->index * 0.2) . 's' : null">
                            <div class="testimonial-header">
                                <div class="testimonial-logo-img">
                                    @if ($testimonial->logo)
                                        <img src="{{ $testimonial->logo_url }}" alt="">
                                    @endif
                                </div>
                                <div class="testimonial-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star" @if ($i > $testimonial->rating) style="opacity: 0.3;" @endif></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="testimonial-content">
                                <p>{{ $testimonial->content }}</p>
                            </div>
                            <div class="testimonial-author">
                                @if ($testimonial->photo)
                                    <div class="author-image">
                                        <figure class="image-anime">
                                            <img src="{{ $testimonial->photo_url }}" alt="">
                                        </figure>
                                    </div>
                                @endif
                                <div class="author-content">
                                    <h3>{{ $testimonial->name }}</h3>
                                    <p>{{ $testimonial->role }}</p>
                                </div>
                            </div>
                        </x-vcms.item>
                        <!-- Testimonial Item End -->
                    </div>
                @endforeach
            </div>

            <x-vcms.add resource="testimonials" :fields="$testimonialFields" label="testimoni" :defaults="['rating' => 5]" />
        </div>
    </div>
    <!-- Page Testimonials End -->

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
                            <a href="{{ route('contact') }}"><img src="images/cta-contact-circle.png" alt=""></a>
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
                            $toolsSubTitle = getTestimonialContent($sections, 'creative-tools', 'tools_sub_title', 'creative toolkit');
                            $toolsMainTitle = getTestimonialContent($sections, 'creative-tools', 'tools_main_title', 'Our toolbox for <span>innovation</span>');
                        @endphp
                        <h3 class="wow fadeInUp">{{ $toolsSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $toolsMainTitle !!}</h2>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                        @php
                            $toolsBtnText = getTestimonialContent($sections, 'creative-tools', 'tools_btn_text', 'let\'s get started !');
                            $toolsBtnLink = getTestimonialContent($sections, 'creative-tools', 'tools_btn_link', route('contact'));
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
                                $toolName = getTestimonialContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_name", $tool['name'] ?? 'Tool');
                                $toolCat = getTestimonialContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_cat", $tool['cat'] ?? 'Design tool');
                                $toolIcon = getTestimonialContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_icon", $tool['icon'] ?? 'images/icon-creative-tools-1.svg');
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
