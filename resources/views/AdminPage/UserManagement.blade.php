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
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar primary" style="width: {{ $stats['total'] > 0 ? min(100, ($stats['pending'] / $stats['total']) * 100) : 0 }}%"></div>
                </div>
            </div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending Verification</div>
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
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                </select>
                <button class="btn btn-primary" onclick="filterUsers()">
                    <i class="fas fa-search"></i>
                    Filter
                </button>
                <button class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-undo"></i>
                    Reset
                </button>
                <button class="btn btn-warning" onclick="testFunction()">
                    <i class="fas fa-bug"></i>
                    Test JS
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
                                    <option value="active" {{ $user->account_status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->account_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $user->account_status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="pending_verification" {{ $user->account_status == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                                </select>
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="status-badge delivered">Verified</span>
                                @else
                                    <span class="status-badge pending">Not Verified</span>
                                @endif
                            </td>
                            <td><span class="time-value">{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</span></td>
                            <td><span class="time-value">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-success btn-sm save-btn" data-user-id="{{ $user->user_id }}">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-user-id="{{ $user->user_id }}">
                                        <i class="fas fa-trash"></i>
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
                        {{ $users->appends(request()->query())->links() }}
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
// Simple test to ensure script loads
console.log('=== User Management Script Loaded ===');
console.log('Testing console output...');

// Simple test function
function testFunction() {
    console.log('Test function called!');
    alert('Test function works!');
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM Content Loaded ===');
    alert('DOM is ready!');
    
    // Test if we can find any buttons
    const allButtons = document.querySelectorAll('button');
    console.log('Total buttons found:', allButtons.length);
    
    // Add event listeners for save buttons
    const saveButtons = document.querySelectorAll('.save-btn');
    console.log('Found save buttons:', saveButtons.length);
    
    saveButtons.forEach((button, index) => {
        const userId = button.getAttribute('data-user-id');
        console.log(`Setting up save button ${index} for user:`, userId);
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Save button clicked!');
            const userId = this.getAttribute('data-user-id');
            console.log('Save button clicked for user:', userId);
            saveUserChanges(userId);
        });
    });
    
    // Add event listeners for delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    
    deleteButtons.forEach((button, index) => {
        const userId = button.getAttribute('data-user-id');
        console.log(`Setting up delete button ${index} for user:`, userId);
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Delete button clicked!');
            const userId = this.getAttribute('data-user-id');
            console.log('Delete button clicked for user:', userId);
            deleteUser(userId);
        });
    });
    
    // Add event listeners for status select changes
    const statusSelects = document.querySelectorAll('.status-select');
    console.log('Found status selects:', statusSelects.length);
    
    statusSelects.forEach((select, index) => {
        const userId = select.getAttribute('data-user-id');
        console.log(`Setting up status select ${index} for user:`, userId);
        
        select.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const status = this.value;
            console.log('Status changed for user:', userId, 'to:', status);
        });
    });
    
    console.log('Event listeners set up successfully');
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
    console.log('Updating user status:', userId, status);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        showMessage('CSRF token not found. Please refresh the page.', 'error');
        return;
    }
    
    console.log('CSRF Token:', csrfToken.getAttribute('content'));
    console.log('Request URL:', `/admin/api/users/${userId}/status`);
    console.log('Request body:', JSON.stringify({ status: status }));
    
    fetch(`/admin/api/users/${userId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showMessage('User status updated successfully!', 'success');
            // Optionally reload the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showMessage('Failed to update user status: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Error updating user status: ' + error.message, 'error');
    });
}

function saveUserChanges(userId) {
    console.log('saveUserChanges called for user:', userId);
    
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (!row) {
        console.error('Row not found for user:', userId);
        showMessage('Error: User row not found', 'error');
        return;
    }
    
    const statusSelect = row.querySelector('.status-select');
    if (!statusSelect) {
        console.error('Status select not found for user:', userId);
        showMessage('Error: Status select not found', 'error');
        return;
    }
    
    const status = statusSelect.value;
    console.log('Saving status:', status, 'for user:', userId);
    
    updateUserStatus(userId, status);
}

function deleteUser(userId) {
    console.log('Deleting user:', userId);
    
    if (confirm('Are you sure you want to delete this user?')) {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            showMessage('CSRF token not found. Please refresh the page.', 'error');
            return;
        }
        
        console.log('CSRF Token:', csrfToken.getAttribute('content'));
        console.log('Request URL:', `/admin/api/users/${userId}`);
        
        fetch(`/admin/api/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Delete response data:', data);
            if (data.success) {
                showMessage('User deleted successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showMessage('Failed to delete user: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showMessage('Error deleting user: ' + error.message, 'error');
        });
    }
}

function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const messageText = document.getElementById('message-text');
    const messageContent = document.getElementById('message-content');
    
    messageText.textContent = message;
    messageContent.className = `message-content message-${type}`;
    container.style.display = 'block';
    
    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

function closeMessage() {
    document.getElementById('message-container').style.display = 'none';
}

function exportUsers() {
    const search = document.getElementById('search-username').value;
    const status = document.getElementById('filter-status').value;
    
    let url = '/admin/api/users/export?';
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (status) url += `status=${encodeURIComponent(status)}&`;
    
    window.location.href = url;
}
</script>
@endpush