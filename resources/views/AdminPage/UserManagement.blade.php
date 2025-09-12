@extends('Layout.master_admin')

@section('title', 'ADMIN - User Management')
@section('page-title', 'User Management')

@push('styles')
    <link href="{{ asset('css/AdminPage/UserManagement.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar primary" style="width: {{ $stats['total'] > 0 ? min(100, ($stats['active'] / $stats['total']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar success" style="width: {{ $stats['total'] > 0 ? min(100, ($stats['active'] / $stats['total']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="stat-value">{{ $stats['active'] }}</div>
            <div class="stat-label">Active Users</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon danger">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar danger" style="width: {{ $stats['total'] > 0 ? min(100, ($stats['suspended'] / $stats['total']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="stat-value">{{ $stats['suspended'] }}</div>
            <div class="stat-label">Suspended Users</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar primary" style="width: {{ $stats['total'] > 0 ? min(100, ($stats['admin'] / $stats['total']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="stat-value">{{ $stats['admin'] }}</div>
            <div class="stat-label">Admins</div>
            <div class="stat-period">All Time</div>
        </div>
    </div>

    <!-- User Management Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User Management</h3>
            <div class="card-actions">
                <button class="btn btn-primary" onclick="exportUsers()">
                    <i class="fas fa-download"></i>
                    Export Users
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-group">
                <input type="text" id="search-username" placeholder="Search by name or email" value="{{ request('search') }}" class="filter-input">
                <select id="filter-status" class="filter-select">
                    <option value="">All Status</option>
                    @isset($allowedStatuses)
                        @foreach($allowedStatuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    @endisset
                </select>
                <button class="btn btn-primary" onclick="filterUsers()">
                    <i class="fas fa-search"></i>
                    Filter
                </button>
                <button class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-undo"></i>
                    Reset
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            @if(isset($users) && $users->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Email Verified</th>
                            <th>Created At</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr data-user-id="{{ $user->user_id }}">
                            <td><span class="id-value">{{ $user->user_id }}</span></td>
                            <td>{{ $user->first_name ?? 'N/A' }} {{ $user->last_name ?? 'N/A' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <select class="status-select" data-user-id="{{ $user->user_id }}">
                                    @isset($allowedStatuses)
                                        @foreach($allowedStatuses as $value => $label)
                                            <option value="{{ $value }}" {{ $user->account_status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </td>
                            <td>
                                @php($badge = $user->getStatusBadge())
                                <span class="{{ $badge['class'] }}">{{ $badge['text'] }}</span>
                            </td>
                            <td><span class="time-value">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</span></td>
                            <td><span class="time-value">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-success btn-sm save-btn" data-user-id="{{ $user->user_id }}">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="pagination-container">
                        {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No users found</h3>
                    <p>Try adjusting your search criteria or filters.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="message-container" style="display: none;" class="message-container">
        <div id="message-content" class="message-content">
            <span id="message-text"></span>
            <button onclick="closeMessage()" class="close-btn">&times;</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveButtons = document.querySelectorAll('.save-btn');
    saveButtons.forEach((button) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const userId = this.getAttribute('data-user-id');
            saveUserChanges(userId);
        });
    });

    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach((select) => {
        select.addEventListener('change', function() {
            // Intentionally no-op; admin must click save to persist
        });
    });
});

function filterUsers() {
    const search = document.getElementById('search-username').value;
    const status = document.getElementById('filter-status').value;
    let url = '{{ route("user-management") }}?';
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (status) url += `status=${encodeURIComponent(status)}&`;
    window.location.href = url;
}

function resetFilters() {
    window.location.href = '{{ route("user-management") }}';
}

function updateUserStatus(userId, status) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showMessage('CSRF token not found. Please refresh the page.', 'error');
        return;
    }
    fetch(`/api/admin/users/${userId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status })
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage('User status updated successfully!', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showMessage('Failed to update user status: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('Error updating user status: ' + error.message, 'error');
    });
}

function saveUserChanges(userId) {
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!row) { showMessage('Error: User row not found', 'error'); return; }
    const statusSelect = row.querySelector('.status-select');
    if (!statusSelect) { showMessage('Error: Status select not found', 'error'); return; }
    updateUserStatus(userId, statusSelect.value);
}

function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const messageText = document.getElementById('message-text');
    const messageContent = document.getElementById('message-content');
    messageText.textContent = message;
    messageContent.className = `message-content message-${type}`;
    container.style.display = 'block';
    setTimeout(() => { container.style.display = 'none'; }, 5000);
}

function closeMessage() {
    document.getElementById('message-container').style.display = 'none';
}

async function exportUsers() {
    try {
        showMessage('Preparing export...', 'success');
        // Step 1: issue token via AdminController export endpoint
        const issueRes = await fetch('/api/admin/export?type=users&format=csv');
        const issueData = await issueRes.json().catch(() => ({}));
        if (!issueRes.ok || !issueData.success) {
            const msg = (issueData && issueData.message) ? issueData.message : `Export failed (HTTP ${issueRes.status})`;
            showMessage(msg, 'error');
            return;
        }
        if (!issueData.success) {
            showMessage(issueData.message || 'Failed to issue export token', 'error');
            return;
        }
        const token = issueData.download_token;
        // Step 2: download via token (JSON payload -> Blob)
        const dlRes = await fetch(`/api/admin/export/download?token=${encodeURIComponent(token)}`);
        const dlData = await dlRes.json().catch(() => ({}));
        if (!dlRes.ok || !dlData.success) {
            const msg = (dlData && dlData.message) ? dlData.message : `Download failed (HTTP ${dlRes.status})`;
            showMessage(msg, 'error');
            return;
        }
        const filename = dlData.filename || 'users_export.csv';
        const blob = new Blob([dlData.content], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        showMessage('Export completed', 'success');
    } catch (e) {
        showMessage('Export error: ' + (e && e.message ? e.message : 'Unknown'), 'error');
    }
}
</script>
@endpush