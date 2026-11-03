<aside class="admin-sidebar" id="adminSidebar">
    <!-- Sidebar Header Start -->
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo-dikelolain.webp') }}" alt="Dikelolain Logo">
        </a>
        <button class="sidebar-burger" id="sidebarBurger" title="Toggle Fullscreen">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
    <!-- Sidebar Header End -->

    <!-- Sidebar Menu Start (Scrollable) -->
    <nav class="sidebar-menu">
        <ul>
            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-gauge"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('admin.pages.analytics') ? 'active' : '' }}">
                <a href="{{ route('admin.pages.analytics') }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Analytics</span>
                </a>
            </li>
            
            <li class="menu-section">
                <span>Edit Halaman</span>
            </li>

            {{-- Each link turns edit mode on and opens the public page. --}}
            @foreach (\App\Vcms\EditablePages::all() as $page)
                <li class="menu-item">
                    <a href="{{ route('admin.edit-mode.on', ['to' => $page['path']]) }}">
                        <i class="fa-solid {{ $page['icon'] }}"></i>
                        <span>{{ $page['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
    <!-- Sidebar Menu End -->

    <!-- Sidebar Footer Start -->
    <div class="sidebar-footer">
        <div class="admin-user-info">
            <div class="user-avatar">
                <a href="{{route('admin.profile-detail')}}" style="text-decoration: none;"><img src="{{ asset('images/default-profilee.webp') }}" alt="Admin">
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
