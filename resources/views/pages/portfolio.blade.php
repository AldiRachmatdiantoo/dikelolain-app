@extends('layouts.app')

@section('title', 'Our Portfolio - Dikelolain')

@php
    $totalProjects = $projects->count();
    $videoCount = $projects->filter->isVideo()->count();
    $imageCount = $totalProjects - $videoCount;

    // Pagination
    $projectsPerPage = 12;
    $totalPages = (int) ceil($totalProjects / $projectsPerPage);
@endphp

@section('content')

    <x-vcms.settings :keys="['portfolio.header.title', 'portfolio.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('portfolio.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('portfolio.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

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
                        @forelse ($projects as $project)
                            <div class="col-lg-4 col-md-6 work-item-box {{ $project->media_type }} portfolio-item" data-page="{{ intdiv($loop->index, $projectsPerPage) + 1 }}">
                                @include('partials.work-item', ['delay' => 0.2 * ($loop->index % 3)])
                            </div>
                        @empty
                            <div class="col-12 text-center py-5" style="color: #888;">
                                <p>No projects yet.</p>
                            </div>
                        @endforelse
                    </div>

                    <x-vcms.add resource="projects" :fields="$projectFields" label="project" />
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

            // Initial filter - show only page 1 (images are loaded by now: this runs on window load)
            $portfolio.isotope({ filter: getIsotopeFilter() });
        }
    });

    // Number the pages within the current filter, so a filtered list starts on page 1 too
    function paginate() {
        var $all = $('.portfolio-item');
        var $matching = currentFilter === '*' ? $all : $all.filter(currentFilter);

        $all.attr('data-page', 0);
        $matching.each(function (index) {
            $(this).attr('data-page', Math.floor(index / portfolioProjectsPerPage) + 1);
        });
    }

    // Category filter click handler
    $('.our-work-nav ul li a').on('click', function(e) {
        e.preventDefault();
        // function.js binds a plain filter (no pages) to the same links later; this handler replaces it here.
        e.stopImmediatePropagation();

        var $this = $(this);
        var filterValue = $this.attr('data-filter');

        $('.our-work-nav ul li a').removeClass('active-btn');
        $this.addClass('active-btn');

        // Update current filter
        currentFilter = filterValue;

        // Reset to page 1 when changing filter
        currentPortfolioPage = 1;
        paginate();

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
