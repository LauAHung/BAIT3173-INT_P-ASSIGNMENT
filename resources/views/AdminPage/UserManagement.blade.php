@extends('Layout.master_admin')

@section('title', 'Admin - User Management')

@push('styles')
    <link href="{{ asset('css/AdminPage/UserManagement.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="user-management-container">
    <h2 class="page-title">User Management</h2>

    <div class="user-table-filters">
        <input type="text" id="search-username" placeholder="Search by name or email" value="{{ request('search') }}">
        <select id="filter-status">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
            <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
        </select>
        <button class="btn-primary" onclick="filterUsers()">Filter</button>
        <button class="btn-secondary" onclick="resetFilters()">Reset</button>
    </div>

    <div class="user-table-section">
        @if(isset($users['data']) && $users['data']->count() > 0)
            <table>
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
                    @foreach($users['data'] as $user)
                    <tr data-user-id="{{ $user->user_id }}">
                        <td>{{ $user->user_id }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
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
                                <span class="badge badge-success">Verified</span>
                            @else
                                <span class="badge badge-warning">Not Verified</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}</td>
                        <td>
                            <button class="btn-primary btn-sm save-btn" data-user-id="{{ $user->user_id }}">Save</button>
                            <button class="btn-danger btn-sm delete-btn" data-user-id="{{ $user->user_id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            @if($users['data']->hasPages())
                <div class="pagination-container">
                    {{ $users['data']->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="no-users-message">
                <p>No users found.</p>
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
document.addEventListener('DOMContentLoadd', function() {
    // Add event listeners for save buttons
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            saveUserChanges(userId);
        });
    });
    
    // Add event listeners for delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            deleteUser(userId);
        });
    });
    
    // Add event listeners for status select changes
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const status = this.value;
            updateUserStatus(userId, status);
        });
    });
});

function filterUsers() {
    const search = document.getElementById('search-username').value;
    const status = document.getElementById('filter-status').value;
    
    let url = '{{ route("admin.users") }}?';
    if (search) url += `search=${encodeURIComponent(search)}&`;
    if (status) url += `status=${encodeURIComponent(status)}&`;
    
    window.location.href = url;
}

function resetFilters() {
    window.location.href = '{{ route("admin.users") }}';
}

function updateUserStatus(userId, status) {
    fetch(`/admin/api/users/${userId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('User status updated successfully!', 'success');
        } else {
            showMessage('Failed to update user status: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showMessage('Error updating user status: ' + error.message, 'error');
    });
}

function saveUserChanges(userId) {
    const row = document.querySelector(`tr[data-user-id="${userId}"]`);
    const status = row.querySelector('.status-select').value;
    
    updateUserStatus(userId, status);
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(`/admin/api/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage('User deleted successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showMessage('Failed to delete user: ' + data.message, 'error');
            }
        })
        .catch(error => {
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
</script>
@endpush