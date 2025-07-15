<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TravelFree')</title>
    <link href="css/master.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <header>
        <div>
            <a href="{{ route('HomePage') }}">
                <img src="{{ asset('images/logo_black.png') }}" alt="Logo" id="logo">
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="{{ route('TrainSelectionPage') }}">Ticketing</a></li>
                <li><a href="#">Discover</a></li>
                <li><a href="#">Feedback</a></li>
                <li><a href="#">Schedule</a></li>
            </ul>
        </nav>
        <nav>
            <ul>
                <li><a href="#">Booking</a></li>
                <li><a href="{{ route('signup') }}">Account</a></li>
            </ul>
        </nav>
        <div class="nav-animate"></div>
    </header>


    <main>
        @yield('content')
    </main>
    <footer>
        
    </footer>
    <script src="{{ asset('js/master.js') }}" defer></script>
</body>
</html>