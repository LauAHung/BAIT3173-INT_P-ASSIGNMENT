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
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <select name="state" id="filterState">
                    <option value="">All States</option>
                </select>
                <select name="station" id="filterStation">
                    <option value="">All Stations</option>
                </select>
            </div>
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
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const stateSel = document.getElementById('filterState');
    const stationSel = document.getElementById('filterStation');

    // Load filters
    try {
        const f = await fetch('/api/admin/dashboard/filters');
        const fjson = await f.json();
        if (fjson.success) {
            (fjson.data.states || []).forEach(s => { const o = document.createElement('option'); o.value = s; o.textContent = s; stateSel.appendChild(o); });
            (fjson.data.stations || []).forEach(s => { const o = document.createElement('option'); o.value = s; o.textContent = s; stationSel.appendChild(o); });
        }
    } catch (e) { console.error(e); }

    const tripsCtx = document.getElementById('tripsChart').getContext('2d');
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    const profitCtx = document.getElementById('profitChart').getContext('2d');

    const tripsChart = new Chart(tripsCtx, { type: 'bar', data: { labels: [], datasets: [{ label: 'Trips', data: [], backgroundColor: '#667eea' }] }, options: { responsive: true } });
    const usersChart = new Chart(usersCtx, { type: 'line', data: { labels: [], datasets: [{ label: 'Users', data: [], borderColor: '#4CAF50', backgroundColor: 'rgba(76,175,80,0.2)' }] }, options: { responsive: true } });
    const profitChart = new Chart(profitCtx, { type: 'line', data: { labels: [], datasets: [{ label: 'Profit (RM)', data: [], borderColor: '#ffbe00', backgroundColor: 'rgba(255,190,0,0.2)' }] }, options: { responsive: true } });

    async function loadTrips() {
        const params = new URLSearchParams();
        if (stateSel.value) params.append('state', stateSel.value);
        if (stationSel.value) params.append('station', stationSel.value);
        const r = await fetch('/api/admin/dashboard/trips?' + params.toString());
        const j = await r.json();
        if (j.success) {
            const labels = j.data.map(x => x.ym);
            const data = j.data.map(x => x.total);
            tripsChart.data.labels = labels; tripsChart.data.datasets[0].data = data; tripsChart.update();
        }
    }

    async function loadUsers() {
        const r = await fetch('/api/admin/dashboard/users-growth');
        const j = await r.json();
        if (j.success) { usersChart.data.labels = j.data.map(x => x.ym); usersChart.data.datasets[0].data = j.data.map(x => x.total); usersChart.update(); }
    }

    async function loadProfit() {
        const r = await fetch('/api/admin/dashboard/profit');
        const j = await r.json();
        if (j.success) { profitChart.data.labels = j.data.map(x => x.ym); profitChart.data.datasets[0].data = j.data.map(x => x.total); profitChart.update(); }
    }

    stateSel.addEventListener('change', loadTrips);
    stationSel.addEventListener('change', loadTrips);

    await Promise.all([loadTrips(), loadUsers(), loadProfit()]);
});
</script>
@endpush
@endsection