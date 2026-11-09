<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
	<meta name="author" content="Dikelolain">

    <title>@yield('title')</title>

	<link rel="icon" type="image/png" href="{{ asset('images/favicon-dikelolain.png') }}">

    {{-- Montserrat comes from this site (css/fonts.css); fetching it now, alongside the CSS, means it is usually there before the first paint. --}}
    <link rel="preload" href="{{ asset('webfonts/montserrat/montserrat-latin.woff2') }}" as="font" type="font/woff2" crossorigin>

    {{-- Template CSS joined and minified (resources/site-assets.json, npm run build:site). --}}
    <link href="{{ asset('css/site.min.css') }}?v={{ filemtime(public_path('css/site.min.css')) }}" rel="stylesheet">

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

    {{-- jQuery, plugins and function.js joined and minified (resources/site-assets.json, npm run build:site). --}}
    <script src="{{ asset('js/site.min.js') }}?v={{ filemtime(public_path('js/site.min.js')) }}"></script>

    @stack('scripts')

    @auth
        @include('vcms.admin-bar')
    @endauth
</body>
</html>
