@extends('layouts.app')

@section('title', 'Our Portfolio - Dikelolain')

@php
    // Helper function to get content from database
    if (!function_exists('getPortfolioContent')) {
        function getPortfolioContent($sections, $sectionKey, $contentKey, $default = '') {
            $section = collect($sections)->firstWhere('key', $sectionKey);
            if (!$section || !isset($section['contents'])) {
                return $default;
            }
            $content = collect($section['contents'])->firstWhere('key', $contentKey);
            return $content ? $content['value'] : $default;
        }
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
            $mediaType = getPortfolioContent($sections, 'our-work', "project-list-1_project_{$id}_media_type", 'video');
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
    $projectsPerPage = 12;
    $totalPages = ceil($totalProjects / $projectsPerPage);

    // Page Header data
    $headerTitle = getPortfolioContent($sections, 'portfolio-header', 'portfolio-header-1_title', 'Our <span>portfolio</span>');
    $headerBreadcrumb = getPortfolioContent($sections, 'portfolio-header', 'portfolio-header-1_breadcrumb', 'Portfolio');
@endphp

@section('content')

    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>

    <div class="page-works">
        <div class="container">
            <!-- Experiment Link Banner -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="alert alert-warning d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #ffcc01 0%, #ffa500 100%); border: none; border-radius: 15px; padding: 20px 30px;">
                        <div>
                            <h5 class="mb-1" style="color: #1a1a1a; font-weight: 700;">
                                <i class="fa-solid fa-flask me-2"></i>Try Our New Horizontal Slider!
                            </h5>
                            <p class="mb-0" style="color: #333; font-size: 14px;">Experience portfolio items sliding horizontally instead of vertically scrolling</p>
                        </div>
                        <a href="{{ route('portfolio.experiment') }}" class="btn btn-dark btn-lg" style="background: #1a1a1a; border: none; padding: 12px 30px; font-weight: 600;">
                            View Experiment <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

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
                    <div class="row work-item-boxes align-items-center" id="portfolio-container">
                        @if (count($projects) == 0)
                            <div class="col-12 text-center py-5" style="color: #888;">
                                <p>No projects yet.</p>
                            </div>
                        @endif

                        @foreach ($projects as $index => $project)
                            @php
                                $projectId = $project['id'];
                                $projectTitle = getPortfolioContent($sections, 'our-work', "project-list-1_project_{$projectId}_title", $project['title'] ?? 'Project Title');
                                $projectDesc = getPortfolioContent($sections, 'our-work', "project-list-1_project_{$projectId}_desc", 'Project description');
                                $projectLink = getPortfolioContent($sections, 'our-work', "project-list-1_project_{$projectId}_link", '#');
                                $projectMediaSrc = getPortfolioContent($sections, 'our-work', "project-list-1_project_{$projectId}_media_src", 'https://www.youtube.com/embed/EszaEO236lw');
                                $projectMediaType = $project['media_type'];
                                $pageNum = floor($index / $projectsPerPage) + 1;
                                $filterClass = $projectMediaType == 'video' ? 'video' : 'image';
                            @endphp
                            <div class="col-lg-4 col-md-6 work-item-box {{ $filterClass }} portfolio-item" data-page="{{ $pageNum }}">
                                <div class="work-item wow fadeInUp" data-wow-delay="{{ 0.2 * ($index % 3) }}s">
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
                                               data-portfolio="{{ $projectId }}"
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

                @if ($totalPages > 1)
                    <div class="col-lg-12">
                        <nav class="portfolio-pagination mt-5">
                            <ul class="pagination justify-content-center" id="portfolio-pagination">
                                <li class="page-item disabled" id="portfolio-prev-page">
                                    <a class="page-link" href="#" onclick="changePortfolioPage('prev'); return false;">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </a>
                                </li>
                                @for ($i = 1; $i <= $totalPages; $i++)
                                    <li class="page-item {{ $i == 1 ? 'active' : '' }}" data-page="{{ $i }}">
                                        <a class="page-link" href="#" onclick="changePortfolioPage({{ $i }}); return false;">{{ $i }}</a>
                                    </li>
                                @endfor
                                <li class="page-item {{ $totalPages == 1 ? 'disabled' : '' }}" id="portfolio-next-page">
                                    <a class="page-link" href="#" onclick="changePortfolioPage('next'); return false;">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                            <div class="text-center mt-2" style="color: #888; font-size: 14px;">
                                Showing <span id="portfolio-showing-start">1</span>-<span id="portfolio-showing-end">{{ min($projectsPerPage, $totalProjects) }}</span> of <span id="portfolio-total-projects">{{ $totalProjects }}</span> projects
                            </div>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>

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
    background: #ffcc01;
    border-color: #ffcc01;
    color: #1a1a1a;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 204, 1, 0.4);
}
.our-work-nav ul li a.active-btn {
    background: #ffcc01;
    border-color: #ffcc01;
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
.portfolio-pagination .pagination {
    gap: 8px;
}
.portfolio-pagination .page-item .page-link {
    background: #252525;
    border: 1px solid #3a3a3a;
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.portfolio-pagination .page-item .page-link:hover {
    background: #FFD700;
    border-color: #FFD700;
    color: #161616;
}
.portfolio-pagination .page-item.active .page-link {
    background: #FFD700;
    border-color: #FFD700;
    color: #161616;
    font-weight: 700;
}
.portfolio-pagination .page-item.disabled .page-link {
    background: #1a1a1a;
    border-color: #2a2a2a;
    color: #555;
    cursor: not-allowed;
}
.portfolio-item {
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

    var currentPortfolioPage = 1;
    var totalPortfolioPages = {{ $totalPages }};
    var portfolioProjectsPerPage = {{ $projectsPerPage }};
    var totalPortfolioProjects = {{ $totalProjects }};
    var currentFilter = '*';
    var $portfolio = null;

    // Combined filter function for Isotope
    function getIsotopeFilter() {
        var pageFilter = '[data-page="' + currentPortfolioPage + '"]';
        if (currentFilter === '*') {
            return pageFilter;
        }
        return currentFilter + pageFilter;
    }

    // Apply filter to Isotope
    function applyFilter() {
        if ($portfolio && $portfolio.data('isotope')) {
            $portfolio.isotope({ filter: getIsotopeFilter() });
        }
    }

    // Update pagination UI
    function updatePaginationUI() {
        // Update pagination buttons
        $('#portfolio-pagination .page-item[data-page]').removeClass('active');
        $('#portfolio-pagination .page-item[data-page="' + currentPortfolioPage + '"]').addClass('active');

        // Update prev/next buttons
        $('#portfolio-prev-page').toggleClass('disabled', currentPortfolioPage === 1);
        $('#portfolio-next-page').toggleClass('disabled', currentPortfolioPage === totalPortfolioPages);

        // Update showing text
        var start = (currentPortfolioPage - 1) * portfolioProjectsPerPage + 1;
        var end = Math.min(currentPortfolioPage * portfolioProjectsPerPage, totalPortfolioProjects);
        $('#portfolio-showing-start').text(start);
        $('#portfolio-showing-end').text(end);
    }

    // Pagination function
    window.changePortfolioPage = function(page) {
        if (page === 'prev') {
            if (currentPortfolioPage > 1) currentPortfolioPage--;
            else return;
        } else if (page === 'next') {
            if (currentPortfolioPage < totalPortfolioPages) currentPortfolioPage++;
            else return;
        } else {
            currentPortfolioPage = parseInt(page);
        }

        // Apply combined filter (page + category)
        applyFilter();
        updatePaginationUI();
        
        // Scroll to top of portfolio section
        $('html, body').animate({
            scrollTop: $('.page-works').offset().top - 100
        }, 300);
    };

    // Initialize Isotope
    $(window).on('load', function() {
        $portfolio = $('.work-item-boxes');
        
        if ($.fn.isotope && $portfolio.length) {
            $portfolio.isotope({
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
            $portfolio.isotope({ filter: getIsotopeFilter() });

            // Re-layout after images loaded
            $portfolio.imagesLoaded().progress(function() {
                $portfolio.isotope('layout');
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
        currentFilter = filterValue;
        
        // Reset to page 1 when changing filter
        currentPortfolioPage = 1;
        
        // Count visible items for this filter across all pages
        var visibleCount = 0;
        if (filterValue === '*') {
            visibleCount = totalPortfolioProjects;
        } else {
            visibleCount = $('.portfolio-item' + filterValue).length;
        }
        
        // Update total pages based on filter
        totalPortfolioPages = Math.ceil(visibleCount / portfolioProjectsPerPage) || 1;
        
        // Rebuild pagination if needed
        rebuildPagination(visibleCount);
        
        // Apply filter
        applyFilter();
        updatePaginationUI();
    });

    // Rebuild pagination buttons
    function rebuildPagination(totalItems) {
        var $pagination = $('#portfolio-pagination');
        var newTotalPages = Math.ceil(totalItems / portfolioProjectsPerPage) || 1;
        
        // Remove existing page numbers (keep prev/next)
        $pagination.find('.page-item[data-page]').remove();
        
        // Add new page numbers
        var $nextBtn = $('#portfolio-next-page');
        for (var i = 1; i <= newTotalPages; i++) {
            var activeClass = i === 1 ? 'active' : '';
            var $pageItem = $('<li class="page-item ' + activeClass + '" data-page="' + i + '"><a class="page-link" href="#" onclick="changePortfolioPage(' + i + '); return false;">' + i + '</a></li>');
            $nextBtn.before($pageItem);
        }
        
        totalPortfolioPages = newTotalPages;
        
        // Update showing text
        var end = Math.min(portfolioProjectsPerPage, totalItems);
        $('#portfolio-showing-end').text(end);
        $('#portfolio-total-projects').text(totalItems);
        
        // Hide pagination if only 1 page
        $pagination.closest('nav').toggle(newTotalPages > 1);
    }
})(jQuery);
</script>
@endpush
