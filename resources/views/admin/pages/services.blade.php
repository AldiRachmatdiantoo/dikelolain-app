@extends('admin.layouts.admin')

@section('title', 'Edit Services Page')
@section('page-title', 'Services Page - VCMS Editor')

@php
    $previewUrl = route('services');
@endphp

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

    // No dummy services - only show data from database

    $totalServices = count($services);

    // Pagination settings
    $servicesPerPage = 12;
    $totalPages = ceil($totalServices / $servicesPerPage);
@endphp

    @php
        // Page Header data
        $headerTitle = getContent($sections, 'services-header', 'services-header-1_title', 'Explore our <span>services</span>');
        $headerBreadcrumb = getContent($sections, 'services-header', 'services-header-1_breadcrumb', 'Services');
    @endphp

    <div class="page-header bg-section vcms-editable-section" data-section="services-header" data-item-id="services-header-1">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="page-header">
                <i class="fa-solid fa-pen"></i> Edit Page Header
            </button>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="services-header-1"
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

    <div class="page-services vcms-editable-section" data-section="services" data-section-id="2">
        <div class="container">
            @if ($totalPages > 1)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="admin-services-pagination d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            @for ($page = 1; $page <= $totalPages; $page++)
                                <button type="button"
                                    class="btn admin-service-page-btn {{ $page == 1 ? 'active' : '' }}"
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

            <div class="row vcms-editable-item position-relative service-items-container" data-edit-type="service-list" data-item-id="services-list-1" style="min-height: 400px;">
                <div class="vcms-edit-indicator-small" title="Manage Services List"
                    style="position: absolute; top: -10px; right: 20px; z-index: 10;">
                    <i class="fa-solid fa-concierge-bell"></i>
                </div>

                @if ($totalServices == 0)
                    <div class="col-12 text-center py-5" style="color: #888;">
                        <i class="fa-solid fa-folder-open" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>No services yet. Click the <i class="fa-solid fa-concierge-bell"></i> button to add services.</p>
                    </div>
                @endif

                @foreach ($services as $index => $service)
                    @php
                        $serviceId = $service['id'];
                        $serviceTitle = getContent($sections, 'services', "services-list-1_service_{$serviceId}_title", $service['title'] ?? 'Service Title');
                        $serviceDesc = getContent($sections, 'services', "services-list-1_service_{$serviceId}_desc", 'We provide excellent service for your needs.');
                        $serviceImage = getContent($sections, 'services', "services-list-1_service_{$serviceId}_image", "images/service-image-" . (($index % 6) + 1) . ".jpg");

                        // Link to admin service detail page
                        $detailLink = route('admin.pages.service.detail', ['serviceId' => $serviceId]);

                        // Calculate animation delay
                        $delay = ($index % 3) * 0.2;

                        // Calculate page number for this service
                        $pageNum = floor($index / $servicesPerPage) + 1;
                    @endphp
                    <div class="col-lg-4 col-md-6 admin-service-item" data-page="{{ $pageNum }}" style="{{ $pageNum > 1 ? 'display: none;' : '' }}">
                        <div class="service-item wow fadeInUp" data-service-id="{{ $serviceId }}" @if($delay > 0) data-wow-delay="{{ $delay }}s" @endif>
                            <div class="service-image">
                                <a href="{{ $detailLink }}" data-cursor-text="Edit">
                                    <figure class="image-anime">
                                        <img src="{{ asset($serviceImage) }}" alt="{{ $serviceTitle }}">
                                    </figure>
                                </a>
                            </div>
                            <div class="service-body">
                                <div class="service-content">
                                    <h3><a href="{{ $detailLink }}">{{ $serviceTitle }}</a></h3>
                                    <p>{{ $serviceDesc }}</p>
                                </div>
                                <div class="service-btn">
                                    <a href="{{ $detailLink }}" title="Edit Service Details"><img src="{{ asset('images/arrrow-light.svg') }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Creative Tools Section Start -->


@endsection

@push('scripts')
    <script src="{{ asset('js/admin-vcms.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Admin services page pagination
            $('.admin-service-page-btn').on('click', function() {
                const page = $(this).data('page');

                // Update active button
                $('.admin-service-page-btn').css({
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
                $('.admin-service-item').hide();
                $('.admin-service-item[data-page="' + page + '"]').show();

                // Scroll to top of services section
                $('html, body').animate({
                    scrollTop: $('.page-services').offset().top - 100
                }, 300);
            });
        });
    </script>
@endpush
