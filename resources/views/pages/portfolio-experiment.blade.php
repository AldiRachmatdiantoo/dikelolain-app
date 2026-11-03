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
                    <div class="alert alert-info d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%); border: none; border-radius: 15px; padding: 20px 30px;">
                        <div>
                            <h5 class="mb-1" style="color: #fff; font-weight: 700;">
                                <i class="fa-solid fa-lightbulb me-2"></i>Experimental Portfolio View
                            </h5>
                            <p class="mb-0" style="color: #e3f2fd; font-size: 14px;">This is an experimental horizontal slider. Return to normal grid view anytime.</p>
                        </div>
                        <a href="{{ route('portfolio') }}" class="btn btn-light btn-lg" style="background: #fff; border: none; padding: 12px 30px; font-weight: 600; color: #1976D2;">
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
                                    <div class="text-center py-5" style="color: #888;">
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
                        <p style="color: #888; font-size: 14px;">
                            <span id="current-slide">1</span> / <span id="total-slides">{{ $totalProjects }}</span> projects
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
/* Experiment Header */
.experiment-header {
    padding: 30px 0;
}
.experiment-title {
    font-size: 42px;
    font-weight: 700;
    color: #ffcc01;
    margin-bottom: 10px;
}
.experiment-subtitle {
    font-size: 16px;
    color: #888;
    margin: 0;
}

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

/* Swiper Styles */
.portfolio-swiper {
    width: 100%;
    padding: 40px 0 80px 0;
}

.portfolio-swiper .swiper-slide {
    width: 400px;
    display: flex;
    justify-content: center;
}

.portfolio-swiper .work-item {
    width: 100%;
    max-width: 400px;
}

/* Swiper Navigation Buttons */
.portfolio-swiper .swiper-button-next,
.portfolio-swiper .swiper-button-prev {
    width: 50px;
    height: 50px;
    background: #252525;
    border: 2px solid #ffcc01;
    border-radius: 50%;
    color: #ffcc01;
    transition: all 0.3s ease;
}

.portfolio-swiper .swiper-button-next:after,
.portfolio-swiper .swiper-button-prev:after {
    font-size: 20px;
    font-weight: 900;
}

.portfolio-swiper .swiper-button-next:hover,
.portfolio-swiper .swiper-button-prev:hover {
    background: #ffcc01;
    color: #1a1a1a;
    transform: scale(1.1);
}

/* Swiper Pagination */
.portfolio-swiper .swiper-pagination {
    bottom: 20px;
}

.portfolio-swiper .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: #555;
    opacity: 1;
    transition: all 0.3s ease;
}

.portfolio-swiper .swiper-pagination-bullet-active {
    background: #ffcc01;
    width: 30px;
    border-radius: 6px;
}

/* Work Item Styles */
.work-item {
    background: #1a1a1a;
    border-radius: 30px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.work-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(255, 204, 1, 0.2);
}

.work-image {
    position: relative;
    overflow: hidden;
    border-radius: 30px 30px 0 0;
}

.work-image img {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.5s ease;
}

.work-item:hover .work-image img {
    transform: scale(1.05);
}

.work-body {
    padding: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.work-content {
    flex: 1;
}

.work-content h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
}

.work-content h3 a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.work-content h3 a:hover {
    color: #ffcc01;
}

.work-content p {
    color: #888;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

.work-btn a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: #ffcc01;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.work-btn a:hover {
    transform: rotate(45deg) scale(1.1);
    box-shadow: 0 5px 15px rgba(255, 204, 1, 0.4);
}

.work-btn img {
    width: 20px;
    filter: brightness(0);
}

/* Slider Info */
.slider-info {
    margin-top: 30px;
}

/* Responsive */
@media (max-width: 768px) {
    .portfolio-swiper .swiper-slide {
        width: 320px;
    }

    .experiment-title {
        font-size: 32px;
    }

    .portfolio-swiper .swiper-button-next,
    .portfolio-swiper .swiper-button-prev {
        width: 40px;
        height: 40px;
    }

    .portfolio-swiper .swiper-button-next:after,
    .portfolio-swiper .swiper-button-prev:after {
        font-size: 16px;
    }
}

@media (max-width: 576px) {
    .portfolio-swiper .swiper-slide {
        width: 280px;
    }

    .work-body {
        padding: 20px;
    }

    .work-content h3 {
        font-size: 20px;
    }
}
</style>
@endpush

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
