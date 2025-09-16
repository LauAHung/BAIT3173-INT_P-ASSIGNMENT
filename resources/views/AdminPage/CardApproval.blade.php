@extends('Layout.master_admin')

@section('title', 'Admin - Card Approval')
@section('page-title', 'Card Approval')

@push('styles')
    <link href="{{ asset('css/AdminPage/CardApproval.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="card-approval-container">
    <div class="approval-header">
        <h2>Concession Card Applications</h2>
        <div class="stats-container">
            <div class="stat-item">
                <span class="stat-label">Total:</span>
                <span class="stat-value" id="total-count">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Pending:</span>
                <span class="stat-value" id="pending-count">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Approved:</span>
                <span class="stat-value" id="approved-count">0</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Rejected:</span>
                <span class="stat-value" id="rejected-count">0</span>
            </div>
        </div>
    </div>

    <div class="filters-container">
        <div class="filter-group">
            <label for="status-filter">Status:</label>
            <select id="status-filter" onchange="filterApplications()">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="type-filter">Type:</label>
            <select id="type-filter" onchange="filterApplications()">
                <option value="">All</option>
                <option value="student">Student</option>
                <option value="senior">Senior</option>
                <option value="oku">OKU</option>
            </select>
        </div>
    </div>

    <div class="table-container">
        <table class="applications-table">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>Type</th>
                    <th>IC Number</th>
                    <th>Passport</th>
                    <th>Status</th>
                    <th>Applied Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="applications-tbody">
                <!-- Applications will be loaded here -->
            </tbody>
        </table>
        
        <div id="empty-state" class="empty-state" style="display: none;">
            <p>No applications found</p>
        </div>
    </div>
</div>

<!-- Application Details Modal -->
<div id="application-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modal-body">
            <!-- Application details will be loaded here -->
        </div>
    </div>
</div>

<!-- Message Container -->
<div id="message-container" class="message-container"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/AdminConcessionApproval.js') }}"></script>
<script>
    // Initialize the page when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        loadApplications();
    });
</script>
@endpush