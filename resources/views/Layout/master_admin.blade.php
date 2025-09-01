<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ADMIN - Dashboard')</title>
    <link href="css/AdminPage/AdminPage.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>

<body>
    <div class="main-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">ADMIN</h2>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    <li class="menu-item">
                        <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('train-management') }}" class="menu-link {{ request()->routeIs('train-management') ? 'active' : '' }}">
                            <i class="fas fa-train"></i>
                            <span>Train Management</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('user-management') }}" class="menu-link {{ request()->routeIs('user-management') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('news-email-publish') }}" class="menu-link {{ request()->routeIs('news-email-publish') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>News & Email</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('card-approval') }}" class="menu-link {{ request()->routeIs('card-approval') ? 'active' : '' }}">
                            <i class="fas fa-id-card"></i>
                            <span>Card Approval</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('scan_qr') }}" class="menu-link {{ request()->routeIs('scan_qr') ? 'active' : '' }}">
                            <i class="fas fa-qrcode"></i>
                            <span>Scan QR</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('log') }}" class="menu-link {{ request()->routeIs('log') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Logs</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div class="header-left">
                    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="header-right">
                    <div class="header-controls">
                        <button class="theme-toggle-btn" title="Toggle Theme">
                            <i class="fas fa-sun"></i>
                        </button>
                        <button class="notification-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div class="user-profile">
                            <span class="user-greeting">Hey, Admin</span>
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
    
    <script src="{{ asset('js/AdminPage.js') }}" defer></script>
    @stack('scripts')
</body>

</html>