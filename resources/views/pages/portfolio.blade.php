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
                    <div class="our-work-nav">
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
                            <div class="col-lg-4 col-md-6 work-item-box {{ $project->media_type }} portfolio-item" {{ $loop->index >= $projectsPerPage ? 'hidden' : '' }}>
                                @include('partials.work-item')
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
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled" id="portfolio-prev-page">
                                    <a class="page-link" href="#" data-goto="prev" aria-label="Sebelumnya"><i class="fa-solid fa-chevron-left"></i></a>
                                </li>
                                @for ($i = 1; $i <= $totalPages; $i++)
                                    <li class="page-item page-number {{ $i == 1 ? 'active' : '' }}">
                                        <a class="page-link" href="#" data-goto="{{ $i }}">{{ $i }}</a>
                                    </li>
                                @endfor
                                <li class="page-item" id="portfolio-next-page">
                                    <a class="page-link" href="#" data-goto="next" aria-label="Berikutnya"><i class="fa-solid fa-chevron-right"></i></a>
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
    animation: portfolio-item-in 0.4s ease;
}
@keyframes portfolio-item-in {
    from { opacity: 0; transform: translateY(20px); }
}

/* Prevent layout shift on pagination */
.work-item-boxes {
    min-height: 600px;
}
</style>
@endpush

@push('scripts')
<script>
// Category filter and pagination of the grid: 12 projects per page, every filter starts on page 1.
(function ($) {
    'use strict';

    var PER_PAGE = {{ $projectsPerPage }};
    var $items = $('#portfolio-container .portfolio-item');
    var $filters = $('.our-work-nav a');
    var $pagination = $('.portfolio-pagination');
    var $nextPage = $('#portfolio-next-page');
    var filter = '*';
    var page = 1;

    function matchingItems() {
        return $items.filter(filter);
    }

    function render() {
        var $matching = matchingItems();
        var total = $matching.length;
        var pages = Math.max(1, Math.ceil(total / PER_PAGE));
        var first = (page - 1) * PER_PAGE;

        $items.prop('hidden', true);
        $matching.slice(first, first + PER_PAGE).prop('hidden', false);

        $pagination.find('.page-number').remove();
        for (var number = 1; number <= pages; number++) {
            $('<li class="page-item page-number"><a class="page-link" href="#"></a></li>')
                .toggleClass('active', number === page)
                .children().attr('data-goto', number).text(number).end()
                .insertBefore($nextPage);
        }
        $('#portfolio-prev-page').toggleClass('disabled', page === 1);
        $nextPage.toggleClass('disabled', page === pages);
        $('#portfolio-showing-start').text(first + 1);
        $('#portfolio-showing-end').text(Math.min(first + PER_PAGE, total));
        $('#portfolio-total-projects').text(total);
        $pagination.toggle(pages > 1);
    }

    $filters.on('click', function (event) {
        event.preventDefault();
        $filters.removeClass('active-btn');
        filter = $(this).addClass('active-btn').attr('data-filter');
        page = 1;
        render();
    });

    $pagination.on('click', '[data-goto]', function (event) {
        event.preventDefault();
        var target = $(this).attr('data-goto');
        var pages = Math.ceil(matchingItems().length / PER_PAGE);
        var next = target === 'prev' ? page - 1 : target === 'next' ? page + 1 : Number(target);

        if (next < 1 || next > pages || next === page) {
            return;
        }
        page = next;
        render();
        $('html, body').animate({ scrollTop: $('.page-works').offset().top - 100 }, 300);
    });
})(jQuery);
</script>
@endpush
