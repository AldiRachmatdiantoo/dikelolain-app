<!DOCTYPE html>
<html lang="zxx">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	{{-- $seo comes from App\View\Composers\SeoComposer; detail pages set meta_description themselves. --}}
	<meta name="description" content="@yield('meta_description', $seo['description'])">
	<link rel="canonical" href="{{ url()->current() }}">
	<meta property="og:type" content="website">
	<meta property="og:site_name" content="Dikelolain">
	<meta property="og:title" content="@yield('title')">
	<meta property="og:description" content="@yield('meta_description', $seo['description'])">
	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:image" content="{{ $seo['image'] }}">
	@stack('meta')
	<meta name="author" content="Awaiken">

    <title>@yield('title')</title>

	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-dikelolain-potrait.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('css/slicknav.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
	<link href="{{ asset('css/all.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
	<link rel="stylesheet" href="{{ asset('css/mousecursor.css') }}">
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('css/frontend-override.css') }}" rel="stylesheet" media="screen">

    @stack('styles')
</head>
<body>

    @include('partials.preloader')
	@include('partials.header')
	<main>
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Portfolio Detail Modal (Global) --}}
    <div class="portfolio-modal" id="portfolioModal">
        <div class="portfolio-modal-overlay"></div>
        <div class="portfolio-modal-content">
            <button class="portfolio-modal-close">&times;</button>
            <div class="portfolio-modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="work-single-sidebar">
                                <div class="work-category-list">
                                    <div class="work-category-title">
                                        <h3>Project Details</h3>
                                    </div>
                                    <div class="category-item-list" id="modalProjectDetails"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="work-single-content">
                                <div class="work-entry">
                                    <div class="project-overview" id="modalProjectOverview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/validator.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
	<script src="{{ asset('js/isotope.min.js') }}"></script>
    <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/SmoothScroll.js') }}"></script>
    <script src="{{ asset('js/parallaxie.js') }}"></script>
    <script src="{{ asset('js/gsap.min.js') }}"></script>
    <script src="{{ asset('js/magiccursor.js') }}"></script>
    <script src="{{ asset('js/SplitText.js') }}"></script>
    <script src="{{ asset('js/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mb.YTPlayer.min.js') }}"></script>
    <script src="{{ asset('js/wow.min.js') }}"></script>
    <script src="{{ asset('js/function.js') }}"></script>

    <!-- Disable animations gently -->
    <script>
    (function() {
        // Just disable WOW.js, keep everything else normal
        document.addEventListener('DOMContentLoaded', function() {
            // Make all .wow elements visible immediately
            document.querySelectorAll('.wow').forEach(function(el) {
                el.style.visibility = 'visible';
                el.style.animationName = 'none';
            });
        });
    })();
    </script>

    <!-- Portfolio Modal Script -->
    <script>
    (function($) {
        "use strict";

        // Built with text()/attr() so project titles and descriptions can never inject HTML.
        // The media source is prepared by the server (YouTube embed or image URL).
        function openPortfolioModal($trigger) {
            var title = $trigger.attr('data-title') || '';
            var desc = $trigger.attr('data-desc') || '';
            var link = $trigger.attr('data-link') || '';
            var category = $trigger.attr('data-category') || '';
            var mediaType = $trigger.attr('data-media-type') === 'video' ? 'video' : 'image';
            var mediaSrc = $trigger.attr('data-media-src') || '';

            var $details = $('#modalProjectDetails').empty();
            [['Project Name:', title], ['Category:', category]].forEach(function (row) {
                $('<div class="category-list-item">').append($('<h3>').text(row[0]), $('<p>').text(row[1])).appendTo($details);
            });

            var $overview = $('#modalProjectOverview').empty().append('<h2>Project <span>Overview</span></h2>');
            if (mediaSrc) {
                var $media = mediaType === 'video'
                    ? $('<iframe allowfullscreen>').attr({ src: mediaSrc, title: title })
                    : $('<img>').attr({ src: mediaSrc, alt: title });
                $('<div class="project-media">').append($media).appendTo($overview);
            }
            if (desc) {
                $('<p>').text(desc).appendTo($overview);
            }
            if (/^https?:\/\//i.test(link)) {
                $('<a class="visit-btn" target="_blank" rel="noopener">').attr('href', link).text('Visit Project').appendTo($overview);
            }

            $('#portfolioModal').addClass('active');
            $('body').css({'overflow': 'hidden', 'overflow-x': 'clip'});
        }

        function closePortfolioModal() {
            var $modal = $('#portfolioModal');
            $modal.addClass('closing');
            setTimeout(function() {
                $modal.removeClass('active closing');
                $('#modalProjectOverview').empty(); // stops a playing video
                $('body').css({'overflow-y': 'auto', 'overflow-x': 'clip'});
            }, 300);
        }

        $(document).ready(function() {
            $(document).on('click', '.open-portfolio-modal', function(e) {
                e.preventDefault();
                openPortfolioModal($(this));
            });

            $(document).on('click', '.portfolio-modal-close, .portfolio-modal-overlay', function() {
                closePortfolioModal();
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#portfolioModal').hasClass('active')) {
                    closePortfolioModal();
                }
            });
        });
    })(jQuery);
    </script>

    @stack('scripts')

    @auth
        @include('vcms.admin-bar')
    @endauth
</body>
</html>
