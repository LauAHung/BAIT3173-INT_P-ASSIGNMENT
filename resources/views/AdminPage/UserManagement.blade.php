@extends('Layout.master_admin')

@section('title', 'Admin - User Management')

@push('styles')
    <link href="css/AdminPage/UserManagement.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="user-management-container">
    <h2 class="page-title">User Management</h2>

    <div class="user-table-filters">
        <input type="text" id="search-username" placeholder="Search by username">
        <select id="filter-status">
            <option value="">All Status</option>
            <option value="normal">Normal</option>
            <option value="locked">Locked</option>
        </select>
        <select id="filter-role">
            <option value="">All Roles</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        <button class="btn-primary" onclick="filterUsers()">Filter</button>
    </div>

    <div class="user-table-section">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
                <tr>
                    <td>1</td>
                    <td>alice</td>
                    <td>2024-06-01 10:00</td>
                    <td>
                        <select>
                            <option selected>normal</option>
                            <option>locked</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option selected>user</option>
                            <option>admin</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn-primary">Save</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>bob</td>
                    <td>2024-06-02 11:30</td>
                    <td>
                        <select>
                            <option>normal</option>
                            <option selected>locked</option>
                        </select>
                    </td>
                    <td>
                        <select>
                            <option selected>user</option>
                            <option>admin</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn-primary">Save</button>
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>
@endsection