<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - Dikelolain VCMS</title>

    <!-- Favicon and Touch Icons -->
    <link href="{{ asset('images/favicon-dikelolain.png') }}" rel="icon" type="image/png">

    <!-- Main CSS Files -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('css/all.min.css') }}" rel="stylesheet" media="screen">
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
                    <div class="admin-actions">
                        <a href="{{ route('home') }}" class="btn-preview" target="_blank">
                            <i class="fa-solid fa-external-link"></i> Preview Site
                        </a>
                    </div>
                </div>
            </div>
            <!-- Admin Topbar End -->

            <div class="admin-page-content">
                @yield('content')
            </div>

        </div>
        <!-- Admin Content Area End -->

    </div>
    <!-- Admin Wrapper End -->

    <!-- Bootstrap (modals on the analytics page) and the sidebar toggle -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>

    @stack('scripts')

</body>
</html>
