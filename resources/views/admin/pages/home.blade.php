@extends('admin.layouts.admin')

@section('title', 'Edit Home Page')
@section('page-title', 'Home page - VCMS Editor')

@php
    $previewUrl = route('home');
@endphp

@section('content')

    @php
        // Helper function to get content from database
        if (!function_exists('getContent')) {
            function getContent($sections, $sectionKey, $contentKey, $default = '')
            {
                if (!isset($sections[$sectionKey])) {
                    return $default;
                }
                $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
                // Terapkan default jika konten tidak ada
                $value = $content ? $content->value : $default;
    
                // Logika ini yang memastikan path lokal dikembalikan MENTAH (RAW)
                // agar asset() bisa dipanggil di template TANPA double asset()
                if (
                    strpos($contentKey, 'image') !== false ||
                    strpos($contentKey, 'video') !== false ||
                    strpos($contentKey, 'icon') !== false
                ) {
                    if (!preg_match('/^(http|https):\/\//i', $value)) {
                        // Mengembalikan nilai mentah, biarkan template yang menambahkan asset()
                        return $value;
                    }
                }
                return $value;
            }
        }
    @endphp

    {{-- DITAMBAHKAN data-item-id="hero-section" --}}
    <div class="hero vcms-editable-section" data-section="hero" data-section-id="1" data-item-id="hero-section">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="hero">
                <i class="fa-solid fa-pen"></i> Edit Hero Section
            </button>
        </div>

        <div class="hero-bg-video vcms-editable-item" data-edit-type="video" data-item-id="hero-video">
            <div class="vcms-edit-indicator-small" title="Edit Background Video">
                <i class="fa-solid fa-video"></i>
            </div>
            @php
                $videoSrc = getContent(
                    $sections,
                    'hero',
                    'hero-video_video_url',
                    'https://demo.awaikenthemes.com/assets/videos/rovex-video-1.mp4',
                );
            @endphp
            {{-- Menggunakan asset() secara eksplisit --}}
            <video autoplay muted loop id="myVideo">
                <source src="{{ asset($videoSrc) }}" type="video/mp4">
            </video>
        </div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="hero-content vcms-editable-item" data-edit-type="hero-content"
                        data-item-id="hero-content-1">
                        <div class="vcms-edit-indicator-small" title="Edit Hero Text & Button">
                            <i class="fa-solid fa-pen"></i>
                        </div>
                        <div class="section-title">
                            @php
                                $heroTitle = getContent(
                                    $sections,
                                    'hero',
                                    'hero-content-1_hero_title',
                                    'Where <span>creativity</span> meets strategy',
                                );
                                $heroDesc = getContent(
                                    $sections,
                                    'hero',
                                    'hero-content-1_hero_description',
                                    'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.',
                                );
                                $heroBtn = getContent(
                                    $sections,
                                    'hero',
                                    'hero-content-1_btn_text',
                                    'view our portfolio',
                                );
                                $heroLink = getContent(
                                    $sections,
                                    'hero',
                                    'hero-content-1_btn_link',
                                    route('portfolio'),
                                );
                            @endphp
                            <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $heroTitle !!}</h1>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $heroDesc }}</p>
                        </div>
                        <div class="hero-btn wow fadeInUp" data-wow-delay="0.4s">
                            <a href="{{ $heroLink }}" class="btn-default">{{ $heroBtn }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-images vcms-editable-item" data-edit-type="hero-images" data-item-id="hero-images-1">
                        <div class="vcms-edit-indicator-small" title="Edit Hero Images Gallery">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        @php
                            $img1 = getContent($sections, 'hero', 'hero-images-1_image_1', 'images/hero-img-1.jpg');
                            $img2 = getContent($sections, 'hero', 'hero-images-1_image_2', 'images/hero-img-2.jpg');
                            $img3 = getContent($sections, 'hero', 'hero-images-1_image_3', 'images/hero-img-3.jpg');
                            $img4 = getContent($sections, 'hero', 'hero-images-1_image_4', 'images/hero-img-4.jpg');
                            $img5 = getContent($sections, 'hero', 'hero-images-1_image_5', 'images/hero-img-5.jpg');
                        @endphp
                        <div class="hero-image-box-1">
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    {{-- Menggunakan asset() secara eksplisit --}}
                                    <img src="{{ asset($img1) }}" alt="Hero Image 1">
                                </figure>
                            </div>

                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    {{-- Menggunakan asset() secara eksplisit --}}
                                    <img src="{{ asset($img2) }}" alt="Hero Image 2">
                                </figure>
                            </div>
                        </div>

                        <div class="hero-image-box-2">
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    {{-- Menggunakan asset() secara eksplisit --}}
                                    <img src="{{ asset($img3) }}" alt="Hero Image 3">
                                </figure>
                            </div>
                        </div>

                        <div class="hero-image-box-1">
                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    {{-- Menggunakan asset() secara eksplisit --}}
                                    <img src="{{ asset($img4) }}" alt="Hero Image 4">
                                </figure>
                            </div>

                            <div class="hero-img">
                                <figure class="image-anime reveal">
                                    {{-- Menggunakan asset() secara eksplisit --}}
                                    <img src="{{ asset($img5) }}" alt="Hero Image 5">
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- DITAMBAHKAN data-item-id="services-section" --}}
    <div class="our-services vcms-editable-section" data-section="services" data-section-id="2" data-item-id="services">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="services">
                <i class="fa-solid fa-pen"></i> Edit Services Header
            </button>
        </div>
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-6">
                    <div class="section-title vcms-editable-item" data-edit-type="title-text" data-item-id="services-title">
                        @php
                            $servicesSubTitle = getContent($sections, 'services', 'services_sub_title', 'our Services');
                            $servicesMainTitle = getContent(
                                $sections,
                                'services',
                                'services_main_title',
                                'Our innovative <span>services</span>',
                            );
                        @endphp
                        <h3>{{ $servicesSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $servicesMainTitle !!}</h2>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="section-btn wow fadeInUp vcms-editable-item" data-wow-delay="0.2s" data-edit-type="button"
                        data-item-id="services-btn">
                        @php
                            $servicesBtnText = getContent(
                                $sections,
                                'services',
                                'services_btn_text',
                                'view all services',
                            );
                            $servicesBtnLink = getContent(
                                $sections,
                                'services',
                                'services_btn_link',
                                route('services'),
                            );
                        @endphp
                        <a href="{{ $servicesBtnLink }}" class="btn-default">{{ $servicesBtnText }}</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="our-services-boxes tab-content wow fadeInUp vcms-editable-item" data-wow-delay="0.25s"
                        id="servicesbox" data-edit-type="service-list" data-item-id="services-list-1">
                        <div class="vcms-edit-indicator-small" title="Edit Service List Content">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <div class="our-services-nav">
                            <ul class="nav nav-tabs" id="servicestab" role="tablist">
                                @php
                                    // Default services as fallback
                                    $defaultServices = [
                                        ['id' => '01', 'name' => 'UI/UX Design'],
                                        ['id' => '02', 'name' => 'Web Development'],
                                        ['id' => '03', 'name' => '3D Designs'],
                                        ['id' => '04', 'name' => 'Digital Marketing Design'],
                                        ['id' => '05', 'name' => 'Motion Graphics'],
                                    ];

                                    // Try to get dynamic services from database
                                    $dynamicServices = [];
                                    $servicesSection = collect($sections)->firstWhere('key', 'services');

                                    if ($servicesSection && isset($servicesSection['contents'])) {
                                        // Find all unique service IDs from content keys
                                        // Pattern matches: services-list-1_service_XX_title where XX can be 01, 02, new_100, etc.
                                        $serviceIds = [];
                                        foreach ($servicesSection['contents'] as $content) {
                                            if (preg_match('/services-list-1_service_(.+)_title$/', $content['key'], $matches)) {
                                                $serviceIds[$matches[1]] = $content['value'];
                                            }
                                        }

                                        // Build dynamic services array
                                        $index = 0;
                                        foreach ($serviceIds as $id => $name) {
                                            $dynamicServices[] = ['id' => $id, 'name' => $name, 'index' => $index];
                                            $index++;
                                        }
                                    }

                                    // Use dynamic if available, otherwise use defaults
                                    // Limit to first 5 services for home page display (tabs)
                                    $allServices = !empty($dynamicServices) ? $dynamicServices : $defaultServices;
                                    $services = array_slice($allServices, 0, 5);
                                @endphp

                                @foreach ($services as $index => $service)
                                    @php
                                        $tabTitle = getContent(
                                            $sections,
                                            'services',
                                            'services-list-1_service_' . $service['id'] . '_title',
                                            $service['name'] ?? 'Service ' . ($index + 1),
                                        );
                                    @endphp
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                            id="{{ $service['id'] }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#{{ $service['id'] }}" type="button" role="tab"
                                            aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                            <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span> {{ $tabTitle }}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                        </div>
                        @foreach ($services as $index => $service)
                            @php
                                $serviceIndex = $service['id'];
                                $activeClass = $index == 0 ? 'show active' : '';
                                $titleDefault = $service['name'] ?? 'Service ' . ($index + 1);
                                $descDefault =
                                    'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.';
                                $imgDefault = 'images/service-image-' . (($index % 5) + 1) . '.jpg';
                                $iconDefault = 'images/icon-service-1.svg';

                                $serviceTitle = getContent(
                                    $sections,
                                    'services',
                                    "services-list-1_service_{$serviceIndex}_title",
                                    $titleDefault,
                                );
                                $serviceDesc = getContent(
                                    $sections,
                                    'services',
                                    "services-list-1_service_{$serviceIndex}_desc",
                                    $descDefault,
                                );
                                $serviceImg = getContent(
                                    $sections,
                                    'services',
                                    "services-list-1_service_{$serviceIndex}_image",
                                    $imgDefault,
                                );
                                $serviceIcon = getContent(
                                    $sections,
                                    'services',
                                    "services-list-1_service_{$serviceIndex}_icon",
                                    $iconDefault,
                                );
                                $readMoreLink = getContent(
                                    $sections,
                                    'services',
                                    "services-list-1_service_{$serviceIndex}_link",
                                    route('services'),
                                );
                            @endphp
                            <div class="our-service-box tab-pane fade {{ $activeClass }}" id="{{ $service['id'] }}"
                                role="tabpanel">
                                <div class="service-box-image">
                                    <figure>
                                        {{-- Menggunakan asset() secara eksplisit --}}
                                        <img src="{{ asset($serviceImg) }}" alt="{{ $serviceTitle }}">
                                    </figure>
                                </div>
                                <div class="service-box-item">
                                    <div class="icon-box">
                                        {{-- Menggunakan asset() secara eksplisit --}}
                                        <img src="{{ asset('images/icon-service-1.svg') }}" alt="Service Icon">
                                    </div>
                                    <div class="service-box-item-content">
                                        <h3><a href="{{ $readMoreLink }}">{{ $serviceTitle }}</a></h3>
                                        <p>{{ $serviceDesc }}</p>
                                    </div>
                                    <div class="service-box-item-btn">
                                        <a href="{{ $readMoreLink }}" class="readmore-btn">read more</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- DITAMBAHKAN data-item-id="our-work-section" --}}
    <div class="our-work bg-section vcms-editable-section" data-section="our-work" data-section-id="3"
        data-item-id="work">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="our-work">
                <i class="fa-solid fa-pen"></i> Edit Work Section
            </button>
        </div>
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-7">
                    <div class="section-title vcms-editable-item" data-edit-type="title-text" data-item-id="work-title">
                        @php
                            $workSubTitle = getContent($sections, 'our-work', 'work_sub_title', 'our work');
                            $workMainTitle = getContent(
                                $sections,
                                'our-work',
                                'work_main_title',
                                'Our latest <span>Projects</span>',
                            );
                        @endphp
                        <h3 class="wow fadeInUp">{{ $workSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $workMainTitle !!}</h2>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="section-btn wow fadeInUp vcms-editable-item" data-wow-delay="0.2s"
                        data-edit-type="button" data-item-id="work-btn">
                        @php
                            $workBtnText = getContent($sections, 'our-work', 'work_btn_text', 'view all portfolio');
                            $workBtnLink = getContent($sections, 'our-work', 'work_btn_link', route('portfolio'));
                        @endphp
                        <a href="{{ $workBtnLink }}" class="btn-default">{{ $workBtnText }}</a>
                    </div>
                </div>
            </div>

            @php
                // Get dynamic projects from database only (no defaults)
                $allProjects = [];
                $workSection = collect($sections)->firstWhere('key', 'our-work');

                if ($workSection && isset($workSection['contents'])) {
                    $projectIds = [];
                    foreach ($workSection['contents'] as $content) {
                        if (preg_match('/project-list-1_project_(.+)_title$/', $content['key'], $matches)) {
                            $projectIds[$matches[1]] = $content['value'];
                        }
                    }
                    foreach ($projectIds as $id => $title) {
                        $mediaType = getContent($sections, 'our-work', "project-list-1_project_{$id}_media_type", 'video');
                        $allProjects[] = [
                            'id' => $id, 
                            'title' => $title,
                            'media_type' => $mediaType
                        ];
                    }
                }

                // Separate by media type: max 6 video and max 6 design (image)
                $videoProjects = array_filter($allProjects, fn($p) => $p['media_type'] === 'video');
                $imageProjects = array_filter($allProjects, fn($p) => $p['media_type'] === 'image');

                // Take max 6 of each type
                $videoProjects = array_slice($videoProjects, 0, 6);
                $imageProjects = array_slice($imageProjects, 0, 6);

                // Combine: videos first, then images
                $projects = array_merge($videoProjects, $imageProjects);
            @endphp

            <div class="row vcms-editable-item position-relative" data-edit-type="project-list"
                data-item-id="project-list-1" style="min-height: 100px;">
                <div class="vcms-edit-indicator-small" title="Manage Projects List"
                    style="position: absolute; top: 10px; right: 20px; z-index: 10;">
                    <i class="fa-solid fa-photo-film"></i>
                </div>

                @if (count($projects) == 0)
                    <div class="col-12 text-center py-5" style="color: #888;">
                        <i class="fa-solid fa-folder-open" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>No projects yet. Click the <i class="fa-solid fa-photo-film"></i> button to add projects.</p>
                    </div>
                @endif

                @foreach ($projects as $index => $project)
                    @php
                        $projectId = $project['id'];
                        $projectTitle = getContent(
                            $sections,
                            'our-work',
                            "project-list-1_project_{$projectId}_title",
                            $project['title'] ?? 'Project Title',
                        );
                        $projectDesc = getContent(
                            $sections,
                            'our-work',
                            "project-list-1_project_{$projectId}_desc",
                            $project['desc'] ?? 'Project description',
                        );
                        $projectLink = getContent(
                            $sections,
                            'our-work',
                            "project-list-1_project_{$projectId}_link",
                            $project['link'] ?? route('portfolio'),
                        );
                        $projectMediaSrc = getContent(
                            $sections,
                            'our-work',
                            "project-list-1_project_{$projectId}_media_src",
                            $project['src'] ?? 'https://www.youtube.com/embed/EszaEO236lw',
                        );
                        $projectMediaType = $project['media_type'] ?? getContent(
                            $sections,
                            'our-work',
                            "project-list-1_project_{$projectId}_media_type",
                            'video',
                        );
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="work-item wow fadeInUp" data-wow-delay="{{ 0.2 * ($index % 3) }}s" data-project-id="{{ $projectId }}" data-media-type="{{ $projectMediaType }}">
                            <div class="work-image">
                                <div class="ratio"
                                    style="--bs-aspect-ratio: 76%; border-radius: 30px; overflow: hidden;">
                                    @if ($projectMediaType == 'video')
                                        @php
                                            $embedUrl = $projectMediaSrc;
                                            if (str_contains($embedUrl, 'youtube.com/embed/')) {
                                                $embedUrl = str_replace('youtube.com/embed/', 'youtube-nocookie.com/embed/', $embedUrl);
                                            } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                                preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $embedUrl, $matches);
                                                if (!empty($matches[1])) {
                                                    $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
                                                }
                                            } elseif (str_contains($embedUrl, 'youtube.com/watch')) {
                                                preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $embedUrl, $matches);
                                                if (!empty($matches[1])) {
                                                    $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
                                                }
                                            }
                                        @endphp
                                        <iframe src="{{ $embedUrl }}" title="YouTube video player"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                                        </iframe>
                                    @else
                                        <figure class="image-anime">
                                            <img src="{{ asset($projectMediaSrc) }}" alt="{{ $projectTitle }}">
                                        </figure>
                                    @endif
                                </div>
                            </div>
                            <div class="work-body">
                                <div class="work-content">
                                    <h3><a href="javascript:void(0)" class="portfolio-title">{{ $projectTitle }}</a></h3>
                                    <p>{{ $projectDesc }}</p>
                                </div>
                                <div class="work-btn">
                                    <a href="javascript:void(0)" class="open-portfolio-modal"
                                       data-title="{{ $projectTitle }}"
                                       data-desc="{{ $projectDesc }}"
                                       data-link="{{ $projectLink }}"
                                       data-media-type="{{ $projectMediaType }}"
                                       data-media-src="{{ $projectMediaSrc }}">
                                        <img src="{{ asset('images/arrrow-light.svg') }}" alt="Arrow">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- DITAMBAHKAN data-item-id="creative-tools-section" --}}
    <div class="creative-tools vcms-editable-section" data-section="creative-tools" data-section-id="4"
        data-item-id="tools">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="creative-tools">
                <i class="fa-solid fa-pen"></i> Edit Tools Header
            </button>
        </div>
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-9">
                    <div class="section-title vcms-editable-item" data-edit-type="title-text" data-item-id="tools-title">
                        @php
                            $toolsSubTitle = getContent(
                                $sections,
                                'creative-tools',
                                'tools_sub_title',
                                'creative toolkit',
                            );
                            $toolsMainTitle = getContent(
                                $sections,
                                'creative-tools',
                                'tools_main_title',
                                'Our toolbox for <span>innovation</span>',
                            );
                        @endphp
                        <h3>{{ $toolsSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $toolsMainTitle !!}</h2>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="section-btn wow fadeInUp vcms-editable-item" data-wow-delay="0.2s"
                        data-edit-type="button" data-item-id="tools-btn">
                        @php
                            $toolsBtnText = getContent(
                                $sections,
                                'creative-tools',
                                'tools_btn_text',
                                'let\'s get started !',
                            );
                            $toolsBtnLink = getContent($sections, 'creative-tools', 'tools_btn_link', route('contact'));
                        @endphp
                        <a href="{{ $toolsBtnLink }}" class="btn-default">{{ $toolsBtnText }}</a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="creative-tools-box vcms-editable-item" data-edit-type="tools-list"
                        data-item-id="tools-list-1">
                        <div class="vcms-edit-indicator-small" title="Manage Tools List">
                            <i class="fa-solid fa-toolbox"></i>
                        </div>
                        @php
                            // Get tools from database only (no defaults)
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
                        @endphp

                        @if(count($tools) == 0)
                            <div class="text-center py-5" style="color: #888; width: 100%;">
                                <i class="fa-solid fa-toolbox" style="font-size: 48px; margin-bottom: 15px;"></i>
                                <p>No tools yet. Click the <i class="fa-solid fa-toolbox"></i> button to add tools.</p>
                            </div>
                        @endif

                        @foreach ($tools as $index => $tool)
                            @php
                                $toolId = $tool['id'];
                                $toolName = getContent(
                                    $sections,
                                    'creative-tools',
                                    "tools-list-1_tool_{$toolId}_name",
                                    $tool['name'],
                                );
                                $toolCat = getContent(
                                    $sections,
                                    'creative-tools',
                                    "tools-list-1_tool_{$toolId}_cat",
                                    'Design tool',
                                );
                                $toolIcon = getContent(
                                    $sections,
                                    'creative-tools',
                                    "tools-list-1_tool_{$toolId}_icon",
                                    'images/icon-creative-tools-1.svg',
                                );
                            @endphp
                            <div class="creative-tool-item wow fadeInUp" data-wow-delay="{{ 0.2 * $index }}s" data-tool-id="{{ $toolId }}">
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
    {{-- DITAMBAHKAN data-item-id="how-it-works-section" --}}
    <div class="how-it-work bg-section vcms-editable-section" data-section="how-it-works" data-section-id="5"
        data-item-id="how">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="how-it-works">
                <i class="fa-solid fa-pen"></i> Edit How It Works Section
            </button>
        </div>
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-8">
                    <div class="section-title vcms-editable-item" data-edit-type="title-text" data-item-id="how-title">
                        @php
                            $howSubTitle = getContent($sections, 'how-it-works', 'how_sub_title', 'how we work');
                            $howMainTitle = getContent(
                                $sections,
                                'how-it-works',
                                'how_main_title',
                                'Our methodology for <span>success</span>',
                            );
                        @endphp
                        <h3>{{ $howSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $howMainTitle !!}</h2>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="contact-now-circle">
                        @php
                            $ctaImg = getContent(
                                $sections,
                                'how-it-works',
                                'how-cta-btn_image_file',
                                'images/contact-circle-yellow.png',
                            );
                            $ctaLink = getContent($sections, 'how-it-works', 'how-cta-btn_link_url', route('contact'));
                        @endphp
                        <a href="{{ $ctaLink }}"><img src="{{ asset($ctaImg) }}" alt="Contact Circle"></a>
                    </div>
                </div>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="how-it-work-images vcms-editable-item" data-edit-type="image-group"
                        data-item-id="how-images">
                        <div class="vcms-edit-indicator-small" title="Edit Images" style="top: 10px; left: 10px;">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        @php
                            $circleDeco = getContent(
                                $sections,
                                'how-it-works',
                                'how-images_circle_deco',
                                'images/how-work-image-circle.png',
                            );
                            $mainWorkImg = getContent(
                                $sections,
                                'how-it-works',
                                'how-images_main_image',
                                'images/how-it-work-image.jpg',
                            );
                        @endphp
                        <div class="how-work-image-circle">
                            {{-- Menggunakan asset() secara eksplisit --}}
                            <img src="{{ asset($circleDeco) }}" alt="Decorative Circle">
                        </div>
                        <div class="how-it-work-image">
                            <figure class="image-anime">
                                {{-- Menggunakan asset() secara eksplisit --}}
                                <img src="{{ asset($mainWorkImg) }}" alt="Work Image">
                            </figure>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="work-faq-accordion vcms-editable-item" id="workaccordion" data-edit-type="process-list"
                        data-item-id="process-list-1">
                        <div class="vcms-edit-indicator-small" title="Manage Process Steps">
                            <i class="fa-solid fa-list-ol"></i>
                        </div>

                        @php
                            // Get processes from database only (no defaults)
                            $processes = [];
                            $howSection = collect($sections)->firstWhere('key', 'how-it-works');

                            if ($howSection && isset($howSection['contents'])) {
                                $processIds = [];
                                foreach ($howSection['contents'] as $content) {
                                    if (preg_match('/process-list-1_step_(.+)_title$/', $content['key'], $matches)) {
                                        $processIds[$matches[1]] = $content['value'];
                                    }
                                }
                                foreach ($processIds as $id => $title) {
                                    $processes[] = ['id' => $id, 'title' => $title];
                                }
                            }
                        @endphp

                        @if(count($processes) == 0)
                            <div class="text-center py-5" style="color: #888;">
                                <i class="fa-solid fa-list-ol" style="font-size: 48px; margin-bottom: 15px;"></i>
                                <p>No process steps yet. Click the <i class="fa-solid fa-list-ol"></i> button to add steps.</p>
                            </div>
                        @endif

                        @foreach ($processes as $index => $process)
                            @php
                                $processId = $process['id'];
                                $processTitle = getContent(
                                    $sections,
                                    'how-it-works',
                                    "process-list-1_step_{$processId}_title",
                                    $process['title'],
                                );
                                $processDesc = getContent(
                                    $sections,
                                    'how-it-works',
                                    "process-list-1_step_{$processId}_desc",
                                    'Description goes here.',
                                );
                                $processActive = $index == 0 ? 'show' : '';
                                // Format display title with step number
                                $displayTitle = preg_replace('/^\d{2}\.\s*/', '', $processTitle);
                                $stepNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                $displayTitle = $stepNum . '. ' . $displayTitle;
                            @endphp
                            <div class="work-accordion-item wow fadeInUp" data-wow-delay="{{ 0.2 * $index }}s" data-process-id="{{ $processId }}">
                                <h2 class="accordion-header" id="heading{{ $processId }}">
                                    <button class="accordion-button {{ $processActive == 'show' ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $processId }}"
                                        aria-expanded="{{ $processActive == 'show' ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $processId }}">
                                        {!! $displayTitle !!}
                                    </button>
                                </h2>
                                <div id="collapse{{ $processId }}"
                                    class="accordion-collapse collapse {{ $processActive }}"
                                    aria-labelledby="heading{{ $processId }}" data-bs-parent="#workaccordion">
                                    <div class="accordion-body">
                                        <p>{{ $processDesc }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- DITAMBAHKAN data-item-id="our-team-section" --}}
    <div class="our-team vcms-editable-section" data-section="our-team" data-section-id="6" data-item-id="team">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="our-team">
                <i class="fa-solid fa-pen"></i> Edit Team Header
            </button>
        </div>
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-8">
                    <div class="section-title vcms-editable-item" data-edit-type="title-text" data-item-id="team-title">
                        @php
                            $teamSubTitle = getContent($sections, 'our-team', 'team_sub_title', 'our team');
                            $teamMainTitle = getContent(
                                $sections,
                                'our-team',
                                'team_main_title',
                                'The Minds Behind <span>the Magic</span>',
                            );
                        @endphp
                        <h3 class="wow fadeInUp">{{ $teamSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $teamMainTitle !!}</h2>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="section-btn wow fadeInUp vcms-editable-item" data-wow-delay="0.2s"
                        data-edit-type="button" data-item-id="team-btn">
                        @php
                            $teamBtnText = getContent($sections, 'our-team', 'team_btn_text', 'all team members');
                            $teamBtnLink = getContent($sections, 'our-team', 'team_btn_link', route('team'));
                        @endphp
                        <a href="{{ $teamBtnLink }}" class="btn-default">{{ $teamBtnText }}</a>
                    </div>
                </div>
            </div>

            <div class="row vcms-editable-item" data-edit-type="team-list" data-item-id="team-list-1"
                style="position: relative;">
                <div class="vcms-edit-indicator-small" title="Manage Team Members"
                    style="position: absolute; top: -15px; right: 0; z-index: 99;">
                    <i class="fa-solid fa-users"></i>
                </div>
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
                    // Home page menampilkan maksimal 4 member
                    $displayCount = min($teamCount, 4);
                @endphp

                @if ($displayCount === 0)
                    <div class="col-12 text-center py-5">
                        <p style="color: #888;">No team members yet. Click the <i class="fa-solid fa-users"></i> icon above to add members.</p>
                    </div>
                @endif

                @for ($index = 0; $index < $displayCount; $index++)
                    @php
                        $memberName = getContent($sections, 'our-team', "team-list-1_member_{$index}_name", 'Team Member');
                        $memberRole = getContent($sections, 'our-team', "team-list-1_member_{$index}_role", 'Position');
                        $memberImg = getContent($sections, 'our-team', "team-list-1_member_{$index}_img", 'images/team-1.jpg');
                        $memberLink = route('pages.team.single', ['index' => $index]);
                        $memberExperience = getContent($sections, 'our-team', "team-list-1_member_{$index}_experience", '');
                        $memberAbout = getContent($sections, 'our-team', "team-list-1_member_{$index}_about", '');
                        $memberSkillsDesc = getContent($sections, 'our-team', "team-list-1_member_{$index}_skills_desc", '');
                        $memberSkillsCount = getContent($sections, 'our-team', "team-list-1_member_{$index}_skills_count", '1');
                        $skillItems = [];
                        for ($s = 1; $s <= (int)$memberSkillsCount; $s++) {
                            $skillItems[$s] = [
                                'title' => getContent($sections, 'our-team', "team-list-1_member_{$index}_skill_{$s}_title", ''),
                                'percent' => getContent($sections, 'our-team', "team-list-1_member_{$index}_skill_{$s}_percent", '80')
                            ];
                        }
                        $memberExpertiseDesc = getContent($sections, 'our-team', "team-list-1_member_{$index}_expertise_desc", '');
                        $memberExpertiseCount = getContent($sections, 'our-team', "team-list-1_member_{$index}_expertise_count", '4');
                        $expertiseItems = [];
                        for ($e = 1; $e <= (int)$memberExpertiseCount; $e++) {
                            $expertiseItems[$e] = getContent($sections, 'our-team', "team-list-1_member_{$index}_expertise_{$e}", '');
                        }
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="team-item wow fadeInUp" data-wow-delay="{{ 0.2 * $index }}s"
                             data-experience="{{ $memberExperience }}"
                             data-about="{{ $memberAbout }}"
                             data-skills-desc="{{ $memberSkillsDesc }}"
                             data-skills-count="{{ $memberSkillsCount }}"
                             @for ($s = 1; $s <= (int)$memberSkillsCount; $s++)
                             data-skill-{{ $s }}-title="{{ $skillItems[$s]['title'] ?? '' }}"
                             data-skill-{{ $s }}-percent="{{ $skillItems[$s]['percent'] ?? '80' }}"
                             @endfor
                             data-expertise-desc="{{ $memberExpertiseDesc }}"
                             data-expertise-count="{{ $memberExpertiseCount }}"
                             @for ($e = 1; $e <= (int)$memberExpertiseCount; $e++)
                             data-expertise-{{ $e }}="{{ $expertiseItems[$e] ?? '' }}"
                             @endfor
                        >
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
    {{-- DITAMBAHKAN data-item-id="achievements-section" --}}
    <div class="our-achievements vcms-editable-section" data-section="achievements" data-section-id="7"
        data-item-id="achievements">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="achievements">
                <i class="fa-solid fa-pen"></i> Edit Achievements Section
            </button>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="our-achievements-content">
                        <div class="section-title vcms-editable-item" data-edit-type="achievements-title"
                            data-item-id="achievements-title">
                            @php
                                $achievementsSubTitle = getContent(
                                    $sections,
                                    'achievements',
                                    'achievements_sub_title',
                                    'our achievements',
                                );
                                $achievementsMainTitle = getContent(
                                    $sections,
                                    'achievements',
                                    'achievements_main_title',
                                    'Proud moments & <span>milestones</span>',
                                );
                                $achievementsDescription = getContent(
                                    $sections,
                                    'achievements',
                                    'achievements_description',
                                    'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.',
                                );
                            @endphp
                            <h3 class="wow fadeInUp">{{ $achievementsSubTitle }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $achievementsMainTitle !!}</h2>
                            <p class="wow fadeInUp" data-wow-delay="0.2s">{{ $achievementsDescription }}</p>
                        </div>
                        <div class="achievements-content-btn wow fadeInUp vcms-editable-item" data-wow-delay="0.4s"
                            data-edit-type="button" data-item-id="achievements-btn">
                            @php
                                $achievementsBtnText = getContent(
                                    $sections,
                                    'achievements',
                                    'achievements_btn_text',
                                    'let\'s get started !',
                                );
                                $achievementsBtnLink = getContent(
                                    $sections,
                                    'achievements',
                                    'achievements_btn_link',
                                    route('contact'),
                                );
                            @endphp
                            <a href="{{ $achievementsBtnLink }}" class="btn-default">{{ $achievementsBtnText }}</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="our-achievements-box wow fadeInUp vcms-editable-item" data-edit-type="achievement-list"
                        data-item-id="achievement-list-1" style="position: relative;">
                        <div class="vcms-edit-indicator-small" title="Manage Achievements List">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        @php
                            // Get achievements from database only (no defaults)
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
                        @endphp

                        @if(count($achievements) == 0)
                            <div class="text-center py-5" style="color: #888; width: 100%;">
                                <i class="fa-solid fa-trophy" style="font-size: 48px; margin-bottom: 15px;"></i>
                                <p>No achievements yet. Click the <i class="fa-solid fa-trophy"></i> button to add achievements.</p>
                            </div>
                        @endif

                        @foreach ($achievements as $index => $achievement)
                            @php
                                $achId = $achievement['id'];
                                $achYear = getContent(
                                    $sections,
                                    'achievements',
                                    "achievement-list-1_ach_{$achId}_year",
                                    '2024 - 2025',
                                );
                                $achName = getContent(
                                    $sections,
                                    'achievements',
                                    "achievement-list-1_ach_{$achId}_name",
                                    $achievement['name'],
                                );
                                $achDesc = getContent(
                                    $sections,
                                    'achievements',
                                    "achievement-list-1_ach_{$achId}_desc",
                                    'Achievement description.',
                                );
                            @endphp
                            <div class="achievements-item" data-achievement-id="{{ $achId }}">
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
    {{-- DITAMBAHKAN data-item-id="cta-section" --}}
    <div class="cta-section bg-section vcms-editable-section" data-section="cta" data-section-id="8"
        data-item-id="cta-section">
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
                            <div class="cta-box-content wow fadeInUp vcms-editable-item" data-edit-type="rich-text"
                                data-item-id="cta">
                                <div class="vcms-edit-indicator-small" title="Edit Description">
                                    <i class="fa-solid fa-file-alt"></i>
                                </div>
                                @php
                                    $ctaDesc = getContent(
                                        $sections,
                                        'cta',
                                        'cta_text_content',
                                        'We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.',
                                    );
                                @endphp
                                <p>{{ $ctaDesc }}</p>
                            </div>
                            <div class="cta-counter-box vcms-editable-item" data-edit-type="counter-list"
                                data-item-id="cta-counters">
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
                                        $countNum = getContent(
                                            $sections,
                                            'cta',
                                            "cta-counters_cnt_{$index}_num",
                                            $counter['num'],
                                        );
                                        $countSym = getContent(
                                            $sections,
                                            'cta',
                                            "cta-counters_cnt_{$index}_sym",
                                            $counter['sym'],
                                        );
                                        $countLabel = getContent(
                                            $sections,
                                            'cta',
                                            "cta-counters_cnt_{$index}_label",
                                            $counter['label'],
                                        );
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
                                $ctaImg = getContent(
                                    $sections,
                                    'cta',
                                    'cta-circle-img_image_file',
                                    'images/cta-contact-circle.png',
                                );
                                $ctaLink = getContent($sections, 'cta', 'cta-circle-img_link_url', route('contact'));
                            @endphp
                            <a href="{{ $ctaLink }}"><img src="{{ asset($ctaImg) }}" alt="Contact Circle"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 80px;"></div>
    {{-- DITAMBAHKAN data-item-id="testimonials-section" --}}
    <div class="our-testimonial bg-section vcms-editable-section" data-section="testimonials" data-section-id="9"
        data-item-id="testimonials">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="testimonials">
                <i class="fa-solid fa-pen"></i> Edit Testimonial/Clients Section
            </button>
        </div>
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-12">
                    <div class="section-title vcms-editable-item" data-edit-type="title-text"
                        data-item-id="testimonial-title">
                        @php
                            $testiSubTitle = getContent(
                                $sections,
                                'testimonials',
                                'testimonials_sub_title',
                                'testimonials',
                            );
                            $testiMainTitle = getContent(
                                $sections,
                                'testimonials',
                                'testimonials_main_title',
                                'Trusted by <span>Teams at</span>',
                            );
                        @endphp
                        <h3 class="wow fadeInUp">{{ $testiSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $testiMainTitle !!}</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="trusted-client-box vcms-editable-item" data-edit-type="client-logo-slider"
                        data-item-id="client-logos-2">
                        <div class="vcms-edit-indicator-small" title="Manage Client Logos Slider">
                            <i class="fa-solid fa-images"></i>
                        </div>
                        <div class="trusted-client-title vcms-editable-item" data-edit-type="trusted-clients"
                            data-item-id="trusted">

                            {{-- INDIKATOR PENA DITAMBAHKAN DI SINI --}}
                            <div class="vcms-edit-indicator-small" title="Edit Client Title">
                                <i class="fa-solid fa-pen"></i>
                            </div>
                            {{-- AKHIR INDIKATOR PENA --}}

                            @php
                                $clientTitle = getContent(
                                    $sections,
                                    'testimonials',
                                    'trusted_section_title_text',
                                    'Our Clients',
                                );
                            @endphp
                            <h3>{{ $clientTitle ?? 'Our Clients' }}</h3>
                        </div>
                        <div class="trusted-clients-slider">
                            <div class="swiper">
                                <div class="swiper-wrapper">
                                    @php
                                        // Get client logos from database only (no defaults)
                                        $clientLogos = [];
                                        $testimonialsSection = collect($sections)->firstWhere('key', 'testimonials');

                                        if ($testimonialsSection && isset($testimonialsSection['contents'])) {
                                            foreach ($testimonialsSection['contents'] as $content) {
                                                if (preg_match('/client-logos-2_client_logo_(\d+)$/', $content['key'], $matches)) {
                                                    $clientLogos[] = [
                                                        'key' => (int)$matches[1],
                                                        'value' => $content['value']
                                                    ];
                                                }
                                            }
                                        }

                                        // Sort by key to ensure consistent order
                                        usort($clientLogos, fn($a, $b) => $a['key'] <=> $b['key']);

                                        // Group logos in pairs for slider (2 logos per slide)
                                        $logoChunks = array_chunk($clientLogos, 2);
                                    @endphp
                                    @if(count($clientLogos) == 0)
                                        <div class="swiper-slide">
                                            <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100%; color: #888;">
                                                <i class="fa-solid fa-images" style="font-size: 32px; margin-bottom: 10px;"></i>
                                                <p style="margin: 0;">No client logos yet</p>
                                            </div>
                                        </div>
                                    @else
                                        @foreach ($logoChunks as $chunkIndex => $logoPair)
                                            <div class="swiper-slide">
                                                <div class="d-flex flex-column justify-content-between align-items-center"
                                                    style="height: 100%; gap: 40px;">
                                                    @foreach ($logoPair as $logo)
                                                        <div class="trusted-client-logo">
                                                            <img src="{{ asset($logo['value']) }}" alt="Client Logo" data-logo-key="{{ $logo['key'] }}" data-from-db="1">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="height: 80px;"></div>
    {{-- FOOTER SECTION --}}
    @php
        // Footer data - keys use footer_footer_* pattern because section is 'footer' and input names are 'footer_*'
        $footerTitle = getContent($sections, 'footer', 'footer_footer_title', "Let's Connect");
        $footerTitleLink = getContent($sections, 'footer', 'footer_footer_title_link', route('contact'));
        $footerLogo = getContent($sections, 'footer', 'footer_footer_logo_file', 'images/logo-dikelolain-full-hitam.png');
        $footerDesc = getContent($sections, 'footer', 'footer_footer_description', 'The creativity and professionalism shown by Rovex exceeded our expectations. Their design transformed our brand, and the results speak for themselves.');
        $socialFb = getContent($sections, 'footer', 'footer_footer_social_fb', '#');
        $socialX = getContent($sections, 'footer', 'footer_footer_social_x', '#');
        $socialLi = getContent($sections, 'footer', 'footer_footer_social_li', '#');
        $socialIg = getContent($sections, 'footer', 'footer_footer_social_ig', '#');
        $quickLink1Text = getContent($sections, 'footer', 'footer_footer_quick_1_text', 'home');
        $quickLink1Url = getContent($sections, 'footer', 'footer_footer_quick_1_url', route('home'));
        $quickLink2Text = getContent($sections, 'footer', 'footer_footer_quick_2_text', 'about us');
        $quickLink2Url = getContent($sections, 'footer', 'footer_footer_quick_2_url', route('about'));
        $quickLink3Text = getContent($sections, 'footer', 'footer_footer_quick_3_text', 'team');
        $quickLink3Url = getContent($sections, 'footer', 'footer_footer_quick_3_url', route('team'));
        $quickLink4Text = getContent($sections, 'footer', 'footer_footer_quick_4_text', 'our portfolio');
        $quickLink4Url = getContent($sections, 'footer', 'footer_footer_quick_4_url', route('portfolio'));
        $serviceLink1Text = getContent($sections, 'footer', 'footer_footer_service_1_text', 'UI/UX Design');
        $serviceLink1Url = getContent($sections, 'footer', 'footer_footer_service_1_url', route('services'));
        $serviceLink2Text = getContent($sections, 'footer', 'footer_footer_service_2_text', '3D Designs');
        $serviceLink2Url = getContent($sections, 'footer', 'footer_footer_service_2_url', route('services'));
        $serviceLink3Text = getContent($sections, 'footer', 'footer_footer_service_3_text', 'Web Development');
        $serviceLink3Url = getContent($sections, 'footer', 'footer_footer_service_3_url', route('services'));
        $serviceLink4Text = getContent($sections, 'footer', 'footer_footer_service_4_text', 'Motion Graphics');
        $serviceLink4Url = getContent($sections, 'footer', 'footer_footer_service_4_url', route('services'));
        $contactPhone = getContent($sections, 'footer', 'footer_footer_contact_phone', '+01 456-852-789');
        $contactEmail = getContent($sections, 'footer', 'footer_footer_contact_email', 'info@domainname.com');
        $contactAddress = getContent($sections, 'footer', 'footer_footer_contact_address', '843, Apple LanePeoria');
        $copyrightText = getContent($sections, 'footer', 'footer_footer_copyright', 'Copyright © 2025 All Rights Reserved.');
    @endphp
    <footer class="footer-section vcms-editable-section" data-section="footer" data-section-id="10" data-item-id="footer">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="footer">
                <i class="fa-solid fa-pen"></i> Edit Footer Section
            </button>
        </div>
        <div class="footer-box bg-section vcms-editable-item" data-edit-type="footer-content" data-item-id="footer-content"
             data-title="{{ $footerTitle }}"
             data-title-link="{{ $footerTitleLink }}"
             data-logo="{{ $footerLogo }}"
             data-description="{{ $footerDesc }}"
             data-social-fb="{{ $socialFb }}"
             data-social-x="{{ $socialX }}"
             data-social-li="{{ $socialLi }}"
             data-social-ig="{{ $socialIg }}"
             data-quick-1-text="{{ $quickLink1Text }}"
             data-quick-1-url="{{ $quickLink1Url }}"
             data-quick-2-text="{{ $quickLink2Text }}"
             data-quick-2-url="{{ $quickLink2Url }}"
             data-quick-3-text="{{ $quickLink3Text }}"
             data-quick-3-url="{{ $quickLink3Url }}"
             data-quick-4-text="{{ $quickLink4Text }}"
             data-quick-4-url="{{ $quickLink4Url }}"
             data-service-1-text="{{ $serviceLink1Text }}"
             data-service-1-url="{{ $serviceLink1Url }}"
             data-service-2-text="{{ $serviceLink2Text }}"
             data-service-2-url="{{ $serviceLink2Url }}"
             data-service-3-text="{{ $serviceLink3Text }}"
             data-service-3-url="{{ $serviceLink3Url }}"
             data-service-4-text="{{ $serviceLink4Text }}"
             data-service-4-url="{{ $serviceLink4Url }}"
             data-contact-phone="{{ $contactPhone }}"
             data-contact-email="{{ $contactEmail }}"
             data-contact-address="{{ $contactAddress }}"
             data-copyright="{{ $copyrightText }}"
        >
            <div class="vcms-edit-indicator-small" title="Edit Footer Content" style="position: absolute; top: 20px; right: 20px; z-index: 10;">
                <i class="fa-solid fa-pen"></i>
            </div>
            <div class="footer-title">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="footer-title-content">
                                <h2><a href="{{ $footerTitleLink }}">{{ $footerTitle }}</a></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="about-footer">
                                <div class="footer-logo">
                                    <img src="{{ asset($footerLogo) }}" alt="">
                                </div>
                                <div class="about-footer-content">
                                    <p>{{ $footerDesc }}</p>
                                </div>
                                <div class="footer-social-links">
                                    <ul>
                                        <li><a href="{{ $socialFb }}"><i class="fa-brands fa-facebook-f"></i></a></li>
                                        <li><a href="{{ $socialX }}"><i class="fa-brands fa-x-twitter"></i></a></li>
                                        <li><a href="{{ $socialLi }}"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                        <li><a href="{{ $socialIg }}"><i class="fa-brands fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <div class="footer-links">
                                <h3>quick links</h3>
                                <ul>
                                    <li><a href="{{ $quickLink1Url }}">{{ $quickLink1Text }}</a></li>
                                    <li><a href="{{ $quickLink2Url }}">{{ $quickLink2Text }}</a></li>
                                    <li><a href="{{ $quickLink3Url }}">{{ $quickLink3Text }}</a></li>
                                    <li><a href="{{ $quickLink4Url }}">{{ $quickLink4Text }}</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="footer-links">
                                <h3>our services</h3>
                                <ul>
                                    <li><a href="{{ $serviceLink1Url }}">{{ $serviceLink1Text }}</a></li>
                                    <li><a href="{{ $serviceLink2Url }}">{{ $serviceLink2Text }}</a></li>
                                    <li><a href="{{ $serviceLink3Url }}">{{ $serviceLink3Text }}</a></li>
                                    <li><a href="{{ $serviceLink4Url }}">{{ $serviceLink4Text }}</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-5">
                            <div class="footer-contact">
                                <h3>contact info</h3>
                                <div class="footer-contact-details">
                                    <div class="footer-info-box">
                                        <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
                                        <div class="footer-info-box-content">
                                            <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}">{{ $contactPhone }}</a></p>
                                        </div>
                                    </div>
                                    <div class="footer-info-box">
                                        <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
                                        <div class="footer-info-box-content">
                                            <p><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
                                        </div>
                                    </div>
                                    <div class="footer-info-box">
                                        <div class="icon-box"><i class="fa-solid fa-location-dot"></i></div>
                                        <div class="footer-info-box-content">
                                            <p>{{ $contactAddress }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-copyright">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="footer-copyright-text">
                                <p>{{ $copyrightText }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div style="height: 80px;"></div>

@endsection

@push('scripts')
@endpush
