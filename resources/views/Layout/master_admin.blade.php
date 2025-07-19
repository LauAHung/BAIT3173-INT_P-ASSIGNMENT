<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TravelFree - Admin')</title>
    <link href="css/AdminPage/AdminPage.css" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="main-layout">
        <aside class="sidebar">
            <h2 class="sidebar-title">Admin Panel</h2>
            <ul class="sidebar-menu">
                <li><a href="{{ route('dashboard') }}" title="Dashboard"><img src="{{ asset('images/icon/dashboard.png') }}" /> <span>Dashboard</span></a></li>
                <li><a href="{{ route('train-management') }}" title="Train Management"><img src="{{ asset('images/icon/train.png') }}" /> <span>Train Management</span></a></li>
                <li><a href="{{ route('user-management') }}" title="User Management"><img src="{{ asset('images/icon/user.png') }}" /> <span>User Management</span></a></li>
                <li><a href="{{ route('news-email-publish') }}" title="News Email Publish"><img src="{{ asset('images/icon/email.png') }}" /> <span>News Email Publish</span></a></li>
                <li><a href="{{ route('card-approval') }}" title="Concession Card Approval"><img src="{{ asset('images/icon/application.png') }}" /> <span>Concession Card Approval</span></a></li>
                <li><a href="{{ route('scan_qr') }}"><img src="{{ asset('images/icon/scan.png') }}" /> <span>Scan QR Code</span></a></li>
                <li><a href="{{ route('log') }}"><img src="{{ asset('images/icon/log.png') }}" /> <span>Log</span></a></li>
            </ul>
        </aside>

        <div class="theme-toggle">
            <button id="theme-toggle-btn" title="Toggle Dark/Light Mode">
                <i class="fas fa-lightbulb"></i>
            </button>
        </div>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
    <script src="{{ asset('js/AdminPage.js') }}" defer></script>
</body>

</html>