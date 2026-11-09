<header class="main-header">
    <div class="header-sticky">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                {{-- The logo link itself is the edit zone, so visitors get the same <a class="navbar-brand"> as before. Shown 45px high (custom.css). --}}
                <x-vcms.settings :keys="['header.logo']" label="Logo" tag="a" class="navbar-brand" :href="route('home')">
                    <x-img :src="$settings->url('header.logo')" sizes="180px" alt="Logo" />
                </x-vcms.settings>
                <div class="collapse navbar-collapse main-menu">
                    <div class="nav-menu-wrapper">
                        <ul class="navbar-nav mr-auto" id="menu">
                            <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('about') }}">About Us</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('services', 'pages.service.single') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('services') }}">Services</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('portfolio') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('portfolio') }}">Portfolio</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('pricing') ? 'active' : '' }}">
                                <a href="{{ route('pricing') }}" class="nav-link">Pricing</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('faqs') ? 'active' : '' }}">
                                <a href="{{ route('faqs') }}" class="nav-link">Faqs</a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('testimonials', 'pages.team.single') ? 'active' : '' }}">
                                <a href="{{ route('testimonials') }}" class="nav-link">Testimonials</a>
                            </li>

                            {{-- <li class="nav-item submenu">
                                <a class="nav-link" href="#">Pages</a>
                                <ul>

                                    <li class="nav-item"><a class="nav-link" href="404.html">404</a></li>
                                </ul>
                            </li> --}}
                            <li class="nav-item highlighted-menu {{ request()->routeIs('contact') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('contact') }}">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                    <div class="header-btn">
                        <a href="{{ route('contact') }}" class="btn-default">Contact Us</a>
                    </div>
                    </div>
                <div class="navbar-toggle"></div>
            </div>
        </nav>
        <div class="responsive-menu"></div>
    </div>
</header>
