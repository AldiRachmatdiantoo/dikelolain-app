<aside class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header Start -->
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo-dikelolain.png') }}" alt="Dikelolain Logo">
        </a>
        <button class="sidebar-burger" id="sidebarBurger" title="Toggle Fullscreen">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
    <!-- Sidebar Header End -->

    <!-- Sidebar Menu Start (Scrollable) -->
    <nav class="sidebar-menu">
        <ul>
            <li class="menu-item {{ request()->routeIs('admin.pages.analytics') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.analytics') }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="menu-section">
                <span>Pages Management</span>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.home') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.home') }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Home Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.about') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.about') }}">
                    <i class="fa-solid fa-info-circle"></i>
                    <span>About Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.services') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.services') }}">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>Services Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.portfolio') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.portfolio') }}">
                    <i class="fa-solid fa-folder-open"></i>
                    <span>Portfolio Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.team') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.team') }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Team Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.pricing') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.pricing') }}">
                    <i class="fa-solid fa-tag"></i>
                    <span>Pricing Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.contact') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.contact') }}">
                    <i class="fa-solid fa-envelope"></i>
                    <span>Contact Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.faqs') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.faqs') }}">
                    <i class="fa-solid fa-circle-question"></i>
                    <span>FAQs Page</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.testimonials') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.testimonials') }}">
                    <i class="fa-solid fa-star"></i>
                    <span>Testimonials Page</span>
                </a>
            </li>
        </ul>
    </nav>
    <!-- Sidebar Menu End -->

    <!-- Sidebar Footer Start -->
    <div class="sidebar-footer">
        <div class="admin-user-info">
            <div class="user-avatar">
                <a href="{{route('admin.profile-detail')}}" style="text-decoration: none;"><img src="{{ asset('images/default-profilee.png') }}" alt="Admin">
                </a>
            </div>
            <div class="user-details">
                <h4>{{Auth::user()->name}}</h4>
                <p>{{Auth::user()->email}}</p>
            </div>
            <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
    <!-- Sidebar Footer End -->

</aside>
