@extends('Layout.master_admin')

@section('title', 'Admin - Dashboard')

@push('styles')
    <link href="css/AdminPage/Dashboard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
        <div class="dashboard-container">
            <h2 class="dashboard-title">Dashboard</h2>

            <div class="stats-cards">
                <div class="card card-blue">
                    <div class="card-left">
                        <p class="card-number">2</p>
                        <p class="card-label">Total Routes</p>
                    </div>
                    <div class="card-right">
                        <i class="fas fa-route"></i>
                    </div>
                </div>
                <div class="card card-green">
                    <div class="card-left">
                        <p class="card-number">5</p>
                        <p class="card-label">Total Stations</p>
                    </div>
                    <div class="card-right">
                        <i class="fas fa-train"></i>
                    </div>
                </div>
                <div class="card card-yellow">
                    <div class="card-left">
                        <p class="card-number">10</p>
                        <p class="card-label">Total Journeys</p>
                    </div>
                    <div class="card-right">
                        <i class="fas fa-road"></i>
                    </div>
                </div>
                <div class="card card-red">
                    <div class="card-left">
                        <p class="card-number">8</p>
                        <p class="card-label">Total Active Users</p>
                    </div>
                    <div class="card-right">
                        <i class="fas fa-users"></i>
                    </div>
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
@endsection