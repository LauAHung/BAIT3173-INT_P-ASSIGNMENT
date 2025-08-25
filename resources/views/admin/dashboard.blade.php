@extends('Layout.master')

@section('title', 'Admin Dashboard - TravelFree')

@push('styles')
<style>
.admin-dashboard {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 18px;
}

.stat-card .number {
    font-size: 36px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
}

.stat-card .label {
    color: #666;
    font-size: 14px;
}

.recent-section {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.recent-section h2 {
    margin: 0 0 20px 0;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.user-item, .booking-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.user-item:last-child, .booking-item:last-child {
    border-bottom: none;
}

.user-info, .booking-info {
    flex: 1;
}

.user-name, .booking-id {
    font-weight: bold;
    color: #333;
}

.user-email, .booking-details {
    color: #666;
    font-size: 14px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-suspended {
    background: #f8d7da;
    color: #721c24;
}

.admin-nav {
    background: #333;
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 10px;
}

.admin-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 20px;
}

.admin-nav a {
    color: white;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 5px;
    transition: background 0.3s;
}

.admin-nav a:hover {
    background: #555;
}

.admin-nav a.active {
    background: #007bff;
}
</style>
@endpush

@section('content')
<div class="admin-dashboard">
    <div class="admin-nav">
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="active">Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}">Users</a></li>
            <li><a href="{{ route('admin.trains') }}">Trains</a></li>
            <li><a href="{{ route('admin.qr-scanner') }}">QR Scanner</a></li>
            <li><a href="{{ route('admin.newsletter') }}">Newsletter</a></li>
            <li><a href="{{ route('admin.refunds') }}">Refunds</a></li>
            <li><a href="{{ route('admin.system-info') }}">System Info</a></li>
        </ul>
    </div>

    <h1>Admin Dashboard</h1>

    @if(isset($stats) && $stats['success'])
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number">{{ $stats['data']['total_users'] ?? 0 }}</div>
                <div class="label">Registered Users</div>
            </div>
            
            <div class="stat-card">
                <h3>Active Users</h3>
                <div class="number">{{ $stats['data']['active_users'] ?? 0 }}</div>
                <div class="label">Verified & Active</div>
            </div>
            
            <div class="stat-card">
                <h3>Social Users</h3>
                <div class="number">{{ $stats['data']['social_users'] ?? 0 }}</div>
                <div class="label">Google/Facebook Login</div>
            </div>
            
            <div class="stat-card">
                <h3>Pending Users</h3>
                <div class="number">{{ $stats['data']['pending_users'] ?? 0 }}</div>
                <div class="label">Awaiting Verification</div>
            </div>
            
            <div class="stat-card">
                <h3>Total Trains</h3>
                <div class="number">{{ $stats['data']['total_trains'] ?? 0 }}</div>
                <div class="label">Available Routes</div>
            </div>
            
            <div class="stat-card">
                <h3>Active Trains</h3>
                <div class="number">{{ $stats['data']['active_trains'] ?? 0 }}</div>
                <div class="label">Currently Running</div>
            </div>
            
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="number">{{ $stats['data']['total_bookings'] ?? 0 }}</div>
                <div class="label">All Time</div>
            </div>
            
            <div class="stat-card">
                <h3>Pending Refunds</h3>
                <div class="number">{{ $stats['data']['pending_refunds'] ?? 0 }}</div>
                <div class="label">Awaiting Processing</div>
            </div>
        </div>

        @if(isset($stats['data']['recent_users']) && count($stats['data']['recent_users']) > 0)
        <div class="recent-section">
            <h2>Recent Users</h2>
            @foreach($stats['data']['recent_users'] as $user)
            <div class="user-item">
                <div class="user-info">
                    <div class="user-name">{{ $user->first_name }} {{ $user->last_name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                </div>
                <span class="status-badge status-{{ $user->account_status }}">
                    {{ ucfirst(str_replace('_', ' ', $user->account_status)) }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

        @if(isset($stats['data']['recent_bookings']) && count($stats['data']['recent_bookings']) > 0)
        <div class="recent-section">
            <h2>Recent Bookings</h2>
            @foreach($stats['data']['recent_bookings'] as $booking)
            <div class="booking-item">
                <div class="booking-info">
                    <div class="booking-id">Booking #{{ $booking->id ?? 'N/A' }}</div>
                    <div class="booking-details">
                        {{ $booking->user->first_name ?? 'Unknown' }} {{ $booking->user->last_name ?? 'User' }}
                        - {{ $booking->created_at ?? 'N/A' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    @else
        <div class="alert alert-danger">
            <h3>Error Loading Dashboard</h3>
            <p>{{ $stats['message'] ?? 'Unable to load dashboard statistics' }}</p>
        </div>
    @endif
</div>

<script>
// Auto-refresh dashboard stats every 30 seconds
setInterval(function() {
    fetch('/admin/api/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Update stats without page refresh
                console.log('Dashboard stats updated');
            }
        })
        .catch(error => {
            console.error('Failed to refresh dashboard stats:', error);
        });
}, 30000);
</script>
@endsection 