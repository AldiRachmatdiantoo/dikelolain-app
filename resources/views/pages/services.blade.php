@extends('layouts.app')

@section('title', 'Dikelolain - Our Services')

@section('content')

@php
    // Helper function to get content from database
    function getContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }

    // Get services from database
    $services = [];
    $servicesSection = collect($sections)->firstWhere('key', 'services');

    if ($servicesSection && isset($servicesSection['contents'])) {
        $serviceIds = [];
        foreach ($servicesSection['contents'] as $content) {
            // Match service title but exclude sub-section titles (benefits_title, process_title, faq_title, etc.)
            if (preg_match('/services-list-1_service_(.+)_title$/', $content['key'], $matches)) {
                $serviceId = $matches[1];
                // Skip if this is a sub-section title (contains _benefits, _process, _faq, _cta anywhere)
                if (!preg_match('/_(benefits|process|faq|cta)/', $serviceId)) {
                    $serviceIds[$serviceId] = $content['value'];
                }
            }
        }
        foreach ($serviceIds as $id => $title) {
            $services[] = ['id' => $id, 'title' => $title];
        }
    }

    // Default services if database is empty
    if (empty($services)) {
        $services = [
            ['id' => '01', 'title' => 'UI/UX Design'],
            ['id' => '02', 'title' => 'Web Development'],
            ['id' => '03', 'title' => '3D Designs'],
            ['id' => '04', 'title' => 'Digital Marketing'],
            ['id' => '05', 'title' => 'Motion Graphics'],
            ['id' => '06', 'title' => 'Logos & Branding'],
        ];
    }

    $totalServices = count($services);

    // Pagination settings
    $servicesPerPage = 12;
    $totalPages = ceil($totalServices / $servicesPerPage);

    // Page Header data from database
    $headerTitle = getContent($sections, 'services-header', 'services-header-1_title', 'Explore our <span>services</span>');
    $headerBreadcrumb = getContent($sections, 'services-header', 'services-header-1_breadcrumb', 'Services');
@endphp

    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>

    <div class="page-services">
        <div class="container">
            @if ($totalPages > 1)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="services-pagination d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            @for ($page = 1; $page <= $totalPages; $page++)
                                <button type="button"
                                    class="btn service-page-btn {{ $page == 1 ? 'active' : '' }}"
                                    data-page="{{ $page }}"
                                    style="min-width: 45px; padding: 8px 16px; border-radius: 8px; font-weight: 600;
                                           {{ $page == 1 ? 'background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; border: none;' : 'background: #252525; color: #e0e0e0; border: 1px solid #3a3a3a;' }}">
                                    {{ $page }}
                                </button>
                            @endfor
                            <span style="color: #888; font-size: 13px; margin-left: 10px;">
                                ({{ $totalServices }} services)
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row service-items-row" style="min-height: 400px;">
                @foreach ($services as $index => $service)
                    @php
                        $serviceId = $service['id'];
                        $serviceTitle = getContent($sections, 'services', "services-list-1_service_{$serviceId}_title", $service['title'] ?? 'Service Title');
                        $serviceDesc = getContent($sections, 'services', "services-list-1_service_{$serviceId}_desc", 'We provide excellent service for your needs.');
                        $serviceImage = getContent($sections, 'services', "services-list-1_service_{$serviceId}_image", "images/service-image-" . (($index % 6) + 1) . ".jpg");
                        $serviceLink = route('pages.service.single', ['serviceId' => $serviceId]);

                        // Calculate animation delay
                        $delay = ($index % 3) * 0.2;

                        // Calculate page number for this service
                        $pageNum = floor($index / $servicesPerPage) + 1;
                    @endphp
                    <div class="col-lg-4 col-md-6 service-item-col" data-page="{{ $pageNum }}" style="{{ $pageNum > 1 ? 'display: none;' : '' }}">
                        <div class="service-item wow fadeInUp" data-service-id="{{ $serviceId }}" @if($delay > 0) data-wow-delay="{{ $delay }}s" @endif>
                            <div class="service-image">
                                <a href="{{ $serviceLink }}" data-cursor-text="View">
                                    <figure class="image-anime">
                                        <img src="{{ asset($serviceImage) }}" alt="{{ $serviceTitle }}">
                                    </figure>
                                </a>
                            </div>
                            <div class="service-body">
                                <div class="service-content">
                                    <h3><a href="{{ $serviceLink }}">{{ $serviceTitle }}</a></h3>
                                    <p>{{ $serviceDesc }}</p>
                                </div>
                                <div class="service-btn">
                                    <a href="{{ $serviceLink }}"><img src="{{ asset('images/arrrow-light.svg') }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Creative Tools Section Start -->
    <div class="creative-tools">
        <div class="container">
            <div class="row section-row align-items-center">
                <div class="col-lg-9">
                    <div class="section-title">
                        @php
                            $toolsSubTitle = getContent($sections, 'creative-tools', 'tools_sub_title', 'creative toolkit');
                            $toolsMainTitle = getContent($sections, 'creative-tools', 'tools_main_title', 'Our toolbox for <span>innovation</span>');
                        @endphp
                        <h3 class="wow fadeInUp">{{ $toolsSubTitle }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $toolsMainTitle !!}</h2>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                        @php
                            $toolsBtnText = getContent($sections, 'creative-tools', 'tools_btn_text', 'let\'s get started !');
                            $toolsBtnLink = getContent($sections, 'creative-tools', 'tools_btn_link', route('contact'));
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
                                $toolName = getContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_name", $tool['name'] ?? 'Tool');
                                $toolCat = getContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_cat", $tool['cat'] ?? 'Design tool');
                                $toolIcon = getContent($sections, 'creative-tools', "tools-list-1_tool_{$toolId}_icon", $tool['icon'] ?? 'images/icon-creative-tools-1.svg');
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
    <script>
        $(document).ready(function() {
            // Services page pagination
            $('.service-page-btn').on('click', function() {
                const page = $(this).data('page');

                // Update active button
                $('.service-page-btn').css({
                    'background': '#252525',
                    'color': '#e0e0e0',
                    'border': '1px solid #3a3a3a'
                }).removeClass('active');

                $(this).css({
                    'background': 'linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%)',
                    'color': '#161616',
                    'border': 'none'
                }).addClass('active');

                // Show/hide service items
                $('.service-item-col').hide();
                $('.service-item-col[data-page="' + page + '"]').show();

                // Scroll to top of services section
                $('html, body').animate({
                    scrollTop: $('.page-services').offset().top - 100
                }, 300);
            });
        });
    </script>
@endpush
