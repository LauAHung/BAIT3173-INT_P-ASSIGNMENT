@extends('Layout.master_admin')

@section('title', 'ADMIN - Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <link href="css/AdminPage/Dashboard.css" rel="stylesheet">
@endpush

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-route"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar primary" style="width: 75%"></div>
                </div>
            </div>
            <div class="stat-value">2</div>
            <div class="stat-label">Total Routes</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-train"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar success" style="width: 85%"></div>
                </div>
            </div>
            <div class="stat-value">5</div>
            <div class="stat-label">Total Stations</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon danger">
                    <i class="fas fa-road"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar danger" style="width: 60%"></div>
                </div>
            </div>
            <div class="stat-value">10</div>
            <div class="stat-label">Total Journeys</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar primary" style="width: 90%"></div>
                </div>
            </div>
            <div class="stat-value">8</div>
            <div class="stat-label">Total Active Users</div>
            <div class="stat-period">All Time</div>
        </div>
    </div>

    <!-- Charts Section -->
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
@endsection