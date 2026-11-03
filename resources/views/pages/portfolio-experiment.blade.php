@extends('layouts.app')

@section('title', 'Portfolio Slider - Dikelolain')

@php
    $totalProjects = $projects->count();
    $videoCount = $projects->filter->isVideo()->count();
    $imageCount = $totalProjects - $videoCount;
@endphp

@section('content')

    <x-vcms.settings :keys="['portfolio.header.title', 'portfolio.experiment_breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('portfolio.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('portfolio.experiment_breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

    <div class="page-works-experiment">
        <div class="container">
            <!-- Back to Normal View Banner -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="alert alert-info d-flex justify-content-between align-items-center portfolio-banner portfolio-banner-info">
                        <div>
                            <h5 class="mb-1">
                                <i class="fa-solid fa-lightbulb me-2"></i>Experimental Portfolio View
                            </h5>
                            <p class="mb-0">This is an experimental horizontal slider. Return to normal grid view anytime.</p>
                        </div>
                        <a href="{{ route('portfolio') }}" class="btn btn-light btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Back to Grid View
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="experiment-header mb-5 text-center">
                        <h2 class="experiment-title">Horizontal Slider Portfolio</h2>
                        <p class="experiment-subtitle">Swipe or use navigation to explore projects</p>
                    </div>
                </div>

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
                    <!-- Swiper Container -->
                    <div class="swiper portfolio-swiper">
                        <div class="swiper-wrapper">
                            @forelse ($projects as $project)
                                <div class="swiper-slide portfolio-slide {{ $project->media_type }}">
                                    @include('partials.work-item')
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <div class="text-center py-5 text-dim">
                                        <p>No projects yet.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>

                        <!-- Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                    <!-- Slider Info -->
                    <div class="slider-info text-center mt-4">
                        <p class="text-note">
                            <span id="current-slide">1</span> / <span id="total-slides">{{ $totalProjects }}</span> projects
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
// Horizontal slider of the projects (Swiper comes with js/site.min.js), with the Portfolio page's category filter.
(function ($) {
    'use strict';

    var $slides = $('.portfolio-slide');
    var $filters = $('.our-work-nav a');
    var filter = '*';
    var swiper = new Swiper('.portfolio-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        centeredSlides: true,
        grabCursor: true,
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        breakpoints: { 768: { spaceBetween: 30 }, 1024: { spaceBetween: 40 } },
        on: { slideChange: updateCounter },
    });

    function updateCounter() {
        $('#current-slide').text(swiper.activeIndex + 1);
        $('#total-slides').text($slides.filter(filter).length);
    }

    $filters.on('click', function (event) {
        event.preventDefault();
        $filters.removeClass('active-btn');
        filter = $(this).addClass('active-btn').attr('data-filter');
        $slides.prop('hidden', true).filter(filter).prop('hidden', false);
        swiper.update();
        swiper.slideTo(0);
        updateCounter();
    });

    updateCounter();
})(jQuery);
</script>
@endpush
