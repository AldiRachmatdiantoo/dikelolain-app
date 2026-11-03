<!DOCTYPE html>
<html lang="zxx">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<meta name="description" content="">
	<meta name="keywords" content="">
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

        function openPortfolioModal($trigger) {
            var title = $trigger.data('title') || 'Project Title';
            var desc = $trigger.data('desc') || 'Project description';
            var link = $trigger.data('link') || '#';
            var mediaType = $trigger.data('media-type') || 'image';
            var mediaSrc = $trigger.data('media-src') || '';

            var detailsHTML = '<div class="category-list-item"><h3>Project Name:</h3><p>' + title + '</p></div>' +
                '<div class="category-list-item"><h3>Category:</h3><p>' + (mediaType === 'video' ? 'Video Production' : 'Design') + '</p></div>';
            $('#modalProjectDetails').html(detailsHTML);

            var mediaHTML = '';
            if (mediaType === 'video' && mediaSrc) {
                var embedUrl = mediaSrc;
                if (embedUrl.indexOf('youtube.com/watch') !== -1) {
                    var match = embedUrl.match(/[?&]v=([a-zA-Z0-9_-]+)/);
                    if (match) embedUrl = 'https://www.youtube-nocookie.com/embed/' + match[1];
                } else if (embedUrl.indexOf('youtu.be/') !== -1) {
                    var match = embedUrl.match(/youtu\.be\/([a-zA-Z0-9_-]+)/);
                    if (match) embedUrl = 'https://www.youtube-nocookie.com/embed/' + match[1];
                }
                mediaHTML = '<div class="project-media"><iframe src="' + embedUrl + '" allowfullscreen></iframe></div>';
            } else if (mediaSrc) {
                mediaHTML = '<div class="project-media"><img src="' + mediaSrc + '" alt="' + title + '"></div>';
            }

            var overviewHTML = '<h2>Project <span>Overview</span></h2>' + mediaHTML +
                '<p>' + desc + '</p>' +
                '<a href="' + link + '" class="visit-btn" target="_blank">Visit Project</a>';
            $('#modalProjectOverview').html(overviewHTML);

            $('#portfolioModal').addClass('active');
            $('body').css({'overflow': 'hidden', 'overflow-x': 'clip'});
        }

        function closePortfolioModal() {
            var $modal = $('#portfolioModal');
            $modal.addClass('closing');
            setTimeout(function() {
                $modal.removeClass('active closing');
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
</body>
</html>
