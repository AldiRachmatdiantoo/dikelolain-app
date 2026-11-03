@extends('admin.layouts.admin')

@section('title', 'Edit Portfolio Page')
@section('page-title', 'Portfolio Page - VCMS Editor')

@php
    $previewUrl = route('portfolio');
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

    // Get projects from database
    $projects = [];
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
            $projects[] = [
                'id' => $id,
                'title' => $title,
                'media_type' => $mediaType
            ];
        }
    }

    // Count for filter tabs
    $totalProjects = count($projects);
    $videoCount = count(array_filter($projects, fn($p) => $p['media_type'] == 'video'));
    $imageCount = count(array_filter($projects, fn($p) => $p['media_type'] == 'image'));

    // Pagination
    $adminProjectsPerPage = 12;
    $adminTotalPages = ceil($totalProjects / $adminProjectsPerPage);

    // Page Header data
    $headerTitle = getContent($sections, 'portfolio-header', 'portfolio-header-1_title', 'Our <span>portfolio</span>');
    $headerBreadcrumb = getContent($sections, 'portfolio-header', 'portfolio-header-1_breadcrumb', 'Portfolio');
@endphp

    <div class="page-header bg-section vcms-editable-section" data-section="portfolio-header" data-item-id="portfolio-header-1">
        <div class="vcms-edit-indicator">
            <button class="vcms-edit-btn" data-edit-type="page-header">
                <i class="fa-solid fa-pen"></i> Edit Header
            </button>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="portfolio-header-1"
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

    <div class="page-works vcms-editable-section" data-section="our-work" data-section-id="3" data-item-id="portfolio-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="our-work-nav wow fadeInUp">
                        <ul>
                            <li><a href="#" class="active-btn" data-filter="*">All projects <span class="count-badge">{{ $totalProjects }}</span></a></li>
                            <li><a href="#" data-filter=".video">Video <span class="count-badge">{{ $videoCount }}</span></a></li>
                            <li><a href="#" data-filter=".image">Design <span class="count-badge">{{ $imageCount }}</span></a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row work-item-boxes align-items-center vcms-editable-item position-relative"
                         data-edit-type="project-list" data-item-id="project-list-1" style="min-height: 100px;">
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
                                $adminPageNum = floor($index / $adminProjectsPerPage) + 1;
                                $projectId = $project['id'];
                                $projectTitle = getContent($sections, 'our-work', "project-list-1_project_{$projectId}_title", $project['title'] ?? 'Project Title');
                                $projectDesc = getContent($sections, 'our-work', "project-list-1_project_{$projectId}_desc", 'Project description');
                                $projectLink = getContent($sections, 'our-work', "project-list-1_project_{$projectId}_link", '#');
                                $projectMediaSrc = getContent($sections, 'our-work', "project-list-1_project_{$projectId}_media_src", 'https://www.youtube.com/embed/EszaEO236lw');
                                $projectMediaType = $project['media_type'];
                                $filterClass = $projectMediaType == 'video' ? 'video' : 'image';
                            @endphp
                            <div class="col-lg-4 col-md-6 work-item-box {{ $filterClass }} admin-portfolio-item"
                                 data-page="{{ $adminPageNum }}">
                                <div class="work-item wow fadeInUp" data-wow-delay="{{ 0.2 * ($index % 3) }}s"
                                     data-project-id="{{ $projectId }}" data-media-type="{{ $projectMediaType }}">
                                    <div class="work-image">
                                        @if ($projectMediaType == 'video')
                                            <div class="ratio" style="--bs-aspect-ratio: 76%; border-radius: 30px; overflow: hidden;">
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
                                            </div>
                                        @else
                                            <a href="{{ $projectLink }}" data-cursor-text="View">
                                                <figure class="image-anime">
                                                    <img src="{{ asset($projectMediaSrc) }}" alt="{{ $projectTitle }}">
                                                </figure>
                                            </a>
                                        @endif
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

                @if ($adminTotalPages > 1)
                    <div class="col-lg-12">
                        <nav class="admin-portfolio-pagination mt-5">
                            <ul class="pagination justify-content-center" id="admin-portfolio-pagination">
                                <li class="page-item disabled" id="admin-portfolio-prev-page">
                                    <a class="page-link" href="#" onclick="changeAdminPortfolioPage('prev'); return false;">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                </li>
                                @for ($i = 1; $i <= $adminTotalPages; $i++)
                                    <li class="page-item {{ $i == 1 ? 'active' : '' }}" data-page="{{ $i }}">
                                        <a class="page-link" href="#" onclick="changeAdminPortfolioPage({{ $i }}); return false;">{{ $i }}</a>
                                    </li>
                                @endfor
                                <li class="page-item {{ $adminTotalPages == 1 ? 'disabled' : '' }}" id="admin-portfolio-next-page">
                                    <a class="page-link" href="#" onclick="changeAdminPortfolioPage('next'); return false;">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center mt-2" style="color: #888; font-size: 14px;">
                                Showing <span id="admin-portfolio-showing-start">1</span>-<span id="admin-portfolio-showing-end">{{ min($adminProjectsPerPage, $totalProjects) }}</span> of <span id="admin-portfolio-total-projects">{{ $totalProjects }}</span> projects
                            </div>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <br><br><br><br>
@endsection

@push('styles')
<style>
/* Filter Tabs */
.our-work-nav {
    margin-bottom: 50px;
}
.our-work-nav ul {
    display: flex;
    justify-content: center;
    gap: 15px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
}
.our-work-nav ul li a {
    display: inline-flex;
    align-items: center;
    padding: 12px 28px;
    background: transparent;
    border: 2px solid #555;
    border-radius: 50px;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}
.our-work-nav ul li a:hover {
    background: #ffcc01 !important;
    border-color: #ffcc01 !important;
    color: #1a1a1a;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 204, 1, 0.4);
}
.our-work-nav ul li a.active-btn {
    background: #ffcc01 !important;
    border-color: #ffcc01 !important;
    color: #1a1a1a;
    box-shadow: 0 4px 15px rgba(255, 204, 1, 0.5);
}
.count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    color: inherit;
    font-size: 11px;
    font-weight: 700;
    min-width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-left: 10px;
    padding: 0 6px;
}
.our-work-nav ul li a.active-btn .count-badge,
.our-work-nav ul li a:hover .count-badge {
    background: rgba(0, 0, 0, 0.2);
}

/* Pagination */
.admin-portfolio-pagination .pagination {
    gap: 8px;
}
.admin-portfolio-pagination .page-item .page-link {
    background: #252525;
    border: 1px solid #3a3a3a;
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.admin-portfolio-pagination .page-item .page-link:hover {
    background: #FFD700;
    border-color: #FFD700;
    color: #161616;
}
.admin-portfolio-pagination .page-item.active .page-link {
    background: #FFD700;
    border-color: #FFD700;
    color: #161616;
    font-weight: 700;
}
.admin-portfolio-pagination .page-item.disabled .page-link {
    background: #1a1a1a;
    border-color: #2a2a2a;
    color: #555;
    cursor: not-allowed;
}
.admin-portfolio-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Prevent layout shift on pagination */
.work-item-boxes {
    min-height: 600px;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/isotope.min.js') }}"></script>
<script>
(function ($) {
    "use strict";

    var adminPortfolioCurrentPage = 1;
    var adminPortfolioTotalPages = {{ $adminTotalPages }};
    var adminPortfolioProjectsPerPage = {{ $adminProjectsPerPage }};
    var adminPortfolioTotalProjects = {{ $totalProjects }};
    var adminCurrentFilter = '*';
    var $adminPortfolio = null;

    // Combined filter function for Isotope
    function getAdminIsotopeFilter() {
        var pageFilter = '[data-page="' + adminPortfolioCurrentPage + '"]';
        if (adminCurrentFilter === '*') {
            return pageFilter;
        }
        return adminCurrentFilter + pageFilter;
    }

    // Apply filter to Isotope
    function applyAdminFilter() {
        if ($adminPortfolio && $adminPortfolio.data('isotope')) {
            $adminPortfolio.isotope({ filter: getAdminIsotopeFilter() });
        }
    }

    // Update pagination UI
    function updateAdminPaginationUI() {
        // Update pagination buttons
        $('#admin-portfolio-pagination .page-item[data-page]').removeClass('active');
        $('#admin-portfolio-pagination .page-item[data-page="' + adminPortfolioCurrentPage + '"]').addClass('active');

        // Update prev/next buttons
        $('#admin-portfolio-prev-page').toggleClass('disabled', adminPortfolioCurrentPage === 1);
        $('#admin-portfolio-next-page').toggleClass('disabled', adminPortfolioCurrentPage === adminPortfolioTotalPages);

        // Update showing text
        var start = (adminPortfolioCurrentPage - 1) * adminPortfolioProjectsPerPage + 1;
        var end = Math.min(adminPortfolioCurrentPage * adminPortfolioProjectsPerPage, adminPortfolioTotalProjects);
        $('#admin-portfolio-showing-start').text(start);
        $('#admin-portfolio-showing-end').text(end);
    }

    // Pagination function
    window.changeAdminPortfolioPage = function(page) {
        if (page === 'prev') {
            if (adminPortfolioCurrentPage > 1) adminPortfolioCurrentPage--;
            else return;
        } else if (page === 'next') {
            if (adminPortfolioCurrentPage < adminPortfolioTotalPages) adminPortfolioCurrentPage++;
            else return;
        } else {
            adminPortfolioCurrentPage = parseInt(page);
        }

        // Apply combined filter (page + category)
        applyAdminFilter();
        updateAdminPaginationUI();

        // Scroll to top of portfolio section
        $('html, body').animate({
            scrollTop: $('.page-works').offset().top - 100
        }, 300);
    };

    // Initialize Isotope
    $(window).on('load', function() {
        $adminPortfolio = $('.work-item-boxes');

        if ($.fn.isotope && $adminPortfolio.length) {
            $adminPortfolio.isotope({
                itemSelector: '.work-item-box',
                layoutMode: 'fitRows',
                transitionDuration: '0.5s',
                stagger: 30,
                percentPosition: true,
                fitRows: {
                    gutter: 0
                },
                hiddenStyle: {
                    opacity: 0,
                    transform: 'scale(0.8)'
                },
                visibleStyle: {
                    opacity: 1,
                    transform: 'scale(1)'
                }
            });

            // Initial filter - show only page 1
            $adminPortfolio.isotope({ filter: getAdminIsotopeFilter() });

            // Re-layout after images loaded
            $adminPortfolio.imagesLoaded().progress(function() {
                $adminPortfolio.isotope('layout');
            });
        }
    });

    // Category filter click handler
    $('.our-work-nav ul li a').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);
        var filterValue = $this.attr('data-filter');

        $('.our-work-nav ul li a').removeClass('active-btn');
        $this.addClass('active-btn');

        // Update current filter
        adminCurrentFilter = filterValue;

        // Reset to page 1 when changing filter
        adminPortfolioCurrentPage = 1;

        // Count visible items for this filter
        var visibleCount = 0;
        if (filterValue === '*') {
            visibleCount = adminPortfolioTotalProjects;
        } else {
            visibleCount = $('.admin-portfolio-item' + filterValue).length;
        }

        // Update total pages based on filter
        adminPortfolioTotalPages = Math.ceil(visibleCount / adminPortfolioProjectsPerPage) || 1;

        // Rebuild pagination
        rebuildAdminPagination(visibleCount);

        // Apply filter
        applyAdminFilter();
        updateAdminPaginationUI();
    });

    // Rebuild pagination buttons
    function rebuildAdminPagination(totalItems) {
        var $pagination = $('#admin-portfolio-pagination');
        var newTotalPages = Math.ceil(totalItems / adminPortfolioProjectsPerPage) || 1;

        // Remove existing page numbers (keep prev/next)
        $pagination.find('.page-item[data-page]').remove();

        // Add new page numbers
        var $nextBtn = $('#admin-portfolio-next-page');
        for (var i = 1; i <= newTotalPages; i++) {
            var activeClass = i === 1 ? 'active' : '';
            var $pageItem = $('<li class="page-item ' + activeClass + '" data-page="' + i + '"><a class="page-link" href="#" onclick="changeAdminPortfolioPage(' + i + '); return false;">' + i + '</a></li>');
            $nextBtn.before($pageItem);
        }

        adminPortfolioTotalPages = newTotalPages;

        // Update showing text
        var end = Math.min(adminPortfolioProjectsPerPage, totalItems);
        $('#admin-portfolio-showing-end').text(end);
        $('#admin-portfolio-total-projects').text(totalItems);

        // Hide pagination if only 1 page
        $pagination.closest('nav').toggle(newTotalPages > 1);
    }
})(jQuery);
</script>
@endpush
