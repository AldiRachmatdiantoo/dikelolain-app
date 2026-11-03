<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Admin Dashboard') - Dikelolain VCMS</title>

    <!-- Favicon and Touch Icons -->
    <link href="{{ asset('images/logo-dikelolain-potrait.png') }}" rel="shortcut icon" type="image/png">

    <!-- Main CSS Files -->
   
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('css/slicknav.min.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}">
	<link href="{{ asset('css/all.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}">
	<link rel="stylesheet" href="{{ asset('css/mousecursor.css') }}">
	<link href="{{ asset('css/custom.css') }}" rel="stylesheet" media="screen">
    
    <!-- Admin panel layout (sidebar, topbar) -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>

<body class="admin-body">
    
    <!-- Admin Wrapper Start -->
    <div class="admin-wrapper">
        
        <!-- Mobile Sidebar Overlay -->
        <div class="admin-sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Admin Sidebar Start -->
        @include('admin.partials.sidebar')
        <!-- Admin Sidebar End -->

        <!-- Admin Content Area Start -->
        <div class="admin-content-area">
            
            <!-- Admin Topbar Start -->
            <div class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="sidebar-burger-topbar" id="sidebarBurgerTopbar" title="Toggle Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="admin-topbar-right">
                    <div class="vcms-mode-indicator">
                        <i class="fa-solid fa-eye"></i>
                        <span>VCMS Edit Mode</span>
                    </div>
                    <div class="admin-actions">
                        <a href="{{ $previewUrl ?? route('home') }}" class="btn-preview" target="_blank">
                            <i class="fa-solid fa-external-link"></i> Preview Site
                        </a>
                    </div>
                </div>
            </div>
            <!-- Admin Topbar End -->

            <!-- VCMS Visual Preview Start -->
            <div class="vcms-preview-container">
                @yield('content')
            </div>
            <!-- VCMS Visual Preview End -->

        </div>
        <!-- Admin Content Area End -->

    </div>
    <!-- Admin Wrapper End -->

    <!-- Portfolio Detail Modal (Global) -->
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

    <!-- Main JS Files -->
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
    
    <!-- Admin panel sidebar -->
    <script src="{{ asset('js/admin.js') }}"></script>

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