<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('Admin Page')</title>
    <link href="css/AdminPage.css" rel="stylesheet">
    @stack('styles')
</head>

<body>
    <div class="main-layout">
        <aside class="sidebar">
            <h2 class="sidebar-title">Admin Panel</h2>
            <ul class="sidebar-menu">
                <li><a href="#"><img src="{{ asset('images/icon/dashboard.png') }}" /> <span>Dashboard</span></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/train.png') }}" /> <span>Train Management</span></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/user.png') }}" /> <span>User Management</span></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/email.png') }}" /> <span>News Email Publish</span></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/application.png') }}" /> <span>Concession Card Approval</span></a></li>
                <li><a href="#"><img src="{{ asset('images/icon/scan.png') }}" /> <span>Scan QR Code</span></a></li>
            </ul>

        </aside>

        <div class="dashboard-container">
            <h2 class="dashboard-title">Dashboard</h2>

            <div class="stats-cards">
                <div class="card card-blue">
                    {{-- <p class="card-number">{{ $routesCount }}</p> --}}
                    <p class="card-number">2</p>
                    <p class="card-label">Total Routes</p>
                </div>
                <div class="card card-green">
                    {{-- <p class="card-number">{{ $stationsCount }}</p> --}}
                    <p class="card-number">5</p>
                    <p class="card-label">Total Stations</p>
                </div>
                <div class="card card-yellow">
                    {{-- <p class="card-number">{{ $journeysCount }}</p> --}}
                    <p class="card-number">10</p>
                    <p class="card-label">Total Journeys</p>
                </div>
                <div class="card card-red">
                    {{-- <p class="card-number">{{ $activeUsersCount }}</p> --}}
                    <p class="card-number">8</p>
                    <p class="card-label">Total Active Users</p>
                </div>
            </div>

            <div class="charts-section">
                <div class="chart-card">
                    <h3>Total Trips Per Month</h3>
                    <select name="state" id="filterState">
                        <option value="">All States</option>
                        {{-- @foreach($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                        @endforeach
                    </select>
                    <select name="station" id="filterStation">
                        <option value="">All Stations</option>
                        @foreach($stations as $station)
                        <option value="{{ $station }}">{{ $station }}</option>
                        @endforeach
                    </select>--}}
                    <canvas id="tripsChart"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Registered Users Growth</h3>
                    <canvas id="usersChart"></canvas>
                </div>

                <div class="chart-card">
                    <h3>Total Profit Per Month</h3>
                    <canvas id="profitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/AdminPage.js') }}" defer></script>
</body>

</html>