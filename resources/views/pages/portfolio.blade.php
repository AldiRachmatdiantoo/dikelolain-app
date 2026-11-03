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
                    <div class="alert alert-warning d-flex justify-content-between align-items-center portfolio-banner">
                        <div>
                            <h5 class="mb-1">
                                <i class="fa-solid fa-flask me-2"></i>Try Our New Horizontal Slider!
                            </h5>
                            <p class="mb-0">Experience portfolio items sliding horizontally instead of vertically scrolling</p>
                        </div>
                        <a href="{{ route('portfolio.experiment') }}" class="btn btn-dark btn-lg">
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
                            <div class="col-12 text-center py-5 text-dim">
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
                            <div class="text-center mt-2 text-note">
                                Showing <span id="portfolio-showing-start">1</span>-<span id="portfolio-showing-end">{{ min($projectsPerPage, $totalProjects) }}</span> of <span id="portfolio-total-projects">{{ $totalProjects }}</span> projects
                            </div>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

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
